<?php

/**
 * Migration: Add logo_path column to clients table
 */

return [
    'mysql' => "
        SET @dbname = DATABASE();
        SET @tablename = 'clients';
        SET @columnname = 'logo_path';
        SET @preparedStatement = (SELECT IF(
            (
                SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = @dbname
                AND TABLE_NAME = @tablename
                AND COLUMN_NAME = @columnname
            ) > 0,
            'SELECT 1',
            'ALTER TABLE clients ADD COLUMN logo_path VARCHAR(500) NULL'
        ));
        PREPARE alterIfNotExists FROM @preparedStatement;
        EXECUTE alterIfNotExists;
        DEALLOCATE PREPARE alterIfNotExists;
    ",
    'pgsql' => "
        ALTER TABLE clients ADD COLUMN IF NOT EXISTS logo_path VARCHAR(500) NULL;
    "
];
