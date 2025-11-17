<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Middleware\AuthMiddleware;

/**
 * Executive Dashboard Controller
 * Provides high-level overview of compliance across all clients
 */
class ExecutiveDashboardController
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

        // Only allow admins to view executive dashboard
        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        // Get all active clients with their compliance stats
        $clients = $this->getClientComplianceStats();

        // Get overall statistics
        $overallStats = $this->getOverallStats();

        // Get at-risk clients
        $atRiskClients = $this->getAtRiskClients();

        // Get compliance by framework
        $frameworkStats = $this->getFrameworkStats();

        // Get recent activity
        $recentActivity = $this->getRecentActivity();

        $content = View::render('executive/index', [
            'clients' => $clients,
            'overall_stats' => $overallStats,
            'at_risk_clients' => $atRiskClients,
            'framework_stats' => $frameworkStats,
            'recent_activity' => $recentActivity,
        ]);

        return new Response($content);
    }

    /**
     * Get compliance statistics for all clients
     */
    private function getClientComplianceStats(): array
    {
        $clients = $this->db->fetchAll(
            "SELECT id, name, created_at FROM clients WHERE active = 1 ORDER BY name ASC"
        );

        foreach ($clients as &$client) {
            $clientId = $client['id'];

            // Get latest assessment
            $latestAssessment = $this->db->fetchOne(
                "SELECT * FROM assessments
                 WHERE customer_id = ?
                 ORDER BY created_at DESC
                 LIMIT 1",
                [$clientId]
            );

            // Get open POA&M count
            $openPoam = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM poam_items
                 WHERE customer_id = ?
                 AND status IN ('open', 'in_progress')",
                [$clientId]
            );

            // Get overdue POA&M count
            $overduePoam = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM poam_items
                 WHERE customer_id = ?
                 AND status IN ('open', 'in_progress')
                 AND planned_completion_date < CURDATE()",
                [$clientId]
            );

            // Get document count
            $docCount = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM documents WHERE customer_id = ?",
                [$clientId]
            );

            // Determine compliance status
            $status = 'unknown';
            $statusClass = 'secondary';
            $daysSinceAssessment = null;

            if ($latestAssessment) {
                $assessedDate = strtotime($latestAssessment['assessed_at']);
                $daysSinceAssessment = floor((time() - $assessedDate) / 86400);

                if ($daysSinceAssessment <= 90 && $latestAssessment['status'] === 'published') {
                    $status = 'compliant';
                    $statusClass = 'success';
                } elseif ($daysSinceAssessment <= 180) {
                    $status = 'needs_review';
                    $statusClass = 'warning';
                } else {
                    $status = 'expired';
                    $statusClass = 'danger';
                }
            }

            $client['latest_assessment'] = $latestAssessment;
            $client['open_poam'] = $openPoam;
            $client['overdue_poam'] = $overduePoam;
            $client['document_count'] = $docCount;
            $client['status'] = $status;
            $client['status_class'] = $statusClass;
            $client['days_since_assessment'] = $daysSinceAssessment;
        }

        return $clients;
    }

    /**
     * Get overall statistics across all clients
     */
    private function getOverallStats(): array
    {
        return [
            'total_clients' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM clients WHERE active = 1"
            ),
            'compliant_clients' => $this->db->fetchColumn(
                "SELECT COUNT(DISTINCT a.customer_id)
                 FROM assessments a
                 JOIN clients c ON a.customer_id = c.id
                 WHERE c.active = 1
                 AND a.status = 'published'
                 AND a.assessed_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)"
            ),
            'total_assessments' => $this->db->fetchColumn(
                "SELECT COUNT(*)
                 FROM assessments a
                 JOIN clients c ON a.customer_id = c.id
                 WHERE c.active = 1"
            ),
            'open_poam_items' => $this->db->fetchColumn(
                "SELECT COUNT(*)
                 FROM poam_items p
                 JOIN clients c ON p.customer_id = c.id
                 WHERE c.active = 1
                 AND p.status IN ('open', 'in_progress')"
            ),
            'overdue_poam_items' => $this->db->fetchColumn(
                "SELECT COUNT(*)
                 FROM poam_items p
                 JOIN clients c ON p.customer_id = c.id
                 WHERE c.active = 1
                 AND p.status IN ('open', 'in_progress')
                 AND p.planned_completion_date < CURDATE()"
            ),
        ];
    }

    /**
     * Get clients that need attention
     */
    private function getAtRiskClients(): array
    {
        $atRisk = [];

        // Clients with expired assessments (>180 days)
        $expiredAssessments = $this->db->fetchAll(
            "SELECT c.id, c.name, a.assessed_at, a.framework,
                    DATEDIFF(CURDATE(), a.assessed_at) as days_expired
             FROM clients c
             JOIN assessments a ON c.id = a.customer_id
             WHERE c.active = 1
             AND a.assessed_at < DATE_SUB(CURDATE(), INTERVAL 180 DAY)
             AND a.id IN (
                 SELECT MAX(id) FROM assessments
                 WHERE customer_id = c.id
                 GROUP BY customer_id
             )
             ORDER BY days_expired DESC
             LIMIT 10"
        );

        foreach ($expiredAssessments as $client) {
            $atRisk[] = [
                'client_id' => $client['id'],
                'client_name' => $client['name'],
                'risk_type' => 'expired_assessment',
                'details' => "Assessment expired {$client['days_expired']} days ago",
                'severity' => 'high'
            ];
        }

        // Clients with overdue POA&M items
        $overduePoam = $this->db->fetchAll(
            "SELECT c.id, c.name, COUNT(*) as overdue_count,
                    MIN(p.planned_completion_date) as oldest_due_date
             FROM clients c
             JOIN poam_items p ON c.id = p.customer_id
             WHERE c.active = 1
             AND p.status IN ('open', 'in_progress')
             AND p.planned_completion_date < CURDATE()
             GROUP BY c.id, c.name
             HAVING overdue_count >= 3
             ORDER BY overdue_count DESC
             LIMIT 10"
        );

        foreach ($overduePoam as $client) {
            $atRisk[] = [
                'client_id' => $client['id'],
                'client_name' => $client['name'],
                'risk_type' => 'overdue_poam',
                'details' => "{$client['overdue_count']} overdue POA&M items",
                'severity' => 'medium'
            ];
        }

        // Clients with no assessments
        $noAssessments = $this->db->fetchAll(
            "SELECT c.id, c.name, c.created_at
             FROM clients c
             LEFT JOIN assessments a ON c.id = a.customer_id
             WHERE c.active = 1
             AND a.id IS NULL
             LIMIT 10"
        );

        foreach ($noAssessments as $client) {
            $atRisk[] = [
                'client_id' => $client['id'],
                'client_name' => $client['name'],
                'risk_type' => 'no_assessment',
                'details' => 'No assessments on file',
                'severity' => 'low'
            ];
        }

        return $atRisk;
    }

    /**
     * Get compliance statistics by framework
     */
    private function getFrameworkStats(): array
    {
        $frameworks = $this->db->fetchAll(
            "SELECT
                a.framework,
                COUNT(DISTINCT a.customer_id) as client_count,
                COUNT(*) as total_assessments,
                SUM(CASE WHEN a.status = 'published' THEN 1 ELSE 0 END) as published_count
             FROM assessments a
             JOIN clients c ON a.customer_id = c.id
             WHERE c.active = 1
             GROUP BY a.framework
             ORDER BY client_count DESC"
        );

        return $frameworks;
    }

    /**
     * Get recent activity across all clients
     */
    private function getRecentActivity(): array
    {
        $activities = [];

        // Recent assessments
        $recentAssessments = $this->db->fetchAll(
            "SELECT
                a.id, a.framework, a.assessed_at, a.status,
                c.name as client_name, c.id as client_id
             FROM assessments a
             JOIN clients c ON a.customer_id = c.id
             WHERE c.active = 1
             ORDER BY a.created_at DESC
             LIMIT 5"
        );

        foreach ($recentAssessments as $assessment) {
            $activities[] = [
                'type' => 'assessment',
                'icon' => '✅',
                'description' => "{$assessment['client_name']} - {$assessment['framework']} assessment {$assessment['status']}",
                'date' => $assessment['assessed_at'],
                'client_id' => $assessment['client_id'],
            ];
        }

        // Recent POA&M closures
        $recentPoamClosures = $this->db->fetchAll(
            "SELECT
                p.id, p.control_code, p.updated_at,
                c.name as client_name, c.id as client_id
             FROM poam_items p
             JOIN clients c ON p.customer_id = c.id
             WHERE c.active = 1
             AND p.status = 'closed'
             ORDER BY p.updated_at DESC
             LIMIT 5"
        );

        foreach ($recentPoamClosures as $poam) {
            $activities[] = [
                'type' => 'poam_closed',
                'icon' => '✓',
                'description' => "{$poam['client_name']} - POA&M item {$poam['control_code']} closed",
                'date' => $poam['updated_at'],
                'client_id' => $poam['client_id'],
            ];
        }

        // Sort by date
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 10);
    }
}
