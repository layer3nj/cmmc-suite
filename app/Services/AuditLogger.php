<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Session;

/**
 * Audit Logger Service
 * Records all security-relevant actions to the audit log
 */
class AuditLogger
{
    public static function log(string $action, string $entity, ?int $entityId, ?array $delta, string $ip): void
    {
        global $app;
        $db = $app->getDatabase();

        Session::start();
        $userId = Session::get('user_id');

        $db->insert('audit_log', [
            'ts' => date('Y-m-d H:i:s'),
            'user_id' => $userId,
            'ip' => $ip,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'delta' => $delta ? json_encode($delta) : null,
        ]);
    }

    public static function logChange(string $entity, int $entityId, array $oldData, array $newData, string $ip): void
    {
        $changes = [];

        foreach ($newData as $key => $value) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            self::log('update', $entity, $entityId, $changes, $ip);
        }
    }
}
