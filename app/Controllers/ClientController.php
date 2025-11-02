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
 * Client Controller - Multi-tenant Management
 */
class ClientController
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

        $clients = $this->db->fetchAll(
            "SELECT * FROM clients WHERE active = 1 ORDER BY name ASC"
        );

        $content = View::render('clients/index', [
            'clients' => $clients,
        ]);

        return new Response($content);
    }

    public function create(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        // Get available frameworks
        $frameworks = ['CMMC', 'NIST800171', 'STIG', 'HIPAA', 'FTC-SAFEGUARDS', 'PCI-DSS', 'SOC2', 'ISO27001'];

        $content = View::render('clients/create', [
            'frameworks' => $frameworks,
        ]);
        return new Response($content);
    }

    public function store(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/clients/create');
        }

        $name = trim($request->post('name'));
        $contactEmail = trim($request->post('contact_email'));
        $contactPhone = trim($request->post('contact_phone'));
        $address = trim($request->post('address'));

        if (empty($name)) {
            Session::flash('error', 'Client name is required.');
            return Response::redirect($request->baseUrl() . '/clients/create');
        }

        $clientId = $this->db->insert('clients', [
            'name' => $name,
            'contact_email' => $contactEmail,
            'contact_phone' => $contactPhone,
            'address' => $address,
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Handle framework assignments
        $selectedFrameworks = $request->post('frameworks', []);
        $primaryFramework = $request->post('primary_framework');

        foreach ($selectedFrameworks as $framework) {
            $this->db->insert('client_frameworks', [
                'client_id' => $clientId,
                'framework' => $framework,
                'is_primary' => ($framework === $primaryFramework) ? 1 : 0,
                'assigned_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        AuditLogger::log('create', 'client', $clientId, [
            'name' => $name,
            'frameworks' => $selectedFrameworks,
        ], $request->ip());

        Session::flash('success', 'Client created successfully.');
        return Response::redirect($request->baseUrl() . '/clients/' . $clientId);
    }

    public function show(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $client = $this->db->fetchOne(
            "SELECT * FROM clients WHERE id = ?",
            [$id]
        );

        if (!$client) {
            return new Response('Client not found', 404);
        }

        // Get assigned frameworks
        $assignedFrameworks = $this->db->fetchAll(
            "SELECT * FROM client_frameworks WHERE client_id = ? ORDER BY is_primary DESC, framework ASC",
            [$id]
        );

        // Get client statistics
        $stats = [
            'total_assessments' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM assessments WHERE customer_id = ?",
                [$id]
            ),
            'open_poam' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM poam_items WHERE customer_id = ? AND status IN ('open', 'in_progress')",
                [$id]
            ),
            'total_documents' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM documents WHERE customer_id = ?",
                [$id]
            ),
            'latest_sprs' => null, // SPRS score is calculated, not stored in assessments table
        ];

        // Get recent assessments
        $recent_assessments = $this->db->fetchAll(
            "SELECT * FROM assessments
             WHERE customer_id = ?
             ORDER BY created_at DESC
             LIMIT 5",
            [$id]
        );

        // Get recent POA&M items
        $recent_poam = $this->db->fetchAll(
            "SELECT * FROM poam_items
             WHERE customer_id = ? AND status IN ('open', 'in_progress')
             ORDER BY planned_completion_date ASC
             LIMIT 5",
            [$id]
        );

        $content = View::render('clients/show', [
            'client' => $client,
            'stats' => $stats,
            'recent_assessments' => $recent_assessments,
            'recent_poam' => $recent_poam,
            'assigned_frameworks' => $assignedFrameworks,
        ]);

        return new Response($content);
    }

    public function edit(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $client = $this->db->fetchOne(
            "SELECT * FROM clients WHERE id = ?",
            [$id]
        );

        if (!$client) {
            return new Response('Client not found', 404);
        }

        // Get assigned frameworks
        $assignedFrameworks = $this->db->fetchAll(
            "SELECT * FROM client_frameworks WHERE client_id = ?",
            [$id]
        );

        // Get available frameworks
        $frameworks = ['CMMC', 'NIST800171', 'STIG', 'HIPAA', 'FTC-SAFEGUARDS', 'PCI-DSS', 'SOC2', 'ISO27001'];

        $content = View::render('clients/edit', [
            'client' => $client,
            'assigned_frameworks' => $assignedFrameworks,
            'frameworks' => $frameworks,
        ]);

        return new Response($content);
    }

    public function update(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/clients/' . $id . '/edit');
        }

        $client = $this->db->fetchOne(
            "SELECT * FROM clients WHERE id = ?",
            [$id]
        );

        if (!$client) {
            return new Response('Client not found', 404);
        }

        $data = [
            'name' => trim($request->post('name')),
            'contact_email' => trim($request->post('contact_email')),
            'contact_phone' => trim($request->post('contact_phone')),
            'address' => trim($request->post('address')),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->update('clients', $data, 'id = :id', [':id' => $id]);

        // Update framework assignments
        $selectedFrameworks = $request->post('frameworks', []);
        $primaryFramework = $request->post('primary_framework');

        // Delete existing framework assignments
        $this->db->query(
            "DELETE FROM client_frameworks WHERE client_id = ?",
            [$id]
        );

        // Insert new framework assignments
        foreach ($selectedFrameworks as $framework) {
            $this->db->insert('client_frameworks', [
                'client_id' => $id,
                'framework' => $framework,
                'is_primary' => ($framework === $primaryFramework) ? 1 : 0,
                'assigned_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        AuditLogger::logChange('client', $id, $client, $data, $request->ip());

        Session::flash('success', 'Client updated successfully.');
        return Response::redirect($request->baseUrl() . '/clients/' . $id);
    }

    public function delete(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Soft delete - mark as inactive
        $this->db->update('clients', ['active' => 0], 'id = :id', [':id' => $id]);

        AuditLogger::log('delete', 'client', $id, null, $request->ip());

        Session::flash('success', 'Client deleted successfully.');
        return Response::redirect($request->baseUrl() . '/clients');
    }

    public function select(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();

        $client = $this->db->fetchOne(
            "SELECT * FROM clients WHERE id = ? AND active = 1",
            [$id]
        );

        if (!$client) {
            Session::flash('error', 'Client not found.');
            return Response::redirect($request->baseUrl() . '/clients');
        }

        Session::set('current_customer_id', $id); // Keep session key for backward compatibility
        Session::set('current_customer_name', $client['name']); // Keep session key for backward compatibility

        Session::flash('success', 'Switched to ' . $client['name']);
        return Response::redirect($request->baseUrl() . '/');
    }
}
