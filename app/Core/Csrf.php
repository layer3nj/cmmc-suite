<?php

namespace App\Core;

/**
 * CSRF Protection
 */
class Csrf
{
    private const TOKEN_NAME = '_csrf_token';

    public static function generateToken(): string
    {
        if (!Session::has(self::TOKEN_NAME)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::TOKEN_NAME, $token);
            error_log('CSRF: Generated new token: ' . substr($token, 0, 16) . '... (Session ID: ' . session_id() . ')');
        }

        return Session::get(self::TOKEN_NAME);
    }

    public static function validateToken(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $sessionToken = Session::get(self::TOKEN_NAME);

        if (!$sessionToken) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public static function field(): string
    {
        $token = self::generateToken();
        // Use autocomplete="off" to prevent browser autofill from interfering
        // Use data-csrf attribute to help with debugging
        $html = '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . htmlspecialchars($token) . '" autocomplete="off" data-csrf="true">';
        error_log('CSRF: Generated field HTML for page render (token: ' . substr($token, 0, 16) . '..., session: ' . session_id() . ')');
        return $html;
    }

    public static function validate(Request $request): bool
    {
        // Try the correct field name first
        $token = $request->post(self::TOKEN_NAME);

        // Fallback: check for field without underscore (browser autocomplete workaround)
        if (empty($token)) {
            $token = $request->post('csrf_token');
        }

        $sessionToken = Session::get(self::TOKEN_NAME);

        // Debug logging
        error_log('CSRF Validation Debug:');
        error_log('  Looking for field: ' . self::TOKEN_NAME);

        $underscoreValue = $request->post('_csrf_token');
        $noUnderscoreValue = $request->post('csrf_token');

        error_log('  POST[_csrf_token]: ' . (is_null($underscoreValue) ? 'NULL' : ($underscoreValue === '' ? 'EMPTY STRING' : substr($underscoreValue, 0, 16) . '...')));
        error_log('  POST[csrf_token]: ' . (is_null($noUnderscoreValue) ? 'NULL' : ($noUnderscoreValue === '' ? 'EMPTY STRING' : substr($noUnderscoreValue, 0, 16) . '...')));
        error_log('  Submitted token: ' . (empty($token) ? 'EMPTY/NULL' : substr($token, 0, 16) . '...'));
        error_log('  Session token: ' . ($sessionToken ? substr($sessionToken, 0, 16) . '...' : 'NULL'));
        error_log('  Session ID: ' . session_id());
        error_log('  All POST data: ' . json_encode($request->post()));

        $result = self::validateToken($token);
        error_log('  Validation result: ' . ($result ? 'PASS' : 'FAIL'));

        return $result;
    }
}
