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
 * Risk Assessment Controller
 * Manages cybersecurity risk assessments
 */
class RiskAssessmentController
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

        Session::start();
        $customerId = Session::get('current_customer_id');

        if (!$customerId) {
            Session::flash('error', 'Please select a client first.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        // Get all assessments for this customer
        $assessments = $this->db->fetchAll(
            "SELECT ra.*, u.display_name as created_by_name
             FROM risk_assessments ra
             LEFT JOIN users u ON ra.created_by = u.id
             WHERE ra.client_id = ?
             ORDER BY ra.created_at DESC",
            [$customerId]
        );

        // Get customer name
        $customer = $this->db->fetchOne(
            "SELECT name FROM clients WHERE id = ?",
            [$customerId]
        );

        $content = View::render('risk-assessments/index', [
            'assessments' => $assessments,
            'customer' => $customer,
        ]);

        return new Response($content);
    }

    public function create(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        if (!$customerId) {
            Session::flash('error', 'Please select a client first.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        $content = View::render('risk-assessments/create', [
            'customer_id' => $customerId,
        ]);

        return new Response($content);
    }

    public function store(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/risk-assessments');
        }

        $customerId = Session::get('current_customer_id');
        $userId = Session::get('user_id');

        if (!$customerId) {
            Session::flash('error', 'Please select a client first.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        $title = $request->post('title');
        $description = $request->post('description');

        if (empty($title)) {
            Session::flash('error', 'Assessment title is required.');
            return Response::redirect($request->baseUrl() . '/risk-assessments/create');
        }

        // Create assessment
        $assessmentId = $this->db->insert('risk_assessments', [
            'client_id' => $customerId,
            'title' => $title,
            'description' => $description,
            'status' => 'in_progress',
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('create', 'risk_assessment', $assessmentId, [
            'title' => $title,
            'client_id' => $customerId
        ], $request->ip());

        Session::flash('success', 'Risk assessment created successfully.');
        return Response::redirect($request->baseUrl() . '/risk-assessments/' . $assessmentId . '/questionnaire');
    }

    public function questionnaire(Request $request, int $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        // Get assessment
        $assessment = $this->db->fetchOne(
            "SELECT * FROM risk_assessments WHERE id = ? AND client_id = ?",
            [$id, $customerId]
        );

        if (!$assessment) {
            return new Response('Assessment not found', 404);
        }

        // Get all active questions
        $questions = $this->db->fetchAll(
            "SELECT * FROM risk_assessment_questions
             WHERE active = 1
             ORDER BY category, order_number",
            []
        );

        // Get existing responses
        $responses = $this->db->fetchAll(
            "SELECT * FROM risk_assessment_responses WHERE assessment_id = ?",
            [$id]
        );

        // Index responses by question_id
        $responsesByQuestion = [];
        foreach ($responses as $response) {
            $responsesByQuestion[$response['question_id']] = $response;
        }

        // Group questions by category
        $questionsByCategory = [];
        foreach ($questions as $question) {
            $category = $question['category'];
            if (!isset($questionsByCategory[$category])) {
                $questionsByCategory[$category] = [];
            }
            $questionsByCategory[$category][] = $question;
        }

        // Calculate progress
        $totalQuestions = count($questions);
        $answeredQuestions = count($responses);
        $progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;

        $content = View::render('risk-assessments/questionnaire', [
            'assessment' => $assessment,
            'questions_by_category' => $questionsByCategory,
            'responses' => $responsesByQuestion,
            'progress' => $progress,
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
        ]);

        return new Response($content);
    }

    public function saveResponse(Request $request, int $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $customerId = Session::get('current_customer_id');

        // Verify assessment exists and belongs to customer
        $assessment = $this->db->fetchOne(
            "SELECT * FROM risk_assessments WHERE id = ? AND client_id = ?",
            [$id, $customerId]
        );

        if (!$assessment) {
            return Response::json(['success' => false, 'message' => 'Assessment not found']);
        }

        $questionId = $request->post('question_id');
        $responseValue = $request->post('response_value');
        $likelihood = $request->post('likelihood', 1);
        $impact = $request->post('impact', 1);
        $notes = $request->post('notes', '');

        // Calculate risk score (1-25, likelihood * impact)
        $riskScore = $likelihood * $impact;

        // Determine risk level
        if ($riskScore >= 15) {
            $riskLevel = 'critical';
        } elseif ($riskScore >= 10) {
            $riskLevel = 'high';
        } elseif ($riskScore >= 5) {
            $riskLevel = 'medium';
        } else {
            $riskLevel = 'low';
        }

        // Check if response already exists
        $existing = $this->db->fetchOne(
            "SELECT id FROM risk_assessment_responses WHERE assessment_id = ? AND question_id = ?",
            [$id, $questionId]
        );

        if ($existing) {
            // Update existing response
            $this->db->update('risk_assessment_responses', $existing['id'], [
                'response_value' => $responseValue,
                'likelihood' => $likelihood,
                'impact' => $impact,
                'risk_score' => $riskScore,
                'risk_level' => $riskLevel,
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            // Create new response
            $this->db->insert('risk_assessment_responses', [
                'assessment_id' => $id,
                'question_id' => $questionId,
                'response_value' => $responseValue,
                'likelihood' => $likelihood,
                'impact' => $impact,
                'risk_score' => $riskScore,
                'risk_level' => $riskLevel,
                'notes' => $notes,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Update assessment statistics
        $this->updateAssessmentStats($id);

        return Response::json(['success' => true, 'risk_level' => $riskLevel]);
    }

    public function complete(Request $request, int $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token');
            return Response::redirect($request->baseUrl() . '/risk-assessments/' . $id . '/questionnaire');
        }

        $customerId = Session::get('current_customer_id');

        // Verify assessment
        $assessment = $this->db->fetchOne(
            "SELECT * FROM risk_assessments WHERE id = ? AND client_id = ?",
            [$id, $customerId]
        );

        if (!$assessment) {
            return new Response('Assessment not found', 404);
        }

        // Update assessment status
        $this->db->update('risk_assessments', $id, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('complete', 'risk_assessment', $id, [], $request->ip());

        Session::flash('success', 'Risk assessment completed successfully.');
        return Response::redirect($request->baseUrl() . '/risk-assessments/' . $id . '/results');
    }

    public function results(Request $request, int $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        // Get assessment
        $assessment = $this->db->fetchOne(
            "SELECT ra.*, c.name as customer_name, u.display_name as created_by_name
             FROM risk_assessments ra
             LEFT JOIN clients c ON ra.client_id = c.id
             LEFT JOIN users u ON ra.created_by = u.id
             WHERE ra.id = ? AND ra.client_id = ?",
            [$id, $customerId]
        );

        if (!$assessment) {
            return new Response('Assessment not found', 404);
        }

        // Get responses with questions
        $responses = $this->db->fetchAll(
            "SELECT rar.*, raq.category, raq.subcategory, raq.question_text, raq.weight
             FROM risk_assessment_responses rar
             JOIN risk_assessment_questions raq ON rar.question_id = raq.id
             WHERE rar.assessment_id = ?
             ORDER BY rar.risk_score DESC, raq.category, raq.order_number",
            [$id]
        );

        // Group by category
        $responsesByCategory = [];
        foreach ($responses as $response) {
            $category = $response['category'];
            if (!isset($responsesByCategory[$category])) {
                $responsesByCategory[$category] = [];
            }
            $responsesByCategory[$category][] = $response;
        }

        // Get risk summary
        $riskSummary = $this->db->fetchOne(
            "SELECT
                COUNT(*) as total_risks,
                SUM(CASE WHEN risk_level = 'critical' THEN 1 ELSE 0 END) as critical_risks,
                SUM(CASE WHEN risk_level = 'high' THEN 1 ELSE 0 END) as high_risks,
                SUM(CASE WHEN risk_level = 'medium' THEN 1 ELSE 0 END) as medium_risks,
                SUM(CASE WHEN risk_level = 'low' THEN 1 ELSE 0 END) as low_risks,
                AVG(risk_score) as avg_risk_score
             FROM risk_assessment_responses
             WHERE assessment_id = ?",
            [$id]
        );

        // Get top risks
        $topRisks = $this->db->fetchAll(
            "SELECT rar.*, raq.question_text, raq.category
             FROM risk_assessment_responses rar
             JOIN risk_assessment_questions raq ON rar.question_id = raq.id
             WHERE rar.assessment_id = ? AND rar.risk_level IN ('critical', 'high')
             ORDER BY rar.risk_score DESC
             LIMIT 10",
            [$id]
        );

        $content = View::render('risk-assessments/results', [
            'assessment' => $assessment,
            'responses_by_category' => $responsesByCategory,
            'risk_summary' => $riskSummary,
            'top_risks' => $topRisks,
        ]);

        return new Response($content);
    }

    public function delete(Request $request, int $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $customerId = Session::get('current_customer_id');

        // Verify assessment
        $assessment = $this->db->fetchOne(
            "SELECT * FROM risk_assessments WHERE id = ? AND client_id = ?",
            [$id, $customerId]
        );

        if (!$assessment) {
            return Response::json(['success' => false, 'message' => 'Assessment not found']);
        }

        $this->db->delete('risk_assessments', $id);

        AuditLogger::log('delete', 'risk_assessment', $id, [], $request->ip());

        return Response::json(['success' => true]);
    }

    private function updateAssessmentStats(int $assessmentId): void
    {
        // Calculate overall statistics
        $stats = $this->db->fetchOne(
            "SELECT
                AVG(risk_score) as avg_score,
                SUM(CASE WHEN risk_level = 'critical' THEN 1 ELSE 0 END) as critical_count,
                SUM(CASE WHEN risk_level = 'high' THEN 1 ELSE 0 END) as high_count,
                SUM(CASE WHEN risk_level = 'medium' THEN 1 ELSE 0 END) as medium_count,
                SUM(CASE WHEN risk_level = 'low' THEN 1 ELSE 0 END) as low_count
             FROM risk_assessment_responses
             WHERE assessment_id = ?",
            [$assessmentId]
        );

        if ($stats) {
            $this->db->update('risk_assessments', $assessmentId, [
                'overall_risk_score' => round($stats['avg_score'] ?? 0, 2),
                'high_risks' => ($stats['critical_count'] ?? 0) + ($stats['high_count'] ?? 0),
                'medium_risks' => $stats['medium_count'] ?? 0,
                'low_risks' => $stats['low_count'] ?? 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
