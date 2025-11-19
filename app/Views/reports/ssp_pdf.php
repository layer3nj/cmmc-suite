<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>System Security Plan - <?= $e($client['name']) ?></title>
    <style>
        @page { margin: 0.75in; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.4; color: #333; margin: 0; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #000; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18pt; }
        .section { margin: 25px 0; page-break-inside: avoid; }
        .section-title { font-size: 14pt; font-weight: bold; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #333; }
        .info-grid { display: grid; grid-template-columns: 150px 1fr; gap: 10px; margin: 15px 0; }
        .info-label { font-weight: bold; color: #555; }
        .info-value { color: #000; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #999; padding: 8px; font-size: 10pt; }
        th { background: #f0f0f0; font-weight: bold; text-align: left; }
        .control-row { page-break-inside: avoid; }
        .compliant { color: #28a745; font-weight: bold; }
        .non-compliant { color: #dc3545; font-weight: bold; }
        .not-applicable { color: #6c757d; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc; text-align: center; font-size: 9pt; color: #666; }
        @media print {
            .no-print { display: none; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
        <button onclick="window.print()" style="padding: 10px 20px;">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px;">Close</button>
    </div>

    <div class="header">
        <h1>System Security Plan (SSP)</h1>
        <h2><?= $e($client['name']) ?></h2>
        <div style="margin-top: 10px; font-size: 11pt;">
            <?php if ($assessment): ?>
                Framework: <?= $e($assessment['framework']) ?>
                <?= $assessment['target_level'] ? 'ML' . $assessment['target_level'] : '' ?>
            <?php endif; ?>
        </div>
        <div style="font-size: 10pt; color: #666;">
            Generated: <?= date('F d, Y') ?>
        </div>
    </div>

    <!-- Section 1: System Information -->
    <div class="section">
        <div class="section-title">1. System Information</div>
        <div class="info-grid">
            <div class="info-label">Organization:</div>
            <div class="info-value"><?= $e($client['name']) ?></div>

            <div class="info-label">Primary Contact:</div>
            <div class="info-value"><?= $e($client['contact_name'] ?? 'Not specified') ?></div>

            <div class="info-label">Contact Email:</div>
            <div class="info-value"><?= $e($client['contact_email'] ?? 'Not specified') ?></div>

            <div class="info-label">Contact Phone:</div>
            <div class="info-value"><?= $e($client['contact_phone'] ?? 'Not specified') ?></div>

            <div class="info-label">Address:</div>
            <div class="info-value"><?= $e($client['address'] ?? 'Not specified') ?></div>
        </div>
    </div>

    <?php if ($assessment): ?>
    <!-- Section 2: Assessment Overview -->
    <div class="section">
        <div class="section-title">2. Assessment Overview</div>
        <div class="info-grid">
            <div class="info-label">Framework:</div>
            <div class="info-value"><?= $e($assessment['framework']) ?></div>

            <div class="info-label">Target Level:</div>
            <div class="info-value"><?= $assessment['target_level'] ? 'Maturity Level ' . $assessment['target_level'] : 'Not specified' ?></div>

            <div class="info-label">Assessment Type:</div>
            <div class="info-value"><?= $e($assessment['assessment_type']) ?></div>

            <div class="info-label">Assessment Date:</div>
            <div class="info-value"><?= date('F d, Y', strtotime($assessment['assessed_at'])) ?></div>

            <div class="info-label">Assessed By:</div>
            <div class="info-value"><?= $e($assessment['assessed_by']) ?></div>

            <div class="info-label">Status:</div>
            <div class="info-value"><?= ucfirst($assessment['status']) ?></div>
        </div>
    </div>

    <!-- Section 3: Security Control Implementation Status -->
    <div class="section">
        <div class="section-title">3. Security Control Implementation Status</div>

        <?php if (!empty($responses)): ?>
            <?php
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

            $compliance_rate = $stats['total'] > 0
                ? round(($stats['compliant'] / $stats['total']) * 100, 1)
                : 0;
            ?>

            <div style="margin: 15px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff;">
                <strong>Overall Compliance Rate: <?= $compliance_rate ?>%</strong>
                (<?= $stats['compliant'] ?> compliant out of <?= $stats['total'] ?> applicable controls)
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 120px;">Control Code</th>
                        <th style="width: 120px;">Status</th>
                        <th>Implementation Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($responses as $response): ?>
                    <tr class="control-row">
                        <td><?= $e($response['control_code']) ?></td>
                        <td class="<?= $response['status'] === 'met' ? 'compliant' : ($response['status'] === 'not_applicable' ? 'not-applicable' : 'non-compliant') ?>">
                            <?= ucfirst(str_replace('_', ' ', $response['status'])) ?>
                        </td>
                        <td><?= $e($response['objective_evidence'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #666; font-style: italic;">No assessment responses available.</p>
        <?php endif; ?>
    </div>

    <!-- Section 4: Security Responsibilities -->
    <div class="section">
        <div class="section-title">4. Security Responsibilities</div>
        <div class="info-grid">
            <div class="info-label">Managed By:</div>
            <div class="info-value"><?= $e($settings['site_name'] ?? 'CMMC Compliance Suite') ?></div>

            <?php if (!empty($settings['company_email'])): ?>
            <div class="info-label">Contact Email:</div>
            <div class="info-value"><?= $e($settings['company_email']) ?></div>
            <?php endif; ?>

            <?php if (!empty($settings['company_phone'])): ?>
            <div class="info-label">Contact Phone:</div>
            <div class="info-value"><?= $e($settings['company_phone']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section 5: Document Control -->
    <div class="section">
        <div class="section-title">5. Document Control</div>
        <table style="width: 60%;">
            <thead>
                <tr>
                    <th>Version</th>
                    <th>Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.0</td>
                    <td><?= date('F d, Y') ?></td>
                    <td>Initial System Security Plan</td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="section">
        <p style="color: #666; font-style: italic;">
            No assessment data available for this client. Please complete an assessment to generate a comprehensive System Security Plan.
        </p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p><strong><?= $e($settings['site_name'] ?? 'CMMC Compliance Suite') ?></strong></p>
        <p>This document contains sensitive security information and should be protected accordingly.</p>
        <p style="font-size: 8pt; margin-top: 10px;">
            Generated on <?= date('F d, Y \a\t g:i A') ?>
        </p>
    </div>
</body>
</html>
