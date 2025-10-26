<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

/**
 * Authentication Middleware
 * Ensures user is logged in before accessing protected routes
 */
class AuthMiddleware
{
    public static function handle(Request $request): ?Response
    {
        Session::start();

        if (!Session::has('user_id')) {
            // Store intended URL
            Session::set('intended_url', $request->url());

            // Redirect to login
            return Response::redirect($request->baseUrl() . '/login');
        }

        return null; // Allow request to continue
    }

    public static function requireRole(string $role): ?Response
    {
        Session::start();

        if (!Session::has('user_id')) {
            return Response::redirect('/login');
        }

        $userRole = Session::get('user_role');
        $roleHierarchy = ['viewer' => 1, 'contributor' => 2, 'auditor' => 3, 'admin' => 4];

        if (!isset($roleHierarchy[$userRole]) || !isset($roleHierarchy[$role])) {
            return new Response('Forbidden', 403);
        }

        if ($roleHierarchy[$userRole] < $roleHierarchy[$role]) {
            return new Response('Insufficient permissions', 403);
        }

        return null;
    }

    public static function checkPermission(string $permission): bool
    {
        Session::start();

        if (!Session::has('user_id')) {
            return false;
        }

        $role = Session::get('user_role');

        // Define role permissions
        $permissions = [
            'admin' => ['*'],
            'auditor' => ['view_all', 'create_assessment', 'edit_assessment', 'view_reports', 'export_data'],
            'contributor' => ['view_all', 'create_assessment', 'edit_assessment', 'view_reports'],
            'viewer' => ['view_all', 'view_reports'],
        ];

        if (!isset($permissions[$role])) {
            return false;
        }

        $rolePermissions = $permissions[$role];

        return in_array('*', $rolePermissions) || in_array($permission, $rolePermissions);
    }
}
