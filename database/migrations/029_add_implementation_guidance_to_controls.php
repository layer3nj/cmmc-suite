<?php
/**
 * Migration: Add implementation guidance fields to controls table
 * Adds practical guidance fields to help clients understand how to implement controls
 */

return [
    'mysql' => "
        ALTER TABLE controls
        ADD COLUMN implementation_guidance TEXT AFTER description,
        ADD COLUMN who_implements VARCHAR(255) AFTER implementation_guidance,
        ADD COLUMN tools_needed TEXT AFTER who_implements,
        ADD COLUMN common_solutions TEXT AFTER tools_needed,
        ADD COLUMN estimated_effort ENUM('Low', 'Medium', 'High', 'Very High') AFTER common_solutions,
        ADD COLUMN helpful_resources TEXT AFTER estimated_effort;
    ",

    'pgsql' => "
        ALTER TABLE controls
        ADD COLUMN implementation_guidance TEXT,
        ADD COLUMN who_implements VARCHAR(255),
        ADD COLUMN tools_needed TEXT,
        ADD COLUMN common_solutions TEXT,
        ADD COLUMN estimated_effort VARCHAR(20),
        ADD COLUMN helpful_resources TEXT;
    "
];
