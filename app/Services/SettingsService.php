<?php

namespace App\Services;

use App\Core\Database;

/**
 * Settings Service
 *
 * Provides centralized access to application settings
 */
class SettingsService
{
    private Database $db;
    private static ?array $cache = null;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get all settings as key-value array
     */
    public function getAll(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $settingsRows = $this->db->fetchAll("SELECT k, v FROM settings ORDER BY k");
        $settings = [];

        foreach ($settingsRows as $row) {
            $settings[$row['k']] = $row['v'];
        }

        // Set defaults
        $defaults = [
            'site_name' => 'CMMC Compliance Suite',
            'primary_color' => '#667eea',
            'secondary_color' => '#764ba2',
            'timezone' => 'UTC',
        ];

        self::$cache = array_merge($defaults, $settings);
        return self::$cache;
    }

    /**
     * Get a specific setting value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->getAll();
        return $settings[$key] ?? $default;
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        self::$cache = null;
    }
}
