<?php

/**
 * Create audit_log table for tracking all sensitive actions
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS audit_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            user_id INT,
            ip VARCHAR(45),
            action VARCHAR(100) NOT NULL,
            entity VARCHAR(100),
            entity_id INT,
            delta JSON,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_ts (ts),
            INDEX idx_user (user_id),
            INDEX idx_action (action),
            INDEX idx_entity (entity, entity_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS audit_log (
            id SERIAL PRIMARY KEY,
            ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            user_id INT,
            ip VARCHAR(45),
            action VARCHAR(100) NOT NULL,
            entity VARCHAR(100),
            entity_id INT,
            delta JSONB,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );

        CREATE INDEX IF NOT EXISTS idx_audit_ts ON audit_log(ts);
        CREATE INDEX IF NOT EXISTS idx_audit_user ON audit_log(user_id);
        CREATE INDEX IF NOT EXISTS idx_audit_action ON audit_log(action);
        CREATE INDEX IF NOT EXISTS idx_audit_entity ON audit_log(entity, entity_id);
    "
];
