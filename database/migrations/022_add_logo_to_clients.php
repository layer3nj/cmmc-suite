<?php

/**
 * Migration: Add logo_path column to clients table
 */

return [
    'mysql' => "
        ALTER TABLE clients ADD COLUMN logo_path VARCHAR(500) NULL AFTER notes;
    ",
    'pgsql' => "
        ALTER TABLE clients ADD COLUMN logo_path VARCHAR(500) NULL;
    "
];
