<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuditLogger;

/**
 * SAML SSO Controller
 * Handles Microsoft Entra (Azure AD) authentication
 */
class SamlController
{
    private $db;
    private $config;

    public function __construct()
    {
        global $app;
        $this->db = $app->getDatabase();
        $this->config = $app->getConfig();
    }

    public function login(Request $request): Response
    {
        Session::start();

        // Load SAML settings from database
        $settingsService = new \App\Services\SettingsService($this->db);
        $settings = $settingsService->getAll();

        if (($settings['saml_enabled'] ?? '0') !== '1') {
            Session::flash('error', 'SAML SSO is not enabled.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        $idpSsoUrl = $settings['saml_idp_sso_url'] ?? '';
        $entityId = $settings['saml_idp_entity_id'] ?? '';

        if (empty($idpSsoUrl)) {
            Session::flash('error', 'SAML IdP SSO URL is not configured. Please contact your administrator.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Store SAML request ID in session
        $requestId = 'id-' . bin2hex(random_bytes(16));
        Session::set('saml_request_id', $requestId);

        // Build SAML AuthnRequest (simplified - production should use OneLogin SAML toolkit)
        $acsUrl = $request->baseUrl() . '/saml/acs';
        $spEntityId = $request->baseUrl();
        $issueInstant = gmdate('Y-m-d\TH:i:s\Z');

        $samlRequest = '<?xml version="1.0" encoding="UTF-8"?>
<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
                    ID="' . $requestId . '"
                    Version="2.0"
                    IssueInstant="' . $issueInstant . '"
                    Destination="' . htmlspecialchars($idpSsoUrl) . '"
                    AssertionConsumerServiceURL="' . htmlspecialchars($acsUrl) . '"
                    ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST">
    <saml:Issuer>' . htmlspecialchars($spEntityId) . '</saml:Issuer>
</samlp:AuthnRequest>';

        $encodedRequest = base64_encode(gzdeflate($samlRequest));

        // Redirect to IdP
        return Response::redirect($idpSsoUrl . '?SAMLRequest=' . urlencode($encodedRequest));
    }

    public function acs(Request $request): Response
    {
        Session::start();

        // Debug logging
        $logFile = BASE_PATH . '/storage/logs/saml_debug.log';
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        // Load SAML settings from database
        $settingsService = new \App\Services\SettingsService($this->db);
        $settings = $settingsService->getAll();

        if (($settings['saml_enabled'] ?? '0') !== '1') {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - SAML not enabled\n", FILE_APPEND);
            Session::flash('error', 'SAML SSO is not enabled.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        $samlResponse = $request->post('SAMLResponse');

        if (empty($samlResponse)) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - No SAMLResponse in POST\n", FILE_APPEND);
            Session::flash('error', 'Invalid SAML response - no SAMLResponse received.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Decode SAML response
        $decoded = base64_decode($samlResponse);

        if (!$decoded) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Failed to decode base64\n", FILE_APPEND);
            Session::flash('error', 'Failed to decode SAML response.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Log decoded XML for debugging
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Decoded SAML Response:\n" . $decoded . "\n\n", FILE_APPEND);

        // Parse XML response
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($decoded);

        if ($xml === false) {
            $errors = libxml_get_errors();
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - XML parsing failed: " . print_r($errors, true) . "\n", FILE_APPEND);
            Session::flash('error', 'Invalid SAML XML response.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Register SAML namespaces
        $xml->registerXPathNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
        $xml->registerXPathNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');

        // Extract email from SAML attributes
        $email = null;
        $displayName = null;

        // Try common email attribute names
        $emailPaths = [
            "//saml:Attribute[@Name='http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress']/saml:AttributeValue",
            "//saml:Attribute[@Name='email']/saml:AttributeValue",
            "//saml:Attribute[@Name='mail']/saml:AttributeValue",
            "//saml:NameID"
        ];

        foreach ($emailPaths as $path) {
            $result = $xml->xpath($path);
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Tried XPath: $path - Results: " . count($result) . "\n", FILE_APPEND);
            if ($result && count($result) > 0) {
                $email = (string) $result[0];
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Found email: $email\n", FILE_APPEND);
                break;
            }
        }

        // Try common display name attribute names
        $namePaths = [
            "//saml:Attribute[@Name='http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name']/saml:AttributeValue",
            "//saml:Attribute[@Name='displayName']/saml:AttributeValue",
            "//saml:Attribute[@Name='name']/saml:AttributeValue"
        ];

        foreach ($namePaths as $path) {
            $result = $xml->xpath($path);
            if ($result && count($result) > 0) {
                $displayName = (string) $result[0];
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Found display name: $displayName\n", FILE_APPEND);
                break;
            }
        }

        if (empty($email)) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - No email found in SAML response\n", FILE_APPEND);
            Session::flash('error', 'Email not provided in SAML assertion. Please contact your administrator.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Find or create user (Just-In-Time provisioning)
        $user = $this->db->fetchOne('SELECT * FROM users WHERE email = ?', [$email]);

        if (!$user) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Creating new user for: $email\n", FILE_APPEND);
            // Create new user from SAML assertion
            try {
                $userId = $this->db->insert('users', [
                    'email' => $email,
                    'display_name' => $displayName ?: $email,
                    'role' => 'viewer', // Default role for new SAML users
                    'saml_subject' => $email,
                    'password_hash' => null, // SAML-only account
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                file_put_contents($logFile, date('Y-m-d H:i:s') . " - User created with ID: $userId\n", FILE_APPEND);

                $user = $this->db->fetchOne('SELECT * FROM users WHERE id = ?', [$userId]);

                AuditLogger::log('user_created', 'user', $userId, [
                    'method' => 'saml_jit',
                    'email' => $email
                ], $request->ip());
            } catch (\Exception $e) {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Failed to create user: " . $e->getMessage() . "\n", FILE_APPEND);
                Session::flash('error', 'Failed to create user account. Please contact your administrator.');
                return Response::redirect($request->baseUrl() . '/login');
            }
        } else {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Existing user found: {$user['id']}\n", FILE_APPEND);
            // Update last login
            $this->db->update(
                'users',
                ['last_login_at' => date('Y-m-d H:i:s')],
                'id = :id',
                [':id' => $user['id']]
            );
        }

        // Log in the user
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_name', $user['display_name']);
        Session::set('user_role', $user['role']);

        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Session created for user {$user['id']}, redirecting to dashboard\n", FILE_APPEND);

        AuditLogger::log('login', 'user', $user['id'], [
            'method' => 'saml',
            'email' => $user['email']
        ], $request->ip());

        return Response::redirect($request->baseUrl() . '/');
    }

    public function metadata(Request $request): Response
    {
        $entityId = $request->baseUrl();
        $acsUrl = $request->baseUrl() . '/saml/acs';

        $xml = '<?xml version="1.0"?>
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="' . htmlspecialchars($entityId) . '">
    <md:SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . htmlspecialchars($acsUrl) . '" index="0"/>
    </md:SPSSODescriptor>
</md:EntityDescriptor>';

        return new Response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
