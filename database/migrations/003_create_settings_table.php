<?php

/**
 * Create settings table for key-value configuration
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS settings (
            k VARCHAR(255) PRIMARY KEY,
            v TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS settings (
            k VARCHAR(255) PRIMARY KEY,
            v TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    "
];
