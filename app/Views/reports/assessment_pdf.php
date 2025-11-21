<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($framework ?? 'Compliance') ?> Report</title>
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

        .control-item {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 12px;
            background: #fff;
            page-break-inside: avoid;
        }

        .control-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
        }

        .control-code {
            font-family: 'Courier New', monospace;
            font-size: 11pt;
            font-weight: bold;
            color: #007bff;
        }

        .control-title {
            font-weight: bold;
            font-size: 10pt;
            color: #1a1a1a;
            margin-top: 3px;
        }

        .control-description {
            font-size: 9pt;
            color: #555;
            margin-top: 8px;
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

        .badge-met, .badge-Met {
            background: #d4edda;
            color: #155724;
        }

        .badge-partially_met, .badge-Partially_Met {
            background: #fff3cd;
            color: #856404;
        }

        .badge-not_met, .badge-Not_Met {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-not_applicable, .badge-Not_Applicable {
            background: #e2e3e5;
            color: #383d41;
        }

        .badge-not_assessed, .badge-Not_Assessed {
            background: #e7e7e7;
            color: #666;
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

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-bottom: 25px;
        }

        .stat-box {
            padding: 10px;
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
            font-size: 16pt;
            font-weight: bold;
            color: #1a1a1a;
        }

        .ml-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            margin-left: 5px;
        }

        .ml-badge-1 { background: #d1ecf1; color: #0c5460; }
        .ml-badge-2 { background: #fff3cd; color: #856404; }
        .ml-badge-3 { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print to PDF</button>

    <h1><?= $e($framework_name ?? $framework ?? 'Compliance Report') ?></h1>

    <div class="header-info">
        <p><strong>Report Type:</strong> <?= $e($report_type ?? 'Compliance Assessment') ?></p>
        <p><strong>Framework:</strong> <?= $e($framework ?? 'N/A') ?></p>
        <?php if (!empty($customer_name)): ?>
        <p><strong>Customer:</strong> <?= $e($customer_name) ?></p>
        <?php endif; ?>
        <?php if (!empty($assessor_name)): ?>
        <p><strong>Assessed by:</strong> <?= $e($assessor_name) ?></p>
        <?php endif; ?>
        <p><strong>Generated:</strong> <?= $e($generated_date ?? date('F j, Y g:i A')) ?></p>
        <p><strong>Status:</strong> <?= $e($status ?? 'Published') ?></p>
    </div>

    <?php if (!empty($stats)): ?>
    <h2>Summary Statistics</h2>
    <div class="stats-summary">
        <?php if (isset($stats['total'])): ?>
        <div class="stat-box">
            <div class="stat-label">Total Controls</div>
            <div class="stat-value"><?= $e($stats['total']) ?></div>
        </div>
        <?php endif; ?>

        <?php if (isset($stats['met'])): ?>
        <div class="stat-box">
            <div class="stat-label">Met</div>
            <div class="stat-value" style="color: #28a745;"><?= $e($stats['met']) ?></div>
        </div>
        <?php endif; ?>

        <?php if (isset($stats['partially_met'])): ?>
        <div class="stat-box">
            <div class="stat-label">Partially Met</div>
            <div class="stat-value" style="color: #ffc107;"><?= $e($stats['partially_met']) ?></div>
        </div>
        <?php endif; ?>

        <?php if (isset($stats['not_met'])): ?>
        <div class="stat-box">
            <div class="stat-label">Not Met</div>
            <div class="stat-value" style="color: #dc3545;"><?= $e($stats['not_met']) ?></div>
        </div>
        <?php endif; ?>

        <?php if (isset($stats['not_applicable'])): ?>
        <div class="stat-box">
            <div class="stat-label">Not Applicable</div>
            <div class="stat-value"><?= $e($stats['not_applicable']) ?></div>
        </div>
        <?php endif; ?>

        <?php if (isset($stats['not_assessed'])): ?>
        <div class="stat-box">
            <div class="stat-label">Not Assessed</div>
            <div class="stat-value"><?= $e($stats['not_assessed']) ?></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <h2>Control Details</h2>

    <?php if (!empty($controls) && is_array($controls)): ?>
        <?php foreach ($controls as $control): ?>
        <div class="control-item">
            <div class="control-header">
                <div>
                    <span class="control-code"><?= $e($control['code'] ?? $control['control_code'] ?? 'N/A') ?></span>
                    <?php if (!empty($control['ml_level'])): ?>
                        <span class="ml-badge ml-badge-<?= $e($control['ml_level']) ?>">ML<?= $e($control['ml_level']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($control['status'])): ?>
                <div>
                    <?php
                    $status = $control['status'];
                    $statusClass = 'badge-' . str_replace(' ', '_', $status);
                    $statusText = str_replace('_', ' ', ucfirst($status));
                    ?>
                    <span class="status-badge <?= $statusClass ?>"><?= $e($statusText) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($control['title'])): ?>
            <div class="control-title"><?= $e($control['title']) ?></div>
            <?php endif; ?>

            <?php if (!empty($control['description'])): ?>
            <div class="control-description">
                <?= nl2br($e($control['description'])) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No controls found for this report.</p>
    <?php endif; ?>

    <footer>
        <p>
            <strong><?= $e($framework_name ?? $framework ?? 'Compliance Report') ?></strong><br>
            Generated <?= $e($generated_date ?? date('F j, Y g:i A')) ?>
            <?php if (!empty($stats['total'])): ?>
                | <?= $e($stats['total']) ?> Controls
            <?php endif; ?>
            <br>
            Layer3 | Trident Cyber OneComply
        </p>
    </footer>
</body>
</html>
