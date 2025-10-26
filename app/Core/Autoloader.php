<?php

namespace App\Core;

/**
 * Simple PSR-4 Autoloader
 */
class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([__CLASS__, 'load']);
    }

    public static function load(string $className): void
    {
        // Convert namespace to file path
        $className = str_replace('App\\', '', $className);
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $file = APP_PATH . DIRECTORY_SEPARATOR . $className . '.php';

        if (file_exists($file)) {
            require_once $file;
        }

        // Also check vendor directory for third-party libraries
        $vendorFile = VENDOR_PATH . DIRECTORY_SEPARATOR . $className . '.php';
        if (file_exists($vendorFile)) {
            require_once $vendorFile;
        }
    }
}
