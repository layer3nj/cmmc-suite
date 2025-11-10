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

        // Get all clients for the access assignment UI
        $clients = $this->db->fetchAll(
            "SELECT id, name FROM clients WHERE active = 1 ORDER BY name ASC"
        );

        // Get client access for each user (only if table exists)
        try {
            foreach ($users as &$user) {
                $user['client_access'] = $this->db->fetchAll(
                    "SELECT uca.client_id, uca.access_level, c.name as client_name
                     FROM user_client_access uca
                     JOIN clients c ON uca.client_id = c.id
                     WHERE uca.user_id = ?
                     ORDER BY c.name ASC",
                    [$user['id']]
                );
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet - migration hasn't been run
            foreach ($users as &$user) {
                $user['client_access'] = [];
            }
        }

        // Get system stats
        $stats = [
            'total_users' => count($users),
            'total_customers' => $this->db->fetchColumn("SELECT COUNT(*) FROM clients WHERE active = 1"),
            'total_assessments' => $this->db->fetchColumn("SELECT COUNT(*) FROM assessments"),
            'total_poam' => $this->db->fetchColumn("SELECT COUNT(*) FROM poam_items"),
        ];

        // Get SAML status from database settings
        $settingsService = new \App\Services\SettingsService($this->db);
        $settings = $settingsService->getAll();

        $content = View::render('admin/index', [
            'users' => $users,
            'clients' => $clients,
            'stats' => $stats,
            'db_driver' => $this->db->getDriver(),
            'saml_enabled' => ($settings['saml_enabled'] ?? '0') === '1',
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

        // Get all clients for the access assignment UI
        $clients = $this->db->fetchAll(
            "SELECT id, name FROM clients WHERE active = 1 ORDER BY name ASC"
        );

        // Get client access for each user (only if table exists)
        try {
            foreach ($users as &$user) {
                $user['client_access'] = $this->db->fetchAll(
                    "SELECT uca.client_id, uca.access_level, c.name as client_name
                     FROM user_client_access uca
                     JOIN clients c ON uca.client_id = c.id
                     WHERE uca.user_id = ?
                     ORDER BY c.name ASC",
                    [$user['id']]
                );
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet - migration hasn't been run
            foreach ($users as &$user) {
                $user['client_access'] = [];
            }
        }

        $content = View::render('admin/users', [
            'users' => $users,
            'clients' => $clients,
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

        // Handle client access assignments (only if table exists)
        $clientAccess = $request->post('client_access'); // Format: [{client_id: 1, access_level: 'read-only'}, ...]
        if ($clientAccess && is_string($clientAccess)) {
            $clientAccess = json_decode($clientAccess, true);
        }

        if (is_array($clientAccess)) {
            try {
                foreach ($clientAccess as $access) {
                    if (isset($access['client_id']) && isset($access['access_level'])) {
                        $this->db->insert('user_client_access', [
                            'user_id' => $userId,
                            'client_id' => $access['client_id'],
                            'access_level' => $access['access_level'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Table doesn't exist yet - migration hasn't been run, skip client access
            }
        }

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

        // Handle client access assignments (only if table exists)
        try {
            // First, remove all existing access for this user
            $this->db->query("DELETE FROM user_client_access WHERE user_id = ?", [$id]);

            // Then add new assignments
            $clientAccess = $request->post('client_access');
            if ($clientAccess && is_string($clientAccess)) {
                $clientAccess = json_decode($clientAccess, true);
            }

            if (is_array($clientAccess)) {
                foreach ($clientAccess as $access) {
                    if (isset($access['client_id']) && isset($access['access_level'])) {
                        $this->db->insert('user_client_access', [
                            'user_id' => $id,
                            'client_id' => $access['client_id'],
                            'access_level' => $access['access_level'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet - migration hasn't been run, skip client access
        }

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

        $displayName = $request->post('display_name');
        $role = $request->post('role');
        $clientAccessJson = $request->post('client_access');

        // Validate required fields
        if (empty($displayName) || empty($role)) {
            return Response::json(['success' => false, 'message' => 'Display name and role are required']);
        }

        // Validate role
        $validRoles = ['viewer', 'contributor', 'auditor', 'admin'];
        if (!in_array($role, $validRoles)) {
            return Response::json(['success' => false, 'message' => 'Invalid role selected']);
        }

        try {
            // Update user
            $this->db->update('users', [
                'display_name' => $displayName,
                'role' => $role,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', [':id' => $id]);

            // Update client access
            // First, delete existing access
            try {
                $this->db->delete('user_client_access', 'user_id = :user_id', [':user_id' => $id]);
            } catch (\Exception $e) {
                // Table might not exist yet
            }

            // Then, insert new access if provided
            if (!empty($clientAccessJson)) {
                $clientAccess = json_decode($clientAccessJson, true);
                if (is_array($clientAccess) && !empty($clientAccess)) {
                    foreach ($clientAccess as $access) {
                        try {
                            $this->db->insert('user_client_access', [
                                'user_id' => $id,
                                'client_id' => $access['client_id'],
                                'access_level' => $access['access_level'],
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        } catch (\Exception $e) {
                            // Skip if table doesn't exist
                        }
                    }
                }
            }

            AuditLogger::log('update', 'user', $id, [
                'display_name' => $displayName,
                'role' => $role,
                'client_access_count' => $clientAccess ? count($clientAccess) : 0,
            ], $request->ip());

            return Response::json(['success' => true, 'message' => 'User updated successfully']);
        } catch (\Exception $e) {
            return Response::json(['success' => false, 'message' => 'Failed to update user: ' . $e->getMessage()]);
        }
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

        // Clear settings cache so changes take effect immediately
        \App\Services\SettingsService::clearCache();

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
            BASE_PATH . '/database/seeds/007_enhanced_frameworks.php',
            BASE_PATH . '/database/seeds/008_complete_nist_800_171.php',
        ];

        $totalInserted = 0;
        $totalUpdated = 0;
        foreach ($seedFiles as $seedFile) {
            if (file_exists($seedFile)) {
                $seeder = require $seedFile;
                if (is_callable($seeder)) {
                    $result = $seeder($this->db);

                    // Handle both old format (integer) and new format (array)
                    if (is_array($result)) {
                        $totalInserted += $result['inserted'] ?? 0;
                        $totalUpdated += $result['updated'] ?? 0;
                    } else {
                        $totalInserted += (int)$result;
                    }
                }
            }
        }

        if ($totalInserted > 0 || $totalUpdated > 0) {
            $message = "Successfully imported {$totalInserted} new control(s)";
            if ($totalUpdated > 0) {
                $message .= " and updated {$totalUpdated} existing control(s)";
            }
            $message .= " across all frameworks.";
            Session::flash('success', $message);
            AuditLogger::log('import', 'controls', null, ['source' => $source, 'inserted' => $totalInserted, 'updated' => $totalUpdated], $request->ip());
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
                "SELECT id FROM clients WHERE name = ?",
                [$data['name']]
            );

            if (!$existing) {
                $this->db->insert('clients', [
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

    public function runSeeders(Request $request): Response
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
            $seedersDir = BASE_PATH . '/database/seeds';
            $seederFiles = glob($seedersDir . '/*.php');
            sort($seederFiles);

            $seedersRun = 0;
            $totalInserted = 0;
            $totalUpdated = 0;

            foreach ($seederFiles as $file) {
                $seederName = basename($file);

                if (file_exists($file)) {
                    $seeder = require $file;

                    // Handle different seeder formats
                    if (is_callable($seeder)) {
                        try {
                            $result = $seeder($this->db);

                            // Handle both old format (integer) and new format (array)
                            if (is_array($result)) {
                                $totalInserted += $result['inserted'] ?? 0;
                                $totalUpdated += $result['updated'] ?? 0;
                            } else {
                                $totalInserted += (int)$result;
                            }
                            $seedersRun++;
                        } catch (\Exception $e) {
                            // Log but continue with other seeders
                            error_log("Seeder {$seederName} failed: " . $e->getMessage());
                        }
                    } elseif (is_array($seeder)) {
                        // Handle array-based seeders (like policy templates)
                        try {
                            foreach ($seeder as $item) {
                                // Detect table based on seeder data structure
                                if (isset($item['title']) && isset($item['category']) && isset($item['content'])) {
                                    // This is a policy seeder
                                    $existing = $this->db->fetchOne(
                                        "SELECT id FROM policies WHERE title = ? AND category = ?",
                                        [$item['title'], $item['category']]
                                    );

                                    if (!$existing) {
                                        $this->db->insert('policies', array_merge($item, [
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ]));
                                        $totalInserted++;
                                    } else {
                                        $totalUpdated++;
                                    }
                                }
                            }
                            $seedersRun++;
                        } catch (\Exception $e) {
                            error_log("Seeder {$seederName} failed: " . $e->getMessage());
                        }
                    }
                }
            }

            if ($seedersRun > 0) {
                $message = "Successfully ran {$seedersRun} seeder(s): {$totalInserted} inserted, {$totalUpdated} skipped (already exist).";
                Session::flash('success', $message);
                AuditLogger::log('run_seeders', 'database', null, [
                    'seeders' => $seedersRun,
                    'inserted' => $totalInserted,
                    'updated' => $totalUpdated
                ], $request->ip());
            } else {
                Session::flash('info', 'No seeders were run.');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Seeders failed: ' . $e->getMessage());
        }

        return Response::redirect($request->baseUrl() . '/admin/import');
    }

    public function updateSprsScores(Request $request): Response
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
            // Reset all NIST 800-171 and CMMC controls to default (3 points)
            $updated1 = $this->db->query(
                "UPDATE controls SET sprs_score = 3 WHERE framework = 'NIST800171'"
            );

            $updated2 = $this->db->query(
                "UPDATE controls SET sprs_score = 3 WHERE framework = 'CMMC'"
            );

            // HIGH-RISK CONTROLS (5 points) - Access Control: Privileged functions and remote access
            $this->db->query(
                "UPDATE controls SET sprs_score = 5
                 WHERE framework = 'NIST800171'
                 AND code IN ('3.1.5', '3.1.6', '3.1.7')"
            );

            $this->db->query(
                "UPDATE controls SET sprs_score = 5
                 WHERE framework = 'CMMC'
                 AND (code LIKE '%3.1.5' OR code LIKE '%3.1.6' OR code LIKE '%3.1.7')"
            );

            // HIGH-RISK CONTROLS (5 points) - Multi-factor authentication
            $this->db->query(
                "UPDATE controls SET sprs_score = 5
                 WHERE framework = 'NIST800171'
                 AND code IN ('3.5.3', '3.5.4')"
            );

            $this->db->query(
                "UPDATE controls SET sprs_score = 5
                 WHERE framework = 'CMMC'
                 AND (code LIKE '%3.5.3' OR code LIKE '%3.5.4')"
            );

            // HIGH-RISK CONTROLS (5 points) - Incident Response
            $this->db->query(
                "UPDATE controls SET sprs_score = 5
                 WHERE framework = 'NIST800171'
                 AND code IN ('3.6.1', '3.6.2')"
            );

            $this->db->query(
                "UPDATE controls SET sprs_score = 5
                 WHERE framework = 'CMMC'
                 AND (code LIKE '%3.6.1' OR code LIKE '%3.6.2')"
            );

            // HIGH-RISK CONTROLS (5 points) - Encryption
            $this->db->query(
                "UPDATE controls SET sprs_score = 5
                 WHERE framework = 'NIST800171'
                 AND code IN ('3.13.8', '3.13.11', '3.13.16')"
            );

            $this->db->query(
                "UPDATE controls SET sprs_score = 5
                 WHERE framework = 'CMMC'
                 AND (code LIKE '%3.13.8' OR code LIKE '%3.13.11' OR code LIKE '%3.13.16')"
            );

            // LOW-RISK CONTROLS (1 point) - Awareness and Training
            $this->db->query(
                "UPDATE controls SET sprs_score = 1
                 WHERE framework = 'NIST800171'
                 AND code LIKE '3.2.%'"
            );

            $this->db->query(
                "UPDATE controls SET sprs_score = 1
                 WHERE framework = 'CMMC'
                 AND code LIKE '%3.2.%'"
            );

            // LOW-RISK CONTROLS (1 point) - Some maintenance and personnel security
            $this->db->query(
                "UPDATE controls SET sprs_score = 1
                 WHERE framework = 'NIST800171'
                 AND code IN ('3.7.3', '3.7.6', '3.9.2')"
            );

            $this->db->query(
                "UPDATE controls SET sprs_score = 1
                 WHERE framework = 'CMMC'
                 AND (code LIKE '%3.7.3' OR code LIKE '%3.7.6' OR code LIKE '%3.9.2')"
            );

            Session::flash('success', 'SPRS scores updated successfully for both NIST 800-171 and CMMC! High-risk controls set to 5 points, low-risk to 1 point, others to 3 points.');
            AuditLogger::log('update_sprs_scores', 'controls', null, null, $request->ip());

        } catch (\Exception $e) {
            Session::flash('error', 'Failed to update SPRS scores: ' . $e->getMessage());
        }

        return Response::redirect($request->baseUrl() . '/admin/import');
    }

    public function updateCategories(Request $request): Response
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
            // NIST 800-171 Categories (based on numeric code like 3.1.x)
            $this->db->query("UPDATE controls SET category = 'Access Control' WHERE framework = 'NIST800171' AND code LIKE '3.1.%'");
            $this->db->query("UPDATE controls SET category = 'Awareness and Training' WHERE framework = 'NIST800171' AND code LIKE '3.2.%'");
            $this->db->query("UPDATE controls SET category = 'Audit and Accountability' WHERE framework = 'NIST800171' AND code LIKE '3.3.%'");
            $this->db->query("UPDATE controls SET category = 'Configuration Management' WHERE framework = 'NIST800171' AND code LIKE '3.4.%'");
            $this->db->query("UPDATE controls SET category = 'Identification and Authentication' WHERE framework = 'NIST800171' AND code LIKE '3.5.%'");
            $this->db->query("UPDATE controls SET category = 'Incident Response' WHERE framework = 'NIST800171' AND code LIKE '3.6.%'");
            $this->db->query("UPDATE controls SET category = 'Maintenance' WHERE framework = 'NIST800171' AND code LIKE '3.7.%'");
            $this->db->query("UPDATE controls SET category = 'Media Protection' WHERE framework = 'NIST800171' AND code LIKE '3.8.%'");
            $this->db->query("UPDATE controls SET category = 'Personnel Security' WHERE framework = 'NIST800171' AND code LIKE '3.9.%'");
            $this->db->query("UPDATE controls SET category = 'Physical Protection' WHERE framework = 'NIST800171' AND code LIKE '3.10.%'");
            $this->db->query("UPDATE controls SET category = 'Risk Assessment' WHERE framework = 'NIST800171' AND code LIKE '3.11.%'");
            $this->db->query("UPDATE controls SET category = 'Security Assessment' WHERE framework = 'NIST800171' AND code LIKE '3.12.%'");
            $this->db->query("UPDATE controls SET category = 'System and Communications Protection' WHERE framework = 'NIST800171' AND code LIKE '3.13.%'");
            $this->db->query("UPDATE controls SET category = 'System and Information Integrity' WHERE framework = 'NIST800171' AND code LIKE '3.14.%'");

            // CMMC Categories (based on prefix like AC.%, MP.%, etc.)
            $this->db->query("UPDATE controls SET category = 'Access Control' WHERE framework = 'CMMC' AND code LIKE 'AC.%'");
            $this->db->query("UPDATE controls SET category = 'Awareness and Training' WHERE framework = 'CMMC' AND code LIKE 'AT.%'");
            $this->db->query("UPDATE controls SET category = 'Audit and Accountability' WHERE framework = 'CMMC' AND code LIKE 'AU.%'");
            $this->db->query("UPDATE controls SET category = 'Configuration Management' WHERE framework = 'CMMC' AND code LIKE 'CM.%'");
            $this->db->query("UPDATE controls SET category = 'Identification and Authentication' WHERE framework = 'CMMC' AND code LIKE 'IA.%'");
            $this->db->query("UPDATE controls SET category = 'Incident Response' WHERE framework = 'CMMC' AND code LIKE 'IR.%'");
            $this->db->query("UPDATE controls SET category = 'Maintenance' WHERE framework = 'CMMC' AND code LIKE 'MA.%'");
            $this->db->query("UPDATE controls SET category = 'Media Protection' WHERE framework = 'CMMC' AND code LIKE 'MP.%'");
            $this->db->query("UPDATE controls SET category = 'Personnel Security' WHERE framework = 'CMMC' AND code LIKE 'PS.%'");
            $this->db->query("UPDATE controls SET category = 'Physical Protection' WHERE framework = 'CMMC' AND code LIKE 'PE.%'");
            $this->db->query("UPDATE controls SET category = 'Risk Assessment' WHERE framework = 'CMMC' AND code LIKE 'RA.%'");
            $this->db->query("UPDATE controls SET category = 'Security Assessment' WHERE framework = 'CMMC' AND code LIKE 'CA.%'");
            $this->db->query("UPDATE controls SET category = 'System and Communications Protection' WHERE framework = 'CMMC' AND code LIKE 'SC.%'");
            $this->db->query("UPDATE controls SET category = 'System and Information Integrity' WHERE framework = 'CMMC' AND code LIKE 'SI.%'");

            // Set remaining to General
            $this->db->query("UPDATE controls SET category = 'General' WHERE category IS NULL");

            Session::flash('success', 'Control categories updated successfully for both NIST 800-171 and CMMC!');
            AuditLogger::log('update_categories', 'controls', null, null, $request->ip());

        } catch (\Exception $e) {
            Session::flash('error', 'Failed to update categories: ' . $e->getMessage());
        }

        return Response::redirect($request->baseUrl() . '/admin/import');
    }
}
