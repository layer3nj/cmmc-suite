<?php
/**
 * CMMC Compliance Suite - Front Controller
 *
 * This is the main entry point for all requests.
 * No CLI required - this app runs entirely through the web server.
 */

// Display errors during development (disable in production via .env.php)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VENDOR_PATH', BASE_PATH . '/vendor');

// Autoloader
require_once BASE_PATH . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();

// Start session with security settings
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? '1' : '0');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.save_path', STORAGE_PATH . '/sessions');

// Create session directory if it doesn't exist
if (!is_dir(STORAGE_PATH . '/sessions')) {
    mkdir(STORAGE_PATH . '/sessions', 0770, true);
}

session_start();

// Check if installed
$envFile = CONFIG_PATH . '/.env.php';
$isInstalled = file_exists($envFile);

// Route to installer if not installed and not already on install page
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUri = rtrim($scriptName, '/');
$path = str_replace($baseUri, '', $requestUri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

if (!$isInstalled && !str_starts_with($path, 'install')) {
    header('Location: ' . $baseUri . '/install');
    exit;
}

// Bootstrap the application
try {
    $app = new App\Core\Application($isInstalled);
    $app->run();
} catch (Throwable $e) {
    // Log the error
    error_log('Application Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

    // Show user-friendly error
    http_response_code(500);
    if (file_exists(APP_PATH . '/Views/errors/500.php')) {
        require APP_PATH . '/Views/errors/500.php';
    } else {
        echo '<!DOCTYPE html><html><head><title>Application Error</title></head><body>';
        echo '<h1>Application Error</h1>';
        echo '<p>An unexpected error occurred. Please contact your administrator.</p>';
        if (ini_get('display_errors')) {
            echo '<pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
        }
        echo '</body></html>';
    }
}
