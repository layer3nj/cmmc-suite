<?php
/**
 * Migration: Rename customers table to clients
 *
 * Renames the customers table to clients throughout the database
 */

return [
    'mysql' => "
        RENAME TABLE customers TO clients;
    ",

    'pgsql' => "
        ALTER TABLE customers RENAME TO clients;
    "
];
