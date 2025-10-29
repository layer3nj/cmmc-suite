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
}
