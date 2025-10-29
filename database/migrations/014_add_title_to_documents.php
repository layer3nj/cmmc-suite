<?php

/**
 * Add title column to documents table
 *
 * Allows users to provide a custom title for documents instead of just showing filename.
 * Falls back to filename if no title is provided.
 */

return [
    'mysql' => "
        ALTER TABLE documents
        ADD COLUMN title VARCHAR(255) NULL AFTER customer_id;

        -- Update existing records to use file_name as title
        UPDATE documents SET title = file_name WHERE title IS NULL;
    ",
    'pgsql' => "
        ALTER TABLE documents
        ADD COLUMN title VARCHAR(255) NULL;

        -- Update existing records to use file_name as title
        UPDATE documents SET title = file_name WHERE title IS NULL;
    "
];
