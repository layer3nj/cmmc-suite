<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Middleware\AuthMiddleware;

/**
 * Compliance Gap Analysis Controller
 * Identifies gaps between current state and compliance requirements
 */
class ComplianceGapController
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

        // Get all assessments for this client
        $assessments = $this->db->fetchAll(
            "SELECT id, framework, target_level, assessed_at, status
             FROM assessments
             WHERE customer_id = ?
             ORDER BY created_at DESC",
            [$customerId]
        );

        $content = View::render('compliance-gap/index', [
            'current_customer' => $customerName,
            'assessments' => $assessments,
        ]);

        return new Response($content);
    }

    /**
     * Analyze compliance gaps for a specific framework
     */
    public function analyze(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');
        $customerName = Session::get('current_customer_name');

        if (!$customerId) {
            return new Response(json_encode(['error' => 'No client selected']), 400);
        }

        $framework = $request->getParam('framework');
        $targetLevel = $request->getParam('target_level');

        if (!$framework) {
            return new Response(json_encode(['error' => 'Framework is required']), 400);
        }

        // Get latest assessment for this framework
        $assessment = $this->db->fetchOne(
            "SELECT * FROM assessments
             WHERE customer_id = ? AND framework = ?
             ORDER BY created_at DESC
             LIMIT 1",
            [$customerId, $framework]
        );

        // Get all controls for this framework
        $query = "SELECT * FROM controls WHERE framework = ?";
        $params = [$framework];

        // Filter by maturity level if specified
        if ($targetLevel && $framework === 'CMMC') {
            $query .= " AND ml_level <= ?";
            $params[] = $targetLevel;
        }

        $query .= " ORDER BY code";
        $allControls = $this->db->fetchAll($query, $params);

        // Get assessment responses if assessment exists
        $responses = [];
        if ($assessment) {
            $responseRows = $this->db->fetchAll(
                "SELECT * FROM control_findings WHERE assessment_id = ?",
                [$assessment['id']]
            );

            foreach ($responseRows as $row) {
                $responses[$row['control_code']] = $row;
            }
        }

        // Analyze gaps
        $gaps = [];
        $stats = [
            'total_controls' => count($allControls),
            'compliant' => 0,
            'non_compliant' => 0,
            'not_assessed' => 0,
            'high_priority' => 0,
            'medium_priority' => 0,
            'low_priority' => 0,
        ];

        foreach ($allControls as $control) {
            $controlCode = $control['code'];
            $response = $responses[$controlCode] ?? null;

            $status = 'not_assessed';
            $priority = 'medium';

            if ($response) {
                if ($response['status'] === 'met') {
                    $status = 'compliant';
                    $stats['compliant']++;
                    continue; // Skip compliant controls
                } elseif (in_array($response['status'], ['partially_met', 'not_met'])) {
                    $status = 'non_compliant';
                    $stats['non_compliant']++;
                } elseif ($response['status'] === 'not_applicable') {
                    continue; // Skip N/A controls
                }
            } else {
                $stats['not_assessed']++;
            }

            // Determine priority based on control characteristics
            if ($framework === 'CMMC') {
                // Higher maturity levels are higher priority
                if ($control['ml_level'] >= 3) {
                    $priority = 'high';
                    $stats['high_priority']++;
                } elseif ($control['ml_level'] == 2) {
                    $priority = 'medium';
                    $stats['medium_priority']++;
                } else {
                    $priority = 'low';
                    $stats['low_priority']++;
                }
            } else {
                // For other frameworks, use severity/category if available
                if (strpos(strtolower($control['title']), 'critical') !== false ||
                    strpos(strtolower($control['title']), 'encryption') !== false ||
                    strpos(strtolower($control['title']), 'access control') !== false) {
                    $priority = 'high';
                    $stats['high_priority']++;
                } else {
                    $priority = 'medium';
                    $stats['medium_priority']++;
                }
            }

            $gaps[] = [
                'control_code' => $controlCode,
                'title' => $control['title'],
                'description' => $control['description'] ?? '',
                'ml_level' => $control['ml_level'] ?? null,
                'category' => $control['category'] ?? 'General',
                'status' => $status,
                'priority' => $priority,
                'notes' => $response['objective_evidence'] ?? '',
            ];
        }

        // Sort gaps by priority (high -> medium -> low) and then by control code
        usort($gaps, function($a, $b) {
            $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
            $aPriority = $priorityOrder[$a['priority']] ?? 2;
            $bPriority = $priorityOrder[$b['priority']] ?? 2;

            if ($aPriority !== $bPriority) {
                return $aPriority - $bPriority;
            }

            return strcmp($a['control_code'], $b['control_code']);
        });

        // Calculate compliance percentage
        $assessedControls = $stats['compliant'] + $stats['non_compliant'];
        $complianceRate = $assessedControls > 0
            ? round(($stats['compliant'] / $assessedControls) * 100, 1)
            : 0;

        return new Response(json_encode([
            'success' => true,
            'framework' => $framework,
            'target_level' => $targetLevel,
            'assessment_date' => $assessment['assessed_at'] ?? null,
            'gaps' => $gaps,
            'stats' => $stats,
            'compliance_rate' => $complianceRate,
        ]), 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Export gap analysis report
     */
    public function export(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');
        $customerName = Session::get('current_customer_name');

        $framework = $request->getParam('framework');
        $targetLevel = $request->getParam('target_level');

        if (!$customerId || !$framework) {
            return new Response('Invalid request', 400);
        }

        // Get the same data as analyze()
        $analysisRequest = new Request($_GET, $_POST);
        $analysisResponse = $this->analyze($analysisRequest);
        $data = json_decode($analysisResponse->getContent(), true);

        if (!$data['success']) {
            return new Response('Analysis failed', 500);
        }

        // Generate CSV
        $csv = "Control Code,Title,Category,Status,Priority,Notes\n";
        foreach ($data['gaps'] as $gap) {
            $csv .= sprintf(
                "%s,\"%s\",\"%s\",%s,%s,\"%s\"\n",
                $gap['control_code'],
                str_replace('"', '""', $gap['title']),
                str_replace('"', '""', $gap['category']),
                $gap['status'],
                $gap['priority'],
                str_replace('"', '""', $gap['notes'])
            );
        }

        return Response::download(
            $csv,
            'compliance_gap_' . $framework . '_' . date('Y-m-d') . '.csv',
            'text/csv'
        );
    }
}
