<?php

/**
 * Expand controls table framework column to support additional frameworks
 *
 * Changes the framework column from ENUM('CMMC', 'NIST800171', 'STIG')
 * to VARCHAR(50) to support additional compliance frameworks:
 * - HIPAA
 * - FTC-SAFEGUARDS
 * - PCI-DSS
 * - SOC2
 * - ISO27001
 */

return [
    'mysql' => "
        ALTER TABLE controls
        MODIFY COLUMN framework VARCHAR(50) NOT NULL;
    ",
    'pgsql' => "
        -- Drop the old ENUM type constraint and use VARCHAR
        ALTER TABLE controls
        ALTER COLUMN framework TYPE VARCHAR(50);

        -- Drop the old ENUM type if it exists (PostgreSQL)
        -- This will be recreated if needed
        DROP TYPE IF EXISTS control_framework CASCADE;
    "
];
