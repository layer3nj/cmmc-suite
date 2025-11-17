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
 * Policy Controller - Manages boilerplate policy templates
 */
class PolicyController
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

        $category = $request->query('category', '');

        // Get all policies
        if ($category) {
            $policies = $this->db->fetchAll(
                "SELECT * FROM policies WHERE category = ? AND is_active = 1 ORDER BY category, title",
                [$category]
            );
        } else {
            $policies = $this->db->fetchAll(
                "SELECT * FROM policies WHERE is_active = 1 ORDER BY category, title"
            );
        }

        // Get all unique categories
        $categories = $this->db->fetchAll(
            "SELECT DISTINCT category FROM policies WHERE is_active = 1 ORDER BY category"
        );

        $content = View::render('policies/index', [
            'policies' => $policies,
            'categories' => $categories,
            'selected_category' => $category,
        ]);

        return new Response($content);
    }

    public function show(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $policy = $this->db->fetchOne(
            "SELECT * FROM policies WHERE id = ?",
            [$id]
        );

        if (!$policy) {
            return new Response('Policy not found', 404);
        }

        $content = View::render('policies/show', [
            'policy' => $policy,
        ]);

        return new Response($content);
    }

    public function create(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $frameworks = ['CMMC', 'NIST800171', 'STIG', 'HIPAA', 'FTC-SAFEGUARDS', 'PCI-DSS', 'SOC2', 'ISO27001'];

        $content = View::render('policies/create', [
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
            return Response::redirect($request->baseUrl() . '/policies/create');
        }

        $selectedFrameworks = $request->post('frameworks', []);
        $frameworksString = implode(',', $selectedFrameworks);

        $policyId = $this->db->insert('policies', [
            'title' => trim($request->post('title')),
            'category' => trim($request->post('category')),
            'frameworks' => $frameworksString,
            'description' => trim($request->post('description')),
            'content' => trim($request->post('content')),
            'version' => trim($request->post('version', '1.0')),
            'last_reviewed_date' => $request->post('last_reviewed_date') ?: null,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('create', 'policy', $policyId, [
            'title' => $request->post('title'),
        ], $request->ip());

        Session::flash('success', 'Policy template created successfully.');
        return Response::redirect($request->baseUrl() . '/policies/' . $policyId);
    }

    public function edit(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $policy = $this->db->fetchOne(
            "SELECT * FROM policies WHERE id = ?",
            [$id]
        );

        if (!$policy) {
            return new Response('Policy not found', 404);
        }

        $frameworks = ['CMMC', 'NIST800171', 'STIG', 'HIPAA', 'FTC-SAFEGUARDS', 'PCI-DSS', 'SOC2', 'ISO27001'];

        $content = View::render('policies/edit', [
            'policy' => $policy,
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
            return Response::redirect($request->baseUrl() . '/policies/' . $id . '/edit');
        }

        $policy = $this->db->fetchOne(
            "SELECT * FROM policies WHERE id = ?",
            [$id]
        );

        if (!$policy) {
            return new Response('Policy not found', 404);
        }

        $selectedFrameworks = $request->post('frameworks', []);
        $frameworksString = implode(',', $selectedFrameworks);

        $data = [
            'title' => trim($request->post('title')),
            'category' => trim($request->post('category')),
            'frameworks' => $frameworksString,
            'description' => trim($request->post('description')),
            'content' => trim($request->post('content')),
            'version' => trim($request->post('version', '1.0')),
            'last_reviewed_date' => $request->post('last_reviewed_date') ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->update('policies', $data, 'id = :id', [':id' => $id]);

        AuditLogger::logChange('policy', $id, $policy, $data, $request->ip());

        Session::flash('success', 'Policy updated successfully.');
        return Response::redirect($request->baseUrl() . '/policies/' . $id);
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

        // Soft delete
        $this->db->update('policies', ['is_active' => 0], 'id = :id', [':id' => $id]);

        AuditLogger::log('delete', 'policy', $id, null, $request->ip());

        Session::flash('success', 'Policy deleted successfully.');
        return Response::redirect($request->baseUrl() . '/policies');
    }

    /**
     * Show form to generate policy for specific client
     */
    public function generateForm(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $policy = $this->db->fetchOne(
            "SELECT * FROM policies WHERE id = ?",
            [$id]
        );

        if (!$policy) {
            return new Response('Policy not found', 404);
        }

        // Get all active clients
        $clients = $this->db->fetchAll(
            "SELECT id, name FROM clients WHERE active = 1 ORDER BY name ASC"
        );

        // Get available variables from TemplateProcessor
        $processor = new \App\Services\TemplateProcessor();
        $availableVars = $processor->getAvailableVariables();

        $content = View::render('policies/generate', [
            'policy' => $policy,
            'clients' => $clients,
            'available_vars' => $availableVars,
        ]);

        return new Response($content);
    }

    /**
     * Generate customized policy for client(s)
     */
    public function generate(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $policy = $this->db->fetchOne(
            "SELECT * FROM policies WHERE id = ?",
            [$id]
        );

        if (!$policy) {
            return Response::json(['success' => false, 'message' => 'Policy not found']);
        }

        $clientIds = $request->post('client_ids', []);
        $saveAsDocument = $request->post('save_as_document', false);

        if (empty($clientIds)) {
            return Response::json(['success' => false, 'message' => 'Please select at least one client']);
        }

        try {
            global $app;
            $settingsService = new \App\Services\SettingsService($this->db);
            $settings = $settingsService->getAll();

            $processor = new \App\Services\TemplateProcessor();
            $processor->setCompany($settings);

            $generated = 0;

            foreach ($clientIds as $clientId) {
                // Get client data
                $client = $this->db->fetchOne(
                    "SELECT * FROM clients WHERE id = ?",
                    [$clientId]
                );

                if (!$client) continue;

                // Get latest assessment for client (if any)
                $assessment = $this->db->fetchOne(
                    "SELECT * FROM assessments WHERE customer_id = ? ORDER BY created_at DESC LIMIT 1",
                    [$clientId]
                );

                // Set up template variables
                $processor->setClient($client);
                $processor->setAssessment($assessment);

                // Process template
                $processedContent = $processor->process($policy['content']);

                if ($saveAsDocument) {
                    // Save as document in the documents table
                    $this->db->insert('documents', [
                        'customer_id' => $clientId,
                        'title' => $policy['title'],
                        'category' => $policy['category'] ?? 'Policy',
                        'content' => $processedContent,
                        'version' => $policy['version'] ?? '1.0',
                        'created_by' => Session::get('user_name'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $generated++;
            }

            AuditLogger::log('policy_generate', 'policy', $id, [
                'clients' => count($clientIds),
                'generated' => $generated,
                'saved' => $saveAsDocument
            ], $request->ip());

            $message = $saveAsDocument
                ? "Generated and saved $generated document(s)"
                : "Generated $generated document(s) (preview mode)";

            return Response::json([
                'success' => true,
                'message' => $message,
                'generated' => $generated
            ]);

        } catch (\Exception $e) {
            error_log('Policy generate error: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'message' => 'Error generating documents: ' . $e->getMessage()
            ]);
        }
    }
}
