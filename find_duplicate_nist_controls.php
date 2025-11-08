<?php
/**
 * Find duplicate NIST 800-171 controls
 */

// Define base paths
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VENDOR_PATH', BASE_PATH . '/vendor');

// Autoloader
require_once BASE_PATH . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();

try {
    // Initialize config and database
    $config = new App\Core\Config(true);
    $db = new App\Core\Database($config);

    echo "=== NIST 800-171 Duplicate Control Finder ===\n\n";

    // Count total NIST 800-171 controls
    $totalCount = $db->fetchColumn(
        "SELECT COUNT(*) FROM controls WHERE framework = 'NIST800171'"
    );
    echo "Total NIST 800-171 controls: $totalCount\n";
    echo "Expected: 110 controls\n";
    echo "Difference: " . ($totalCount - 110) . "\n\n";

    // Find duplicate codes
    echo "Finding duplicate control codes...\n";
    $duplicates = $db->fetchAll(
        "SELECT code, COUNT(*) as count
         FROM controls
         WHERE framework = 'NIST800171'
         GROUP BY code
         HAVING COUNT(*) > 1
         ORDER BY code"
    );

    if (empty($duplicates)) {
        echo "✓ No duplicate control codes found.\n\n";
    } else {
        echo "✗ Found duplicate control codes:\n";
        foreach ($duplicates as $dup) {
            echo "  - Code '{$dup['code']}' appears {$dup['count']} times\n";

            // Show the duplicates
            $instances = $db->fetchAll(
                "SELECT id, code, title, LEFT(description, 100) as description
                 FROM controls
                 WHERE framework = 'NIST800171' AND code = ?
                 ORDER BY id",
                [$dup['code']]
            );

            foreach ($instances as $inst) {
                echo "    ID: {$inst['id']} - {$inst['title']}\n";
                echo "    Description: {$inst['description']}...\n\n";
            }
        }
    }

    // Find controls with identical titles
    echo "\nFinding controls with identical titles...\n";
    $duplicateTitles = $db->fetchAll(
        "SELECT title, COUNT(*) as count
         FROM controls
         WHERE framework = 'NIST800171'
         GROUP BY title
         HAVING COUNT(*) > 1
         ORDER BY title"
    );

    if (empty($duplicateTitles)) {
        echo "✓ No duplicate titles found.\n";
    } else {
        echo "✗ Found duplicate titles:\n";
        foreach ($duplicateTitles as $dup) {
            echo "  - Title '{$dup['title']}' appears {$dup['count']} times\n";

            // Show the duplicates
            $instances = $db->fetchAll(
                "SELECT id, code, title
                 FROM controls
                 WHERE framework = 'NIST800171' AND title = ?
                 ORDER BY id",
                [$dup['title']]
            );

            foreach ($instances as $inst) {
                echo "    ID: {$inst['id']}, Code: {$inst['code']}\n";
            }
            echo "\n";
        }
    }

    // List all NIST 800-171 controls for verification
    echo "\n=== All NIST 800-171 Controls (sorted by code) ===\n";
    $allControls = $db->fetchAll(
        "SELECT id, code, LEFT(title, 60) as title
         FROM controls
         WHERE framework = 'NIST800171'
         ORDER BY code"
    );

    foreach ($allControls as $ctrl) {
        printf("%4d | %-10s | %s\n", $ctrl['id'], $ctrl['code'], $ctrl['title']);
    }

    echo "\n=== Summary ===\n";
    echo "Total controls: $totalCount\n";
    echo "Expected: 110\n";
    if ($totalCount == 110) {
        echo "✓ Count is correct!\n";
    } else {
        echo "✗ Count mismatch - found " . ($totalCount - 110) . " extra control(s)\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
