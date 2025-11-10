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

        // Get metrics
        $metrics = $this->getMetrics($currentCustomerId);

        // Get recent assessments
        $recentAssessments = $this->getRecentAssessments($currentCustomerId);

        // Get upcoming POA&M milestones
        $upcomingMilestones = $this->getUpcomingMilestones($currentCustomerId);

        $content = View::render('dashboard/index', [
            'metrics' => $metrics,
            'recent_assessments' => $recentAssessments,
            'upcoming_milestones' => $upcomingMilestones,
            'current_customer' => $this->getCurrentCustomer($currentCustomerId),
        ]);

        return new Response($content);
    }

    private function getMetrics(?int $customerId): array
    {
        if (!$customerId) {
            return [
                'current_sprs' => 0,
                'projected_sprs' => 0,
                'ml1_percent' => 0,
                'ml2_percent' => 0,
                'ml3_percent' => 0,
                'open_poam' => 0,
                'total_controls' => 0,
            ];
        }

        // Get latest published assessment
        $assessment = $this->db->fetchOne(
            "SELECT id FROM assessments
             WHERE customer_id = ? AND status = 'published'
             ORDER BY assessed_at DESC LIMIT 1",
            [$customerId]
        );

        if (!$assessment) {
            return [
                'current_sprs' => -203,
                'projected_sprs' => -203,
                'ml1_percent' => 0,
                'ml2_percent' => 0,
                'ml3_percent' => 0,
                'open_poam' => 0,
                'total_controls' => $this->db->fetchColumn("SELECT COUNT(*) FROM controls WHERE framework = 'CMMC'"),
            ];
        }

        // Calculate SPRS scores
        $sprsCalculator = new SprsScoreCalculator($this->db);
        $scores = $sprsCalculator->calculate($assessment['id']);

        // Calculate ML completion percentages
        $mlStats = $this->getMLCompletionStats($assessment['id']);

        // Get open POA&M count
        $openPoam = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM poam_items
             WHERE customer_id = ? AND status IN ('open', 'in_progress')",
            [$customerId]
        );

        // Get total CMMC controls
        $totalControls = $this->db->fetchColumn("SELECT COUNT(*) FROM controls WHERE framework = 'CMMC'");

        return [
            'current_sprs' => $scores['current_score'],
            'projected_sprs' => $scores['projected_score'],
            'ml1_percent' => $mlStats['ml1_percent'],
            'ml2_percent' => $mlStats['ml2_percent'],
            'ml3_percent' => $mlStats['ml3_percent'],
            'open_poam' => $openPoam,
            'total_controls' => $totalControls,
        ];
    }

    private function getMLCompletionStats(int $assessmentId): array
    {
        $stats = ['ml1_percent' => 0, 'ml2_percent' => 0, 'ml3_percent' => 0];

        for ($level = 1; $level <= 3; $level++) {
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
