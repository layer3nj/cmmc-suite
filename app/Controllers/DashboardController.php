<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Services\SprsScoreCalculator;
use App\Middleware\AuthMiddleware;

/**
 * Dashboard Controller
 */
class DashboardController
{
    private $db;

    public function __construct()
    {
        global $app;
        $this->db = $app->getDatabase();
    }

    public function index(Request $request): Response
    {
        // Debug logging
        $logFile = BASE_PATH . '/storage/logs/saml_debug.log';
        Session::start();
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Dashboard loading, session user_id: " . (Session::has('user_id') ? Session::get('user_id') : 'NONE') . "\n", FILE_APPEND);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Session ID: " . session_id() . "\n", FILE_APPEND);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - All session data: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

        // Check authentication
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Auth check failed, redirecting to login\n", FILE_APPEND);
            return $authCheck;
        }

        $currentCustomerId = Session::get('current_customer_id');

        // Get client frameworks
        $clientFrameworks = $this->getClientFrameworks($currentCustomerId);

        // Get metrics
        $metrics = $this->getMetrics($currentCustomerId, $clientFrameworks);

        // Get recent assessments
        $recentAssessments = $this->getRecentAssessments($currentCustomerId);

        // Get upcoming POA&M milestones
        $upcomingMilestones = $this->getUpcomingMilestones($currentCustomerId);

        $content = View::render('dashboard/index', [
            'metrics' => $metrics,
            'recent_assessments' => $recentAssessments,
            'upcoming_milestones' => $upcomingMilestones,
            'current_customer' => $this->getCurrentCustomer($currentCustomerId),
            'client_frameworks' => $clientFrameworks,
        ]);

        return new Response($content);
    }

    private function getClientFrameworks(?int $customerId): array
    {
        if (!$customerId) {
            return [];
        }

        return $this->db->fetchAll(
            "SELECT * FROM client_frameworks WHERE client_id = ? ORDER BY is_primary DESC",
            [$customerId]
        );
    }

    private function getMetrics(?int $customerId, array $clientFrameworks = []): array
    {
        if (!$customerId) {
            return [
                'frameworks' => [],
                'open_poam' => 0,
            ];
        }

        // Get open POA&M count (common to all frameworks)
        $openPoam = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM poam_items
             WHERE customer_id = ? AND STATUS IN ('open', 'in_progress')",
            [$customerId]
        );

        $metrics = [
            'frameworks' => [],
            'open_poam' => $openPoam,
        ];

        // Get metrics for each assigned framework
        foreach ($clientFrameworks as $clientFramework) {
            $framework = $clientFramework['framework'];

            switch ($framework) {
                case 'CMMC':
                    $metrics['frameworks'][$framework] = $this->getCMMCMetrics($customerId, $clientFramework);
                    break;
                case 'NIST800171':
                    $metrics['frameworks'][$framework] = $this->getNIST800171Metrics($customerId);
                    break;
                case 'NIST80053':
                    $metrics['frameworks'][$framework] = $this->getNIST80053Metrics($customerId);
                    break;
                case 'FTC-SAFEGUARDS':
                    $metrics['frameworks'][$framework] = $this->getFTCSafeguardsMetrics($customerId);
                    break;
                default:
                    $metrics['frameworks'][$framework] = $this->getGenericFrameworkMetrics($customerId, $framework);
                    break;
            }
        }

        return $metrics;
    }

    private function getCMMCMetrics(int $customerId, array $clientFramework): array
    {
        // Get client info including CMMC maturity level
        $client = $this->db->fetchOne(
            "SELECT * FROM clients WHERE id = ?",
            [$customerId]
        );

        // Get latest published assessment
        $assessment = $this->db->fetchOne(
            "SELECT id FROM assessments
             WHERE customer_id = ? AND status = 'published'
             ORDER BY assessed_at DESC LIMIT 1",
            [$customerId]
        );

        // Determine which maturity levels to count based on client's target level
        $maxLevel = $this->getMaxLevelFromMaturityLevel($client['cmmc_maturity_level'] ?? null);

        if (!$assessment) {
            // Count controls based on maturity level
            $totalControls = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM controls
                 WHERE framework = 'CMMC' AND ml_level <= ?",
                [$maxLevel]
            );

            return [
                'current_sprs' => -203,
                'projected_sprs' => -203,
                'ml1_percent' => 0,
                'ml2_percent' => 0,
                'ml3_percent' => 0,
                'total_controls' => $totalControls,
                'maturity_level' => $client['cmmc_maturity_level'] ?? 'Not Set',
            ];
        }

        // Calculate SPRS scores
        $sprsCalculator = new SprsScoreCalculator($this->db);
        $scores = $sprsCalculator->calculate($assessment['id']);

        // Calculate ML completion percentages
        $mlStats = $this->getMLCompletionStats($assessment['id'], $maxLevel);

        // Get total CMMC controls for this maturity level
        $totalControls = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM controls
             WHERE framework = 'CMMC' AND ml_level <= ?",
            [$maxLevel]
        );

        return [
            'current_sprs' => $scores['current_score'],
            'projected_sprs' => $scores['projected_score'],
            'ml1_percent' => $mlStats['ml1_percent'],
            'ml2_percent' => $mlStats['ml2_percent'],
            'ml3_percent' => $mlStats['ml3_percent'],
            'total_controls' => $totalControls,
            'maturity_level' => $client['cmmc_maturity_level'] ?? 'Not Set',
        ];
    }

    private function getNIST800171Metrics(int $customerId): array
    {
        $totalControls = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM controls WHERE framework = 'NIST800171'"
        );

        $assessment = $this->db->fetchOne(
            "SELECT id FROM assessments
             WHERE customer_id = ? AND status = 'published'
             ORDER BY assessed_at DESC LIMIT 1",
            [$customerId]
        );

        if (!$assessment) {
            return [
                'total_controls' => $totalControls,
                'met' => 0,
                'not_met' => 0,
                'completion_percent' => 0,
            ];
        }

        $met = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings
             WHERE assessment_id = ? AND control_framework = 'NIST800171' AND status = 'met'",
            [$assessment['id']]
        );

        $notMet = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings
             WHERE assessment_id = ? AND control_framework = 'NIST800171' AND status = 'not_met'",
            [$assessment['id']]
        );

        return [
            'total_controls' => $totalControls,
            'met' => $met,
            'not_met' => $notMet,
            'completion_percent' => $totalControls > 0 ? round(($met / $totalControls) * 100) : 0,
        ];
    }

    private function getNIST80053Metrics(int $customerId): array
    {
        $totalControls = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM controls WHERE framework = 'NIST80053'"
        );

        $assessment = $this->db->fetchOne(
            "SELECT id FROM assessments
             WHERE customer_id = ? AND status = 'published'
             ORDER BY assessed_at DESC LIMIT 1",
            [$customerId]
        );

        if (!$assessment) {
            return [
                'total_controls' => $totalControls,
                'met' => 0,
                'not_met' => 0,
                'completion_percent' => 0,
                'families' => [],
            ];
        }

        $met = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings
             WHERE assessment_id = ? AND control_framework = 'NIST80053' AND status = 'met'",
            [$assessment['id']]
        );

        $notMet = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings
             WHERE assessment_id = ? AND control_framework = 'NIST80053' AND status = 'not_met'",
            [$assessment['id']]
        );

        // Get top control families
        $families = $this->db->fetchAll(
            "SELECT SUBSTRING_INDEX(c.code, '-', 1) as family,
                    COUNT(*) as total,
                    SUM(CASE WHEN cf.status = 'met' THEN 1 ELSE 0 END) as met
             FROM controls c
             LEFT JOIN control_findings cf ON c.code = cf.control_code AND cf.assessment_id = ?
             WHERE c.framework = 'NIST80053'
             GROUP BY family
             ORDER BY family",
            [$assessment['id']]
        );

        return [
            'total_controls' => $totalControls,
            'met' => $met,
            'not_met' => $notMet,
            'completion_percent' => $totalControls > 0 ? round(($met / $totalControls) * 100) : 0,
            'families' => array_slice($families, 0, 5), // Top 5 families
        ];
    }

    private function getFTCSafeguardsMetrics(int $customerId): array
    {
        $totalControls = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM controls WHERE framework = 'FTC-SAFEGUARDS'"
        );

        $assessment = $this->db->fetchOne(
            "SELECT id FROM assessments
             WHERE customer_id = ? AND status = 'published'
             ORDER BY assessed_at DESC LIMIT 1",
            [$customerId]
        );

        if (!$assessment) {
            return [
                'total_controls' => $totalControls,
                'met' => 0,
                'not_met' => 0,
                'completion_percent' => 0,
            ];
        }

        $met = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings
             WHERE assessment_id = ? AND control_framework = 'FTC-SAFEGUARDS' AND status = 'met'",
            [$assessment['id']]
        );

        $notMet = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings
             WHERE assessment_id = ? AND control_framework = 'FTC-SAFEGUARDS' AND status = 'not_met'",
            [$assessment['id']]
        );

        return [
            'total_controls' => $totalControls,
            'met' => $met,
            'not_met' => $notMet,
            'completion_percent' => $totalControls > 0 ? round(($met / $totalControls) * 100) : 0,
        ];
    }

    private function getGenericFrameworkMetrics(int $customerId, string $framework): array
    {
        $totalControls = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM controls WHERE framework = ?",
            [$framework]
        );

        $assessment = $this->db->fetchOne(
            "SELECT id FROM assessments
             WHERE customer_id = ? AND status = 'published'
             ORDER BY assessed_at DESC LIMIT 1",
            [$customerId]
        );

        if (!$assessment) {
            return [
                'total_controls' => $totalControls,
                'met' => 0,
                'not_met' => 0,
                'completion_percent' => 0,
            ];
        }

        $met = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings
             WHERE assessment_id = ? AND control_framework = ? AND status = 'met'",
            [$assessment['id'], $framework]
        );

        $notMet = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM control_findings
             WHERE assessment_id = ? AND control_framework = ? AND status = 'not_met'",
            [$assessment['id'], $framework]
        );

        return [
            'total_controls' => $totalControls,
            'met' => $met,
            'not_met' => $notMet,
            'completion_percent' => $totalControls > 0 ? round(($met / $totalControls) * 100) : 0,
        ];
    }

    /**
     * Convert CMMC maturity level string to numeric max level
     */
    private function getMaxLevelFromMaturityLevel(?string $maturityLevel): int
    {
        if (empty($maturityLevel)) {
            return 3; // Default to all levels if not specified
        }

        switch ($maturityLevel) {
            case 'Level 1':
                return 1;
            case 'Level 2':
                return 2;
            case 'Level 3':
                return 3;
            default:
                return 3;
        }
    }

    private function getMLCompletionStats(int $assessmentId, int $maxLevel = 3): array
    {
        $stats = ['ml1_percent' => 0, 'ml2_percent' => 0, 'ml3_percent' => 0];

        for ($level = 1; $level <= $maxLevel; $level++) {
            $total = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM controls WHERE framework = 'CMMC' AND ml_level = ?",
                [$level]
            );

            $met = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM control_findings cf
                 JOIN controls c ON cf.control_code = c.code AND cf.control_framework = c.framework
                 WHERE cf.assessment_id = ?
                 AND cf.control_framework = 'CMMC'
                 AND c.ml_level = ?
                 AND cf.status = 'met'",
                [$assessmentId, $level]
            );

            $stats["ml{$level}_percent"] = $total > 0 ? round(($met / $total) * 100) : 0;
        }

        return $stats;
    }

    private function getRecentAssessments(?int $customerId): array
    {
        if (!$customerId) {
            return [];
        }

        return $this->db->fetchAll(
            "SELECT a.*, u.display_name as assessor_name
             FROM assessments a
             LEFT JOIN users u ON a.assessor_user_id = u.id
             WHERE a.customer_id = ?
             ORDER BY a.created_at DESC
             LIMIT 5",
            [$customerId]
        );
    }

    private function getUpcomingMilestones(?int $customerId): array
    {
        if (!$customerId) {
            return [];
        }

        return $this->db->fetchAll(
            "SELECT * FROM poam_items
             WHERE customer_id = ?
             AND status IN ('open', 'in_progress')
             AND planned_completion_date >= CURDATE()
             ORDER BY planned_completion_date ASC
             LIMIT 10",
            [$customerId]
        );
    }

    private function getCurrentCustomer(?int $customerId): ?array
    {
        if (!$customerId) {
            return null;
        }

        return $this->db->fetchOne(
            "SELECT * FROM clients WHERE id = ?",
            [$customerId]
        );
    }
}
