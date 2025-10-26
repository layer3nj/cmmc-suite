<?php

/**
 * Create assessments table
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS assessments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            assessed_at TIMESTAMP NULL,
            assessor_user_id INT,
            notes TEXT,
            status ENUM('draft', 'published') DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            FOREIGN KEY (assessor_user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_customer (customer_id),
            INDEX idx_status (status),
            INDEX idx_assessed_at (assessed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        DO $$ BEGIN
            CREATE TYPE assessment_status AS ENUM ('draft', 'published');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        CREATE TABLE IF NOT EXISTS assessments (
            id SERIAL PRIMARY KEY,
            customer_id INT NOT NULL,
            assessed_at TIMESTAMP NULL,
            assessor_user_id INT,
            notes TEXT,
            status assessment_status DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            FOREIGN KEY (assessor_user_id) REFERENCES users(id) ON DELETE SET NULL
        );

        CREATE INDEX IF NOT EXISTS idx_assessments_customer ON assessments(customer_id);
        CREATE INDEX IF NOT EXISTS idx_assessments_status ON assessments(status);
        CREATE INDEX IF NOT EXISTS idx_assessments_assessed_at ON assessments(assessed_at);
    "
];
