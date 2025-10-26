<?php

/**
 * Create documents table for file uploads and evidence
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            mime_type VARCHAR(100),
            file_size INT,
            storage_path VARCHAR(500) NOT NULL,
            uploaded_by INT,
            linked_framework ENUM('CMMC', 'NIST800171', 'STIG'),
            linked_code VARCHAR(100),
            linked_assessment_id INT,
            linked_poam_id INT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (linked_assessment_id) REFERENCES assessments(id) ON DELETE SET NULL,
            FOREIGN KEY (linked_poam_id) REFERENCES poam_items(id) ON DELETE SET NULL,
            INDEX idx_customer (customer_id),
            INDEX idx_linked_control (linked_framework, linked_code),
            INDEX idx_linked_assessment (linked_assessment_id),
            INDEX idx_linked_poam (linked_poam_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS documents (
            id SERIAL PRIMARY KEY,
            customer_id INT NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            mime_type VARCHAR(100),
            file_size INT,
            storage_path VARCHAR(500) NOT NULL,
            uploaded_by INT,
            linked_framework control_framework,
            linked_code VARCHAR(100),
            linked_assessment_id INT,
            linked_poam_id INT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (linked_assessment_id) REFERENCES assessments(id) ON DELETE SET NULL,
            FOREIGN KEY (linked_poam_id) REFERENCES poam_items(id) ON DELETE SET NULL
        );

        CREATE INDEX IF NOT EXISTS idx_documents_customer ON documents(customer_id);
        CREATE INDEX IF NOT EXISTS idx_documents_linked_control ON documents(linked_framework, linked_code);
        CREATE INDEX IF NOT EXISTS idx_documents_linked_assessment ON documents(linked_assessment_id);
        CREATE INDEX IF NOT EXISTS idx_documents_linked_poam ON documents(linked_poam_id);
    "
];
