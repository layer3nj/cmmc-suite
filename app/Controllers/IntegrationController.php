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
            'autotask_connected' => $autotask && $autotask['enabled'],
            'autotask_config' => $autotask ?: [],
            'autotask_last_sync' => $autotask['last_sync_at'] ?? null,
            'itglue_connected' => $itglue && $itglue['enabled'],
            'itglue_config' => $itglue ?: [],
            'itglue_last_sync' => $itglue['last_sync_at'] ?? null,
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
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/integrations');
        }

        $apiUrl = $request->post('api_url');
        $username = $request->post('username');
        $apiSecret = $request->post('secret');
        $integrationCode = $request->post('integration_code');

        // Test connection
        $testResult = $this->testAutotaskConnection($apiUrl, $username, $apiSecret, $integrationCode);

        if (!$testResult) {
            Session::flash('error', 'Connection test failed. Please check your credentials.');
            return Response::redirect($request->baseUrl() . '/integrations');
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
                'api_key' => $integrationCode,
                'enabled' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', [':id' => $existing['id']]);
        } else {
            $this->db->insert('integrations', [
                'provider' => 'autotask',
                'api_url' => $apiUrl,
                'username' => $username,
                'api_secret' => $apiSecret,
                'api_key' => $integrationCode,
                'enabled' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        AuditLogger::log('integration_connect', 'integration', null, [
            'provider' => 'autotask'
        ], $request->ip());

        Session::flash('success', 'Autotask connected successfully');
        return Response::redirect($request->baseUrl() . '/integrations');
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

        try {
            // Call Autotask API to get companies
            $companies = $this->fetchAutotaskCompanies($config);

            $synced = 0;
            $skipped = 0;

            foreach ($companies as $company) {
                // Check if client already exists
                $existing = $this->db->fetchOne(
                    "SELECT id FROM clients WHERE name = ? OR external_id = ?",
                    [$company['name'], 'autotask_' . $company['id']]
                );

                if ($existing) {
                    $skipped++;
                    continue;
                }

                // Import as new client
                $this->db->insert('clients', [
                    'name' => $company['name'],
                    'external_id' => 'autotask_' . $company['id'],
                    'integration_source' => 'autotask',
                    'contact_name' => $company['primaryContact'] ?? null,
                    'contact_email' => $company['email'] ?? null,
                    'contact_phone' => $company['phone'] ?? null,
                    'address' => $company['address'] ?? null,
                    'active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $synced++;
            }

            $this->db->update('integrations', [
                'last_sync_at' => date('Y-m-d H:i:s'),
                'sync_status' => 'success',
                'sync_message' => "Synced $synced, skipped $skipped existing",
            ], 'provider = :provider', [':provider' => 'autotask']);

            AuditLogger::log('integration_sync', 'integration', null, [
                'provider' => 'autotask',
                'synced' => $synced,
                'skipped' => $skipped
            ], $request->ip());

            return Response::json([
                'success' => true,
                'message' => "Successfully synced $synced clients from Autotask ($skipped already existed)"
            ]);

        } catch (\Exception $e) {
            $this->db->update('integrations', [
                'last_sync_at' => date('Y-m-d H:i:s'),
                'sync_status' => 'error',
                'sync_message' => $e->getMessage(),
            ], 'provider = :provider', [':provider' => 'autotask']);

            return Response::json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ]);
        }
    }

    public function itglueConnect(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/integrations');
        }

        $apiUrl = $request->post('api_url');
        $apiKey = $request->post('api_key');

        // Test connection
        $testResult = $this->testITGlueConnection($apiUrl, $apiKey);

        if (!$testResult) {
            Session::flash('error', 'Connection test failed. Please check your API key.');
            return Response::redirect($request->baseUrl() . '/integrations');
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
            ], 'id = :id', [':id' => $existing['id']]);
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

        Session::flash('success', 'ITGlue connected successfully');
        return Response::redirect($request->baseUrl() . '/integrations');
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

        try {
            // Call ITGlue API to get organizations
            $organizations = $this->fetchITGlueOrganizations($config);

            $synced = 0;
            $skipped = 0;

            foreach ($organizations as $org) {
                // Check if client already exists
                $existing = $this->db->fetchOne(
                    "SELECT id FROM clients WHERE name = ? OR external_id = ?",
                    [$org['name'], 'itglue_' . $org['id']]
                );

                if ($existing) {
                    $skipped++;
                    continue;
                }

                // Import as new client
                $this->db->insert('clients', [
                    'name' => $org['name'],
                    'external_id' => 'itglue_' . $org['id'],
                    'integration_source' => 'itglue',
                    'contact_name' => $org['primaryContact'] ?? null,
                    'contact_email' => $org['email'] ?? null,
                    'contact_phone' => $org['phone'] ?? null,
                    'address' => $org['address'] ?? null,
                    'active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $synced++;
            }

            $this->db->update('integrations', [
                'last_sync_at' => date('Y-m-d H:i:s'),
                'sync_status' => 'success',
                'sync_message' => "Synced $synced, skipped $skipped existing",
            ], 'provider = :provider', [':provider' => 'itglue']);

            AuditLogger::log('integration_sync', 'integration', null, [
                'provider' => 'itglue',
                'synced' => $synced,
                'skipped' => $skipped
            ], $request->ip());

            return Response::json([
                'success' => true,
                'message' => "Successfully synced $synced clients from ITGlue ($skipped already existed)"
            ]);

        } catch (\Exception $e) {
            $this->db->update('integrations', [
                'last_sync_at' => date('Y-m-d H:i:s'),
                'sync_status' => 'error',
                'sync_message' => $e->getMessage(),
            ], 'provider = :provider', [':provider' => 'itglue']);

            return Response::json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ]);
        }
    }

    private function testAutotaskConnection(string $apiUrl, string $username, string $apiSecret, string $integrationCode): bool
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, rtrim($apiUrl, '/') . '/v1.0/Companies/query?search={"filter":[{"field":"id","op":"gt","value":0}]}&pagesize=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'ApiIntegrationCode: ' . $integrationCode,
                'UserName: ' . $username,
                'Secret: ' . $apiSecret,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode >= 200 && $httpCode < 300;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function testITGlueConnection(string $apiUrl, string $apiKey): bool
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, rtrim($apiUrl, '/') . '/organizations?page[size]=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'x-api-key: ' . $apiKey,
                'Content-Type: application/vnd.api+json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode >= 200 && $httpCode < 300;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function fetchAutotaskCompanies(array $config): array
    {
        $companies = [];
        $pageSize = 500;
        $page = 1;
        $hasMore = true;

        while ($hasMore) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, rtrim($config['api_url'], '/') . '/v1.0/Companies/query?search={"filter":[{"field":"isActive","op":"eq","value":true}]}&pagesize=' . $pageSize);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'ApiIntegrationCode: ' . $config['api_key'],
                'UserName: ' . $config['username'],
                'Secret: ' . $config['api_secret'],
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new \Exception('Autotask API returned status ' . $httpCode);
            }

            $data = json_decode($response, true);
            if (!isset($data['items'])) {
                break;
            }

            foreach ($data['items'] as $item) {
                $companies[] = [
                    'id' => $item['id'],
                    'name' => $item['companyName'] ?? 'Unknown',
                    'primaryContact' => $item['primaryContact'] ?? null,
                    'email' => $item['email'] ?? null,
                    'phone' => $item['phone'] ?? null,
                    'address' => $this->formatAddress($item),
                ];
            }

            $hasMore = isset($data['pageDetails']) && $data['pageDetails']['nextPageUrl'];
            $page++;

            if ($page > 10) break; // Safety limit
        }

        return $companies;
    }

    private function fetchITGlueOrganizations(array $config): array
    {
        $organizations = [];
        $pageSize = 100;
        $page = 1;
        $hasMore = true;

        while ($hasMore) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, rtrim($config['api_url'], '/') . '/organizations?page[number]=' . $page . '&page[size]=' . $pageSize);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'x-api-key: ' . $config['api_key'],
                'Content-Type: application/vnd.api+json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new \Exception('ITGlue API returned status ' . $httpCode);
            }

            $data = json_decode($response, true);
            if (!isset($data['data']) || empty($data['data'])) {
                break;
            }

            foreach ($data['data'] as $item) {
                $attr = $item['attributes'] ?? [];
                $organizations[] = [
                    'id' => $item['id'],
                    'name' => $attr['name'] ?? 'Unknown',
                    'primaryContact' => $attr['primary-contact-name'] ?? null,
                    'email' => $attr['primary-contact-email'] ?? null,
                    'phone' => $attr['phone'] ?? null,
                    'address' => $this->formatITGlueAddress($attr),
                ];
            }

            $hasMore = isset($data['links']['next']);
            $page++;

            if ($page > 10) break; // Safety limit
        }

        return $organizations;
    }

    private function formatAddress(array $data): ?string
    {
        $parts = [];
        if (!empty($data['address1'])) $parts[] = $data['address1'];
        if (!empty($data['address2'])) $parts[] = $data['address2'];
        if (!empty($data['city'])) $parts[] = $data['city'];
        if (!empty($data['state'])) $parts[] = $data['state'];
        if (!empty($data['zipCode'])) $parts[] = $data['zipCode'];

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    private function formatITGlueAddress(array $attr): ?string
    {
        $parts = [];
        if (!empty($attr['address-1'])) $parts[] = $attr['address-1'];
        if (!empty($attr['address-2'])) $parts[] = $attr['address-2'];
        if (!empty($attr['city'])) $parts[] = $attr['city'];
        if (!empty($attr['region-name'])) $parts[] = $attr['region-name'];
        if (!empty($attr['postal-code'])) $parts[] = $attr['postal-code'];

        return !empty($parts) ? implode(', ', $parts) : null;
    }
}
