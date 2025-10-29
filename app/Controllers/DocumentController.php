<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Core\Csrf;
use App\Middleware\AuthMiddleware;
use App\Services\AuditLogger;

/**
 * Document Management Controller
 */
class DocumentController
{
    private $db;
    private $config;

    public function __construct()
    {
        global $app;
        $this->db = $app->getDatabase();
        $this->config = $app->getConfig();
    }

    public function index(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');
        $currentCustomer = Session::get('current_customer_name');

        if (!$customerId) {
            Session::flash('error', 'Please select a customer first.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        $documents = $this->db->fetchAll(
            "SELECT d.*, u.display_name as uploaded_by_name
             FROM documents d
             LEFT JOIN users u ON d.uploaded_by = u.id
             WHERE d.customer_id = ?
             ORDER BY d.created_at DESC",
            [$customerId]
        );

        $content = View::render('documents/index', [
            'documents' => $documents,
            'current_customer' => $currentCustomer,
        ]);

        return new Response($content);
    }

    public function upload(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $customerId = Session::get('current_customer_id');
        $file = $request->file('file');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return Response::json(['success' => false, 'message' => 'File upload failed']);
        }

        // Validate file size
        $maxSize = $this->config->get('upload.max_size', 10485760);
        if ($file['size'] > $maxSize) {
            return Response::json(['success' => false, 'message' => 'File too large']);
        }

        // Validate file type
        $allowedTypes = $this->config->get('upload.allowed_types', []);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) {
            return Response::json(['success' => false, 'message' => 'File type not allowed']);
        }

        // Generate safe filename
        $hash = bin2hex(random_bytes(16));
        $safeFilename = $hash . '.' . $extension;
        $storagePath = STORAGE_PATH . '/uploads/' . $safeFilename;

        // Ensure upload directory exists
        if (!is_dir(STORAGE_PATH . '/uploads')) {
            mkdir(STORAGE_PATH . '/uploads', 0770, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $storagePath)) {
            return Response::json(['success' => false, 'message' => 'Failed to save file']);
        }

        // Save to database
        $documentId = $this->db->insert('documents', [
            'customer_id' => $customerId,
            'file_name' => $file['name'],
            'mime_type' => $file['type'],
            'file_size' => $file['size'],
            'storage_path' => $safeFilename,
            'uploaded_by' => Session::get('user_id'),
            'linked_framework' => $request->post('framework'),
            'linked_code' => $request->post('control_code'),
            'linked_assessment_id' => $request->post('assessment_id'),
            'linked_poam_id' => $request->post('poam_id'),
            'description' => $request->post('description'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('upload', 'document', $documentId, [
            'filename' => $file['name'],
            'size' => $file['size']
        ], $request->ip());

        return Response::json(['success' => true, 'document_id' => $documentId]);
    }

    public function download(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $document = $this->db->fetchOne(
            "SELECT * FROM documents WHERE id = ? AND customer_id = ?",
            [$id, $customerId]
        );

        if (!$document) {
            return new Response('Document not found', 404);
        }

        $filePath = STORAGE_PATH . '/uploads/' . $document['storage_path'];

        if (!file_exists($filePath)) {
            return new Response('File not found', 404);
        }

        $content = file_get_contents($filePath);

        return Response::download($content, $document['file_name'], $document['mime_type']);
    }

    public function delete(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $customerId = Session::get('current_customer_id');

        $document = $this->db->fetchOne(
            "SELECT * FROM documents WHERE id = ? AND customer_id = ?",
            [$id, $customerId]
        );

        if (!$document) {
            return Response::json(['success' => false, 'message' => 'Document not found']);
        }

        // Delete file from storage
        $filePath = STORAGE_PATH . '/uploads/' . $document['storage_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        $this->db->delete('documents', 'id = :id', [':id' => $id]);

        AuditLogger::log('delete', 'document', $id, null, $request->ip());

        return Response::json(['success' => true]);
    }
}
