<?php

/**
 * Create poam_items table for Plan of Action & Milestones
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS poam_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            control_framework ENUM('CMMC', 'NIST800171', 'STIG'),
            control_code VARCHAR(100),
            title VARCHAR(500) NOT NULL,
            weakness TEXT,
            corrective_action TEXT,
            milestones TEXT,
            responsible_party VARCHAR(255),
            resources VARCHAR(255),
            start_date DATE,
            planned_completion_date DATE,
            actual_completion_date DATE,
            status ENUM('open', 'in_progress', 'closed', 'deferred') DEFAULT 'open',
            residual_risk VARCHAR(50),
            comments TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            INDEX idx_customer (customer_id),
            INDEX idx_control (control_framework, control_code),
            INDEX idx_status (status),
            INDEX idx_dates (planned_completion_date, actual_completion_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        DO $$ BEGIN
            CREATE TYPE poam_status AS ENUM ('open', 'in_progress', 'closed', 'deferred');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        CREATE TABLE IF NOT EXISTS poam_items (
            id SERIAL PRIMARY KEY,
            customer_id INT NOT NULL,
            control_framework control_framework,
            control_code VARCHAR(100),
            title VARCHAR(500) NOT NULL,
            weakness TEXT,
            corrective_action TEXT,
            milestones TEXT,
            responsible_party VARCHAR(255),
            resources VARCHAR(255),
            start_date DATE,
            planned_completion_date DATE,
            actual_completion_date DATE,
            status poam_status DEFAULT 'open',
            residual_risk VARCHAR(50),
            comments TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        );

        CREATE INDEX IF NOT EXISTS idx_poam_customer ON poam_items(customer_id);
        CREATE INDEX IF NOT EXISTS idx_poam_control ON poam_items(control_framework, control_code);
        CREATE INDEX IF NOT EXISTS idx_poam_status ON poam_items(status);
        CREATE INDEX IF NOT EXISTS idx_poam_dates ON poam_items(planned_completion_date, actual_completion_date);
    "
];
