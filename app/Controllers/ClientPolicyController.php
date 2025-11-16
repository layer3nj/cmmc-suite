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
 * Client Policy Controller
 * Manages customer-specific policies with approval workflow
 */
class ClientPolicyController
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

        if (!$customerId) {
            Session::flash('error', 'Please select a client first.');
            return Response::redirect($request->baseUrl() . '/clients');
        }

        // Get all policies for this client
        $policies = $this->db->fetchAll(
            "SELECT cp.*, u1.display_name as approved_by_name, u2.display_name as owner_name
             FROM client_policies cp
             LEFT JOIN users u1 ON cp.approved_by = u1.id
             LEFT JOIN users u2 ON cp.owner_user_id = u2.id
             WHERE cp.client_id = ? AND cp.is_active = 1
             ORDER BY cp.status ASC, cp.title ASC",
            [$customerId]
        );

        // Get client info
        $client = $this->db->fetchOne(
            "SELECT * FROM clients WHERE id = ?",
            [$customerId]
        );

        // Get stats
        $stats = $this->getStats($customerId);

        $content = View::render('policies/index', [
            'policies' => $policies,
            'client' => $client,
            'stats' => $stats,
        ]);

        return new Response($content);
    }

    public function create(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        if (!$customerId) {
            Session::flash('error', 'Please select a client first.');
            return Response::redirect($request->baseUrl() . '/clients');
        }

        // Get available policy templates
        $templates = $this->db->fetchAll(
            "SELECT * FROM policies WHERE is_active = 1 ORDER BY category, title"
        );

        $content = View::render('policies/create', [
            'templates' => $templates,
        ]);

        return new Response($content);
    }

    public function store(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/policies/create');
        }

        $customerId = Session::get('current_customer_id');
        $userId = Session::get('user_id');

        $templateId = $request->post('template_policy_id') ?: null;
        $title = trim($request->post('title'));
        $category = trim($request->post('category'));
        $frameworks = trim($request->post('frameworks'));
        $description = trim($request->post('description'));
        $content = trim($request->post('content'));

        if (empty($title) || empty($category) || empty($content)) {
            Session::flash('error', 'Title, category, and content are required.');
            return Response::redirect($request->baseUrl() . '/policies/create');
        }

        // Create policy
        $policyId = $this->db->insert('client_policies', [
            'client_id' => $customerId,
            'template_policy_id' => $templateId,
            'title' => $title,
            'category' => $category,
            'frameworks' => $frameworks,
            'description' => $description,
            'content' => $content,
            'version' => '1.0',
            'status' => 'draft',
            'owner_user_id' => $userId,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('create', 'client_policy', $policyId, [
            'title' => $title,
            'client_id' => $customerId
        ], $request->ip());

        Session::flash('success', 'Policy created successfully.');
        return Response::redirect($request->baseUrl() . '/policies');
    }

    public function submitForReview(Request $request, int $id): Response
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

        // Verify policy exists and belongs to client
        $policy = $this->db->fetchOne(
            "SELECT * FROM client_policies WHERE id = ? AND client_id = ?",
            [$id, $customerId]
        );

        if (!$policy) {
            return Response::json(['success' => false, 'message' => 'Policy not found']);
        }

        if ($policy['status'] !== 'draft') {
            return Response::json(['success' => false, 'message' => 'Only draft policies can be submitted for review']);
        }

        // Update status
        $this->db->update('client_policies', [
            'status' => 'in_review',
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $id]);

        AuditLogger::log('submit_review', 'client_policy', $id, [
            'title' => $policy['title']
        ], $request->ip());

        return Response::json(['success' => true, 'message' => 'Policy submitted for review']);
    }

    public function approve(Request $request, int $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('admin');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $customerId = Session::get('current_customer_id');
        $userId = Session::get('user_id');

        // Verify policy exists and belongs to client
        $policy = $this->db->fetchOne(
            "SELECT * FROM client_policies WHERE id = ? AND client_id = ?",
            [$id, $customerId]
        );

        if (!$policy) {
            return Response::json(['success' => false, 'message' => 'Policy not found']);
        }

        // Update status
        $this->db->update('client_policies', [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $userId,
            'last_reviewed_at' => date('Y-m-d H:i:s'),
            'review_due_date' => date('Y-m-d', strtotime('+1 year')),
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $id]);

        AuditLogger::log('approve', 'client_policy', $id, [
            'title' => $policy['title']
        ], $request->ip());

        return Response::json(['success' => true, 'message' => 'Policy approved successfully']);
    }

    public function copyFromTemplate(Request $request): Response
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
        $userId = Session::get('user_id');
        $templateId = $request->post('template_id');

        // Get template
        $template = $this->db->fetchOne(
            "SELECT * FROM policies WHERE id = ? AND is_active = 1",
            [$templateId]
        );

        if (!$template) {
            return Response::json(['success' => false, 'message' => 'Template not found']);
        }

        // Create policy from template
        $policyId = $this->db->insert('client_policies', [
            'client_id' => $customerId,
            'template_policy_id' => $templateId,
            'title' => $template['title'],
            'category' => $template['category'],
            'frameworks' => $template['frameworks'],
            'description' => $template['description'],
            'content' => $template['content'],
            'version' => $template['version'],
            'status' => 'draft',
            'owner_user_id' => $userId,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('copy_template', 'client_policy', $policyId, [
            'template_id' => $templateId,
            'title' => $template['title']
        ], $request->ip());

        return Response::json([
            'success' => true,
            'message' => 'Policy created from template',
            'policy_id' => $policyId
        ]);
    }

    private function getStats(int $clientId): array
    {
        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM client_policies WHERE client_id = ? AND is_active = 1",
            [$clientId]
        ) ?: 0;

        $approved = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM client_policies WHERE client_id = ? AND status = 'approved' AND is_active = 1",
            [$clientId]
        ) ?: 0;

        $inReview = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM client_policies WHERE client_id = ? AND status = 'in_review' AND is_active = 1",
            [$clientId]
        ) ?: 0;

        $draft = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM client_policies WHERE client_id = ? AND status = 'draft' AND is_active = 1",
            [$clientId]
        ) ?: 0;

        return [
            'total' => $total,
            'approved' => $approved,
            'in_review' => $inReview,
            'draft' => $draft,
        ];
    }
}
