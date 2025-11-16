<?php

/**
 * Create integrations table for storing API credentials and sync state
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS integrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            provider VARCHAR(50) NOT NULL,
            api_url VARCHAR(500),
            api_key VARCHAR(500),
            api_secret VARCHAR(500),
            username VARCHAR(255),
            enabled TINYINT(1) DEFAULT 0,
            last_sync_at TIMESTAMP NULL,
            sync_status VARCHAR(50),
            sync_message TEXT,
            config JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_provider (provider),
            INDEX idx_provider (provider),
            INDEX idx_enabled (enabled)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS integrations (
            id SERIAL PRIMARY KEY,
            provider VARCHAR(50) NOT NULL UNIQUE,
            api_url VARCHAR(500),
            api_key VARCHAR(500),
            api_secret VARCHAR(500),
            username VARCHAR(255),
            enabled BOOLEAN DEFAULT FALSE,
            last_sync_at TIMESTAMP NULL,
            sync_status VARCHAR(50),
            sync_message TEXT,
            config JSONB,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE INDEX IF NOT EXISTS idx_integrations_provider ON integrations(provider);
        CREATE INDEX IF NOT EXISTS idx_integrations_enabled ON integrations(enabled);
    "
];
