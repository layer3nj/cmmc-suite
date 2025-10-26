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
 * Control Browser Controller
 * Browse and search CMMC, NIST 800-171, and STIG controls
 */
class ControlController
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

        // Redirect to CMMC by default
        return Response::redirect($request->baseUrl() . '/controls/CMMC');
    }

    public function byFramework(Request $request, string $framework): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $framework = strtoupper($framework);

        if (!in_array($framework, ['CMMC', 'NIST800171', 'STIG'])) {
            return new Response('Invalid framework', 404);
        }

        // Get filters
        $search = $request->query('search', '');
        $mlLevel = $request->query('ml_level', '');
        $status = $request->query('status', '');

        // Build query
        $sql = "SELECT * FROM controls WHERE framework = ?";
        $params = [$framework];

        if (!empty($search)) {
            $sql .= " AND (code LIKE ? OR title LIKE ? OR description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($mlLevel) && $framework === 'CMMC') {
            $sql .= " AND ml_level = ?";
            $params[] = $mlLevel;
        }

        $sql .= " ORDER BY code ASC";

        $controls = $this->db->fetchAll($sql, $params);

        // Get control counts by ML level (for CMMC)
        $mlCounts = [];
        if ($framework === 'CMMC') {
            for ($level = 1; $level <= 3; $level++) {
                $mlCounts[$level] = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM controls WHERE framework = 'CMMC' AND ml_level = ?",
                    [$level]
                );
            }
        }

        $content = View::render('controls/index', [
            'framework' => $framework,
            'controls' => $controls,
            'ml_counts' => $mlCounts,
            'filters' => [
                'search' => $search,
                'ml_level' => $mlLevel,
                'status' => $status,
            ],
        ]);

        return new Response($content);
    }

    public function show(Request $request, string $framework, string $code): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $framework = strtoupper($framework);
        $code = urldecode($code);

        // Get control
        $control = $this->db->fetchOne(
            "SELECT * FROM controls WHERE framework = ? AND code = ?",
            [$framework, $code]
        );

        if (!$control) {
            return new Response('Control not found', 404);
        }

        // Get mappings
        $mappings = $this->getMappings($framework, $code);

        // Get latest finding (if customer selected)
        Session::start();
        $customerId = Session::get('current_customer_id');
        $latestFinding = null;

        if ($customerId) {
            $latestFinding = $this->getLatestFinding($customerId, $framework, $code);
        }

        // Get related documents
        $documents = [];
        if ($customerId) {
            $documents = $this->db->fetchAll(
                "SELECT * FROM documents
                 WHERE customer_id = ?
                 AND linked_framework = ?
                 AND linked_code = ?
                 ORDER BY created_at DESC",
                [$customerId, $framework, $code]
            );
        }

        $content = View::render('controls/show', [
            'control' => $control,
            'mappings' => $mappings,
            'latest_finding' => $latestFinding,
            'documents' => $documents,
        ]);

        return new Response($content);
    }

    public function update(Request $request, string $framework, string $code): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // This endpoint is used to update control evidence/notes
        // Control definitions themselves are not editable

        return Response::json(['success' => true]);
    }

    private function getMappings(string $framework, string $code): array
    {
        // Get controls that this maps to
        $mapsTo = $this->db->fetchAll(
            "SELECT cm.*, c.title as target_title
             FROM control_mappings cm
             JOIN controls c ON cm.target_framework = c.framework AND cm.target_code = c.code
             WHERE cm.source_framework = ? AND cm.source_code = ?",
            [$framework, $code]
        );

        // Get controls that map to this
        $mappedFrom = $this->db->fetchAll(
            "SELECT cm.*, c.title as source_title
             FROM control_mappings cm
             JOIN controls c ON cm.source_framework = c.framework AND cm.source_code = c.code
             WHERE cm.target_framework = ? AND cm.target_code = ?",
            [$framework, $code]
        );

        return [
            'maps_to' => $mapsTo,
            'mapped_from' => $mappedFrom,
        ];
    }

    private function getLatestFinding(int $customerId, string $framework, string $code): ?array
    {
        return $this->db->fetchOne(
            "SELECT cf.*, a.assessed_at
             FROM control_findings cf
             JOIN assessments a ON cf.assessment_id = a.id
             WHERE a.customer_id = ?
             AND cf.control_framework = ?
             AND cf.control_code = ?
             AND a.status = 'published'
             ORDER BY a.assessed_at DESC
             LIMIT 1",
            [$customerId, $framework, $code]
        );
    }
}
