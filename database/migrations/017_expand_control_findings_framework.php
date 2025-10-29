<?php

/**
 * Expand control_findings table control_framework column to support additional frameworks
 *
 * Changes the control_framework column from ENUM('CMMC', 'NIST800171', 'STIG')
 * to VARCHAR(50) to support additional compliance frameworks:
 * - HIPAA
 * - FTC-SAFEGUARDS
 * - PCI-DSS
 * - SOC2
 * - ISO27001
 */

return [
    'mysql' => "
        ALTER TABLE control_findings
        MODIFY COLUMN control_framework VARCHAR(50) NOT NULL;
    ",
    'pgsql' => "
        -- Drop the old ENUM type constraint and use VARCHAR
        ALTER TABLE control_findings
        ALTER COLUMN control_framework TYPE VARCHAR(50);
    "
];
