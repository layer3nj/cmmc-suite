<?php
/**
 * Migration: Add integration tracking fields to clients table
 * Tracks which integration source imported each client and their external ID
 */

return [
    'mysql' => "
        SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME='clients' AND COLUMN_NAME='external_id');
        SET @sqlstmt := IF(@exist = 0, 'ALTER TABLE clients ADD COLUMN external_id VARCHAR(255) AFTER id', 'SELECT ''Column external_id already exists''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;

        SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME='clients' AND COLUMN_NAME='integration_source');
        SET @sqlstmt := IF(@exist = 0, 'ALTER TABLE clients ADD COLUMN integration_source VARCHAR(50) AFTER external_id', 'SELECT ''Column integration_source already exists''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;

        SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME='clients' AND COLUMN_NAME='contact_name');
        SET @sqlstmt := IF(@exist = 0, 'ALTER TABLE clients ADD COLUMN contact_name VARCHAR(255) AFTER integration_source', 'SELECT ''Column contact_name already exists''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;

        SET @exist := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_NAME='clients' AND INDEX_NAME='idx_external_id');
        SET @sqlstmt := IF(@exist = 0, 'CREATE INDEX idx_external_id ON clients(external_id)', 'SELECT ''Index idx_external_id already exists''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;

        SET @exist := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_NAME='clients' AND INDEX_NAME='idx_integration_source');
        SET @sqlstmt := IF(@exist = 0, 'CREATE INDEX idx_integration_source ON clients(integration_source)', 'SELECT ''Index idx_integration_source already exists''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;
    ",

    'pgsql' => "
        ALTER TABLE clients
        ADD COLUMN IF NOT EXISTS external_id VARCHAR(255),
        ADD COLUMN IF NOT EXISTS integration_source VARCHAR(50),
        ADD COLUMN IF NOT EXISTS contact_name VARCHAR(255);

        CREATE INDEX IF NOT EXISTS idx_clients_external_id ON clients(external_id);
        CREATE INDEX IF NOT EXISTS idx_clients_integration_source ON clients(integration_source);
    "
];

