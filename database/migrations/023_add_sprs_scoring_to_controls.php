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

        -- Update NIST 800-171 and CMMC controls to have default SPRS score of 3 (medium risk)
        UPDATE controls
        SET sprs_score = 3, partial_credit = 0
        WHERE framework = 'NIST800171' AND sprs_score IS NULL;

        UPDATE controls
        SET sprs_score = 3, partial_credit = 0
        WHERE framework = 'CMMC' AND sprs_score IS NULL;

        -- HIGH-RISK CONTROLS (5 points) - Critical security functions
        -- Access Control - Privileged functions and remote access
        UPDATE controls SET sprs_score = 5
        WHERE framework = 'NIST800171' AND code IN ('3.1.5', '3.1.6', '3.1.7');

        UPDATE controls SET sprs_score = 5
        WHERE framework = 'CMMC' AND code IN ('3.1.5', '3.1.6', '3.1.7');

        -- Identification & Authentication - Multi-factor authentication
        UPDATE controls SET sprs_score = 5
        WHERE framework = 'NIST800171' AND code IN ('3.5.3', '3.5.4');

        UPDATE controls SET sprs_score = 5
        WHERE framework = 'CMMC' AND code IN ('3.5.3', '3.5.4');

        -- Incident Response - Detection and response
        UPDATE controls SET sprs_score = 5
        WHERE framework = 'NIST800171' AND code IN ('3.6.1', '3.6.2');

        UPDATE controls SET sprs_score = 5
        WHERE framework = 'CMMC' AND code IN ('3.6.1', '3.6.2');

        -- System and Communications Protection - Encryption
        UPDATE controls SET sprs_score = 5
        WHERE framework = 'NIST800171' AND code IN ('3.13.8', '3.13.11', '3.13.16');

        UPDATE controls SET sprs_score = 5
        WHERE framework = 'CMMC' AND code IN ('3.13.8', '3.13.11', '3.13.16');

        -- LOW-RISK CONTROLS (1 point) - Awareness, training, procedural
        -- Awareness and Training
        UPDATE controls SET sprs_score = 1
        WHERE framework = 'NIST800171' AND code LIKE '3.2.%';

        UPDATE controls SET sprs_score = 1
        WHERE framework = 'CMMC' AND code LIKE '3.2.%';

        -- Some maintenance requirements
        UPDATE controls SET sprs_score = 1
        WHERE framework = 'NIST800171' AND code IN ('3.7.3', '3.7.6');

        UPDATE controls SET sprs_score = 1
        WHERE framework = 'CMMC' AND code IN ('3.7.3', '3.7.6');

        -- Some personnel security requirements
        UPDATE controls SET sprs_score = 1
        WHERE framework = 'NIST800171' AND code IN ('3.9.2');

        UPDATE controls SET sprs_score = 1
        WHERE framework = 'CMMC' AND code IN ('3.9.2');

        -- Partial credit controls (based on NIST 800-171A assessment procedures)
        UPDATE controls
        SET partial_credit = 1
        WHERE framework = 'NIST800171'
        AND code IN ('3.1.1', '3.1.2', '3.4.1', '3.4.2', '3.5.1', '3.5.2', '3.13.1', '3.13.2');

        UPDATE controls
        SET partial_credit = 1
        WHERE framework = 'CMMC'
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

        -- HIGH-RISK CONTROLS (5 points)
        UPDATE controls SET sprs_score = 5
        WHERE framework IN ('NIST800171', 'CMMC')
        AND code IN ('3.1.5', '3.1.6', '3.1.7', '3.5.3', '3.5.4', '3.6.1', '3.6.2', '3.13.8', '3.13.11', '3.13.16');

        -- LOW-RISK CONTROLS (1 point)
        UPDATE controls SET sprs_score = 1
        WHERE framework IN ('NIST800171', 'CMMC')
        AND (code LIKE '3.2.%' OR code IN ('3.7.3', '3.7.6', '3.9.2'));

        UPDATE controls
        SET partial_credit = TRUE
        WHERE framework IN ('NIST800171', 'CMMC')
        AND code IN ('3.1.1', '3.1.2', '3.4.1', '3.4.2', '3.5.1', '3.5.2', '3.13.1', '3.13.2');
    "
];
