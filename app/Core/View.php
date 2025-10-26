<?php

namespace App\Core;

/**
 * Simple Template Engine
 */
class View
{
    private static array $data = [];

    public static function render(string $view, array $data = []): string
    {
        self::$data = array_merge(self::$data, $data);

        $viewFile = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: $viewFile");
        }

        ob_start();

        // Extract data to variables
        extract(self::$data);

        // Helper functions available in views
        $e = function($value) {
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
        };

        $csrf = function() {
            return Csrf::field();
        };

        $old = function($key, $default = '') {
            return Session::getFlash('old_' . $key, $default);
        };

        $error = function($key) {
            return Session::getFlash('error_' . $key);
        };

        $errors = function() {
            return Session::getFlash('errors', []);
        };

        $success = function() {
            return Session::getFlash('success');
        };

        $url = function($path = '') {
            $request = new Request();
            return rtrim($request->baseUrl(), '/') . '/' . ltrim($path, '/');
        };

        $asset = function($path) use ($url) {
            return $url('assets/' . ltrim($path, '/'));
        };

        require $viewFile;

        return ob_get_clean();
    }

    public static function share(string $key, mixed $value): void
    {
        self::$data[$key] = $value;
    }

    public static function with(array $data): void
    {
        self::$data = array_merge(self::$data, $data);
    }
}
