<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Session;

/**
 * Client Access Service
 *
 * Manages client-level access control for users
 */
class ClientAccessService
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get all clients a user has access to
     */
    public function getUserClients(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, uca.access_level
             FROM clients c
             INNER JOIN user_client_access uca ON c.id = uca.client_id
             WHERE uca.user_id = ? AND c.active = 1
             ORDER BY c.name ASC",
            [$userId]
        );
    }

    /**
     * Check if user has access to a specific client
     */
    public function hasClientAccess(int $userId, int $clientId): bool
    {
        $access = $this->db->fetchOne(
            "SELECT id FROM user_client_access WHERE user_id = ? AND client_id = ?",
            [$userId, $clientId]
        );
        return $access !== null;
    }

    /**
     * Get user's access level for a specific client
     * Returns: 'read-only', 'read-write', or null if no access
     */
    public function getAccessLevel(int $userId, int $clientId): ?string
    {
        $access = $this->db->fetchOne(
            "SELECT access_level FROM user_client_access WHERE user_id = ? AND client_id = ?",
            [$userId, $clientId]
        );
        return $access ? $access['access_level'] : null;
    }

    /**
     * Check if user has write access to a client
     */
    public function hasWriteAccess(int $userId, int $clientId): bool
    {
        $level = $this->getAccessLevel($userId, $clientId);
        return $level === 'read-write';
    }

    /**
     * Grant user access to a client
     */
    public function grantAccess(int $userId, int $clientId, string $accessLevel = 'read-only'): void
    {
        // Check if already exists
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_client_access WHERE user_id = ? AND client_id = ?",
            [$userId, $clientId]
        );

        if ($existing) {
            // Update existing
            $this->db->update(
                'user_client_access',
                ['access_level' => $accessLevel, 'updated_at' => date('Y-m-d H:i:s')],
                'user_id = :user_id AND client_id = :client_id',
                [':user_id' => $userId, ':client_id' => $clientId]
            );
        } else {
            // Insert new
            $this->db->insert('user_client_access', [
                'user_id' => $userId,
                'client_id' => $clientId,
                'access_level' => $accessLevel,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Revoke user access to a client
     */
    public function revokeAccess(int $userId, int $clientId): void
    {
        $this->db->query(
            "DELETE FROM user_client_access WHERE user_id = ? AND client_id = ?",
            [$userId, $clientId]
        );
    }

    /**
     * Revoke all client access for a user
     */
    public function revokeAllAccess(int $userId): void
    {
        $this->db->query(
            "DELETE FROM user_client_access WHERE user_id = ?",
            [$userId]
        );
    }

    /**
     * Check if current session user has access to current client
     */
    public static function checkCurrentClientAccess(): bool
    {
        Session::start();
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        $clientId = Session::get('current_customer_id'); // Keep key for backward compatibility

        // Admins and auditors have access to all clients
        if (in_array($userRole, ['admin', 'auditor'])) {
            return true;
        }

        // Contributors need client-specific access
        if (!$userId || !$clientId) {
            return false;
        }

        global $app;
        $db = $app->getDatabase();
        $service = new self($db);

        return $service->hasClientAccess($userId, $clientId);
    }

    /**
     * Check if current session user has write access to current client
     */
    public static function checkCurrentClientWriteAccess(): bool
    {
        Session::start();
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        $clientId = Session::get('current_customer_id');

        // Admins always have write access
        if ($userRole === 'admin') {
            return true;
        }

        // Auditors have read-only access
        if ($userRole === 'auditor') {
            return false;
        }

        if (!$userId || !$clientId) {
            return false;
        }

        global $app;
        $db = $app->getDatabase();
        $service = new self($db);

        return $service->hasWriteAccess($userId, $clientId);
    }
}
