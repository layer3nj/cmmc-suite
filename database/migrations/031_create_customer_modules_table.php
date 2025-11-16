<?php
/**
 * Migration: Create customer_modules table
 * Tracks which modules are enabled/active for each customer
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS customer_modules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT NOT NULL,
            module_name ENUM('policies', 'training', 'compliance', 'risk') NOT NULL,
            enabled TINYINT(1) DEFAULT 1,
            completion_percentage DECIMAL(5,2) DEFAULT 0,
            last_updated DATETIME,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            UNIQUE KEY unique_client_module (client_id, module_name),
            INDEX idx_client (client_id),
            INDEX idx_module (module_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS customer_modules (
            id SERIAL PRIMARY KEY,
            client_id INT NOT NULL,
            module_name VARCHAR(20) NOT NULL CHECK (module_name IN ('policies', 'training', 'compliance', 'risk')),
            enabled BOOLEAN DEFAULT TRUE,
            completion_percentage DECIMAL(5,2) DEFAULT 0,
            last_updated TIMESTAMP,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            UNIQUE (client_id, module_name)
        );

        CREATE INDEX IF NOT EXISTS idx_customer_modules_client ON customer_modules(client_id);
        CREATE INDEX IF NOT EXISTS idx_customer_modules_module ON customer_modules(module_name);
    "
];
