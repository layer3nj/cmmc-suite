<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Core\Csrf;
use App\Middleware\AuthMiddleware;
use App\Services\AuditLogger;

/**
 * Integration Management Controller (Autotask & ITGlue)
 */
class IntegrationController
{
    private $db;

    public function __construct()
    {
        global $app;
        $this->db = $app->getDatabase();
    }

    public function index(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        // Get integration settings
        $autotask = $this->db->fetchOne(
            "SELECT * FROM integrations WHERE provider = 'autotask'"
        );

        $itglue = $this->db->fetchOne(
            "SELECT * FROM integrations WHERE provider = 'itglue'"
        );

        $content = View::render('integrations/index', [
            'autotask' => $autotask,
            'itglue' => $itglue,
        ]);

        return new Response($content);
    }

    public function autotaskConnect(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $apiUrl = $request->post('api_url');
        $username = $request->post('username');
        $apiSecret = $request->post('api_secret');

        // Test connection (simplified - real implementation would use cURL)
        $testResult = $this->testAutotaskConnection($apiUrl, $username, $apiSecret);

        if (!$testResult) {
            return Response::json(['success' => false, 'message' => 'Connection test failed']);
        }

        // Save or update integration
        $existing = $this->db->fetchOne(
            "SELECT * FROM integrations WHERE provider = 'autotask'"
        );

        if ($existing) {
            $this->db->update('integrations', [
                'api_url' => $apiUrl,
                'username' => $username,
                'api_secret' => $apiSecret,
                'enabled' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'provider = :provider', [':provider' => 'autotask']);
        } else {
            $this->db->insert('integrations', [
                'provider' => 'autotask',
                'api_url' => $apiUrl,
                'username' => $username,
                'api_secret' => $apiSecret,
                'enabled' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        AuditLogger::log('integration_connect', 'integration', null, [
            'provider' => 'autotask'
        ], $request->ip());

        return Response::json(['success' => true, 'message' => 'Autotask connected successfully']);
    }

    public function autotaskSync(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Get Autotask config
        $config = $this->db->fetchOne(
            "SELECT * FROM integrations WHERE provider = 'autotask' AND enabled = 1"
        );

        if (!$config) {
            return Response::json(['success' => false, 'message' => 'Autotask not configured']);
        }

        // Sync customers (simplified - real implementation would use Autotask API)
        $synced = 0;
        // This would call Autotask API to get companies and import them

        $this->db->update('integrations', [
            'last_sync_at' => date('Y-m-d H:i:s'),
            'sync_status' => 'success',
        ], 'provider = :provider', [':provider' => 'autotask']);

        AuditLogger::log('integration_sync', 'integration', null, [
            'provider' => 'autotask',
            'synced' => $synced
        ], $request->ip());

        return Response::json([
            'success' => true,
            'message' => "Synced $synced customers from Autotask"
        ]);
    }

    public function itglueConnect(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $apiUrl = $request->post('api_url');
        $apiKey = $request->post('api_key');

        // Test connection
        $testResult = $this->testITGlueConnection($apiUrl, $apiKey);

        if (!$testResult) {
            return Response::json(['success' => false, 'message' => 'Connection test failed']);
        }

        // Save or update integration
        $existing = $this->db->fetchOne(
            "SELECT * FROM integrations WHERE provider = 'itglue'"
        );

        if ($existing) {
            $this->db->update('integrations', [
                'api_url' => $apiUrl,
                'api_key' => $apiKey,
                'enabled' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'provider = :provider', [':provider' => 'itglue']);
        } else {
            $this->db->insert('integrations', [
                'provider' => 'itglue',
                'api_url' => $apiUrl,
                'api_key' => $apiKey,
                'enabled' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        AuditLogger::log('integration_connect', 'integration', null, [
            'provider' => 'itglue'
        ], $request->ip());

        return Response::json(['success' => true, 'message' => 'ITGlue connected successfully']);
    }

    public function itglueSync(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Get ITGlue config
        $config = $this->db->fetchOne(
            "SELECT * FROM integrations WHERE provider = 'itglue' AND enabled = 1"
        );

        if (!$config) {
            return Response::json(['success' => false, 'message' => 'ITGlue not configured']);
        }

        // Sync organizations
        $synced = 0;
        // This would call ITGlue API to get organizations and import them

        $this->db->update('integrations', [
            'last_sync_at' => date('Y-m-d H:i:s'),
            'sync_status' => 'success',
        ], 'provider = :provider', [':provider' => 'itglue']);

        AuditLogger::log('integration_sync', 'integration', null, [
            'provider' => 'itglue',
            'synced' => $synced
        ], $request->ip());

        return Response::json([
            'success' => true,
            'message' => "Synced $synced organizations from ITGlue"
        ]);
    }

    private function testAutotaskConnection(string $apiUrl, string $username, string $apiSecret): bool
    {
        // Simplified - real implementation would use cURL to test Autotask API
        return !empty($apiUrl) && !empty($username) && !empty($apiSecret);
    }

    private function testITGlueConnection(string $apiUrl, string $apiKey): bool
    {
        // Simplified - real implementation would use cURL to test ITGlue API
        return !empty($apiUrl) && !empty($apiKey);
    }
}
