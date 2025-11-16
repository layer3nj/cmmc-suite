<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Middleware\AuthMiddleware;

/**
 * Admin Dashboard Controller
 * Provides admin-level overview of all customers
 */
class AdminDashboardController
{
    private $db;

    public function __construct()
    {
        global $app;
        $this->db = $app->getDatabase();
    }

    public function customersOverview(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        // Get all customers with their statistics
        $customers = $this->getAllCustomersWithStats();

        // Get summary statistics
        $stats = $this->getSummaryStats($customers);

        $content = View::render('admin/customers-overview', [
            'customers' => $customers,
            'stats' => $stats,
        ]);

        return new Response($content);
    }

    /**
     * AJAX endpoint for customer search
     */
    public function searchCustomers(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $searchTerm = $request->query('q', '');

        $customers = $this->getAllCustomersWithStats($searchTerm);

        return Response::json([
            'success' => true,
            'customers' => $customers
        ]);
    }

    private function getAllCustomersWithStats(string $searchTerm = ''): array
    {
        // Base query
        $sql = "SELECT * FROM clients WHERE active = 1";
        $params = [];

        // Add search filter if provided
        if (!empty($searchTerm)) {
            $sql .= " AND (name LIKE ? OR contact_email LIKE ?)";
            $searchParam = '%' . $searchTerm . '%';
            $params = [$searchParam, $searchParam];
        }

        $sql .= " ORDER BY name ASC";

        $clients = $this->db->fetchAll($sql, $params);

        // Enhance each client with statistics
        foreach ($clients as &$client) {
            $client['modules'] = $this->getModuleStatus($client['id']);
            $client['policies'] = $this->getPolicyStats($client['id']);
            $client['compliance'] = $this->getCompliancePhases($client['id']);
            $client['users'] = $this->getUserStats($client['id']);
        }

        return $clients;
    }

    private function getModuleStatus(int $clientId): array
    {
        $modules = $this->db->fetchAll(
            "SELECT module_name, enabled, completion_percentage
             FROM customer_modules
             WHERE client_id = ?
             ORDER BY FIELD(module_name, 'policies', 'training', 'compliance', 'risk')",
            [$clientId]
        );

        // Ensure all modules exist
        $moduleNames = ['policies', 'training', 'compliance', 'risk'];
        $existingModules = array_column($modules, 'module_name');

        foreach ($moduleNames as $moduleName) {
            if (!in_array($moduleName, $existingModules)) {
                $modules[] = [
                    'module_name' => $moduleName,
                    'enabled' => true,
                    'completion_percentage' => 0
                ];
            }
        }

        return $modules;
    }

    private function getPolicyStats(int $clientId): array
    {
        // Note: The policies table is currently a global template table
        // without customer-specific tracking. Future enhancement would add
        // a customer_policies or policy_assignments table.
        // For now, return zero counts.

        return [
            'approved' => 0,
            'in_review' => 0,
            'draft' => 0,
            'outdated' => 0
        ];
    }

    private function getCompliancePhases(int $clientId): array
    {
        $phases = $this->db->fetchAll(
            "SELECT phase_number, completion_percentage
             FROM compliance_phases
             WHERE client_id = ?
             AND framework = 'CMMC'
             ORDER BY phase_number ASC",
            [$clientId]
        );

        // Ensure we have all 4+ phases
        $phaseData = [];
        for ($i = 1; $i <= 4; $i++) {
            $found = false;
            foreach ($phases as $phase) {
                if ($phase['phase_number'] == $i) {
                    $phaseData[$i] = $phase['completion_percentage'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $phaseData[$i] = 0;
            }
        }

        return $phaseData;
    }

    private function getUserStats(int $clientId): array
    {
        // Get client's SSO and SCIM status from clients table
        $client = $this->db->fetchOne(
            "SELECT sso_enforced, scim_enabled, total_users, active_users, never_logged_in_users
             FROM clients
             WHERE id = ?",
            [$clientId]
        );

        // If stats aren't populated, calculate them from user_client_access table
        if ($client['total_users'] == 0) {
            $totalUsers = $this->db->fetchColumn(
                "SELECT COUNT(DISTINCT uca.user_id)
                 FROM user_client_access uca
                 WHERE uca.client_id = ?",
                [$clientId]
            ) ?: 0;

            $activeUsers = $this->db->fetchColumn(
                "SELECT COUNT(DISTINCT uca.user_id)
                 FROM user_client_access uca
                 JOIN users u ON uca.user_id = u.id
                 WHERE uca.client_id = ?
                 AND u.last_login_at IS NOT NULL
                 AND u.last_login_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)",
                [$clientId]
            ) ?: 0;

            $neverLoggedIn = $this->db->fetchColumn(
                "SELECT COUNT(DISTINCT uca.user_id)
                 FROM user_client_access uca
                 JOIN users u ON uca.user_id = u.id
                 WHERE uca.client_id = ?
                 AND u.last_login_at IS NULL",
                [$clientId]
            ) ?: 0;

            // Update the clients table with these stats
            $this->db->update('clients', [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'never_logged_in_users' => $neverLoggedIn,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = :id', [':id' => $clientId]);

            $client['total_users'] = $totalUsers;
            $client['active_users'] = $activeUsers;
            $client['never_logged_in_users'] = $neverLoggedIn;
        }

        return [
            'sso_enforced' => (bool)$client['sso_enforced'],
            'scim_enabled' => (bool)$client['scim_enabled'],
            'total' => (int)$client['total_users'],
            'active' => (int)$client['active_users'],
            'never_logged_in' => (int)$client['never_logged_in_users']
        ];
    }

    private function getSummaryStats(array $customers): array
    {
        $total = count($customers);

        // Count new customers (created in last 30 days)
        $newCount = 0;
        $thirtyDaysAgo = strtotime('-30 days');

        foreach ($customers as $customer) {
            if (isset($customer['created_at']) && strtotime($customer['created_at']) >= $thirtyDaysAgo) {
                $newCount++;
            }
        }

        return [
            'total' => $total,
            'new_last_30_days' => $newCount
        ];
    }
}
