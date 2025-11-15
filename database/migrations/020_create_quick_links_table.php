<?php
/**
 * Migration: Create quick_links table for dashboard homepage
 *
 * Stores customizable quick links to external applications
 */

return [
    'mysql' => "
        CREATE TABLE IF NOT EXISTS quick_links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            url VARCHAR(500) NOT NULL,
            description TEXT,
            icon VARCHAR(100),
            display_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_display_order (display_order),
            INDEX idx_is_active (is_active),
            UNIQUE KEY idx_unique_title (title)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        INSERT IGNORE INTO quick_links (title, url, description, icon, display_order, is_active) VALUES
        ('DattoRMM', 'https://vidal.rmm.datto.com', 'Remote Monitoring and Management', '🖥️', 1, 1),
        ('ITGlue', 'https://layer3nj.itglue.com', 'IT Documentation Platform', '📚', 2, 1),
        ('Autotask', 'https://www.autotask.net', 'PSA Platform', '🎫', 3, 1),
        ('Microsoft Commercial & GCC', 'https://login.microsoftonline.com', 'Microsoft 365 Commercial and GCC Login', '☁️', 4, 1),
        ('Microsoft GCC High', 'https://login.microsoftonline.us', 'Microsoft 365 GCC High Login', '🔒', 5, 1);
    ",

    'pgsql' => "
        CREATE TABLE IF NOT EXISTS quick_links (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            url VARCHAR(500) NOT NULL,
            description TEXT,
            icon VARCHAR(100),
            display_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE INDEX IF NOT EXISTS idx_quick_links_display_order ON quick_links(display_order);
        CREATE INDEX IF NOT EXISTS idx_quick_links_is_active ON quick_links(is_active);
        CREATE UNIQUE INDEX IF NOT EXISTS idx_quick_links_unique_title ON quick_links(title);

        INSERT INTO quick_links (title, url, description, icon, display_order, is_active) VALUES
        ('DattoRMM', 'https://vidal.rmm.datto.com', 'Remote Monitoring and Management', '🖥️', 1, TRUE),
        ('ITGlue', 'https://layer3nj.itglue.com', 'IT Documentation Platform', '📚', 2, TRUE),
        ('Autotask', 'https://www.autotask.net', 'PSA Platform', '🎫', 3, TRUE),
        ('Microsoft Commercial & GCC', 'https://login.microsoftonline.com', 'Microsoft 365 Commercial and GCC Login', '☁️', 4, TRUE),
        ('Microsoft GCC High', 'https://login.microsoftonline.us', 'Microsoft 365 GCC High Login', '🔒', 5, TRUE)
        ON CONFLICT (title) DO NOTHING;
    "
];
