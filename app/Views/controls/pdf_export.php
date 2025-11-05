<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($framework_name) ?> - Controls Export</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
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
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 24pt;
            color: #1a1a1a;
            margin-bottom: 10px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }

        .header-info {
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .header-info p {
            margin: 5px 0;
            font-size: 10pt;
        }

        .control {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            padding: 15px;
            background: #fff;
        }

        .control-header {
            background: #007bff;
            color: white;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            font-weight: bold;
        }

        .control-code {
            font-family: 'Courier New', monospace;
            font-size: 12pt;
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 8px;
            border-radius: 3px;
        }

        .control-title {
            font-weight: bold;
            font-size: 13pt;
            margin-bottom: 10px;
            color: #1a1a1a;
        }

        .control-description {
            margin-bottom: 10px;
            text-align: justify;
        }

        .control-meta {
            display: flex;
            gap: 20px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 9pt;
            color: #666;
        }

        .meta-item {
            display: flex;
            gap: 5px;
        }

        .meta-label {
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }

        .badge-ml1 { background: #d1ecf1; color: #0c5460; }
        .badge-ml2 { background: #fff3cd; color: #856404; }
        .badge-ml3 { background: #f8d7da; color: #721c24; }
        .badge-high { background: #f8d7da; color: #721c24; }
        .badge-medium { background: #fff3cd; color: #856404; }
        .badge-low { background: #d1ecf1; color: #0c5460; }

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
        }

        .print-button:hover {
            background: #0056b3;
        }

        .toc {
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .toc h2 {
            margin-top: 0;
            font-size: 16pt;
        }

        .toc ul {
            column-count: 2;
            column-gap: 20px;
            font-size: 9pt;
        }

        footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print to PDF</button>

    <?php if (!empty($app_logo)): ?>
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="<?= $app_logo ?>" alt="<?= $e($app_name ?? 'CMMC Compliance Suite') ?>" style="max-height: 80px; max-width: 300px;">
        </div>
    <?php endif; ?>

    <h1><?= $e($framework_name) ?></h1>

    <div class="header-info">
        <p><strong>Framework:</strong> <?= $e($framework) ?></p>
        <p><strong>Total Controls:</strong> <?= $total_controls ?></p>
        <p><strong>Generated:</strong> <?= $e($generated_date) ?></p>
        <p><strong>Document Type:</strong> Control Framework Reference</p>
    </div>

    <?php if ($total_controls > 20): ?>
    <div class="toc no-print">
        <h2>Table of Contents</h2>
        <ul>
            <?php foreach ($controls as $control): ?>
                <li>
                    <strong><?= $e($control['code']) ?></strong> - <?= $e($control['title']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="page-break"></div>
    <?php endif; ?>

    <?php foreach ($controls as $index => $control): ?>
        <div class="control">
            <div class="control-header">
                <span class="control-code"><?= $e($control['code']) ?></span>
            </div>

            <div class="control-title"><?= $e($control['title']) ?></div>

            <div class="control-description">
                <?= nl2br($e($control['description'] ?? 'No description available.')) ?>
            </div>

            <div class="control-meta">
                <?php if (!empty($control['ml_level'])): ?>
                    <div class="meta-item">
                        <span class="meta-label">Maturity Level:</span>
                        <span class="badge badge-ml<?= $control['ml_level'] ?>">ML<?= $control['ml_level'] ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($control['stig_severity'])): ?>
                    <div class="meta-item">
                        <span class="meta-label">STIG Severity:</span>
                        <span class="badge badge-<?= $control['stig_severity'] ?>"><?= ucfirst($e($control['stig_severity'])) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($control['soc2_category'])): ?>
                    <div class="meta-item">
                        <span class="meta-label">SOC 2 Category:</span>
                        <span><?= $e($control['soc2_category']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (($index + 1) % 3 === 0 && ($index + 1) < count($controls)): ?>
            <div class="page-break"></div>
        <?php endif; ?>
    <?php endforeach; ?>

    <footer>
        <p>
            <strong><?= $e($framework_name) ?></strong><br>
            Generated <?= $e($generated_date) ?> | <?= $total_controls ?> Controls<br>
            CMMC Compliance Suite
        </p>
    </footer>
</body>
</html>
