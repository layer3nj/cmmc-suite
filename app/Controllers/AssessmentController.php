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
 * Assessment Controller
 * Create and manage compliance assessments
 */
class AssessmentController
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
        $currentCustomer = Session::get('current_customer_name');

        if (!$customerId) {
            $content = View::render('assessments/index', [
                'assessments' => [],
                'current_customer' => null,
            ]);
            return new Response($content);
        }

        $assessments = $this->db->fetchAll(
            "SELECT a.*, u.display_name as assessor_name
             FROM assessments a
             LEFT JOIN users u ON a.assessor_user_id = u.id
             WHERE a.customer_id = ?
             ORDER BY a.created_at DESC",
            [$customerId]
        );

        // Calculate progress for each assessment
        foreach ($assessments as &$assessment) {
            $stats = $this->getAssessmentStats($assessment['id']);
            $assessment['total_controls'] = $stats['total'] ?? 0;
            $assessment['controls_completed'] = ($stats['met'] ?? 0) + ($stats['partially_met'] ?? 0) + ($stats['not_applicable'] ?? 0);
        }

        $content = View::render('assessments/index', [
            'assessments' => $assessments,
            'current_customer' => $currentCustomer,
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
            Session::flash('error', 'Please select a customer first.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        $content = View::render('assessments/create');
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
            return Response::redirect($request->baseUrl() . '/assessments/create');
        }

        $customerId = Session::get('current_customer_id');

        if (!$customerId) {
            Session::flash('error', 'Please select a customer first.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        $notes = trim($request->post('notes'));
        $framework = $request->post('framework', 'NIST800171');
        $assessmentType = $request->post('assessment_type', 'self');
        $scope = trim($request->post('scope'));
        $targetLevel = $request->post('target_level');

        // Create assessment
        $assessmentId = $this->db->insert('assessments', [
            'customer_id' => $customerId,
            'assessor_user_id' => Session::get('user_id'),
            'framework' => $framework,
            'assessment_type' => $assessmentType,
            'scope' => $scope,
            'target_level' => $targetLevel,
            'notes' => $notes,
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Initialize findings for all controls in the selected framework
        $controls = $this->db->fetchAll(
            "SELECT code FROM controls WHERE framework = ?",
            [$framework]
        );

        foreach ($controls as $control) {
            $this->db->insert('control_findings', [
                'assessment_id' => $assessmentId,
                'control_framework' => $framework,
                'control_code' => $control['code'],
                'status' => 'not_met', // Default status
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        AuditLogger::log('create', 'assessment', $assessmentId, [
            'framework' => $framework,
            'customer_id' => $customerId
        ], $request->ip());

        Session::flash('success', 'Assessment created successfully.');
        return Response::redirect($request->baseUrl() . '/assessments/' . $assessmentId);
    }

    public function show(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $assessment = $this->db->fetchOne(
            "SELECT a.*, u.display_name as assessor_name
             FROM assessments a
             LEFT JOIN users u ON a.assessor_user_id = u.id
             WHERE a.id = ? AND a.customer_id = ?",
            [$id, $customerId]
        );

        if (!$assessment) {
            return new Response('Assessment not found', 404);
        }

        // Get findings grouped by domain
        $findings = $this->db->fetchAll(
            "SELECT cf.*, c.title, c.description, c.ml_level
             FROM control_findings cf
             JOIN controls c ON cf.control_framework = c.framework AND cf.control_code = c.code
             WHERE cf.assessment_id = ?
             ORDER BY cf.control_code ASC",
            [$id]
        );

        // Group by domain (for NIST)
        $groupedFindings = $this->groupFindings($findings);

        // Get statistics
        $stats = $this->getAssessmentStats($id);

        $content = View::render('assessments/show', [
            'assessment' => $assessment,
            'findings' => $groupedFindings,
            'stats' => $stats,
        ]);

        return new Response($content);
    }

    public function updateFindings(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $assessment = $this->db->fetchOne(
            "SELECT * FROM assessments WHERE id = ?",
            [$id]
        );

        if (!$assessment || $assessment['status'] === 'published') {
            return Response::json(['success' => false, 'message' => 'Cannot modify published assessment']);
        }

        // Update findings (bulk update via JSON)
        $findings = json_decode($request->post('findings'), true);

        if (!$findings) {
            return Response::json(['success' => false, 'message' => 'Invalid findings data']);
        }

        $this->db->beginTransaction();

        try {
            foreach ($findings as $findingId => $data) {
                $this->db->update('control_findings', [
                    'status' => $data['status'],
                    'objective_evidence' => $data['objective_evidence'] ?? null,
                    'compensating_controls' => $data['compensating_controls'] ?? null,
                    'severity' => $data['severity'] ?? null,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], 'id = :id', [':id' => $findingId]);
            }

            $this->db->commit();

            AuditLogger::log('update_findings', 'assessment', $id, [
                'count' => count($findings)
            ], $request->ip());

            return Response::json(['success' => true, 'message' => 'Findings updated successfully']);
        } catch (\Exception $e) {
            $this->db->rollback();
            return Response::json(['success' => false, 'message' => 'Failed to update findings']);
        }
    }

    public function publish(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('auditor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $assessment = $this->db->fetchOne(
            "SELECT * FROM assessments WHERE id = ?",
            [$id]
        );

        if (!$assessment) {
            return Response::json(['success' => false, 'message' => 'Assessment not found']);
        }

        $this->db->update('assessments', [
            'status' => 'published',
            'assessed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $id]);

        AuditLogger::log('publish', 'assessment', $id, null, $request->ip());

        Session::flash('success', 'Assessment published successfully.');
        return Response::redirect($request->baseUrl() . '/assessments/' . $id);
    }

    public function delete(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/assessments');
        }

        $assessment = $this->db->fetchOne(
            "SELECT * FROM assessments WHERE id = ?",
            [$id]
        );

        if (!$assessment) {
            Session::flash('error', 'Assessment not found');
            return Response::redirect($request->baseUrl() . '/assessments');
        }

        // Delete associated findings first
        $this->db->delete('control_findings', 'assessment_id = :id', [':id' => $id]);

        // Delete assessment
        $this->db->delete('assessments', 'id = :id', [':id' => $id]);

        AuditLogger::log('delete', 'assessment', $id, null, $request->ip());

        Session::flash('success', 'Assessment deleted successfully.');
        return Response::redirect($request->baseUrl() . '/assessments');
    }

    private function groupFindings(array $findings): array
    {
        $grouped = [];

        foreach ($findings as $finding) {
            // Extract domain from control code (e.g., "3.1.1" -> "3.1")
            $code = $finding['control_code'];
            $parts = explode('.', $code);
            $domain = isset($parts[1]) ? $parts[0] . '.' . $parts[1] : $code;

            if (!isset($grouped[$domain])) {
                $grouped[$domain] = [];
            }

            $grouped[$domain][] = $finding;
        }

        return $grouped;
    }

    private function getAssessmentStats(int $assessmentId): array
    {
        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings WHERE assessment_id = ?",
            [$assessmentId]
        );

        $met = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings WHERE assessment_id = ? AND status = 'met'",
            [$assessmentId]
        );

        $partiallyMet = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings WHERE assessment_id = ? AND status = 'partially_met'",
            [$assessmentId]
        );

        $notMet = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings WHERE assessment_id = ? AND status = 'not_met'",
            [$assessmentId]
        );

        $notApplicable = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings WHERE assessment_id = ? AND status = 'not_applicable'",
            [$assessmentId]
        );

        $completionPercent = $total > 0 ? round(($met / $total) * 100) : 0;

        return [
            'total' => $total,
            'met' => $met,
            'partially_met' => $partiallyMet,
            'not_met' => $notMet,
            'not_applicable' => $notApplicable,
            'completion_percent' => $completionPercent,
            'percentage' => $completionPercent, // Alias for views
        ];
    }
}
