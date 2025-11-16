<?php
/**
 * Migration: Create compliance_phases table
 * Tracks compliance progress across different phases for each customer
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS compliance_phases (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT NOT NULL,
            framework VARCHAR(50) NOT NULL,
            phase_number INT NOT NULL,
            phase_name VARCHAR(100),
            completion_percentage DECIMAL(5,2) DEFAULT 0,
            controls_total INT DEFAULT 0,
            controls_met INT DEFAULT 0,
            last_assessed DATETIME,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            UNIQUE KEY unique_client_framework_phase (client_id, framework, phase_number),
            INDEX idx_client (client_id),
            INDEX idx_framework (framework)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS compliance_phases (
            id SERIAL PRIMARY KEY,
            client_id INT NOT NULL,
            framework VARCHAR(50) NOT NULL,
            phase_number INT NOT NULL,
            phase_name VARCHAR(100),
            completion_percentage DECIMAL(5,2) DEFAULT 0,
            controls_total INT DEFAULT 0,
            controls_met INT DEFAULT 0,
            last_assessed TIMESTAMP,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            UNIQUE (client_id, framework, phase_number)
        );

        CREATE INDEX IF NOT EXISTS idx_compliance_phases_client ON compliance_phases(client_id);
        CREATE INDEX IF NOT EXISTS idx_compliance_phases_framework ON compliance_phases(framework);
    "
];
