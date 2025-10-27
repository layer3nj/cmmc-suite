<?php

namespace App\Core;

/**
 * Session Manager with Security Features
 */
class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (!self::$started && session_status() === PHP_SESSION_NONE) {
            session_start();
            self::$started = true;

            // Initialize session security
            if (!self::has('_initiated')) {
                self::regenerate();
                self::set('_initiated', true);
            }

            // Check for session fixation
            if (!self::has('_user_agent')) {
                self::set('_user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
            } elseif (self::get('_user_agent') !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
                // User agent changed - destroy and start fresh session
                self::destroy();
                session_start();
                self::$started = true;
                self::set('_user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
                self::set('_initiated', true);
                self::set('_created', time());
                self::set('_last_activity', time());
                return;
            }

            // Check idle timeout
            $idleTimeout = 1800; // 30 minutes
            if (self::has('_last_activity')) {
                if (time() - self::get('_last_activity') > $idleTimeout) {
                    // Session expired - destroy and start fresh
                    self::destroy();
                    session_start();
                    self::$started = true;
                    self::set('_user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
                    self::set('_initiated', true);
                    self::set('_created', time());
                    self::set('_last_activity', time());
                    return;
                }
            }
            self::set('_last_activity', time());

            // Check absolute timeout
            $absoluteTimeout = 7200; // 2 hours
            if (self::has('_created')) {
                if (time() - self::get('_created') > $absoluteTimeout) {
                    // Session expired - destroy and start fresh
                    self::destroy();
                    session_start();
                    self::$started = true;
                    self::set('_user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
                    self::set('_initiated', true);
                    self::set('_created', time());
                    self::set('_last_activity', time());
                    return;
                }
            } else {
                self::set('_created', time());
            }
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        self::set('_flash_' . $key, $value);
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = self::get('_flash_' . $key, $default);
        self::remove('_flash_' . $key);
        return $value;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
        self::set('_created', time());
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
        self::$started = false;
    }
}
