<?php
/**
 * Database Column Checker
 * Run this to see what columns exist in the assessments and documents tables
 */

// Bootstrap the application to get config
define('BASE_PATH', __DIR__);
define('CONFIG_PATH', BASE_PATH . '/config');

$envFile = CONFIG_PATH . '/.env.php';
if (!file_exists($envFile)) {
    die("ERROR: Application not installed. Config file not found: $envFile\n\n" .
        "Please run the installer first by visiting http://your-server/install\n");
}

$config = require $envFile;

// Check if database config exists
if (!isset($config['database'])) {
    die("ERROR: Database configuration not found in .env.php\n");
}

$dbConfig = $config['database'];

// Connect directly with PDO
try {
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "=== ASSESSMENTS TABLE COLUMNS ===\n";
    $stmt = $pdo->query("DESCRIBE assessments");
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']}) " .
             ($col['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }

    echo "\n=== DOCUMENTS TABLE COLUMNS ===\n";
    $stmt = $pdo->query("DESCRIBE documents");
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']}) " .
             ($col['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }

    echo "\n=== DATABASE INFO ===\n";
    echo "Database: {$dbConfig['database']}\n";
    echo "Host: {$dbConfig['host']}\n";
    echo "\nDone!\n";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}
