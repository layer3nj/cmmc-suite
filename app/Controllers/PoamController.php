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
 * POA&M (Plan of Action & Milestones) Controller
 */
class PoamController
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
            Session::flash('error', 'Please select a customer first.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        // Get filters
        $status = $request->query('status', '');
        $framework = $request->query('framework', '');

        $sql = "SELECT * FROM poam_items WHERE customer_id = ?";
        $params = [$customerId];

        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if (!empty($framework)) {
            $sql .= " AND control_framework = ?";
            $params[] = $framework;
        }

        $sql .= " ORDER BY planned_completion_date ASC, created_at DESC";

        $items = $this->db->fetchAll($sql, $params);

        // Get statistics
        $stats = [
            'total' => count($items),
            'open' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM poam_items WHERE customer_id = ? AND status = 'open'",
                [$customerId]
            ),
            'in_progress' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM poam_items WHERE customer_id = ? AND status = 'in_progress'",
                [$customerId]
            ),
            'closed' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM poam_items WHERE customer_id = ? AND status = 'closed'",
                [$customerId]
            ),
        ];

        $content = View::render('poam/index', [
            'items' => $items,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'framework' => $framework,
            ],
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

        $content = View::render('poam/create');
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
            return Response::redirect($request->baseUrl() . '/poam/create');
        }

        $customerId = Session::get('current_customer_id');

        $data = [
            'customer_id' => $customerId,
            'control_framework' => $request->post('control_framework') ?: null,
            'control_code' => $request->post('control_code') ?: null,
            'title' => trim($request->post('title')),
            'weakness' => trim($request->post('weakness')),
            'corrective_action' => trim($request->post('corrective_action')),
            'milestones' => trim($request->post('milestones')),
            'responsible_party' => trim($request->post('responsible_party')),
            'resources' => trim($request->post('resources')),
            'start_date' => $request->post('start_date') ?: null,
            'planned_completion_date' => $request->post('planned_completion_date') ?: null,
            'status' => 'open',
            'residual_risk' => $request->post('residual_risk'),
            'comments' => trim($request->post('comments')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $poamId = $this->db->insert('poam_items', $data);

        AuditLogger::log('create', 'poam', $poamId, [
            'title' => $data['title'],
            'control' => $data['control_code']
        ], $request->ip());

        Session::flash('success', 'POA&M item created successfully.');
        return Response::redirect($request->baseUrl() . '/poam/' . $poamId);
    }

    public function show(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $item = $this->db->fetchOne(
            "SELECT * FROM poam_items WHERE id = ? AND customer_id = ?",
            [$id, $customerId]
        );

        if (!$item) {
            return new Response('POA&M item not found', 404);
        }

        // Get related control if specified
        $control = null;
        if ($item['control_framework'] && $item['control_code']) {
            $control = $this->db->fetchOne(
                "SELECT * FROM controls WHERE framework = ? AND code = ?",
                [$item['control_framework'], $item['control_code']]
            );
        }

        $content = View::render('poam/show', [
            'item' => $item,
            'control' => $control,
        ]);

        return new Response($content);
    }

    public function edit(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $item = $this->db->fetchOne(
            "SELECT * FROM poam_items WHERE id = ? AND customer_id = ?",
            [$id, $customerId]
        );

        if (!$item) {
            return new Response('POA&M item not found', 404);
        }

        $content = View::render('poam/edit', [
            'item' => $item,
        ]);

        return new Response($content);
    }

    public function update(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/poam/' . $id . '/edit');
        }

        $customerId = Session::get('current_customer_id');

        $item = $this->db->fetchOne(
            "SELECT * FROM poam_items WHERE id = ? AND customer_id = ?",
            [$id, $customerId]
        );

        if (!$item) {
            return new Response('POA&M item not found', 404);
        }

        $data = [
            'title' => trim($request->post('title')),
            'weakness' => trim($request->post('weakness')),
            'corrective_action' => trim($request->post('corrective_action')),
            'milestones' => trim($request->post('milestones')),
            'responsible_party' => trim($request->post('responsible_party')),
            'resources' => trim($request->post('resources')),
            'start_date' => $request->post('start_date') ?: null,
            'planned_completion_date' => $request->post('planned_completion_date') ?: null,
            'actual_completion_date' => $request->post('actual_completion_date') ?: null,
            'status' => $request->post('status'),
            'residual_risk' => $request->post('residual_risk'),
            'comments' => trim($request->post('comments')),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->update('poam_items', $data, 'id = :id', [':id' => $id]);

        AuditLogger::logChange('poam', $id, $item, $data, $request->ip());

        Session::flash('success', 'POA&M item updated successfully.');
        return Response::redirect($request->baseUrl() . '/poam/' . $id);
    }

    public function delete(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('auditor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $this->db->delete('poam_items', 'id = :id', [':id' => $id]);

        AuditLogger::log('delete', 'poam', $id, null, $request->ip());

        Session::flash('success', 'POA&M item deleted successfully.');
        return Response::redirect($request->baseUrl() . '/poam');
    }

    public function generate(Request $request): Response
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
        $assessmentId = $request->post('assessment_id');

        if (!$assessmentId) {
            return Response::json(['success' => false, 'message' => 'Assessment ID required']);
        }

        // Get all not_met and partially_met findings
        $findings = $this->db->fetchAll(
            "SELECT cf.*, c.title, c.description
             FROM control_findings cf
             JOIN controls c ON cf.control_framework = c.framework AND cf.control_code = c.code
             WHERE cf.assessment_id = ?
             AND cf.status IN ('not_met', 'partially_met')",
            [$assessmentId]
        );

        $created = 0;

        foreach ($findings as $finding) {
            // Check if POA&M already exists for this control
            $existing = $this->db->fetchOne(
                "SELECT id FROM poam_items
                 WHERE customer_id = ?
                 AND control_framework = ?
                 AND control_code = ?
                 AND status IN ('open', 'in_progress')",
                [$customerId, $finding['control_framework'], $finding['control_code']]
            );

            if ($existing) {
                continue; // Skip if already exists
            }

            $this->db->insert('poam_items', [
                'customer_id' => $customerId,
                'control_framework' => $finding['control_framework'],
                'control_code' => $finding['control_code'],
                'title' => 'Remediate: ' . $finding['title'],
                'weakness' => $finding['status'] === 'not_met' ? 'Control not implemented' : 'Control partially implemented',
                'corrective_action' => 'Implement control: ' . $finding['title'],
                'status' => 'open',
                'start_date' => date('Y-m-d'),
                'planned_completion_date' => date('Y-m-d', strtotime('+90 days')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $created++;
        }

        AuditLogger::log('generate_poam', 'assessment', $assessmentId, [
            'items_created' => $created
        ], $request->ip());

        return Response::json([
            'success' => true,
            'message' => "Generated $created POA&M items",
            'count' => $created
        ]);
    }

    public function exportCsv(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $items = $this->db->fetchAll(
            "SELECT * FROM poam_items WHERE customer_id = ? ORDER BY planned_completion_date ASC",
            [$customerId]
        );

        $csv = "Control,Title,Weakness,Corrective Action,Responsible Party,Start Date,Planned Completion,Status,Residual Risk\n";

        foreach ($items as $item) {
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $item['control_code'] ?? '',
                str_replace('"', '""', $item['title']),
                str_replace('"', '""', $item['weakness'] ?? ''),
                str_replace('"', '""', $item['corrective_action'] ?? ''),
                $item['responsible_party'] ?? '',
                $item['start_date'] ?? '',
                $item['planned_completion_date'] ?? '',
                $item['status'],
                $item['residual_risk'] ?? ''
            );
        }

        return Response::download($csv, 'poam_export_' . date('Y-m-d') . '.csv', 'text/csv');
    }

    public function exportPdf(Request $request): Response
    {
        // PDF export would use a bundled PDF library
        // For now, return CSV as fallback
        return $this->exportCsv($request);
    }
}
