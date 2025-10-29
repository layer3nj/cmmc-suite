<?php

/**
 * Expand document framework support
 *
 * Changes linked_framework from ENUM to VARCHAR to support additional frameworks
 * beyond just CMMC, NIST800171, and STIG (e.g., HIPAA, FTC, PCI-DSS, SOC2, ISO27001)
 */

return [
    'mysql' => "
        ALTER TABLE documents
        MODIFY COLUMN linked_framework VARCHAR(50) NULL;
    ",
    'pgsql' => "
        -- Drop the enum constraint and change to VARCHAR
        ALTER TABLE documents
        ALTER COLUMN linked_framework TYPE VARCHAR(50);
    "
];
