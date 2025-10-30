<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Core\Csrf;
use App\Services\AuditLogger;

/**
 * Authentication Controller - Local Login
 */
class AuthController
{
    private $db;
    private $config;

    public function __construct()
    {
        global $app;
        $this->db = $app->getDatabase();
        $this->config = $app->getConfig();
    }

    public function showLogin(Request $request): Response
    {
        // If already logged in, redirect to dashboard
        Session::start();
        if (Session::has('user_id')) {
            return Response::redirect($request->baseUrl() . '/');
        }

        $samlEnabled = $this->config->get('saml.enabled', false);

        // Load settings for branding
        $settingsService = new \App\Services\SettingsService($this->db);
        $settings = $settingsService->getAll();

        $content = View::render('auth/login', [
            'saml_enabled' => $samlEnabled,
            'base_url' => $request->baseUrl(),
            'app_name' => $settings['site_name'] ?? 'Layer3 | Trident Cyber OneComply',
            'app_logo' => $settings['logo_path'] ?? null,
            'primary_color' => $settings['primary_color'] ?? '#667eea',
            'secondary_color' => $settings['secondary_color'] ?? '#764ba2',
        ]);

        return new Response($content);
    }

    public function login(Request $request): Response
    {
        Session::start();

        // Validate CSRF token
        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token. Please try again.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        $email = $request->post('email');
        $password = $request->post('password');

        // Validate input
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email and password are required.');
            Session::flash('old_email', $email);
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Rate limiting check (simple implementation)
        $this->checkRateLimit($request->ip());

        // Find user
        $user = $this->db->fetchOne(
            'SELECT * FROM users WHERE email = ?',
            [$email]
        );

        if (!$user) {
            $this->recordFailedLogin($email, $request->ip());
            Session::flash('error', 'Invalid email or password.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Check if user has password (could be SAML-only)
        if (empty($user['password_hash'])) {
            Session::flash('error', 'This account uses SSO. Please sign in with Microsoft.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            $this->recordFailedLogin($email, $request->ip());
            Session::flash('error', 'Invalid email or password.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Successful login
        $this->performLogin($user, $request);

        // Redirect to intended page or dashboard
        $redirect = Session::get('intended_url', $request->baseUrl() . '/');
        Session::remove('intended_url');

        return Response::redirect($redirect);
    }

    public function logout(Request $request): Response
    {
        Session::start();

        if (Session::has('user_id')) {
            // Log the logout
            AuditLogger::log('logout', 'user', Session::get('user_id'), null, $request->ip());
        }

        Session::destroy();

        return Response::redirect($request->baseUrl() . '/login');
    }

    private function performLogin(array $user, Request $request): void
    {
        Session::regenerate();

        // Set session data
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_name', $user['display_name']);
        Session::set('user_role', $user['role']);

        // Update last login
        $this->db->update(
            'users',
            ['last_login_at' => date('Y-m-d H:i:s')],
            'id = :id',
            [':id' => $user['id']]
        );

        // Log successful login
        AuditLogger::log('login', 'user', $user['id'], [
            'method' => 'local',
            'email' => $user['email']
        ], $request->ip());
    }

    private function checkRateLimit(string $ip): void
    {
        // Simple rate limiting: check failed login attempts in last 15 minutes
        $attempts = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM audit_log
             WHERE action = 'failed_login'
             AND ip = ?
             AND ts > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
            [$ip]
        );

        if ($attempts >= 5) {
            http_response_code(429);
            die('Too many failed login attempts. Please try again in 15 minutes.');
        }
    }

    private function recordFailedLogin(string $email, string $ip): void
    {
        AuditLogger::log('failed_login', 'user', null, [
            'email' => $email
        ], $ip);
    }
}
