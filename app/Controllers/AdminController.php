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
 * Admin Controller
 */
class AdminController
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

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        // Get system stats
        $stats = [
            'total_users' => $this->db->fetchColumn("SELECT COUNT(*) FROM users"),
            'total_customers' => $this->db->fetchColumn("SELECT COUNT(*) FROM customers WHERE active = 1"),
            'total_assessments' => $this->db->fetchColumn("SELECT COUNT(*) FROM assessments"),
            'total_poam' => $this->db->fetchColumn("SELECT COUNT(*) FROM poam_items"),
        ];

        $content = View::render('admin/index', [
            'stats' => $stats,
        ]);

        return new Response($content);
    }

    public function users(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $users = $this->db->fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC"
        );

        $content = View::render('admin/users', [
            'users' => $users,
        ]);

        return new Response($content);
    }

    public function createUser(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $email = $request->post('email');
        $displayName = $request->post('display_name');
        $role = $request->post('role');
        $password = $request->post('password');

        if (empty($email) || empty($displayName) || empty($role)) {
            return Response::json(['success' => false, 'message' => 'All fields are required']);
        }

        $passwordHash = !empty($password) ? password_hash($password, PASSWORD_ARGON2ID) : null;

        $userId = $this->db->insert('users', [
            'email' => $email,
            'display_name' => $displayName,
            'role' => $role,
            'password_hash' => $passwordHash,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('create', 'user', $userId, [
            'email' => $email,
            'role' => $role
        ], $request->ip());

        return Response::json(['success' => true, 'user_id' => $userId]);
    }

    public function updateUser(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
        if (!$user) {
            return Response::json(['success' => false, 'message' => 'User not found']);
        }

        $data = [
            'display_name' => $request->post('display_name'),
            'role' => $request->post('role'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $password = $request->post('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_ARGON2ID);
        }

        $this->db->update('users', $data, 'id = :id', [':id' => $id]);

        AuditLogger::logChange('user', $id, $user, $data, $request->ip());

        return Response::json(['success' => true]);
    }

    public function deleteUser(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Don't allow deleting yourself
        if ($id == Session::get('user_id')) {
            return Response::json(['success' => false, 'message' => 'Cannot delete your own account']);
        }

        $this->db->delete('users', 'id = :id', [':id' => $id]);

        AuditLogger::log('delete', 'user', $id, null, $request->ip());

        return Response::json(['success' => true]);
    }

    public function settings(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $settings = $this->db->fetchAll("SELECT * FROM settings ORDER BY k");

        $content = View::render('admin/settings', [
            'settings' => $settings,
        ]);

        return new Response($content);
    }

    public function updateSettings(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Update settings from POST data
        $settings = $request->post('settings', []);

        foreach ($settings as $key => $value) {
            $existing = $this->db->fetchOne(
                "SELECT * FROM settings WHERE k = ?",
                [$key]
            );

            if ($existing) {
                $this->db->update('settings', [
                    'v' => $value,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], 'k = :k', [':k' => $key]);
            } else {
                $this->db->insert('settings', [
                    'k' => $key,
                    'v' => $value,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        AuditLogger::log('update_settings', 'settings', null, $settings, $request->ip());

        Session::flash('success', 'Settings updated successfully.');
        return Response::redirect($request->baseUrl() . '/admin/settings');
    }

    public function auditLog(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $logs = $this->db->fetchAll(
            "SELECT al.*, u.display_name as user_name
             FROM audit_log al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.ts DESC
             LIMIT 100"
        );

        $content = View::render('admin/audit-log', [
            'logs' => $logs,
        ]);

        return new Response($content);
    }

    public function showImport(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $content = View::render('admin/import');
        return new Response($content);
    }

    public function import(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Handle import file
        Session::flash('info', 'Import functionality coming soon.');
        return Response::redirect($request->baseUrl() . '/admin/import');
    }
}
