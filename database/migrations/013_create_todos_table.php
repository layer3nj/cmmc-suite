<?php

/**
 * Create todos table for client-based to-do list management
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS todos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            due_date DATE,
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            INDEX idx_customer (customer_id),
            INDEX idx_status (status),
            INDEX idx_priority (priority),
            INDEX idx_due_date (due_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",

    'pgsql' => "
        CREATE TYPE todo_status AS ENUM ('pending', 'in_progress', 'completed');
        CREATE TYPE todo_priority AS ENUM ('low', 'medium', 'high');

        CREATE TABLE IF NOT EXISTS todos (
            id SERIAL PRIMARY KEY,
            customer_id INTEGER NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            status todo_status DEFAULT 'pending',
            priority todo_priority DEFAULT 'medium',
            due_date DATE,
            completed_at TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        );

        CREATE INDEX IF NOT EXISTS idx_todos_customer ON todos(customer_id);
        CREATE INDEX IF NOT EXISTS idx_todos_status ON todos(status);
        CREATE INDEX IF NOT EXISTS idx_todos_priority ON todos(priority);
        CREATE INDEX IF NOT EXISTS idx_todos_due_date ON todos(due_date);
    "
];
