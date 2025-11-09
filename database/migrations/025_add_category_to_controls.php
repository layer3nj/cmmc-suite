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
        ADD COLUMN IF NOT EXISTS category VARCHAR(100) NULL COMMENT 'Control family/category (e.g., Access Control, Media Protection)';

        -- NIST 800-171 Categories (based on numeric code like 3.1.x)
        UPDATE controls SET category = 'Access Control' WHERE framework = 'NIST800171' AND code LIKE '3.1.%';
        UPDATE controls SET category = 'Awareness and Training' WHERE framework = 'NIST800171' AND code LIKE '3.2.%';
        UPDATE controls SET category = 'Audit and Accountability' WHERE framework = 'NIST800171' AND code LIKE '3.3.%';
        UPDATE controls SET category = 'Configuration Management' WHERE framework = 'NIST800171' AND code LIKE '3.4.%';
        UPDATE controls SET category = 'Identification and Authentication' WHERE framework = 'NIST800171' AND code LIKE '3.5.%';
        UPDATE controls SET category = 'Incident Response' WHERE framework = 'NIST800171' AND code LIKE '3.6.%';
        UPDATE controls SET category = 'Maintenance' WHERE framework = 'NIST800171' AND code LIKE '3.7.%';
        UPDATE controls SET category = 'Media Protection' WHERE framework = 'NIST800171' AND code LIKE '3.8.%';
        UPDATE controls SET category = 'Personnel Security' WHERE framework = 'NIST800171' AND code LIKE '3.9.%';
        UPDATE controls SET category = 'Physical Protection' WHERE framework = 'NIST800171' AND code LIKE '3.10.%';
        UPDATE controls SET category = 'Risk Assessment' WHERE framework = 'NIST800171' AND code LIKE '3.11.%';
        UPDATE controls SET category = 'Security Assessment' WHERE framework = 'NIST800171' AND code LIKE '3.12.%';
        UPDATE controls SET category = 'System and Communications Protection' WHERE framework = 'NIST800171' AND code LIKE '3.13.%';
        UPDATE controls SET category = 'System and Information Integrity' WHERE framework = 'NIST800171' AND code LIKE '3.14.%';

        -- CMMC Categories (based on prefix like AC.%, MP.%, etc.)
        UPDATE controls SET category = 'Access Control' WHERE framework = 'CMMC' AND code LIKE 'AC.%';
        UPDATE controls SET category = 'Awareness and Training' WHERE framework = 'CMMC' AND code LIKE 'AT.%';
        UPDATE controls SET category = 'Audit and Accountability' WHERE framework = 'CMMC' AND code LIKE 'AU.%';
        UPDATE controls SET category = 'Configuration Management' WHERE framework = 'CMMC' AND code LIKE 'CM.%';
        UPDATE controls SET category = 'Identification and Authentication' WHERE framework = 'CMMC' AND code LIKE 'IA.%';
        UPDATE controls SET category = 'Incident Response' WHERE framework = 'CMMC' AND code LIKE 'IR.%';
        UPDATE controls SET category = 'Maintenance' WHERE framework = 'CMMC' AND code LIKE 'MA.%';
        UPDATE controls SET category = 'Media Protection' WHERE framework = 'CMMC' AND code LIKE 'MP.%';
        UPDATE controls SET category = 'Personnel Security' WHERE framework = 'CMMC' AND code LIKE 'PS.%';
        UPDATE controls SET category = 'Physical Protection' WHERE framework = 'CMMC' AND code LIKE 'PE.%';
        UPDATE controls SET category = 'Risk Assessment' WHERE framework = 'CMMC' AND code LIKE 'RA.%';
        UPDATE controls SET category = 'Security Assessment' WHERE framework = 'CMMC' AND code LIKE 'CA.%';
        UPDATE controls SET category = 'System and Communications Protection' WHERE framework = 'CMMC' AND code LIKE 'SC.%';
        UPDATE controls SET category = 'System and Information Integrity' WHERE framework = 'CMMC' AND code LIKE 'SI.%';

        -- Other frameworks can have categories added later
        UPDATE controls SET category = 'General' WHERE category IS NULL;
    ",
    'pgsql' => "
        ALTER TABLE controls
        ADD COLUMN IF NOT EXISTS category VARCHAR(100) NULL;

        COMMENT ON COLUMN controls.category IS 'Control family/category (e.g., Access Control, Media Protection)';

        -- NIST 800-171 Categories (based on numeric code like 3.1.x)
        UPDATE controls SET category = 'Access Control' WHERE framework = 'NIST800171' AND code LIKE '3.1.%';
        UPDATE controls SET category = 'Awareness and Training' WHERE framework = 'NIST800171' AND code LIKE '3.2.%';
        UPDATE controls SET category = 'Audit and Accountability' WHERE framework = 'NIST800171' AND code LIKE '3.3.%';
        UPDATE controls SET category = 'Configuration Management' WHERE framework = 'NIST800171' AND code LIKE '3.4.%';
        UPDATE controls SET category = 'Identification and Authentication' WHERE framework = 'NIST800171' AND code LIKE '3.5.%';
        UPDATE controls SET category = 'Incident Response' WHERE framework = 'NIST800171' AND code LIKE '3.6.%';
        UPDATE controls SET category = 'Maintenance' WHERE framework = 'NIST800171' AND code LIKE '3.7.%';
        UPDATE controls SET category = 'Media Protection' WHERE framework = 'NIST800171' AND code LIKE '3.8.%';
        UPDATE controls SET category = 'Personnel Security' WHERE framework = 'NIST800171' AND code LIKE '3.9.%';
        UPDATE controls SET category = 'Physical Protection' WHERE framework = 'NIST800171' AND code LIKE '3.10.%';
        UPDATE controls SET category = 'Risk Assessment' WHERE framework = 'NIST800171' AND code LIKE '3.11.%';
        UPDATE controls SET category = 'Security Assessment' WHERE framework = 'NIST800171' AND code LIKE '3.12.%';
        UPDATE controls SET category = 'System and Communications Protection' WHERE framework = 'NIST800171' AND code LIKE '3.13.%';
        UPDATE controls SET category = 'System and Information Integrity' WHERE framework = 'NIST800171' AND code LIKE '3.14.%';

        -- CMMC Categories (based on prefix like AC.%, MP.%, etc.)
        UPDATE controls SET category = 'Access Control' WHERE framework = 'CMMC' AND code LIKE 'AC.%';
        UPDATE controls SET category = 'Awareness and Training' WHERE framework = 'CMMC' AND code LIKE 'AT.%';
        UPDATE controls SET category = 'Audit and Accountability' WHERE framework = 'CMMC' AND code LIKE 'AU.%';
        UPDATE controls SET category = 'Configuration Management' WHERE framework = 'CMMC' AND code LIKE 'CM.%';
        UPDATE controls SET category = 'Identification and Authentication' WHERE framework = 'CMMC' AND code LIKE 'IA.%';
        UPDATE controls SET category = 'Incident Response' WHERE framework = 'CMMC' AND code LIKE 'IR.%';
        UPDATE controls SET category = 'Maintenance' WHERE framework = 'CMMC' AND code LIKE 'MA.%';
        UPDATE controls SET category = 'Media Protection' WHERE framework = 'CMMC' AND code LIKE 'MP.%';
        UPDATE controls SET category = 'Personnel Security' WHERE framework = 'CMMC' AND code LIKE 'PS.%';
        UPDATE controls SET category = 'Physical Protection' WHERE framework = 'CMMC' AND code LIKE 'PE.%';
        UPDATE controls SET category = 'Risk Assessment' WHERE framework = 'CMMC' AND code LIKE 'RA.%';
        UPDATE controls SET category = 'Security Assessment' WHERE framework = 'CMMC' AND code LIKE 'CA.%';
        UPDATE controls SET category = 'System and Communications Protection' WHERE framework = 'CMMC' AND code LIKE 'SC.%';
        UPDATE controls SET category = 'System and Information Integrity' WHERE framework = 'CMMC' AND code LIKE 'SI.%';

        UPDATE controls SET category = 'General' WHERE category IS NULL;
    "
];
