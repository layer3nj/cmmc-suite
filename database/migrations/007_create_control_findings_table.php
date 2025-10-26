<?php

/**
 * Create control_findings table for assessment results
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS control_findings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            assessment_id INT NOT NULL,
            control_framework ENUM('CMMC', 'NIST800171', 'STIG') NOT NULL,
            control_code VARCHAR(100) NOT NULL,
            status ENUM('met', 'partially_met', 'not_met', 'not_applicable') NOT NULL,
            objective_evidence TEXT,
            compensating_controls TEXT,
            related_stig_refs TEXT,
            severity ENUM('low', 'moderate', 'high'),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
            INDEX idx_assessment (assessment_id),
            INDEX idx_control (control_framework, control_code),
            INDEX idx_status (status),
            INDEX idx_severity (severity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        DO $$ BEGIN
            CREATE TYPE finding_status AS ENUM ('met', 'partially_met', 'not_met', 'not_applicable');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        DO $$ BEGIN
            CREATE TYPE finding_severity AS ENUM ('low', 'moderate', 'high');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        CREATE TABLE IF NOT EXISTS control_findings (
            id SERIAL PRIMARY KEY,
            assessment_id INT NOT NULL,
            control_framework control_framework NOT NULL,
            control_code VARCHAR(100) NOT NULL,
            status finding_status NOT NULL,
            objective_evidence TEXT,
            compensating_controls TEXT,
            related_stig_refs TEXT,
            severity finding_severity,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE
        );

        CREATE INDEX IF NOT EXISTS idx_findings_assessment ON control_findings(assessment_id);
        CREATE INDEX IF NOT EXISTS idx_findings_control ON control_findings(control_framework, control_code);
        CREATE INDEX IF NOT EXISTS idx_findings_status ON control_findings(status);
        CREATE INDEX IF NOT EXISTS idx_findings_severity ON control_findings(severity);
    "
];
