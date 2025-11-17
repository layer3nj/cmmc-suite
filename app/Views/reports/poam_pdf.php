<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>POA&M Report - <?= $e($client['name']) ?></title>
    <style>
        @page { margin: 0.75in; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.4; color: #333; margin: 0; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #000; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18pt; }
        .header h2 { margin: 5px 0; font-size: 14pt; color: #666; font-weight: normal; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .info-table td:first-child { font-weight: bold; width: 150px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; font-size: 10pt; }
        th { background: #f0f0f0; font-weight: bold; }
        .status-open { color: #dc3545; font-weight: bold; }
        .status-in-progress { color: #ffc107; font-weight: bold; }
        .status-closed { color: #28a745; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc; font-size: 9pt; color: #666; text-align: center; }
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">Close</button>
    </div>

    <div class="header">
        <h1>Plan of Action & Milestones (POA&M)</h1>
        <h2><?= $e($client['name']) ?></h2>
        <div style="margin-top: 10px; font-size: 10pt;">
            Generated: <?= date('F d, Y') ?> | <?= $e($settings['site_name'] ?? 'CMMC Compliance Suite') ?>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td>Organization:</td>
            <td><?= $e($client['name']) ?></td>
        </tr>
        <tr>
            <td>Report Date:</td>
            <td><?= date('F d, Y') ?></td>
        </tr>
        <tr>
            <td>Total Items:</td>
            <td><?= count($poam_items) ?></td>
        </tr>
        <tr>
            <td>Open Items:</td>
            <td>
                <?php
                $open = count(array_filter($poam_items, fn($item) => in_array($item['status'], ['open', 'in_progress'])));
                echo $open;
                ?>
            </td>
        </tr>
    </table>

    <?php if (empty($poam_items)): ?>
        <p style="text-align: center; padding: 40px; color: #666;">No POA&M items found for this client.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">Control</th>
                    <th>Weakness Description</th>
                    <th style="width: 100px;">Responsible</th>
                    <th style="width: 90px;">Target Date</th>
                    <th style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($poam_items as $item): ?>
                <tr>
                    <td><?= $e($item['control_code'] ?? 'N/A') ?></td>
                    <td>
                        <strong><?= $e($item['weakness']) ?></strong>
                        <?php if (!empty($item['remediation_plan'])): ?>
                            <br><small><strong>Remediation:</strong> <?= $e($item['remediation_plan']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= $e($item['responsible_party'] ?? '-') ?></td>
                    <td><?= $item['planned_completion_date'] ? date('m/d/Y', strtotime($item['planned_completion_date'])) : '-' ?></td>
                    <td class="status-<?= $item['status'] ?>"><?= ucfirst(str_replace('_', ' ', $item['status'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        <p><strong><?= $e($settings['site_name'] ?? 'CMMC Compliance Suite') ?></strong></p>
        <p>This document contains sensitive security information. Handle in accordance with organizational policies.</p>
    </div>
</body>
</html>
