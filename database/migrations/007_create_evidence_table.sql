-- Evidence Collection & Management System
-- Tracks evidence files for compliance controls

CREATE TABLE IF NOT EXISTS evidence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    control_code VARCHAR(50) NOT NULL,
    control_framework VARCHAR(50) NOT NULL,
    evidence_type VARCHAR(50) NOT NULL COMMENT 'Type: policy, procedure, screenshot, certificate, log, report, etc.',
    description TEXT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL COMMENT 'File size in bytes',
    expiration_date DATE NULL COMMENT 'When evidence expires/needs renewal',
    notes TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, approved, rejected',
    uploaded_by INT NOT NULL,
    approved_by INT NULL,
    approved_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (customer_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    INDEX idx_customer_control (customer_id, control_code),
    INDEX idx_expiration (expiration_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
