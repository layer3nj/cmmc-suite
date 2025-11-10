<?php

/**
 * Add CMMC Maturity Level to clients and add NIST 800-53 framework support
 * Also removes the incorrect NIST 800-171 control 3.13.16 (only exists in Rev 3)
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
            'ALTER TABLE clients ADD COLUMN cmmc_maturity_level ENUM(''Level 1'', ''Level 2'', ''Level 3'') DEFAULT NULL AFTER framework',
            'SELECT ''Column cmmc_maturity_level already exists'' AS message'
        );

        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        -- Remove incorrect NIST 800-171 control 3.13.16 (added in Rev 3, not Rev 2)
        DELETE FROM controls
        WHERE framework = 'NIST800171'
        AND code = '3.13.16';

        -- Update framework enum to include NIST800-53
        SET @enum_check = (
            SELECT COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'controls'
            AND COLUMN_NAME = 'framework'
        );

        SET @update_enum = IF(
            @enum_check NOT LIKE '%NIST80053%',
            'ALTER TABLE controls MODIFY COLUMN framework ENUM(''CMMC'', ''NIST800171'', ''NIST80053'', ''STIG'') NOT NULL',
            'SELECT ''NIST80053 already in framework enum'' AS message'
        );

        PREPARE stmt2 FROM @update_enum;
        EXECUTE stmt2;
        DEALLOCATE PREPARE stmt2;

        -- Update client_frameworks table to support NIST 800-53
        SET @enum_check2 = (
            SELECT COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'client_frameworks'
            AND COLUMN_NAME = 'framework_name'
        );

        SET @update_enum2 = IF(
            @enum_check2 NOT LIKE '%NIST80053%',
            'ALTER TABLE client_frameworks MODIFY COLUMN framework_name ENUM(''CMMC'', ''NIST800171'', ''NIST80053'', ''STIG'') NOT NULL',
            'SELECT ''NIST80053 already in client_frameworks enum'' AS message'
        );

        PREPARE stmt3 FROM @update_enum2;
        EXECUTE stmt3;
        DEALLOCATE PREPARE stmt3;

        -- Update documents table framework field
        SET @enum_check3 = (
            SELECT COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'documents'
            AND COLUMN_NAME = 'framework'
        );

        SET @update_enum3 = IF(
            @enum_check3 NOT LIKE '%NIST80053%',
            'ALTER TABLE documents MODIFY COLUMN framework VARCHAR(100) DEFAULT NULL',
            'SELECT ''documents.framework already updated'' AS message'
        );

        PREPARE stmt4 FROM @update_enum3;
        EXECUTE stmt4;
        DEALLOCATE PREPARE stmt4;

        -- Update control_findings table framework field
        SET @enum_check4 = (
            SELECT COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'control_findings'
            AND COLUMN_NAME = 'framework'
        );

        SET @update_enum4 = IF(
            @enum_check4 NOT LIKE '%NIST80053%',
            'ALTER TABLE control_findings MODIFY COLUMN framework VARCHAR(100) DEFAULT NULL',
            'SELECT ''control_findings.framework already updated'' AS message'
        );

        PREPARE stmt5 FROM @update_enum4;
        EXECUTE stmt5;
        DEALLOCATE PREPARE stmt5;
    ",

    'pgsql' => "
        -- Add CMMC maturity level to clients table
        DO $$
        BEGIN
            IF NOT EXISTS (
                SELECT 1 FROM information_schema.columns
                WHERE table_name = 'clients'
                AND column_name = 'cmmc_maturity_level'
            ) THEN
                CREATE TYPE cmmc_level AS ENUM ('Level 1', 'Level 2', 'Level 3');
                ALTER TABLE clients ADD COLUMN cmmc_maturity_level cmmc_level DEFAULT NULL;
            END IF;
        END$$;

        -- Remove incorrect NIST 800-171 control 3.13.16
        DELETE FROM controls
        WHERE framework = 'NIST800171'
        AND code = '3.13.16';

        -- Update framework enum to include NIST 800-53
        DO $$
        BEGIN
            ALTER TYPE control_framework ADD VALUE IF NOT EXISTS 'NIST80053';
        EXCEPTION
            WHEN duplicate_object THEN null;
        END$$;
    "
];
