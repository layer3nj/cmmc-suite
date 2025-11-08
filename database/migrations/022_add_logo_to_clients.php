<?php

/**
 * Migration: Add logo_path column to clients table
 */

return [
    'mysql' => "
        ALTER TABLE clients ADD COLUMN logo_path VARCHAR(500) NULL;
    ",
    'pgsql' => "
        ALTER TABLE clients ADD COLUMN logo_path VARCHAR(500) NULL;
    "
];
