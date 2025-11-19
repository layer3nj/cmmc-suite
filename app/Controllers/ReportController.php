<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Middleware\AuthMiddleware;

/**
 * Report Generation Controller
 */
class ReportController
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
        $currentCustomer = Session::get('current_customer_name');

        $content = View::render('reports/index', [
            'current_customer' => $currentCustomer,
        ]);
        return new Response($content);
    }

    public function cmmc(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $controls = $this->db->fetchAll(
            "SELECT c.*,
             (SELECT status FROM control_findings cf
              JOIN assessments a ON cf.assessment_id = a.id
              WHERE cf.control_code = c.code AND cf.control_framework = 'CMMC'
              AND a.customer_id = ? AND a.status = 'published'
              ORDER BY a.assessed_at DESC LIMIT 1) as status
             FROM controls c
             WHERE c.framework = 'CMMC'
             ORDER BY c.ml_level, c.code",
            [$customerId]
        );

        // Generate CSV
        $csv = "ML Level,Control Code,Title,Status\n";
        foreach ($controls as $control) {
            $csv .= sprintf(
                "%s,%s,\"%s\",%s\n",
                $control['ml_level'],
                $control['code'],
                str_replace('"', '""', $control['title']),
                $control['status'] ?? 'Not Assessed'
            );
        }

        return Response::download($csv, 'cmmc_report_' . date('Y-m-d') . '.csv', 'text/csv');
    }

    public function nist(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();
        $customerId = Session::get('current_customer_id');

        $controls = $this->db->fetchAll(
            "SELECT c.*,
             (SELECT status FROM control_findings cf
              JOIN assessments a ON cf.assessment_id = a.id
              WHERE cf.control_code = c.code AND cf.control_framework = 'NIST800171'
              AND a.customer_id = ? AND a.status = 'published'
              ORDER BY a.assessed_at DESC LIMIT 1) as status
             FROM controls c
             WHERE c.framework = 'NIST800171'
             ORDER BY c.code",
            [$customerId]
        );

        $csv = "Control Code,Title,Status\n";
        foreach ($controls as $control) {
            $csv .= sprintf(
                "%s,\"%s\",%s\n",
                $control['code'],
                str_replace('"', '""', $control['title']),
                $control['status'] ?? 'Not Assessed'
            );
        }

        return Response::download($csv, 'nist_800_171_report_' . date('Y-m-d') . '.csv', 'text/csv');
    }

    public function stig(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $controls = $this->db->fetchAll(
            "SELECT * FROM controls WHERE framework = 'STIG' ORDER BY code"
        );

        $csv = "STIG ID,Title,Version,Severity\n";
        foreach ($controls as $control) {
            $csv .= sprintf(
                "%s,\"%s\",%s,%s\n",
                $control['code'],
                str_replace('"', '""', $control['title']),
                $control['stig_version'] ?? '',
                $control['stig_severity'] ?? ''
            );
        }

        return Response::download($csv, 'stig_report_' . date('Y-m-d') . '.csv', 'text/csv');
    }

    public function sprs(Request $request): Response
    {
        // Redirect to SPRS controller
        return Response::redirect($request->baseUrl() . '/sprs/export');
    }

    public function generate(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        // This would handle custom report generation
        Session::flash('info', 'Custom report generation coming soon.');
        return Response::redirect($request->baseUrl() . '/reports');
    }

    /**
     * Generate System Security Plan (SSP) PDF Report
     */
    public function sspPdf(Request $request, string $clientId): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $client = $this->db->fetchOne("SELECT * FROM clients WHERE id = ?", [$clientId]);
        if (!$client) return new Response('Client not found', 404);

        $assessment = $this->db->fetchOne(
            "SELECT * FROM assessments WHERE customer_id = ? ORDER BY created_at DESC LIMIT 1",
            [$clientId]
        );

        $responses = $assessment ? $this->db->fetchAll(
            "SELECT * FROM control_findings WHERE assessment_id = ? ORDER BY control_code",
            [$assessment['id']]
        ) : [];

        $settingsService = new \App\Services\SettingsService($this->db);
        $settings = $settingsService->getAll();

        $content = View::render('reports/ssp_pdf', [
            'client' => $client,
            'assessment' => $assessment,
            'responses' => $responses,
            'settings' => $settings,
        ]);

        return new Response($content);
    }

    /**
     * Generate POA&M PDF Report
     */
    public function poamPdf(Request $request, string $clientId): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $client = $this->db->fetchOne("SELECT * FROM clients WHERE id = ?", [$clientId]);
        if (!$client) return new Response('Client not found', 404);

        $poamItems = $this->db->fetchAll(
            "SELECT * FROM poam_items WHERE customer_id = ? ORDER BY
             CASE status
                WHEN 'open' THEN 1
                WHEN 'in_progress' THEN 2
                WHEN 'closed' THEN 3
                ELSE 4
             END,
             planned_completion_date ASC",
            [$clientId]
        );

        $settingsService = new \App\Services\SettingsService($this->db);
        $settings = $settingsService->getAll();

        $content = View::render('reports/poam_pdf', [
            'client' => $client,
            'poam_items' => $poamItems,
            'settings' => $settings,
        ]);

        return new Response($content);
    }

    /**
     * Generate Assessment Summary PDF Report
     */
    public function assessmentPdf(Request $request, string $assessmentId): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $assessment = $this->db->fetchOne("SELECT * FROM assessments WHERE id = ?", [$assessmentId]);
        if (!$assessment) return new Response('Assessment not found', 404);

        $client = $this->db->fetchOne("SELECT * FROM clients WHERE id = ?", [$assessment['customer_id']]);

        $responses = $this->db->fetchAll(
            "SELECT * FROM control_findings WHERE assessment_id = ? ORDER BY control_code",
            [$assessmentId]
        );

        // Calculate statistics
        $stats = [
            'total' => count($responses),
            'compliant' => 0,
            'non_compliant' => 0,
            'not_applicable' => 0,
        ];

        foreach ($responses as $response) {
            switch ($response['status']) {
                case 'met':
                    $stats['compliant']++;
                    break;
                case 'partially_met':
                case 'not_met':
                    $stats['non_compliant']++;
                    break;
                case 'not_applicable':
                    $stats['not_applicable']++;
                    break;
            }
        }

        $settingsService = new \App\Services\SettingsService($this->db);
        $settings = $settingsService->getAll();

        $content = View::render('reports/assessment_pdf', [
            'client' => $client,
            'assessment' => $assessment,
            'responses' => $responses,
            'stats' => $stats,
            'settings' => $settings,
        ]);

        return new Response($content);
    }
}
