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
 * Bulk Operations Controller
 * Handles bulk operations across multiple clients for efficiency
 */
class BulkOperationsController
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

        // Get all active clients for selection
        $clients = $this->db->fetchAll(
            "SELECT id, name FROM clients WHERE active = 1 ORDER BY name ASC"
        );

        // Get all assessments for cloning
        $assessments = $this->db->fetchAll(
            "SELECT a.*, c.name as client_name
             FROM assessments a
             JOIN clients c ON a.customer_id = c.id
             WHERE c.active = 1
             ORDER BY a.created_at DESC
             LIMIT 100"
        );

        $content = View::render('bulk/index', [
            'clients' => $clients,
            'assessments' => $assessments,
        ]);

        return new Response($content);
    }

    /**
     * Clone an assessment to multiple clients
     */
    public function cloneAssessment(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $sourceAssessmentId = $request->post('source_assessment_id');
        $targetClientIds = $request->post('target_client_ids', []);

        if (empty($sourceAssessmentId) || empty($targetClientIds)) {
            return Response::json(['success' => false, 'message' => 'Please select source assessment and target clients']);
        }

        try {
            // Get source assessment
            $sourceAssessment = $this->db->fetchOne(
                "SELECT * FROM assessments WHERE id = ?",
                [$sourceAssessmentId]
            );

            if (!$sourceAssessment) {
                return Response::json(['success' => false, 'message' => 'Source assessment not found']);
            }

            // Get all responses for source assessment
            $sourceResponses = $this->db->fetchAll(
                "SELECT * FROM assessment_responses WHERE assessment_id = ?",
                [$sourceAssessmentId]
            );

            $clonedCount = 0;

            foreach ($targetClientIds as $clientId) {
                // Create new assessment for target client
                $newAssessmentData = [
                    'customer_id' => $clientId,
                    'framework' => $sourceAssessment['framework'],
                    'target_level' => $sourceAssessment['target_level'],
                    'assessment_type' => $sourceAssessment['assessment_type'],
                    'assessed_by' => Session::get('user_name'),
                    'assessed_at' => date('Y-m-d H:i:s'),
                    'status' => 'draft', // Always create as draft
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $newAssessmentId = $this->db->insert('assessments', $newAssessmentData);

                // Clone all responses
                foreach ($sourceResponses as $response) {
                    $this->db->insert('assessment_responses', [
                        'assessment_id' => $newAssessmentId,
                        'control_code' => $response['control_code'],
                        'response' => $response['response'],
                        'notes' => $response['notes'],
                        'evidence' => $response['evidence'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $clonedCount++;
            }

            AuditLogger::log('bulk_clone_assessment', 'assessment', $sourceAssessmentId, [
                'target_clients' => count($targetClientIds),
                'cloned' => $clonedCount
            ], $request->ip());

            return Response::json([
                'success' => true,
                'message' => "Successfully cloned assessment to $clonedCount clients"
            ]);

        } catch (\Exception $e) {
            error_log('Bulk clone assessment error: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'message' => 'Error cloning assessment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batch update POA&M items
     */
    public function batchUpdatePoam(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $poamIds = $request->post('poam_ids', []);
        $updateField = $request->post('update_field');
        $updateValue = $request->post('update_value');

        if (empty($poamIds) || empty($updateField)) {
            return Response::json(['success' => false, 'message' => 'Please select items and update field']);
        }

        try {
            $allowedFields = ['status', 'responsible_party', 'planned_completion_date', 'priority'];

            if (!in_array($updateField, $allowedFields)) {
                return Response::json(['success' => false, 'message' => 'Invalid update field']);
            }

            $updated = 0;
            foreach ($poamIds as $poamId) {
                $this->db->update('poam_items', [
                    $updateField => $updateValue,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], 'id = :id', [':id' => $poamId]);
                $updated++;
            }

            AuditLogger::log('bulk_update_poam', 'poam', null, [
                'items_updated' => $updated,
                'field' => $updateField,
                'value' => $updateValue
            ], $request->ip());

            return Response::json([
                'success' => true,
                'message' => "Successfully updated $updated POA&M items"
            ]);

        } catch (\Exception $e) {
            error_log('Bulk POA&M update error: ' . $e->getMessage());
            return Response::json([
                'success' => false,
                'message' => 'Error updating POA&M items: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get POA&M items for bulk operations
     */
    public function getPoamItems(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $clientId = $request->get('client_id');
        $status = $request->get('status');

        $query = "SELECT p.*, c.name as client_name
                  FROM poam_items p
                  JOIN clients c ON p.customer_id = c.id
                  WHERE c.active = 1";

        $params = [];

        if ($clientId) {
            $query .= " AND p.customer_id = ?";
            $params[] = $clientId;
        }

        if ($status) {
            $query .= " AND p.status = ?";
            $params[] = $status;
        }

        $query .= " ORDER BY p.planned_completion_date ASC LIMIT 200";

        $items = $this->db->fetchAll($query, $params);

        return Response::json(['success' => true, 'items' => $items]);
    }
}
