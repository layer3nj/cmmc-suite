<?php

/**
 * Add CMMC Maturity Level to clients
 * Remove incorrect NIST 800-171 control 3.13.16 (only exists in Rev 3)
 *
 * Note: Framework columns already support NIST80053 via VARCHAR(50) from migration 016
 * No need to modify framework columns - they already accept any string value
 */

return [
    'mysql' => "
        -- Add CMMC maturity level to clients table
        SET @col_exists = (
            SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'clients'
            AND COLUMN_NAME = 'cmmc_maturity_level'
        );

        SET @sql = IF(
            @col_exists = 0,
            'ALTER TABLE clients ADD COLUMN cmmc_maturity_level ENUM(''Level 1'', ''Level 2'', ''Level 3'') DEFAULT NULL AFTER logo_path',
            'SELECT ''Column cmmc_maturity_level already exists'' AS message'
        );

        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        -- Remove incorrect NIST 800-171 control 3.13.16 (added in Rev 3, not Rev 2)
        DELETE FROM controls
        WHERE framework = 'NIST800171'
        AND code = '3.13.16';
    ",

    'pgsql' => "
        -- Add CMMC maturity level to clients table
        DO $$
        BEGIN
            -- Create ENUM type if it doesn't exist
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'cmmc_level') THEN
                CREATE TYPE cmmc_level AS ENUM ('Level 1', 'Level 2', 'Level 3');
            END IF;

            -- Add column if it doesn't exist
            IF NOT EXISTS (
                SELECT 1 FROM information_schema.columns
                WHERE table_name = 'clients'
                AND column_name = 'cmmc_maturity_level'
            ) THEN
                ALTER TABLE clients ADD COLUMN cmmc_maturity_level cmmc_level DEFAULT NULL;
            END IF;
        END$$;

        -- Remove incorrect NIST 800-171 control 3.13.16
        DELETE FROM controls
        WHERE framework = 'NIST800171'
        AND code = '3.13.16';
    "
];
