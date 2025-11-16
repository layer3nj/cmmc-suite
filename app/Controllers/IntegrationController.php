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

        // Prepare view variables
        $autotask_connected = $autotask && $autotask['enabled'];
        $autotask_config = $autotask ?: [];
        $autotask_last_sync = $autotask['last_sync_at'] ?? null;

        $itglue_connected = $itglue && $itglue['enabled'];
        $itglue_config = $itglue ?: [];
        $itglue_last_sync = $itglue['last_sync_at'] ?? null;

        $content = View::render('integrations/index', [
            'autotask_connected' => $autotask_connected,
            'autotask_config' => $autotask_config,
            'autotask_last_sync' => $autotask_last_sync,
            'itglue_connected' => $itglue_connected,
            'itglue_config' => $itglue_config,
            'itglue_last_sync' => $itglue_last_sync,
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
            error_log('Autotask Sync CSRF validation failed');
            error_log('Session ID: ' . session_id());
            error_log('Session data: ' . json_encode($_SESSION));
            error_log('POST data: ' . json_encode($request->post()));
            return Response::json(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.']);
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
            error_log('ITGlue Sync CSRF validation failed');
            error_log('Session ID: ' . session_id());
            error_log('Session data: ' . json_encode($_SESSION));
            error_log('POST data: ' . json_encode($request->post()));
            return Response::json(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.']);
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

    /**
     * Client Mapping Interface - allows selective import and mapping
     */
    public function clientMapping(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        // Get all existing OneComply clients for the mapping dropdown
        $onecomplyclients = $this->db->fetchAll(
            "SELECT id, name FROM clients WHERE active = 1 ORDER BY name ASC"
        );

        // Get all stored mappings
        $mappings = $this->db->fetchAll(
            "SELECT * FROM integration_client_mappings ORDER BY provider ASC, external_name ASC"
        );

        // Check integration status
        $autotask = $this->db->fetchOne(
            "SELECT * FROM integrations WHERE provider = 'autotask' AND enabled = 1"
        );

        $itglue = $this->db->fetchOne(
            "SELECT * FROM integrations WHERE provider = 'itglue' AND enabled = 1"
        );

        $content = View::render('integrations/client_mapping', [
            'mappings' => $mappings,
            'onecomply_clients' => $onecomplyclients,
            'autotask_enabled' => !empty($autotask),
            'itglue_enabled' => !empty($itglue),
        ]);

        return new Response($content);
    }

    /**
     * Fetch clients from Autotask and ITGlue APIs and populate mappings table
     */
    public function fetchClients(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/integrations/client-mapping');
        }

        $fetchedCount = 0;

        try {
            // Fetch from Autotask
            $autotaskConfig = $this->db->fetchOne(
                "SELECT * FROM integrations WHERE provider = 'autotask' AND enabled = 1"
            );

            if ($autotaskConfig) {
                $companies = $this->fetchAutotaskCompanies($autotaskConfig);

                foreach ($companies as $company) {
                    // Check if mapping already exists
                    $existing = $this->db->fetchOne(
                        "SELECT id FROM integration_client_mappings WHERE provider = 'autotask' AND external_id = ?",
                        [$company['id']]
                    );

                    if (!$existing) {
                        // Check if there's an existing OneComply client with matching name or autotask_company_id
                        $matchingClient = $this->db->fetchOne(
                            "SELECT id FROM clients WHERE name = ? OR autotask_company_id = ?",
                            [$company['name'], $company['id']]
                        );

                        $this->db->insert('integration_client_mappings', [
                            'provider' => 'autotask',
                            'external_id' => $company['id'],
                            'external_name' => $company['name'],
                            'external_data' => json_encode($company),
                            'onecomply_client_id' => $matchingClient ? $matchingClient['id'] : null,
                            'mapping_action' => $matchingClient ? 'map_existing' : 'ignore',
                            'autotask_company_id' => $company['id'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $fetchedCount++;
                    }
                }
            }

            // Fetch from ITGlue
            $itglueConfig = $this->db->fetchOne(
                "SELECT * FROM integrations WHERE provider = 'itglue' AND enabled = 1"
            );

            if ($itglueConfig) {
                $organizations = $this->fetchITGlueOrganizations($itglueConfig);

                foreach ($organizations as $org) {
                    // Check if mapping already exists
                    $existing = $this->db->fetchOne(
                        "SELECT id FROM integration_client_mappings WHERE provider = 'itglue' AND external_id = ?",
                        [$org['id']]
                    );

                    if (!$existing) {
                        // Check if there's an existing OneComply client with matching name or itglue_organization_id
                        $matchingClient = $this->db->fetchOne(
                            "SELECT id FROM clients WHERE name = ? OR itglue_organization_id = ?",
                            [$org['name'], $org['id']]
                        );

                        $this->db->insert('integration_client_mappings', [
                            'provider' => 'itglue',
                            'external_id' => $org['id'],
                            'external_name' => $org['name'],
                            'external_data' => json_encode($org),
                            'onecomply_client_id' => $matchingClient ? $matchingClient['id'] : null,
                            'mapping_action' => $matchingClient ? 'map_existing' : 'ignore',
                            'itglue_organization_id' => $org['id'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $fetchedCount++;
                    }
                }
            }

            Session::flash('success', "Successfully fetched $fetchedCount new clients from integrations.");
            return Response::redirect($request->baseUrl() . '/integrations/client-mapping');

        } catch (\Exception $e) {
            Session::flash('error', 'Failed to fetch clients: ' . $e->getMessage());
            return Response::redirect($request->baseUrl() . '/integrations/client-mapping');
        }
    }

    /**
     * Save user's mapping choices
     */
    public function saveMappings(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/integrations/client-mapping');
        }

        $mappingActions = $request->post('mapping_action', []);
        $onecomplyclients = $request->post('onecomply_client_id', []);

        $updatedCount = 0;

        foreach ($mappingActions as $mappingId => $action) {
            $onecomplyclientId = $onecomplyclients[$mappingId] ?? null;

            $data = [
                'mapping_action' => $action,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // If action is map_existing, set the client_id
            if ($action === 'map_existing' && !empty($onecomplyclientId)) {
                $data['onecomply_client_id'] = $onecomplyclientId;
            } elseif ($action === 'ignore') {
                $data['onecomply_client_id'] = null;
            }

            $this->db->update('integration_client_mappings', $data, 'id = :id', [':id' => $mappingId]);
            $updatedCount++;
        }

        AuditLogger::log('integration_mapping', 'integration', null, [
            'updated_count' => $updatedCount
        ], $request->ip());

        Session::flash('success', "Successfully updated $updatedCount client mappings.");
        return Response::redirect($request->baseUrl() . '/integrations/client-mapping');
    }

    /**
     * Sync clients based on saved mappings
     */
    public function syncMappedClients(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        try {
            $created = 0;
            $updated = 0;
            $ignored = 0;

            // Get all mappings that need to be processed
            $mappings = $this->db->fetchAll(
                "SELECT * FROM integration_client_mappings WHERE mapping_action IN ('map_existing', 'create_new')"
            );

            foreach ($mappings as $mapping) {
                $externalData = json_decode($mapping['external_data'], true);

                if ($mapping['mapping_action'] === 'create_new') {
                    // Create new client
                    $clientData = [
                        'name' => $mapping['external_name'],
                        'external_id' => $mapping['provider'] . '_' . $mapping['external_id'],
                        'integration_source' => $mapping['provider'],
                        'contact_name' => $externalData['primaryContact'] ?? null,
                        'contact_email' => $externalData['email'] ?? null,
                        'contact_phone' => $externalData['phone'] ?? null,
                        'address' => $externalData['address'] ?? null,
                        'active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    // Add provider-specific IDs
                    if ($mapping['provider'] === 'autotask') {
                        $clientData['autotask_company_id'] = $mapping['external_id'];
                    } elseif ($mapping['provider'] === 'itglue') {
                        $clientData['itglue_organization_id'] = $mapping['external_id'];
                    }

                    $newClientId = $this->db->insert('clients', $clientData);

                    // Update mapping with new client ID
                    $this->db->update('integration_client_mappings', [
                        'onecomply_client_id' => $newClientId,
                        'synced' => 1,
                        'synced_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], 'id = :id', [':id' => $mapping['id']]);

                    $created++;

                } elseif ($mapping['mapping_action'] === 'map_existing' && !empty($mapping['onecomply_client_id'])) {
                    // Update existing client with integration IDs
                    $updateData = [
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    if ($mapping['provider'] === 'autotask' && !empty($mapping['autotask_company_id'])) {
                        $updateData['autotask_company_id'] = $mapping['autotask_company_id'];
                    } elseif ($mapping['provider'] === 'itglue' && !empty($mapping['itglue_organization_id'])) {
                        $updateData['itglue_organization_id'] = $mapping['itglue_organization_id'];
                    }

                    $this->db->update('clients', $updateData, 'id = :id', [':id' => $mapping['onecomply_client_id']]);

                    // Mark mapping as synced
                    $this->db->update('integration_client_mappings', [
                        'synced' => 1,
                        'synced_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], 'id = :id', [':id' => $mapping['id']]);

                    $updated++;
                }
            }

            // Count ignored
            $ignored = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM integration_client_mappings WHERE mapping_action = 'ignore'"
            );

            AuditLogger::log('integration_sync_mapped', 'integration', null, [
                'created' => $created,
                'updated' => $updated,
                'ignored' => $ignored
            ], $request->ip());

            return Response::json([
                'success' => true,
                'message' => "Sync complete: $created created, $updated updated, $ignored ignored"
            ]);

        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ]);
        }
    }
}
