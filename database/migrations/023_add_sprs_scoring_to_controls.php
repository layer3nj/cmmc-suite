<?php

/**
 * Migration: Add SPRS scoring columns to controls table
 *
 * Adds columns for CMMC 2.0 / NIST 800-171 SPRS scoring:
 * - sprs_score: The point value/weight for this control (default 3 points)
 * - partial_credit: Whether partial credit can be awarded for this control
 */

return [
    'mysql' => "
        ALTER TABLE controls
        ADD COLUMN sprs_score INT NULL COMMENT 'SPRS point value for this control (typically 1-5)',
        ADD COLUMN partial_credit BOOLEAN DEFAULT 0 COMMENT 'Whether partial credit can be awarded';

        -- Update NIST 800-171 controls to have default SPRS score of 3
        UPDATE controls
        SET sprs_score = 3, partial_credit = 0
        WHERE framework IN ('NIST800171', 'CMMC') AND sprs_score IS NULL;

        -- Some specific controls allow partial credit (based on NIST 800-171A assessment procedures)
        UPDATE controls
        SET partial_credit = 1
        WHERE framework IN ('NIST800171', 'CMMC')
        AND code IN ('3.1.1', '3.1.2', '3.4.1', '3.4.2', '3.5.1', '3.5.2', '3.13.1', '3.13.2');
    ",
    'pgsql' => "
        ALTER TABLE controls
        ADD COLUMN sprs_score INTEGER NULL,
        ADD COLUMN partial_credit BOOLEAN DEFAULT FALSE;

        COMMENT ON COLUMN controls.sprs_score IS 'SPRS point value for this control (typically 1-5)';
        COMMENT ON COLUMN controls.partial_credit IS 'Whether partial credit can be awarded';

        UPDATE controls
        SET sprs_score = 3, partial_credit = FALSE
        WHERE framework IN ('NIST800171', 'CMMC') AND sprs_score IS NULL;

        UPDATE controls
        SET partial_credit = TRUE
        WHERE framework IN ('NIST800171', 'CMMC')
        AND code IN ('3.1.1', '3.1.2', '3.4.1', '3.4.2', '3.5.1', '3.5.2', '3.13.1', '3.13.2');
    "
];
