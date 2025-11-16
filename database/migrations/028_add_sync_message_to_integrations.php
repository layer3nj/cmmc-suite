<?php
/**
 * Migration: Add sync_message column to integrations table
 * Stores detailed sync messages for integration operations
 */

return [
    'mysql' => "
        ALTER TABLE integrations ADD COLUMN sync_message TEXT AFTER sync_status;
    ",

    'pgsql' => "
        ALTER TABLE integrations ADD COLUMN sync_message TEXT;
    "
];
