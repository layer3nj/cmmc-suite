<?php
/**
 * Manual Database Column Checker
 *
 * INSTRUCTIONS:
 * 1. Edit the database credentials below
 * 2. Run: php check_db_manual.php
 */

// ===== EDIT THESE DATABASE CREDENTIALS =====
$db_host = 'localhost';        // Usually 'localhost' or '127.0.0.1'
$db_name = 'your_database';    // Your database name
$db_user = 'your_username';    // Your database username
$db_pass = 'your_password';    // Your database password
// ===========================================

echo "Connecting to database...\n";
echo "Host: $db_host\n";
echo "Database: $db_name\n\n";

try {
    $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "✓ Connected successfully!\n\n";

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
    die("✗ Database error: " . $e->getMessage() . "\n");
}
