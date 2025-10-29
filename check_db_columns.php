<?php
/**
 * Database Column Checker
 * Run this to see what columns exist in the assessments and documents tables
 */

require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Config.php';

use App\Core\Database;
use App\Core\Config;

$config = Config::get('database');
$db = new Database($config);

echo "=== ASSESSMENTS TABLE COLUMNS ===\n";
try {
    $columns = $db->fetchAll("DESCRIBE assessments");
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']}) " .
             ($col['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DOCUMENTS TABLE COLUMNS ===\n";
try {
    $columns = $db->fetchAll("DESCRIBE documents");
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']}) " .
             ($col['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
