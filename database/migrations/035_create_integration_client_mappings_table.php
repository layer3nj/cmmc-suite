<?php
/**
 * Migration: Create integration_client_mappings table
 * Stores mapping between external clients (Autotask/ITGlue) and OneComply clients
 * Allows selective import and linking of clients across systems
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS integration_client_mappings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            provider ENUM('autotask', 'itglue') NOT NULL,
            external_id VARCHAR(255) NOT NULL,
            external_name VARCHAR(500) NOT NULL,
            external_data JSON,
            onecomply_client_id INT NULL,
            mapping_action ENUM('ignore', 'map_existing', 'create_new') DEFAULT 'ignore',
            autotask_company_id VARCHAR(255) NULL,
            itglue_organization_id VARCHAR(255) NULL,
            synced TINYINT(1) DEFAULT 0,
            synced_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_provider_external (provider, external_id),
            INDEX idx_provider (provider),
            INDEX idx_onecomply_client (onecomply_client_id),
            INDEX idx_mapping_action (mapping_action),
            INDEX idx_autotask_company_id (autotask_company_id),
            INDEX idx_itglue_organization_id (itglue_organization_id),
            FOREIGN KEY (onecomply_client_id) REFERENCES clients(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        DO $$ BEGIN
            CREATE TYPE mapping_action_type AS ENUM ('ignore', 'map_existing', 'create_new');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        DO $$ BEGIN
            CREATE TYPE provider_type AS ENUM ('autotask', 'itglue');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        CREATE TABLE IF NOT EXISTS integration_client_mappings (
            id SERIAL PRIMARY KEY,
            provider provider_type NOT NULL,
            external_id VARCHAR(255) NOT NULL,
            external_name VARCHAR(500) NOT NULL,
            external_data JSONB,
            onecomply_client_id INT NULL,
            mapping_action mapping_action_type DEFAULT 'ignore',
            autotask_company_id VARCHAR(255) NULL,
            itglue_organization_id VARCHAR(255) NULL,
            synced BOOLEAN DEFAULT FALSE,
            synced_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT unique_provider_external UNIQUE (provider, external_id),
            CONSTRAINT fk_onecomply_client FOREIGN KEY (onecomply_client_id) REFERENCES clients(id) ON DELETE SET NULL
        );

        CREATE INDEX IF NOT EXISTS idx_icm_provider ON integration_client_mappings(provider);
        CREATE INDEX IF NOT EXISTS idx_icm_onecomply_client ON integration_client_mappings(onecomply_client_id);
        CREATE INDEX IF NOT EXISTS idx_icm_mapping_action ON integration_client_mappings(mapping_action);
        CREATE INDEX IF NOT EXISTS idx_icm_autotask_company_id ON integration_client_mappings(autotask_company_id);
        CREATE INDEX IF NOT EXISTS idx_icm_itglue_organization_id ON integration_client_mappings(itglue_organization_id);
    "
];
