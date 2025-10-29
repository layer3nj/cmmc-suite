<?php
/**
 * Database Column Checker
 * Run this to see what columns exist in the assessments and documents tables
 */

// Load config directly
$configFile = __DIR__ . '/config/database.php';
if (!file_exists($configFile)) {
    die("Config file not found: $configFile\n");
}
$config = require $configFile;

// Connect directly with PDO
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
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

    echo "\nDone!\n";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}
