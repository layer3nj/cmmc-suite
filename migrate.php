#!/usr/bin/env php
<?php

/**
 * Simple migration runner CLI script
 */

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');

require_once APP_PATH . '/Core/Config.php';
require_once APP_PATH . '/Core/Database.php';

echo "Running database migrations...\n\n";

// Load config
$config = new App\Core\Config(true);

// Connect to database
try {
    $db = new App\Core\Database($config);
    echo "✓ Connected to database\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Get driver
$driver = $db->getDriver();
echo "Database driver: $driver\n\n";

// Run migrations
$migrationsDir = BASE_PATH . '/database/migrations';
$migrationFiles = glob($migrationsDir . '/*.php');
sort($migrationFiles);

$migrationsRun = 0;
$migrationsSkipped = 0;

foreach ($migrationFiles as $file) {
    $migrationName = basename($file);
    echo "Processing: $migrationName\n";

    $migration = require $file;

    if (isset($migration[$driver])) {
        $sql = $migration[$driver];

        // Remove SQL comments
        $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);

        // Split on semicolons
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^\s*$/', $stmt);
            }
        );

        $statementsRun = 0;
        $statementsSkipped = 0;

        foreach ($statements as $statement) {
            try {
                $db->query($statement);
                $statementsRun++;
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();

                // Skip if already exists
                if (stripos($errorMsg, 'already exists') !== false ||
                    stripos($errorMsg, 'Duplicate column') !== false ||
                    stripos($errorMsg, 'Duplicate key') !== false ||
                    stripos($errorMsg, 'Multiple primary key') !== false) {
                    $statementsSkipped++;
                    continue;
                }

                echo "  ✗ Error: " . $errorMsg . "\n";
                exit(1);
            }
        }

        if ($statementsRun > 0) {
            echo "  ✓ Applied ($statementsRun statements)\n";
            $migrationsRun++;
        } elseif ($statementsSkipped > 0) {
            echo "  - Skipped (already applied)\n";
            $migrationsSkipped++;
        }
    } else {
        echo "  - No migration for driver: $driver\n";
    }
}

echo "\n";
echo "===========================================\n";
echo "Migrations complete!\n";
echo "Applied: $migrationsRun\n";
echo "Skipped: $migrationsSkipped\n";
echo "===========================================\n";
