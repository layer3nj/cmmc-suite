<?php

/**
 * Migration: Add category column to controls table
 *
 * Adds a category field to organize controls by their domain/family
 * (e.g., AC - Access Control, MP - Media Protection, etc.)
 */

return [
    'mysql' => "
        ALTER TABLE controls
        ADD COLUMN category VARCHAR(100) NULL COMMENT 'Control family/category (e.g., Access Control, Media Protection)';

        -- NIST 800-171 / CMMC Categories based on control code prefix
        UPDATE controls SET category = 'Access Control' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.1.%';
        UPDATE controls SET category = 'Awareness and Training' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.2.%';
        UPDATE controls SET category = 'Audit and Accountability' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.3.%';
        UPDATE controls SET category = 'Configuration Management' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.4.%';
        UPDATE controls SET category = 'Identification and Authentication' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.5.%';
        UPDATE controls SET category = 'Incident Response' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.6.%';
        UPDATE controls SET category = 'Maintenance' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.7.%';
        UPDATE controls SET category = 'Media Protection' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.8.%';
        UPDATE controls SET category = 'Personnel Security' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.9.%';
        UPDATE controls SET category = 'Physical Protection' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.10.%';
        UPDATE controls SET category = 'Risk Assessment' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.11.%';
        UPDATE controls SET category = 'Security Assessment' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.12.%';
        UPDATE controls SET category = 'System and Communications Protection' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.13.%';
        UPDATE controls SET category = 'System and Information Integrity' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.14.%';

        -- Other frameworks can have categories added later
        UPDATE controls SET category = 'General' WHERE category IS NULL;
    ",
    'pgsql' => "
        ALTER TABLE controls
        ADD COLUMN category VARCHAR(100) NULL;

        COMMENT ON COLUMN controls.category IS 'Control family/category (e.g., Access Control, Media Protection)';

        -- NIST 800-171 / CMMC Categories
        UPDATE controls SET category = 'Access Control' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.1.%';
        UPDATE controls SET category = 'Awareness and Training' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.2.%';
        UPDATE controls SET category = 'Audit and Accountability' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.3.%';
        UPDATE controls SET category = 'Configuration Management' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.4.%';
        UPDATE controls SET category = 'Identification and Authentication' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.5.%';
        UPDATE controls SET category = 'Incident Response' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.6.%';
        UPDATE controls SET category = 'Maintenance' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.7.%';
        UPDATE controls SET category = 'Media Protection' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.8.%';
        UPDATE controls SET category = 'Personnel Security' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.9.%';
        UPDATE controls SET category = 'Physical Protection' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.10.%';
        UPDATE controls SET category = 'Risk Assessment' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.11.%';
        UPDATE controls SET category = 'Security Assessment' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.12.%';
        UPDATE controls SET category = 'System and Communications Protection' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.13.%';
        UPDATE controls SET category = 'System and Information Integrity' WHERE framework IN ('NIST800171', 'CMMC') AND code LIKE '3.14.%';

        UPDATE controls SET category = 'General' WHERE category IS NULL;
    "
];
