<?php

namespace App\Services;

use App\Core\Database;

/**
 * SPRS Score Calculator
 *
 * Implements DoD NIST 800-171 Assessment Methodology (SPRS = Supplier Performance Risk System)
 *
 * SCORING METHODOLOGY:
 * - Starting score: 110 points (maximum possible, all requirements met)
 * - Minimum score: -203 points (all requirements not met)
 * - Each of the 110 NIST 800-171 practices is scored individually
 *
 * DEDUCTION VALUES:
 * - Met: 0 points (no deduction)
 * - Partially Met: -3 points per practice
 * - Not Met: -5 points per practice
 * - Not Applicable: 0 points (no deduction)
 *
 * CALCULATION:
 * Final Score = 110 - (sum of all deductions), with a floor of -203
 *
 * EXAMPLE:
 * - 100 practices Met (0 deduction each) = 0 points deducted
 * - 5 practices Partially Met (3 points each) = -15 points
 * - 5 practices Not Met (5 points each) = -25 points
 * Final Score = 110 - 40 = 70 points
 *
 * WORST CASE:
 * - All 110 practices Not Met = 110 × -5 = -550 points of deductions
 * - But minimum score is capped at -203 points
 */
class SprsScoreCalculator
{
    private Database $db;

    // Deduction values per NIST 800-171 practice implementation status
    private const DEDUCTIONS = [
        'met' => 0,               // Fully implemented: 0 points (no deduction)
        'partially_met' => 3,     // Partially implemented: -3 points
        'not_met' => 5,           // Not implemented: -5 points
        'not_applicable' => 0,    // N/A: 0 points (no deduction)
    ];

    private const STARTING_SCORE = 110;  // Maximum possible score (all practices met)
    private const MINIMUM_SCORE = -203;  // Minimum possible score (floor)

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Calculate SPRS scores for an assessment
     * Supports both NIST 800-171 and CMMC frameworks
     */
    public function calculate(int $assessmentId): array
    {
        // Get the assessment framework
        $assessment = $this->db->fetchOne(
            "SELECT framework FROM assessments WHERE id = ?",
            [$assessmentId]
        );

        $framework = $assessment['framework'] ?? 'NIST800171';

        // Get all relevant findings for this assessment
        // Support both NIST800171 and CMMC (since CMMC L2 = NIST 800-171)
        $findings = $this->db->fetchAll(
            "SELECT control_code, status
             FROM control_findings
             WHERE assessment_id = ?
             AND control_framework IN ('NIST800171', 'CMMC')",
            [$assessmentId]
        );

        // Calculate current score
        $currentDeductions = 0;
        $projectedDeductions = 0;
        $breakdown = [];

        foreach ($findings as $finding) {
            $status = $finding['status'];
            $deduction = self::DEDUCTIONS[$status] ?? 0;

            $currentDeductions += $deduction;

            // For projected score, assume partially_met can become met
            // and not_met stays the same (unless there's an open POA&M)
            if ($status === 'partially_met') {
                $projectedDeductions += 0; // Assume will be fixed
            } else {
                $projectedDeductions += $deduction;
            }

            if ($deduction > 0) {
                $breakdown[] = [
                    'control_code' => $finding['control_code'],
                    'status' => $status,
                    'deduction' => $deduction,
                ];
            }
        }

        $currentScore = max(self::STARTING_SCORE - $currentDeductions, self::MINIMUM_SCORE);
        $projectedScore = max(self::STARTING_SCORE - $projectedDeductions, self::MINIMUM_SCORE);

        return [
            'starting_score' => self::STARTING_SCORE,
            'current_score' => $currentScore,
            'projected_score' => $projectedScore,
            'current_deductions' => $currentDeductions,
            'projected_deductions' => $projectedDeductions,
            'breakdown' => $breakdown,
            'total_practices' => 110,
            'assessed_practices' => count($findings),
        ];
    }

    /**
     * Get SPRS score breakdown by domain
     */
    public function getBreakdownByDomain(int $assessmentId): array
    {
        $domains = [
            '3.1' => 'Access Control',
            '3.2' => 'Awareness and Training',
            '3.3' => 'Audit and Accountability',
            '3.4' => 'Configuration Management',
            '3.5' => 'Identification and Authentication',
            '3.6' => 'Incident Response',
            '3.7' => 'Maintenance',
            '3.8' => 'Media Protection',
            '3.9' => 'Personnel Security',
            '3.10' => 'Physical Protection',
            '3.11' => 'Risk Assessment',
            '3.12' => 'Security Assessment',
            '3.13' => 'System and Communications Protection',
            '3.14' => 'System and Information Integrity',
        ];

        $breakdown = [];

        foreach ($domains as $prefix => $name) {
            $findings = $this->db->fetchAll(
                "SELECT control_code, status
                 FROM control_findings
                 WHERE assessment_id = ?
                 AND control_framework IN ('NIST800171', 'CMMC')
                 AND control_code LIKE ?",
                [$assessmentId, $prefix . '%']
            );

            $deductions = 0;
            $met = 0;
            $partiallyMet = 0;
            $notMet = 0;
            $notApplicable = 0;

            foreach ($findings as $finding) {
                $deductions += self::DEDUCTIONS[$finding['status']] ?? 0;

                switch ($finding['status']) {
                    case 'met':
                        $met++;
                        break;
                    case 'partially_met':
                        $partiallyMet++;
                        break;
                    case 'not_met':
                        $notMet++;
                        break;
                    case 'not_applicable':
                        $notApplicable++;
                        break;
                }
            }

            $breakdown[] = [
                'domain' => $name,
                'prefix' => $prefix,
                'total_practices' => count($findings),
                'met' => $met,
                'partially_met' => $partiallyMet,
                'not_met' => $notMet,
                'not_applicable' => $notApplicable,
                'deductions' => $deductions,
            ];
        }

        return $breakdown;
    }

    /**
     * Export SPRS breakdown to CSV format
     */
    public function exportToCsv(int $assessmentId): string
    {
        $score = $this->calculate($assessmentId);
        $breakdown = $score['breakdown'];

        $csv = "Control,Status,Deduction\n";
        $csv .= "Starting Score,,110\n";

        foreach ($breakdown as $item) {
            $csv .= sprintf(
                "%s,%s,-%d\n",
                $item['control_code'],
                ucfirst(str_replace('_', ' ', $item['status'])),
                $item['deduction']
            );
        }

        $csv .= sprintf("\nCurrent Score,,%d\n", $score['current_score']);
        $csv .= sprintf("Projected Score,,%d\n", $score['projected_score']);

        return $csv;
    }
}
