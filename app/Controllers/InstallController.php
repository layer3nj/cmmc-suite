<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Config;
use App\Core\Database;
use App\Core\Migration;
use App\Core\Seeder;

/**
 * Installation Wizard Controller
 */
class InstallController
{
    public function index(Request $request): Response
    {
        // Check if already installed
        if (file_exists(CONFIG_PATH . '/.env.php')) {
            return Response::redirect($request->baseUrl() . '/');
        }

        $content = View::render('install/index');
        return new Response($content);
    }

    public function check(Request $request): Response
    {
        $checks = [
            'php_version' => [
                'name' => 'PHP Version >= 8.1',
                'passed' => version_compare(PHP_VERSION, '8.1.0', '>='),
                'value' => PHP_VERSION
            ],
            'pdo' => [
                'name' => 'PDO Extension',
                'passed' => extension_loaded('pdo'),
                'value' => extension_loaded('pdo') ? 'Installed' : 'Missing'
            ],
            'pdo_mysql' => [
                'name' => 'PDO MySQL Driver',
                'passed' => extension_loaded('pdo_mysql'),
                'value' => extension_loaded('pdo_mysql') ? 'Installed' : 'Missing'
            ],
            'pdo_pgsql' => [
                'name' => 'PDO PostgreSQL Driver',
                'passed' => extension_loaded('pdo_pgsql'),
                'value' => extension_loaded('pdo_pgsql') ? 'Installed' : 'Missing (optional)'
            ],
            'openssl' => [
                'name' => 'OpenSSL Extension',
                'passed' => extension_loaded('openssl'),
                'value' => extension_loaded('openssl') ? 'Installed' : 'Missing'
            ],
            'mbstring' => [
                'name' => 'Mbstring Extension',
                'passed' => extension_loaded('mbstring'),
                'value' => extension_loaded('mbstring') ? 'Installed' : 'Missing'
            ],
            'curl' => [
                'name' => 'cURL Extension',
                'passed' => extension_loaded('curl'),
                'value' => extension_loaded('curl') ? 'Installed' : 'Missing'
            ],
            'gd' => [
                'name' => 'GD Extension',
                'passed' => extension_loaded('gd'),
                'value' => extension_loaded('gd') ? 'Installed' : 'Missing'
            ],
            'zip' => [
                'name' => 'Zip Extension',
                'passed' => extension_loaded('zip'),
                'value' => extension_loaded('zip') ? 'Installed' : 'Missing'
            ],
            'config_writable' => [
                'name' => 'Config Directory Writable',
                'passed' => is_writable(CONFIG_PATH),
                'value' => is_writable(CONFIG_PATH) ? 'Writable' : 'Not writable'
            ],
            'storage_writable' => [
                'name' => 'Storage Directory Writable',
                'passed' => is_writable(STORAGE_PATH),
                'value' => is_writable(STORAGE_PATH) ? 'Writable' : 'Not writable'
            ],
        ];

        $allPassed = true;
        foreach ($checks as $key => $check) {
            // pdo_pgsql is optional
            if ($key === 'pdo_pgsql') {
                continue;
            }
            if (!$check['passed']) {
                $allPassed = false;
                break;
            }
        }

        return Response::json([
            'success' => $allPassed,
            'checks' => $checks
        ]);
    }

    public function database(Request $request): Response
    {
        // Clean output buffer to prevent any HTML/warnings from being sent
        if (ob_get_level()) ob_clean();

        $driver = $request->post('driver');
        $host = $request->post('host');
        $port = $request->post('port');
        $database = $request->post('database');
        $username = $request->post('username');
        $password = $request->post('password');

        // Validate inputs
        if (empty($driver) || empty($host) || empty($database) || empty($username)) {
            return Response::json([
                'success' => false,
                'message' => 'All fields except password are required'
            ]);
        }

        // Test connection
        $connected = Database::testConnection($driver, $host, $port, $database, $username, $password);

        if (!$connected) {
            return Response::json([
                'success' => false,
                'message' => 'Database connection failed. Please check your credentials and try again.'
            ]);
        }

        // Save to session temporarily (suppress any session warnings)
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['install_db'] = [
            'driver' => $driver,
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8mb4'
        ];

        return Response::json([
            'success' => true,
            'message' => 'Database connection successful'
        ]);
    }

    public function migrate(Request $request): Response
    {
        // Clean output buffer to prevent any HTML/warnings from being sent
        if (ob_get_level()) ob_clean();

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!isset($_SESSION['install_db'])) {
            return Response::json([
                'success' => false,
                'message' => 'Database configuration not found'
            ]);
        }

        try {
            // Create temporary config
            $config = new Config(false);
            $config->set('database', $_SESSION['install_db']);

            // Create database connection
            $db = new Database($config);

            // Run migrations
            $migration = new Migration($db);
            $results = $migration->run();

            // Run seeders
            $seeder = new Seeder($db);
            $seedResults = $seeder->run();

            return Response::json([
                'success' => true,
                'message' => 'Database setup completed successfully',
                'migrations' => $results,
                'seeders' => $seedResults
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage()
            ]);
        }
    }

    public function saml(Request $request): Response
    {
        // Clean output buffer to prevent any HTML/warnings from being sent
        if (ob_get_level()) ob_clean();

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $enabled = $request->post('saml_enabled') === 'true';
        $entityId = $request->post('entity_id');
        $acsUrl = $request->post('acs_url');
        $idpMetadataUrl = $request->post('idp_metadata_url');
        $idpMetadataXml = $request->post('idp_metadata_xml');

        $_SESSION['install_saml'] = [
            'enabled' => $enabled,
            'entity_id' => $entityId,
            'acs_url' => $acsUrl,
            'idp_metadata_url' => $idpMetadataUrl,
            'idp_metadata_xml' => $idpMetadataXml,
        ];

        return Response::json([
            'success' => true,
            'message' => 'SAML configuration saved'
        ]);
    }

    public function admin(Request $request): Response
    {
        // Clean output buffer to prevent any HTML/warnings from being sent
        if (ob_get_level()) ob_clean();

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!isset($_SESSION['install_db'])) {
            return Response::json([
                'success' => false,
                'message' => 'Database configuration not found'
            ]);
        }

        $email = $request->post('email');
        $displayName = $request->post('display_name');
        $password = $request->post('password');

        if (empty($email) || empty($displayName) || empty($password)) {
            return Response::json([
                'success' => false,
                'message' => 'All fields are required'
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid email address'
            ]);
        }

        if (strlen($password) < 8) {
            return Response::json([
                'success' => false,
                'message' => 'Password must be at least 8 characters'
            ]);
        }

        try {
            // Create temporary config
            $config = new Config(false);
            $config->set('database', $_SESSION['install_db']);

            // Create database connection
            $db = new Database($config);

            // Create admin user
            $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

            $db->insert('users', [
                'email' => $email,
                'display_name' => $displayName,
                'role' => 'admin',
                'password_hash' => $passwordHash,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $_SESSION['install_admin'] = [
                'email' => $email,
                'display_name' => $displayName
            ];

            return Response::json([
                'success' => true,
                'message' => 'Admin user created successfully'
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to create admin user: ' . $e->getMessage()
            ]);
        }
    }

    public function finalize(Request $request): Response
    {
        // Clean output buffer to prevent any HTML/warnings from being sent
        if (ob_get_level()) ob_clean();

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!isset($_SESSION['install_db'])) {
            return Response::json([
                'success' => false,
                'message' => 'Installation not complete'
            ]);
        }

        try {
            // Build final configuration
            $config = [
                'app' => [
                    'name' => 'CMMC Compliance Suite',
                    'version' => '1.0.0',
                    'timezone' => 'UTC',
                    'debug' => false,
                    'base_url' => $request->baseUrl(),
                ],
                'database' => $_SESSION['install_db'],
                'saml' => $_SESSION['install_saml'] ?? [
                    'enabled' => false,
                ],
                'session' => [
                    'lifetime' => 7200,
                    'idle_timeout' => 1800,
                ],
                'security' => [
                    'csrf_enabled' => true,
                    'rate_limit_enabled' => true,
                    'force_https' => false,
                ],
                'upload' => [
                    'max_size' => 10485760,
                    'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'png', 'jpg', 'jpeg'],
                ],
            ];

            // Write configuration file
            $envFile = CONFIG_PATH . '/.env.php';
            $content = "<?php\n\n// Auto-generated configuration file\n// Generated on " . date('Y-m-d H:i:s') . "\n\nreturn " . var_export($config, true) . ";\n";

            if (file_put_contents($envFile, $content) === false) {
                throw new \Exception('Failed to write configuration file');
            }

            // Clear session data
            unset($_SESSION['install_db']);
            unset($_SESSION['install_saml']);
            unset($_SESSION['install_admin']);

            return Response::json([
                'success' => true,
                'message' => 'Installation completed successfully',
                'redirect' => $request->baseUrl() . '/login'
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage()
            ]);
        }
    }
}
