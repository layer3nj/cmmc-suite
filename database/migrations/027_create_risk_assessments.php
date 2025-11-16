<?php
/**
 * Migration: Create Risk Assessment Tables
 * Creates tables for cybersecurity risk assessments
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS risk_assessments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            status ENUM('draft', 'in_progress', 'completed', 'archived') DEFAULT 'draft',
            overall_risk_score DECIMAL(5,2) DEFAULT 0,
            high_risks INT DEFAULT 0,
            medium_risks INT DEFAULT 0,
            low_risks INT DEFAULT 0,
            created_by INT,
            completed_at DATETIME,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_client (client_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS risk_assessment_questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category VARCHAR(100) NOT NULL,
            subcategory VARCHAR(100),
            question_text TEXT NOT NULL,
            question_type ENUM('yes_no', 'rating', 'multiple_choice') DEFAULT 'yes_no',
            weight INT DEFAULT 1,
            guidance TEXT,
            order_number INT DEFAULT 0,
            active TINYINT(1) DEFAULT 1,
            created_at DATETIME NOT NULL,
            INDEX idx_category (category),
            INDEX idx_active (active),
            INDEX idx_order (order_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS risk_assessment_responses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            assessment_id INT NOT NULL,
            question_id INT NOT NULL,
            response_value VARCHAR(255),
            risk_level ENUM('low', 'medium', 'high', 'critical'),
            likelihood INT DEFAULT 1,
            impact INT DEFAULT 1,
            risk_score DECIMAL(5,2) DEFAULT 0,
            notes TEXT,
            remediation_notes TEXT,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            FOREIGN KEY (assessment_id) REFERENCES risk_assessments(id) ON DELETE CASCADE,
            FOREIGN KEY (question_id) REFERENCES risk_assessment_questions(id) ON DELETE CASCADE,
            INDEX idx_assessment (assessment_id),
            INDEX idx_risk_level (risk_level)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS risk_assessments (
            id SERIAL PRIMARY KEY,
            client_id INT,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft', 'in_progress', 'completed', 'archived')),
            overall_risk_score DECIMAL(5,2) DEFAULT 0,
            high_risks INT DEFAULT 0,
            medium_risks INT DEFAULT 0,
            low_risks INT DEFAULT 0,
            created_by INT,
            completed_at TIMESTAMP,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        );

        CREATE INDEX IF NOT EXISTS idx_risk_assessments_client ON risk_assessments(client_id);
        CREATE INDEX IF NOT EXISTS idx_risk_assessments_status ON risk_assessments(status);

        CREATE TABLE IF NOT EXISTS risk_assessment_questions (
            id SERIAL PRIMARY KEY,
            category VARCHAR(100) NOT NULL,
            subcategory VARCHAR(100),
            question_text TEXT NOT NULL,
            question_type VARCHAR(20) DEFAULT 'yes_no' CHECK (question_type IN ('yes_no', 'rating', 'multiple_choice')),
            weight INT DEFAULT 1,
            guidance TEXT,
            order_number INT DEFAULT 0,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP NOT NULL
        );

        CREATE INDEX IF NOT EXISTS idx_risk_assessment_questions_category ON risk_assessment_questions(category);
        CREATE INDEX IF NOT EXISTS idx_risk_assessment_questions_active ON risk_assessment_questions(active);
        CREATE INDEX IF NOT EXISTS idx_risk_assessment_questions_order ON risk_assessment_questions(order_number);

        CREATE TABLE IF NOT EXISTS risk_assessment_responses (
            id SERIAL PRIMARY KEY,
            assessment_id INT NOT NULL,
            question_id INT NOT NULL,
            response_value VARCHAR(255),
            risk_level VARCHAR(20) CHECK (risk_level IN ('low', 'medium', 'high', 'critical')),
            likelihood INT DEFAULT 1,
            impact INT DEFAULT 1,
            risk_score DECIMAL(5,2) DEFAULT 0,
            notes TEXT,
            remediation_notes TEXT,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            FOREIGN KEY (assessment_id) REFERENCES risk_assessments(id) ON DELETE CASCADE,
            FOREIGN KEY (question_id) REFERENCES risk_assessment_questions(id) ON DELETE CASCADE
        );

        CREATE INDEX IF NOT EXISTS idx_risk_assessment_responses_assessment ON risk_assessment_responses(assessment_id);
        CREATE INDEX IF NOT EXISTS idx_risk_assessment_responses_risk_level ON risk_assessment_responses(risk_level);
    "
];
