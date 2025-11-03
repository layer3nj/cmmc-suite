<?php
/**
 * Migration: Create user_client_access table
 *
 * Implements client-level access control for users.
 * Allows assigning specific clients to users with read-only or read-write permissions.
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS user_client_access (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            client_id INT NOT NULL,
            access_level ENUM('read-only', 'read-write') DEFAULT 'read-only',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_client (user_id, client_id),
            INDEX idx_user_id (user_id),
            INDEX idx_client_id (client_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        DO $$ BEGIN
            CREATE TYPE user_access_level AS ENUM ('read-only', 'read-write');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;

        CREATE TABLE IF NOT EXISTS user_client_access (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL,
            client_id INT NOT NULL,
            access_level user_access_level DEFAULT 'read-only',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            UNIQUE (user_id, client_id)
        );

        CREATE INDEX IF NOT EXISTS idx_user_client_access_user_id ON user_client_access(user_id);
        CREATE INDEX IF NOT EXISTS idx_user_client_access_client_id ON user_client_access(client_id);
    "
];
