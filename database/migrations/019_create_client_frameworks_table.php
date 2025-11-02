<?php
/**
 * Migration: Create client_frameworks junction table
 *
 * Establishes many-to-many relationship between clients and frameworks
 * Allows each client to be assigned multiple compliance frameworks
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS client_frameworks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT NOT NULL,
            framework VARCHAR(50) NOT NULL,
            is_primary BOOLEAN DEFAULT FALSE,
            assigned_date DATE DEFAULT NULL,
            target_completion_date DATE DEFAULT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            UNIQUE KEY unique_client_framework (client_id, framework),
            INDEX idx_client_id (client_id),
            INDEX idx_framework (framework)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS client_frameworks (
            id SERIAL PRIMARY KEY,
            client_id INT NOT NULL,
            framework VARCHAR(50) NOT NULL,
            is_primary BOOLEAN DEFAULT FALSE,
            assigned_date DATE DEFAULT NULL,
            target_completion_date DATE DEFAULT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            UNIQUE (client_id, framework)
        );

        CREATE INDEX IF NOT EXISTS idx_client_frameworks_client_id ON client_frameworks(client_id);
        CREATE INDEX IF NOT EXISTS idx_client_frameworks_framework ON client_frameworks(framework);
    "
];
