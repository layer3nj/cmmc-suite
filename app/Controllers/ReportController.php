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
        $customerName = Session::get('current_customer_name');
        $format = $request->query('format', 'csv');

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

        // Calculate statistics
        $stats = [
            'total' => count($controls),
            'met' => 0,
            'partially_met' => 0,
            'not_met' => 0,
            'not_applicable' => 0,
            'not_assessed' => 0,
        ];

        foreach ($controls as $control) {
            $status = strtolower($control['status'] ?? 'not_assessed');
            if (isset($stats[$status])) {
                $stats[$status]++;
            } else {
                $stats['not_assessed']++;
            }
        }

        if ($format === 'pdf') {
            // Generate PDF
            $htmlContent = View::render('reports/assessment_pdf', [
                'framework' => 'CMMC',
                'framework_name' => 'CMMC 2.0 - Cybersecurity Maturity Model Certification',
                'report_type' => 'Compliance Assessment Report',
                'customer_name' => $customerName,
                'controls' => $controls,
                'stats' => $stats,
                'generated_date' => date('F j, Y g:i A'),
                'status' => 'Published',
            ]);

            $filename = 'cmmc_report_' . date('Y-m-d') . '.pdf';
            $response = new Response($htmlContent);
            $response->setHeader('Content-Type', 'text/html; charset=utf-8');
            $response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
            return $response;
        }

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
        $customerName = Session::get('current_customer_name');
        $format = $request->query('format', 'csv');

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

        // Calculate statistics
        $stats = [
            'total' => count($controls),
            'met' => 0,
            'partially_met' => 0,
            'not_met' => 0,
            'not_applicable' => 0,
            'not_assessed' => 0,
        ];

        foreach ($controls as $control) {
            $status = strtolower($control['status'] ?? 'not_assessed');
            if (isset($stats[$status])) {
                $stats[$status]++;
            } else {
                $stats['not_assessed']++;
            }
        }

        if ($format === 'pdf') {
            // Generate PDF
            $htmlContent = View::render('reports/assessment_pdf', [
                'framework' => 'NIST800171',
                'framework_name' => 'NIST SP 800-171 Rev 2',
                'report_type' => 'Compliance Assessment Report',
                'customer_name' => $customerName,
                'controls' => $controls,
                'stats' => $stats,
                'generated_date' => date('F j, Y g:i A'),
                'status' => 'Published',
            ]);

            $filename = 'nist_800_171_report_' . date('Y-m-d') . '.pdf';
            $response = new Response($htmlContent);
            $response->setHeader('Content-Type', 'text/html; charset=utf-8');
            $response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
            return $response;
        }

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

        Session::start();
        $customerId = Session::get('current_customer_id');
        $customerName = Session::get('current_customer_name');
        $format = $request->query('format', 'csv');

        $controls = $this->db->fetchAll(
            "SELECT c.*,
             (SELECT status FROM control_findings cf
              JOIN assessments a ON cf.assessment_id = a.id
              WHERE cf.control_code = c.code AND cf.control_framework = 'STIG'
              AND a.customer_id = ? AND a.status = 'published'
              ORDER BY a.assessed_at DESC LIMIT 1) as status
             FROM controls c
             WHERE c.framework = 'STIG'
             ORDER BY c.code",
            [$customerId]
        );

        // Calculate statistics
        $stats = [
            'total' => count($controls),
            'met' => 0,
            'partially_met' => 0,
            'not_met' => 0,
            'not_applicable' => 0,
            'not_assessed' => 0,
        ];

        foreach ($controls as $control) {
            $status = strtolower($control['status'] ?? 'not_assessed');
            if (isset($stats[$status])) {
                $stats[$status]++;
            } else {
                $stats['not_assessed']++;
            }
        }

        if ($format === 'pdf') {
            // Generate PDF
            $htmlContent = View::render('reports/assessment_pdf', [
                'framework' => 'STIG',
                'framework_name' => 'DISA STIG - Security Technical Implementation Guide',
                'report_type' => 'Compliance Assessment Report',
                'customer_name' => $customerName,
                'controls' => $controls,
                'stats' => $stats,
                'generated_date' => date('F j, Y g:i A'),
                'status' => 'Published',
            ]);

            $filename = 'stig_report_' . date('Y-m-d') . '.pdf';
            $response = new Response($htmlContent);
            $response->setHeader('Content-Type', 'text/html; charset=utf-8');
            $response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
            return $response;
        }

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
