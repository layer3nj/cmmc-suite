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
        if (!$this->config->get('saml.enabled', false)) {
            return Response::redirect($request->baseUrl() . '/login');
        }

        // In production, this would initialize OneLogin SAML toolkit
        // For now, redirect to IdP with basic SAML request
        $idpUrl = $this->config->get('saml.idp_metadata_url');
        $acsUrl = $this->config->get('saml.acs_url');

        // Store SAML request ID in session
        Session::start();
        $requestId = bin2hex(random_bytes(16));
        Session::set('saml_request_id', $requestId);

        // Build SAML request (simplified - production would use proper XML)
        $samlRequest = base64_encode('<?xml version="1.0"?><samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="' . $requestId . '" Version="2.0" IssueInstant="' . gmdate('Y-m-d\TH:i:s\Z') . '" AssertionConsumerServiceURL="' . $acsUrl . '"></samlp:AuthnRequest>');

        // Redirect to IdP
        return Response::redirect($idpUrl . '?SAMLRequest=' . urlencode($samlRequest));
    }

    public function acs(Request $request): Response
    {
        Session::start();

        if (!$this->config->get('saml.enabled', false)) {
            Session::flash('error', 'SAML SSO is not enabled.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // In production, this would validate SAML response using OneLogin toolkit
        // For now, we'll accept a simplified flow for demonstration

        $samlResponse = $request->post('SAMLResponse');

        if (empty($samlResponse)) {
            Session::flash('error', 'Invalid SAML response.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Decode and parse SAML response (simplified)
        $decoded = base64_decode($samlResponse);

        // Extract user attributes (in production, use proper XML parsing with signature validation)
        // This is a placeholder - production must use OneLogin SAML toolkit
        $email = $this->extractAttributeFromSaml($decoded, 'email');
        $displayName = $this->extractAttributeFromSaml($decoded, 'displayName');
        $groups = $this->extractAttributeFromSaml($decoded, 'groups');

        if (empty($email)) {
            Session::flash('error', 'Email not provided in SAML assertion.');
            return Response::redirect($request->baseUrl() . '/login');
        }

        // Find or create user (Just-In-Time provisioning)
        $user = $this->db->fetchOne('SELECT * FROM users WHERE email = ?', [$email]);

        if (!$user) {
            // Create new user from SAML assertion
            $role = $this->mapGroupsToRole($groups);

            $userId = $this->db->insert('users', [
                'email' => $email,
                'display_name' => $displayName ?: $email,
                'role' => $role,
                'saml_subject' => $email,
                'password_hash' => null, // SAML-only account
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $user = $this->db->fetchOne('SELECT * FROM users WHERE id = ?', [$userId]);

            AuditLogger::log('user_created', 'user', $userId, [
                'method' => 'saml_jit',
                'email' => $email
            ], $request->ip());
        } else {
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

        AuditLogger::log('login', 'user', $user['id'], [
            'method' => 'saml',
            'email' => $user['email']
        ], $request->ip());

        return Response::redirect($request->baseUrl() . '/');
    }

    public function metadata(Request $request): Response
    {
        $entityId = $this->config->get('saml.entity_id', $request->baseUrl());
        $acsUrl = $this->config->get('saml.acs_url', $request->baseUrl() . '/saml/acs');

        $xml = '<?xml version="1.0"?>
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="' . htmlspecialchars($entityId) . '">
    <md:SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . htmlspecialchars($acsUrl) . '" index="0"/>
    </md:SPSSODescriptor>
</md:EntityDescriptor>';

        return new Response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function extractAttributeFromSaml(string $xml, string $attribute): ?string
    {
        // Simplified attribute extraction - production must use proper XML parsing
        // with signature validation via OneLogin SAML toolkit

        // This is a placeholder for demonstration
        return null;
    }

    private function mapGroupsToRole(array $groups): string
    {
        // Map Azure AD groups to application roles
        $groupMapping = $this->config->get('saml.group_mapping', [
            'CMMC-Admins' => 'admin',
            'CMMC-Auditors' => 'auditor',
            'CMMC-Contributors' => 'contributor',
        ]);

        foreach ($groups as $group) {
            if (isset($groupMapping[$group])) {
                return $groupMapping[$group];
            }
        }

        return 'viewer'; // Default role
    }
}
