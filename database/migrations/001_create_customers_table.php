<?php

/**
 * Create customers table for multi-tenant support
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            autotask_company_id VARCHAR(100),
            itglue_organization_id VARCHAR(100),
            contact_email VARCHAR(255),
            contact_phone VARCHAR(50),
            address TEXT,
            logo_path VARCHAR(255),
            active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_active (active),
            INDEX idx_autotask (autotask_company_id),
            INDEX idx_itglue (itglue_organization_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS customers (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            autotask_company_id VARCHAR(100),
            itglue_organization_id VARCHAR(100),
            contact_email VARCHAR(255),
            contact_phone VARCHAR(50),
            address TEXT,
            logo_path VARCHAR(255),
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE INDEX IF NOT EXISTS idx_customers_active ON customers(active);
        CREATE INDEX IF NOT EXISTS idx_customers_autotask ON customers(autotask_company_id);
        CREATE INDEX IF NOT EXISTS idx_customers_itglue ON customers(itglue_organization_id);
    "
];
