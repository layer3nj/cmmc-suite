<?php

namespace App\Core;

/**
 * Database Migration Manager
 */
class Migration
{
    private Database $db;
    private string $driver;
    private string $migrationsPath;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->driver = $db->getDriver();
        $this->migrationsPath = BASE_PATH . '/database/migrations';
    }

    public function run(): array
    {
        $results = [];

        // Create migrations table if it doesn't exist
        $this->createMigrationsTable();

        // Get all migration files
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');

            // Check if already run
            if ($this->hasRun($migrationName)) {
                $results[] = ['migration' => $migrationName, 'status' => 'already_run'];
                continue;
            }

            try {
                // Run the migration
                $this->runMigration($file);

                // Record it
                $this->recordMigration($migrationName);

                $results[] = ['migration' => $migrationName, 'status' => 'success'];
            } catch (\Exception $e) {
                $results[] = [
                    'migration' => $migrationName,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                error_log("Migration failed: $migrationName - " . $e->getMessage());
                throw $e; // Stop on first failure
            }
        }

        return $results;
    }

    private function createMigrationsTable(): void
    {
        if ($this->driver === 'mysql') {
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY(migration)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        } else { // pgsql
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
        }

        $this->db->query($sql);
    }

    private function hasRun(string $migration): bool
    {
        $result = $this->db->fetchOne(
            'SELECT * FROM migrations WHERE migration = ?',
            [$migration]
        );

        return $result !== null;
    }

    private function runMigration(string $file): void
    {
        $migration = require $file;

        if (!is_array($migration)) {
            throw new \Exception("Migration file must return an array");
        }

        $sql = $migration[$this->driver] ?? null;

        if (!$sql) {
            throw new \Exception("No SQL found for driver: $this->driver");
        }

        // Split into individual statements if multiple
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        // Start transaction
        $transactionStarted = $this->db->beginTransaction();

        try {
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $this->db->query($statement);
                }
            }

            // Only commit if transaction was started
            if ($transactionStarted) {
                $this->db->commit();
            }
        } catch (\Exception $e) {
            // Only rollback if transaction is active
            if ($transactionStarted && $this->db->inTransaction()) {
                $this->db->rollback();
            }
            throw $e;
        }
    }

    private function recordMigration(string $migration): void
    {
        $this->db->insert('migrations', [
            'migration' => $migration,
        ]);
    }

    public function reset(): void
    {
        // Get all tables
        if ($this->driver === 'mysql') {
            $tables = $this->db->fetchAll('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = array_values($table)[0];
                $this->db->query("DROP TABLE IF EXISTS `$tableName`");
            }
        } else { // pgsql
            $tables = $this->db->fetchAll(
                "SELECT tablename FROM pg_tables WHERE schemaname = 'public'"
            );
            foreach ($tables as $table) {
                $tableName = $table['tablename'];
                $this->db->query("DROP TABLE IF EXISTS \"$tableName\" CASCADE");
            }
        }
    }
}
