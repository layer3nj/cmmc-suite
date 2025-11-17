<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Middleware\AuthMiddleware;

/**
 * Evidence Collection & Management Controller
 * Manages evidence files tied to compliance controls
 */
class EvidenceController
{
    private $db;

    public function __construct()
    {
        global $app;
        $this->db = $app->getDatabase();
    }

    public function index(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');
        $customerName = Session::get('current_customer_name');

        if (!$customerId) {
            Session::flash('error', 'Please select a client first');
            return Response::redirect($request->baseUrl() . '/clients');
        }

        // Get all evidence for this client
        $evidence = $this->db->fetchAll(
            "SELECT e.*,
                    CASE
                        WHEN e.expiration_date < CURDATE() THEN 'expired'
                        WHEN e.expiration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring_soon'
                        ELSE 'valid'
                    END as validity_status
             FROM evidence e
             WHERE e.customer_id = ?
             ORDER BY e.created_at DESC",
            [$customerId]
        );

        // Get statistics
        $stats = [
            'total' => count($evidence),
            'valid' => 0,
            'expiring_soon' => 0,
            'expired' => 0,
            'pending_approval' => 0,
        ];

        foreach ($evidence as $item) {
            if ($item['status'] === 'pending') {
                $stats['pending_approval']++;
            }

            switch ($item['validity_status']) {
                case 'valid':
                    $stats['valid']++;
                    break;
                case 'expiring_soon':
                    $stats['expiring_soon']++;
                    break;
                case 'expired':
                    $stats['expired']++;
                    break;
            }
        }

        // Get available controls for filter
        $controls = $this->db->fetchAll(
            "SELECT DISTINCT control_code, control_framework
             FROM evidence
             WHERE customer_id = ?
             ORDER BY control_code",
            [$customerId]
        );

        $content = View::render('evidence/index', [
            'current_customer' => $customerName,
            'evidence' => $evidence,
            'stats' => $stats,
            'controls' => $controls,
        ]);

        return new Response($content);
    }

    /**
     * Upload new evidence
     */
    public function upload(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        if (!$customerId) {
            return new Response(json_encode(['success' => false, 'message' => 'No client selected']), 400);
        }

        // Validate CSRF
        if (!$request->validateCsrf()) {
            return new Response(json_encode(['success' => false, 'message' => 'Invalid security token']), 403);
        }

        // Get form data
        $controlCode = $request->getParam('control_code');
        $controlFramework = $request->getParam('control_framework');
        $evidenceType = $request->getParam('evidence_type');
        $description = $request->getParam('description');
        $expirationDate = $request->getParam('expiration_date');
        $notes = $request->getParam('notes');

        if (!$controlCode || !$controlFramework || !$evidenceType) {
            return new Response(json_encode(['success' => false, 'message' => 'Missing required fields']), 400);
        }

        // Handle file upload
        $uploadedFile = $_FILES['evidence_file'] ?? null;
        if (!$uploadedFile || $uploadedFile['error'] !== UPLOAD_ERR_OK) {
            return new Response(json_encode(['success' => false, 'message' => 'File upload failed']), 400);
        }

        // Validate file
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($uploadedFile['size'] > $maxSize) {
            return new Response(json_encode(['success' => false, 'message' => 'File too large (max 10MB)']), 400);
        }

        $allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'text/plain',
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $uploadedFile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return new Response(json_encode(['success' => false, 'message' => 'Invalid file type']), 400);
        }

        // Create uploads directory if needed
        $uploadsDir = APP_PATH . '/../public/uploads/evidence';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        $filename = uniqid('evidence_') . '.' . $extension;
        $filepath = $uploadsDir . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($uploadedFile['tmp_name'], $filepath)) {
            return new Response(json_encode(['success' => false, 'message' => 'Failed to save file']), 500);
        }

        // Save to database
        $this->db->execute(
            "INSERT INTO evidence
             (customer_id, control_code, control_framework, evidence_type, description,
              file_name, file_path, file_size, expiration_date, notes, status, uploaded_by, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())",
            [
                $customerId,
                $controlCode,
                $controlFramework,
                $evidenceType,
                $description,
                $uploadedFile['name'],
                '/uploads/evidence/' . $filename,
                $uploadedFile['size'],
                $expirationDate ?: null,
                $notes,
                Session::get('user_id'),
            ]
        );

        Session::flash('success', 'Evidence uploaded successfully');
        return new Response(json_encode(['success' => true, 'message' => 'Evidence uploaded successfully']));
    }

    /**
     * Download evidence file
     */
    public function download(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $evidence = $this->db->fetchOne(
            "SELECT * FROM evidence WHERE id = ? AND customer_id = ?",
            [$id, $customerId]
        );

        if (!$evidence) {
            return new Response('Evidence not found', 404);
        }

        $filepath = APP_PATH . '/../public' . $evidence['file_path'];
        if (!file_exists($filepath)) {
            return new Response('File not found', 404);
        }

        return Response::download(
            file_get_contents($filepath),
            $evidence['file_name'],
            mime_content_type($filepath)
        );
    }

    /**
     * Approve evidence
     */
    public function approve(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        if (!$request->validateCsrf()) {
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/evidence');
        }

        $id = $request->getParam('id');

        Session::start();
        $this->db->execute(
            "UPDATE evidence SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?",
            [Session::get('user_id'), $id]
        );

        Session::flash('success', 'Evidence approved');
        return Response::redirect($request->baseUrl() . '/evidence');
    }

    /**
     * Reject evidence
     */
    public function reject(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        if (!$request->validateCsrf()) {
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/evidence');
        }

        $id = $request->getParam('id');
        $rejectionReason = $request->getParam('rejection_reason');

        Session::start();
        $this->db->execute(
            "UPDATE evidence SET status = 'rejected', notes = CONCAT(notes, '\n\nRejection reason: ', ?), approved_by = ?, approved_at = NOW() WHERE id = ?",
            [$rejectionReason, Session::get('user_id'), $id]
        );

        Session::flash('success', 'Evidence rejected');
        return Response::redirect($request->baseUrl() . '/evidence');
    }

    /**
     * Delete evidence
     */
    public function delete(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        if (!$request->validateCsrf()) {
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/evidence');
        }

        $id = $request->getParam('id');

        Session::start();
        $customerId = Session::get('current_customer_id');

        // Get evidence to delete file
        $evidence = $this->db->fetchOne(
            "SELECT * FROM evidence WHERE id = ? AND customer_id = ?",
            [$id, $customerId]
        );

        if ($evidence) {
            // Delete file
            $filepath = APP_PATH . '/../public' . $evidence['file_path'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Delete database record
            $this->db->execute("DELETE FROM evidence WHERE id = ?", [$id]);

            Session::flash('success', 'Evidence deleted');
        }

        return Response::redirect($request->baseUrl() . '/evidence');
    }
}
