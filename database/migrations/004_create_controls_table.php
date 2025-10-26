<?php

/**
 * Create controls table for CMMC, NIST 800-171, and STIG controls
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS controls (
            id INT AUTO_INCREMENT PRIMARY KEY,
            framework ENUM('CMMC', 'NIST800171', 'STIG') NOT NULL,
            code VARCHAR(100) NOT NULL,
            title VARCHAR(500) NOT NULL,
            description TEXT,
            ml_level TINYINT,
            parent_code VARCHAR(100),
            objective_text TEXT,
            stig_version VARCHAR(50),
            stig_severity ENUM('low', 'medium', 'high'),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_framework_code (framework, code),
            INDEX idx_framework (framework),
            INDEX idx_code (code),
            INDEX idx_ml_level (ml_level),
            INDEX idx_parent (parent_code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        DO $$ BEGIN
            CREATE TYPE control_framework AS ENUM ('CMMC', 'NIST800171', 'STIG');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        DO $$ BEGIN
            CREATE TYPE stig_severity AS ENUM ('low', 'medium', 'high');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        CREATE TABLE IF NOT EXISTS controls (
            id SERIAL PRIMARY KEY,
            framework control_framework NOT NULL,
            code VARCHAR(100) NOT NULL,
            title VARCHAR(500) NOT NULL,
            description TEXT,
            ml_level SMALLINT,
            parent_code VARCHAR(100),
            objective_text TEXT,
            stig_version VARCHAR(50),
            stig_severity stig_severity,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT unique_framework_code UNIQUE (framework, code)
        );

        CREATE INDEX IF NOT EXISTS idx_controls_framework ON controls(framework);
        CREATE INDEX IF NOT EXISTS idx_controls_code ON controls(code);
        CREATE INDEX IF NOT EXISTS idx_controls_ml_level ON controls(ml_level);
        CREATE INDEX IF NOT EXISTS idx_controls_parent ON controls(parent_code);
    "
];
