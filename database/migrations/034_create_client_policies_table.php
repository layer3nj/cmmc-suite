<?php
/**
 * Migration: Create client_policies table
 * Tracks customer-specific policies with approval workflow
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS client_policies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT NOT NULL,
            template_policy_id INT NULL COMMENT 'Reference to policies template if based on one',
            title VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            frameworks TEXT COMMENT 'Comma-separated list of applicable frameworks',
            description TEXT,
            content LONGTEXT NOT NULL,
            version VARCHAR(50) DEFAULT '1.0',
            status ENUM('draft', 'in_review', 'approved', 'archived') DEFAULT 'draft',
            approved_at DATETIME NULL,
            approved_by INT NULL COMMENT 'User ID who approved',
            last_reviewed_at DATETIME NULL,
            review_due_date DATE NULL,
            owner_user_id INT NULL COMMENT 'Policy owner/responsible person',
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            FOREIGN KEY (template_policy_id) REFERENCES policies(id) ON DELETE SET NULL,
            FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_client (client_id),
            INDEX idx_status (status),
            INDEX idx_category (category),
            INDEX idx_active (is_active),
            INDEX idx_review_due (review_due_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS client_policies (
            id SERIAL PRIMARY KEY,
            client_id INT NOT NULL,
            template_policy_id INT NULL,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            frameworks TEXT,
            description TEXT,
            content TEXT NOT NULL,
            version VARCHAR(50) DEFAULT '1.0',
            status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft', 'in_review', 'approved', 'archived')),
            approved_at TIMESTAMP NULL,
            approved_by INT NULL,
            last_reviewed_at TIMESTAMP NULL,
            review_due_date DATE NULL,
            owner_user_id INT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            FOREIGN KEY (template_policy_id) REFERENCES policies(id) ON DELETE SET NULL,
            FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE SET NULL
        );

        CREATE INDEX IF NOT EXISTS idx_client_policies_client ON client_policies(client_id);
        CREATE INDEX IF NOT EXISTS idx_client_policies_status ON client_policies(status);
        CREATE INDEX IF NOT EXISTS idx_client_policies_category ON client_policies(category);
        CREATE INDEX IF NOT EXISTS idx_client_policies_active ON client_policies(is_active);
        CREATE INDEX IF NOT EXISTS idx_client_policies_review_due ON client_policies(review_due_date);
    "
];
