<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Services\SprsScoreCalculator;

/**
 * SPRS Scoring Controller
 */
class SprsController
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
        $currentCustomer = Session::get('current_customer_name');

        if (!$customerId) {
            Session::flash('error', 'Please select a customer first.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        // Get latest published NIST 800-171 or CMMC assessment (CMMC L2 = NIST 800-171)
        $assessment = $this->db->fetchOne(
            "SELECT * FROM assessments
             WHERE customer_id = ?
             AND status = 'published'
             AND framework IN ('NIST800171', 'CMMC')
             ORDER BY assessed_at DESC LIMIT 1",
            [$customerId]
        );

        $scores = null;
        $breakdown = null;

        if ($assessment) {
            $calculator = new SprsScoreCalculator($this->db);
            $scores = $calculator->calculate($assessment['id']);
            $breakdown = $calculator->getBreakdownByDomain($assessment['id']);
        }

        $content = View::render('sprs/index', [
            'assessment' => $assessment,
            'scores' => $scores,
            'breakdown' => $breakdown,
            'current_customer' => $currentCustomer,
        ]);

        return new Response($content);
    }

    public function export(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $assessment = $this->db->fetchOne(
            "SELECT * FROM assessments
             WHERE customer_id = ?
             AND status = 'published'
             AND framework IN ('NIST800171', 'CMMC')
             ORDER BY assessed_at DESC LIMIT 1",
            [$customerId]
        );

        if (!$assessment) {
            Session::flash('error', 'No published assessment found.');
            return Response::redirect($request->baseUrl() . '/sprs');
        }

        $calculator = new SprsScoreCalculator($this->db);
        $csv = $calculator->exportToCsv($assessment['id']);

        return Response::download($csv, 'sprs_score_' . date('Y-m-d') . '.csv', 'text/csv');
    }
}
