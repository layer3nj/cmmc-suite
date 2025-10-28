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

        global $app;
        $config = $app->getConfig();

        Session::start();
        $currentUserId = Session::get('user_id');

        // Get all users
        $users = $this->db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");

        // Get system stats
        $stats = [
            'total_users' => count($users),
            'total_customers' => $this->db->fetchColumn("SELECT COUNT(*) FROM customers WHERE active = 1"),
            'total_assessments' => $this->db->fetchColumn("SELECT COUNT(*) FROM assessments"),
            'total_poam' => $this->db->fetchColumn("SELECT COUNT(*) FROM poam_items"),
        ];

        $content = View::render('admin/index', [
            'users' => $users,
            'stats' => $stats,
            'db_driver' => $this->db->getDriver(),
            'saml_enabled' => $config->get('saml.enabled', false),
            'current_user_id' => $currentUserId,
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

        // Fetch settings and convert to key-value array
        $settingsRows = $this->db->fetchAll("SELECT k, v FROM settings ORDER BY k");
        $settings = [];
        foreach ($settingsRows as $row) {
            $settings[$row['k']] = $row['v'];
        }

        $content = View::render('admin/settings', [
            'settings' => $settings,
            'base_url' => $request->baseUrl(),
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
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/admin/settings');
        }

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $uploadDir = BASE_PATH . '/public/uploads/logos';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExt = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['png', 'jpg', 'jpeg', 'svg'];

            if (in_array($fileExt, $allowedExts) && $_FILES['logo']['size'] <= 2097152) { // 2MB limit
                $fileName = 'logo_' . time() . '.' . $fileExt;
                $filePath = $uploadDir . '/' . $fileName;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $filePath)) {
                    // Delete old logo if exists
                    $oldLogo = $this->db->fetchOne("SELECT v FROM settings WHERE k = ?", ['logo_path']);
                    if ($oldLogo && file_exists(BASE_PATH . '/public' . $oldLogo['v'])) {
                        unlink(BASE_PATH . '/public' . $oldLogo['v']);
                    }

                    $_POST['logo_path'] = '/uploads/logos/' . $fileName;
                }
            }
        }

        // Settings fields to save
        $settingsFields = [
            'site_name', 'logo_path', 'primary_color', 'secondary_color', 'timezone',
            'saml_enabled', 'saml_idp_entity_id', 'saml_idp_sso_url', 'saml_idp_cert',
            'force_https', 'session_idle_timeout', 'session_absolute_timeout', 'rate_limit_enabled',
            'max_upload_size', 'allowed_file_types',
            'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password', 'from_email'
        ];

        $savedSettings = [];
        foreach ($settingsFields as $field) {
            $value = $request->post($field);

            // Skip empty password field (only update if changed)
            if ($field === 'smtp_password' && empty($value)) {
                continue;
            }

            // Handle checkboxes (they're only present if checked)
            if (in_array($field, ['saml_enabled', 'force_https', 'rate_limit_enabled'])) {
                $value = $value === '1' ? '1' : '0';
            }

            if ($value !== null) {
                $existing = $this->db->fetchOne(
                    "SELECT * FROM settings WHERE k = ?",
                    [$field]
                );

                if ($existing) {
                    $this->db->update('settings', [
                        'v' => $value,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], 'k = :k', [':k' => $field]);
                } else {
                    $this->db->insert('settings', [
                        'k' => $field,
                        'v' => $value,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $savedSettings[$field] = $value;
            }
        }

        AuditLogger::log('update_settings', 'settings', null, $savedSettings, $request->ip());

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
