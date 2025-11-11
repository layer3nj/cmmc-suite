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
 * Control Browser Controller
 * Browse and search CMMC, NIST 800-171, and STIG controls
 */
class ControlController
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

        // Redirect to CMMC by default
        return Response::redirect($request->baseUrl() . '/controls/CMMC');
    }

    public function byFramework(Request $request, string $framework): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $framework = strtoupper($framework);

        // Support all compliance frameworks
        $validFrameworks = ['CMMC', 'NIST800171', 'NIST80053', 'STIG', 'HIPAA', 'FTC-SAFEGUARDS', 'PCI-DSS', 'SOC2', 'ISO27001'];
        if (!in_array($framework, $validFrameworks)) {
            return new Response('Invalid framework', 404);
        }

        // Get current client's maturity level setting
        Session::start();
        $currentCustomerId = Session::get('current_customer_id');
        $clientMaturityLevel = null;
        $maxMaturityLevel = 3;

        if ($currentCustomerId && $framework === 'CMMC') {
            $client = $this->db->fetchOne(
                "SELECT cmmc_maturity_level FROM clients WHERE id = ?",
                [$currentCustomerId]
            );
            if ($client && !empty($client['cmmc_maturity_level'])) {
                $clientMaturityLevel = $client['cmmc_maturity_level'];
                // Convert to numeric for filtering
                switch ($clientMaturityLevel) {
                    case 'Level 1':
                        $maxMaturityLevel = 1;
                        break;
                    case 'Level 2':
                        $maxMaturityLevel = 2;
                        break;
                    case 'Level 3':
                        $maxMaturityLevel = 3;
                        break;
                }
            }
        }

        // Get filters
        $search = $request->query('search', '');
        $mlLevel = $request->query('ml_level', '');
        $category = $request->query('category', '');
        $status = $request->query('status', '');

        // Build query
        $sql = "SELECT * FROM controls WHERE framework = ?";
        $params = [$framework];

        // Apply maturity level filtering for CMMC
        if ($framework === 'CMMC') {
            if (!empty($mlLevel)) {
                // User has selected a specific level via filter
                $sql .= " AND ml_level = ?";
                $params[] = $mlLevel;
            } elseif (!empty($clientMaturityLevel)) {
                // Filter by client's max maturity level (show all controls up to that level)
                $sql .= " AND ml_level <= ?";
                $params[] = $maxMaturityLevel;
            }
        }

        if (!empty($search)) {
            $sql .= " AND (code LIKE ? OR title LIKE ? OR description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($category)) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY code ASC";

        $controls = $this->db->fetchAll($sql, $params);

        // Apply natural sorting to handle numeric portions correctly (e.g., 3.1, 3.2... 3.9, 3.10)
        usort($controls, function($a, $b) {
            return strnatcmp($a['code'], $b['code']);
        });

        // Get control counts by ML level (for CMMC)
        $mlCounts = [];
        if ($framework === 'CMMC') {
            for ($level = 1; $level <= 3; $level++) {
                $mlCounts[$level] = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM controls WHERE framework = 'CMMC' AND ml_level = ?",
                    [$level]
                );
            }
        }

        // Get control counts by category
        $categoryCountsSql = "SELECT category, COUNT(*) as count FROM controls WHERE framework = ? AND category IS NOT NULL GROUP BY category ORDER BY category ASC";
        $categoryCounts = $this->db->fetchAll($categoryCountsSql, [$framework]);

        // Map category names to abbreviations
        $categoryAbbreviations = [
            'Access Control' => 'AC',
            'Awareness and Training' => 'AT',
            'Audit and Accountability' => 'AU',
            'Configuration Management' => 'CM',
            'Identification and Authentication' => 'IA',
            'Incident Response' => 'IR',
            'Maintenance' => 'MA',
            'Media Protection' => 'MP',
            'Personnel Security' => 'PS',
            'Physical Protection' => 'PE',
            'Risk Assessment' => 'RA',
            'Security Assessment' => 'CA',
            'System and Communications Protection' => 'SC',
            'System and Information Integrity' => 'SI',
        ];

        $content = View::render('controls/index', [
            'framework' => $framework,
            'controls' => $controls,
            'ml_counts' => $mlCounts,
            'category_counts' => $categoryCounts,
            'category_abbreviations' => $categoryAbbreviations,
            'client_maturity_level' => $clientMaturityLevel,
            'filters' => [
                'search' => $search,
                'ml_level' => $mlLevel,
                'category' => $category,
                'status' => $status,
            ],
        ]);

        return new Response($content);
    }

    public function show(Request $request, string $framework, string $code): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $framework = strtoupper($framework);
        $code = urldecode($code);

        // Get control
        $control = $this->db->fetchOne(
            "SELECT * FROM controls WHERE framework = ? AND code = ?",
            [$framework, $code]
        );

        if (!$control) {
            return new Response('Control not found', 404);
        }

        // Get mappings
        $mappings = $this->getMappings($framework, $code);

        // Get latest finding (if customer selected)
        Session::start();
        $customerId = Session::get('current_customer_id');
        $latestFinding = null;

        if ($customerId) {
            $latestFinding = $this->getLatestFinding($customerId, $framework, $code);
        }

        // Get related documents
        $documents = [];
        if ($customerId) {
            $documents = $this->db->fetchAll(
                "SELECT * FROM documents
                 WHERE customer_id = ?
                 AND linked_framework = ?
                 AND linked_code = ?
                 ORDER BY created_at DESC",
                [$customerId, $framework, $code]
            );
        }

        $content = View::render('controls/show', [
            'control' => $control,
            'mappings' => $mappings,
            'latest_finding' => $latestFinding,
            'documents' => $documents,
        ]);

        return new Response($content);
    }

    public function update(Request $request, string $framework, string $code): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // This endpoint is used to update control evidence/notes
        // Control definitions themselves are not editable

        return Response::json(['success' => true]);
    }

    private function getMappings(string $framework, string $code): array
    {
        // Get controls that this maps to
        $mapsTo = $this->db->fetchAll(
            "SELECT cm.*, c.title as target_title
             FROM control_mappings cm
             JOIN controls c ON cm.target_framework = c.framework AND cm.target_code = c.code
             WHERE cm.source_framework = ? AND cm.source_code = ?",
            [$framework, $code]
        );

        // Get controls that map to this
        $mappedFrom = $this->db->fetchAll(
            "SELECT cm.*, c.title as source_title
             FROM control_mappings cm
             JOIN controls c ON cm.source_framework = c.framework AND cm.source_code = c.code
             WHERE cm.target_framework = ? AND cm.target_code = ?",
            [$framework, $code]
        );

        return [
            'maps_to' => $mapsTo,
            'mapped_from' => $mappedFrom,
        ];
    }

    private function getLatestFinding(int $customerId, string $framework, string $code): ?array
    {
        return $this->db->fetchOne(
            "SELECT cf.*, a.assessed_at
             FROM control_findings cf
             JOIN assessments a ON cf.assessment_id = a.id
             WHERE a.customer_id = ?
             AND cf.control_framework = ?
             AND cf.control_code = ?
             AND a.status = 'published'
             ORDER BY a.assessed_at DESC
             LIMIT 1",
            [$customerId, $framework, $code]
        );
    }

    public function exportPdf(Request $request, string $framework): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $framework = strtoupper($framework);

        // Support all compliance frameworks
        $validFrameworks = ['CMMC', 'NIST800171', 'NIST80053', 'STIG', 'HIPAA', 'FTC-SAFEGUARDS', 'PCI-DSS', 'SOC2', 'ISO27001'];
        if (!in_array($framework, $validFrameworks)) {
            return new Response('Invalid framework', 404);
        }

        // Get all controls for this framework
        $controls = $this->db->fetchAll(
            "SELECT * FROM controls WHERE framework = ? ORDER BY code ASC",
            [$framework]
        );

        // Apply natural sorting to handle numeric portions correctly (e.g., 3.1, 3.2... 3.9, 3.10)
        usort($controls, function($a, $b) {
            return strnatcmp($a['code'], $b['code']);
        });

        // Get framework display name
        $frameworkNames = [
            'CMMC' => 'CMMC 2.0 - Cybersecurity Maturity Model Certification',
            'NIST800171' => 'NIST SP 800-171 Rev 2',
            'NIST80053' => 'NIST SP 800-53 Rev 5',
            'STIG' => 'DISA STIG - Security Technical Implementation Guide',
            'HIPAA' => 'HIPAA Security Rule',
            'FTC-SAFEGUARDS' => 'FTC Safeguards Rule',
            'PCI-DSS' => 'PCI-DSS v4.0 - Payment Card Industry Data Security Standard',
            'SOC2' => 'SOC 2 - Trust Services Criteria',
            'ISO27001' => 'ISO/IEC 27001:2022 - Information Security Management',
        ];

        $frameworkName = $frameworkNames[$framework] ?? $framework;

        // Render HTML content for PDF
        $htmlContent = View::render('controls/pdf_export', [
            'framework' => $framework,
            'framework_name' => $frameworkName,
            'controls' => $controls,
            'generated_date' => date('F j, Y'),
            'total_controls' => count($controls),
        ]);

        // Set headers for PDF download
        $filename = strtolower($framework) . '_controls_' . date('Y-m-d') . '.pdf';

        // Use browser's built-in print-to-PDF functionality
        // Set content type to HTML and include print stylesheet
        $response = new Response($htmlContent);
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        $response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');

        return $response;
    }
}
