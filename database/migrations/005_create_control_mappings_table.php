<?php

/**
 * Create control_mappings table for linking controls across frameworks
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS control_mappings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            source_framework ENUM('CMMC', 'NIST800171', 'STIG') NOT NULL,
            source_code VARCHAR(100) NOT NULL,
            target_framework ENUM('CMMC', 'NIST800171', 'STIG') NOT NULL,
            target_code VARCHAR(100) NOT NULL,
            relation_type VARCHAR(50) DEFAULT 'maps_to',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_source (source_framework, source_code),
            INDEX idx_target (target_framework, target_code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS control_mappings (
            id SERIAL PRIMARY KEY,
            source_framework control_framework NOT NULL,
            source_code VARCHAR(100) NOT NULL,
            target_framework control_framework NOT NULL,
            target_code VARCHAR(100) NOT NULL,
            relation_type VARCHAR(50) DEFAULT 'maps_to',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE INDEX IF NOT EXISTS idx_mappings_source ON control_mappings(source_framework, source_code);
        CREATE INDEX IF NOT EXISTS idx_mappings_target ON control_mappings(target_framework, target_code);
    "
];
