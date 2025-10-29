<?php

/**
 * Migration: Add Framework and Assessment Fields
 *
 * Adds missing columns to assessments table:
 * - framework: The compliance framework being assessed (CMMC, NIST, HIPAA, etc.)
 * - assessment_type: Type of assessment (self, external, etc.)
 * - scope: Description of assessment scope
 * - target_level: Target compliance level (for frameworks that have levels)
 */

return [
    'mysql' => "
        ALTER TABLE assessments ADD COLUMN framework VARCHAR(50) NULL AFTER customer_id;
        ALTER TABLE assessments ADD COLUMN assessment_type VARCHAR(50) NULL AFTER framework;
        ALTER TABLE assessments ADD COLUMN scope TEXT NULL AFTER assessment_type;
        ALTER TABLE assessments ADD COLUMN target_level INT NULL AFTER scope;
    ",
    'pgsql' => "
        ALTER TABLE assessments ADD COLUMN framework VARCHAR(50) NULL;
        ALTER TABLE assessments ADD COLUMN assessment_type VARCHAR(50) NULL;
        ALTER TABLE assessments ADD COLUMN scope TEXT NULL;
        ALTER TABLE assessments ADD COLUMN target_level INT NULL;
    "
];
