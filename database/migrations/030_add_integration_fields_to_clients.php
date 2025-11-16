<?php
/**
 * Migration: Add integration tracking fields to clients table
 * Tracks which integration source imported each client and their external ID
 */

return [
    'mysql' => "
        ALTER TABLE clients
        ADD COLUMN external_id VARCHAR(255) AFTER id;

        ALTER TABLE clients
        ADD COLUMN integration_source VARCHAR(50) AFTER external_id;

        ALTER TABLE clients
        ADD COLUMN contact_name VARCHAR(255) AFTER integration_source;

        CREATE INDEX idx_external_id ON clients(external_id);
        CREATE INDEX idx_integration_source ON clients(integration_source);
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

