<?php

/**
 * Migration: Create policies table for boilerplate policy templates
 *
 * This table stores reusable policy templates that apply across multiple
 * compliance frameworks. These serve as starting points/templates for clients.
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS policies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL COMMENT 'e.g., Access Control, Incident Response, Data Protection',
            frameworks TEXT COMMENT 'Comma-separated list of applicable frameworks',
            description TEXT,
            content LONGTEXT NOT NULL COMMENT 'The policy content/template',
            version VARCHAR(50) DEFAULT '1.0',
            last_reviewed_date DATE NULL,
            is_active BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    'pgsql' => "
        CREATE TABLE IF NOT EXISTS policies (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            frameworks TEXT,
            description TEXT,
            content TEXT NOT NULL,
            version VARCHAR(50) DEFAULT '1.0',
            last_reviewed_date DATE NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        COMMENT ON COLUMN policies.category IS 'e.g., Access Control, Incident Response, Data Protection';
        COMMENT ON COLUMN policies.frameworks IS 'Comma-separated list of applicable frameworks';
        COMMENT ON COLUMN policies.content IS 'The policy content/template';

        CREATE INDEX IF NOT EXISTS idx_policies_category ON policies(category);
        CREATE INDEX IF NOT EXISTS idx_policies_active ON policies(is_active);
    "
];
