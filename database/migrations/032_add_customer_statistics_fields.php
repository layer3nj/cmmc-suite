<?php
/**
 * Migration: Add customer statistics tracking fields
 * Adds fields to clients table for tracking SSO, SCM, and user statistics
 */

return [
    'mysql' => "
        ALTER TABLE clients
        ADD COLUMN sso_enforced TINYINT(1) DEFAULT 0 AFTER active,
        ADD COLUMN scim_enabled TINYINT(1) DEFAULT 0 AFTER sso_enforced,
        ADD COLUMN total_users INT DEFAULT 0 AFTER scim_enabled,
        ADD COLUMN active_users INT DEFAULT 0 AFTER total_users,
        ADD COLUMN never_logged_in_users INT DEFAULT 0 AFTER active_users;
    ",

    'pgsql' => "
        ALTER TABLE clients
        ADD COLUMN sso_enforced BOOLEAN DEFAULT FALSE,
        ADD COLUMN scim_enabled BOOLEAN DEFAULT FALSE,
        ADD COLUMN total_users INT DEFAULT 0,
        ADD COLUMN active_users INT DEFAULT 0,
        ADD COLUMN never_logged_in_users INT DEFAULT 0;
    "
];
