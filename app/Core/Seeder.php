<?php

namespace App\Core;

/**
 * Database Seeder
 */
class Seeder
{
    private Database $db;
    private string $seedsPath;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->seedsPath = BASE_PATH . '/database/seeds';
    }

    public function run(): array
    {
        $results = [];

        // Get all seed files
        $files = glob($this->seedsPath . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $seederName = basename($file, '.php');

            try {
                $this->runSeeder($file);
                $results[] = ['seeder' => $seederName, 'status' => 'success'];
            } catch (\Exception $e) {
                $results[] = [
                    'seeder' => $seederName,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                error_log("Seeder failed: $seederName - " . $e->getMessage());
                throw $e;
            }
        }

        return $results;
    }

    private function runSeeder(string $file): void
    {
        $seeder = require $file;

        if (!is_callable($seeder)) {
            throw new \Exception("Seeder file must return a callable");
        }

        // Execute seeder (no transaction needed since seeders are idempotent)
        try {
            $seeder($this->db);
        } catch (\Exception $e) {
            error_log("Seeder execution failed: " . $e->getMessage());
            throw $e;
        }
    }
}
