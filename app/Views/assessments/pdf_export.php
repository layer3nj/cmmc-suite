<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($assessment['framework']) ?> - Assessment Report</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .page-break {
                page-break-after: always;
            }
            .no-print {
                display: none;
            }
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 20pt;
            color: #1a1a1a;
            margin-bottom: 10px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }

        h2 {
            font-size: 14pt;
            color: #1a1a1a;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 5px;
        }

        .header-info {
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .header-info p {
            margin: 5px 0;
            font-size: 9pt;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            padding: 12px;
            border: 1px solid #dee2e6;
            background: #f8f9fa;
            text-align: center;
        }

        .stat-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 18pt;
            font-weight: bold;
            color: #1a1a1a;
        }

        .text-success { color: #28a745; }
        .text-warning { color: #ffc107; }
        .text-danger { color: #dc3545; }

        .finding {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 12px;
            background: #fff;
            page-break-inside: avoid;
        }

        .finding-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .control-info {
            flex: 1;
        }

        .control-code {
            font-family: 'Courier New', monospace;
            font-size: 11pt;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 3px;
        }

        .control-title {
            font-weight: bold;
            font-size: 10pt;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .control-description {
            font-size: 9pt;
            color: #555;
            margin-bottom: 10px;
            text-align: justify;
            line-height: 1.3;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            white-space: nowrap;
        }

        .badge-met {
            background: #d4edda;
            color: #155724;
        }

        .badge-partially_met {
            background: #fff3cd;
            color: #856404;
        }

        .badge-not_met {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-not_applicable {
            background: #e2e3e5;
            color: #383d41;
        }

        .finding-notes {
            margin-top: 8px;
            padding: 8px;
            background: #f8f9fa;
            border-left: 3px solid #007bff;
            font-size: 9pt;
        }

        .notes-label {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
        }

        .domain-section {
            margin-bottom: 30px;
        }

        .domain-header {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 12pt;
            font-weight: bold;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14pt;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }

        footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .summary-table th,
        .summary-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-size: 9pt;
        }

        .summary-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print to PDF</button>

    <h1><?= $e($assessment['framework']) ?> Assessment Report</h1>

    <div class="header-info">
        <p><strong>Assessment ID:</strong> #<?= $e($assessment['id']) ?></p>
        <p><strong>Framework:</strong> <?= $e($assessment['framework']) ?></p>
        <?php if (!empty($assessment['assessment_type'])): ?>
        <p><strong>Assessment Type:</strong> <?= $e(ucfirst($assessment['assessment_type'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($assessment['target_level'])): ?>
        <p><strong>Target Level:</strong> Level <?= $e($assessment['target_level']) ?></p>
        <?php endif; ?>
        <?php if (!empty($assessment['scope'])): ?>
        <p><strong>Scope:</strong> <?= $e($assessment['scope']) ?></p>
        <?php endif; ?>
        <p><strong>Assessor:</strong> <?= $e($assessment['assessor_name'] ?? 'Unknown') ?></p>
        <p><strong>Status:</strong> <?= $e(ucfirst($assessment['status'])) ?></p>
        <p><strong>Created:</strong> <?= date('M d, Y', strtotime($assessment['created_at'])) ?></p>
        <?php if (!empty($assessment['assessed_at'])): ?>
        <p><strong>Assessed:</strong> <?= date('M d, Y', strtotime($assessment['assessed_at'])) ?></p>
        <?php endif; ?>
        <p><strong>Generated:</strong> <?= $e($generated_date) ?></p>
    </div>

    <h2>Compliance Statistics</h2>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Controls</div>
            <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Met</div>
            <div class="stat-value text-success"><?= $stats['met'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Partially Met</div>
            <div class="stat-value text-warning"><?= $stats['partially_met'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Not Met</div>
            <div class="stat-value text-danger"><?= $stats['not_met'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Not Applicable</div>
            <div class="stat-value"><?= $stats['not_applicable'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Compliance %</div>
            <div class="stat-value <?= $stats['percentage'] >= 75 ? 'text-success' : ($stats['percentage'] >= 50 ? 'text-warning' : 'text-danger') ?>">
                <?= number_format($stats['percentage'] ?? 0, 1) ?>%
            </div>
        </div>
    </div>

    <?php if ($assessment['notes']): ?>
    <h2>Assessment Notes</h2>
    <div class="finding-notes">
        <?= nl2br($e($assessment['notes'])) ?>
    </div>
    <?php endif; ?>

    <div class="page-break"></div>

    <h2>Control Findings</h2>

    <?php foreach ($findings as $domain => $domainFindings): ?>
    <div class="domain-section">
        <div class="domain-header">
            <?= $e($domain) ?> (<?= count($domainFindings) ?> controls)
        </div>

        <?php foreach ($domainFindings as $finding): ?>
        <div class="finding">
            <div class="finding-header">
                <div class="control-info">
                    <div class="control-code"><?= $e($finding['control_code']) ?></div>
                    <div class="control-title"><?= $e($finding['title']) ?></div>
                </div>
                <div>
                    <?php
                    $statusClass = 'badge-' . $finding['status'];
                    $statusText = str_replace('_', ' ', ucfirst($finding['status']));
                    ?>
                    <span class="status-badge <?= $statusClass ?>"><?= $e($statusText) ?></span>
                </div>
            </div>

            <div class="control-description">
                <?= nl2br($e($finding['description'])) ?>
            </div>

            <?php if (!empty($finding['notes'])): ?>
            <div class="finding-notes">
                <div class="notes-label">Finding Notes:</div>
                <?= nl2br($e($finding['notes'])) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($finding['objective_evidence'])): ?>
            <div class="finding-notes">
                <div class="notes-label">Objective Evidence:</div>
                <?= nl2br($e($finding['objective_evidence'])) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($finding['compensating_controls'])): ?>
            <div class="finding-notes">
                <div class="notes-label">Compensating Controls:</div>
                <?= nl2br($e($finding['compensating_controls'])) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <footer>
        <p>
            <strong><?= $e($assessment['framework']) ?> Assessment Report</strong><br>
            Generated <?= $e($generated_date) ?> | <?= $stats['total'] ?? 0 ?> Controls<br>
            Layer3 | Trident Cyber OneComply
        </p>
    </footer>
</body>
</html>
