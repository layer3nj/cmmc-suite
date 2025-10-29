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
            // Skip empty password field (only update if changed)
            if ($field === 'smtp_password' && empty($request->post($field))) {
                continue;
            }

            // Handle checkboxes (they're only present if checked)
            if (in_array($field, ['saml_enabled', 'force_https', 'rate_limit_enabled'])) {
                $value = $request->post($field) === '1' ? '1' : '0';
            } else {
                $value = $request->post($field);
            }

            // Skip null values (except for checkboxes which are always set)
            if ($value === null && !in_array($field, ['saml_enabled', 'force_https', 'rate_limit_enabled'])) {
                continue;
            }

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
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/admin/import');
        }

        $importType = $request->post('import_type');

        try {
            if ($importType === 'controls') {
                $this->importControls($request);
            } elseif ($importType === 'customers') {
                $this->importCustomers($request);
            } else {
                Session::flash('error', 'Invalid import type.');
                return Response::redirect($request->baseUrl() . '/admin/import');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Import failed: ' . $e->getMessage());
            return Response::redirect($request->baseUrl() . '/admin/import');
        }

        return Response::redirect($request->baseUrl() . '/admin/import');
    }

    private function importControls(Request $request): void
    {
        $source = $request->post('source');

        // Run the seeders to import controls
        $seedFiles = [
            BASE_PATH . '/database/seeds/001_seed_cmmc_controls.php',
            BASE_PATH . '/database/seeds/002_seed_nist_controls.php',
            BASE_PATH . '/database/seeds/003_seed_stig_controls.php',
            BASE_PATH . '/database/seeds/005_expand_control_coverage.php',
            BASE_PATH . '/database/seeds/006_additional_frameworks.php',
        ];

        $totalInserted = 0;
        foreach ($seedFiles as $seedFile) {
            if (file_exists($seedFile)) {
                $seeder = require $seedFile;
                if (is_callable($seeder)) {
                    $inserted = $seeder($this->db);
                    $totalInserted += $inserted;
                }
            }
        }

        if ($totalInserted > 0) {
            Session::flash('success', "Successfully imported {$totalInserted} controls across all frameworks.");
            AuditLogger::log('import', 'controls', null, ['source' => $source, 'count' => $totalInserted], $request->ip());
        } else {
            Session::flash('info', 'No new controls to import. All controls are already in the database.');
        }
    }

    private function importCustomers(Request $request): void
    {
        if (empty($_FILES['import_file']['name'])) {
            throw new \Exception('No file uploaded');
        }

        $file = $_FILES['import_file']['tmp_name'];
        $handle = fopen($file, 'r');

        if (!$handle) {
            throw new \Exception('Could not open file');
        }

        // Read header
        $header = fgetcsv($handle);
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue; // Skip empty rows

            $data = array_combine($header, $row);

            // Check if customer already exists
            $existing = $this->db->fetchOne(
                "SELECT id FROM customers WHERE name = ?",
                [$data['name']]
            );

            if (!$existing) {
                $this->db->insert('customers', [
                    'name' => $data['name'],
                    'contact_name' => $data['contact_name'] ?? null,
                    'contact_email' => $data['contact_email'] ?? null,
                    'contact_phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $imported++;
            }
        }

        fclose($handle);

        Session::flash('success', "Successfully imported {$imported} customers.");
        AuditLogger::log('import', 'customers', null, ['count' => $imported], $request->ip());
    }

    public function runMigrations(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/admin/import');
        }

        try {
            $migrationsDir = BASE_PATH . '/database/migrations';
            $migrationFiles = glob($migrationsDir . '/*.php');
            sort($migrationFiles);

            $driver = $this->db->getDriver();
            $migrationsRun = 0;
            $migrationsSkipped = 0;

            foreach ($migrationFiles as $file) {
                $migrationName = basename($file);
                $migration = require $file;

                if (isset($migration[$driver])) {
                    // Split migration SQL into individual statements
                    $sql = $migration[$driver];

                    // Remove SQL comments (-- style)
                    $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);

                    // Split on semicolons and filter out empty/whitespace statements
                    $statements = array_filter(
                        array_map('trim', explode(';', $sql)),
                        function($stmt) {
                            // Skip empty statements or statements that are just comments
                            return !empty($stmt) && !preg_match('/^\s*$/', $stmt);
                        }
                    );

                    $statementsRun = 0;
                    $statementsSkipped = 0;

                    foreach ($statements as $statement) {
                        try {
                            $this->db->query($statement);
                            $statementsRun++;
                        } catch (\Exception $e) {
                            $errorMsg = $e->getMessage();

                            // Skip if migration already applied (table/column/index exists)
                            if (stripos($errorMsg, 'already exists') !== false ||
                                stripos($errorMsg, 'Duplicate column') !== false ||
                                stripos($errorMsg, 'Duplicate key') !== false ||
                                stripos($errorMsg, 'Multiple primary key') !== false) {
                                $statementsSkipped++;
                                continue;
                            }

                            // If it's a real error, throw it
                            throw new \Exception("Migration {$migrationName} failed: " . $errorMsg);
                        }
                    }

                    // Count migration as run if at least one statement succeeded
                    if ($statementsRun > 0) {
                        $migrationsRun++;
                    }
                    // Count as skipped only if all statements were skipped
                    if ($statementsSkipped > 0 && $statementsRun === 0) {
                        $migrationsSkipped++;
                    }
                }
            }

            if ($migrationsRun > 0) {
                $message = "Successfully ran {$migrationsRun} database migration(s).";
                if ($migrationsSkipped > 0) {
                    $message .= " ({$migrationsSkipped} already applied)";
                }
                Session::flash('success', $message);
                AuditLogger::log('run_migrations', 'database', null, [
                    'count' => $migrationsRun,
                    'skipped' => $migrationsSkipped
                ], $request->ip());
            } else {
                Session::flash('info', 'All migrations are already up to date.');
            }
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        return Response::redirect($request->baseUrl() . '/admin/import');
    }
}
