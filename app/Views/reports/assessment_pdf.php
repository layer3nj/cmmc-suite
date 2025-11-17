<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Assessment Report - <?= $e($client['name']) ?></title>
    <style>
        @page { margin: 0.75in; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.4; color: #333; margin: 0; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #000; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18pt; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0; }
        .stat-box { border: 2px solid #ddd; padding: 15px; text-align: center; }
        .stat-value { font-size: 24pt; font-weight: bold; margin: 5px 0; }
        .stat-label { font-size: 10pt; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #999; padding: 8px; font-size: 10pt; }
        th { background: #f0f0f0; font-weight: bold; }
        .compliant { color: #28a745; font-weight: bold; }
        .non-compliant { color: #dc3545; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
        <button onclick="window.print()" style="padding: 10px 20px;">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px;">Close</button>
    </div>

    <div class="header">
        <h1><?= $e($assessment['framework']) ?> Assessment Report</h1>
        <h2><?= $e($client['name']) ?></h2>
        <div style="margin-top: 10px;">
            Generated: <?= date('F d, Y') ?> | Assessment Date: <?= date('F d, Y', strtotime($assessment['assessed_at'])) ?>
        </div>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-label">Total Controls</div>
            <div class="stat-value"><?= $stats['total'] ?></div>
        </div>
        <div class="stat-box" style="border-color: #28a745;">
            <div class="stat-label">Compliant</div>
            <div class="stat-value" style="color: #28a745;"><?= $stats['compliant'] ?></div>
            <div class="stat-label"><?= $stats['total'] > 0 ? round(($stats['compliant'] / $stats['total']) * 100) : 0 ?>%</div>
        </div>
        <div class="stat-box" style="border-color: #dc3545;">
            <div class="stat-label">Non-Compliant</div>
            <div class="stat-value" style="color: #dc3545;"><?= $stats['non_compliant'] ?></div>
            <div class="stat-label"><?= $stats['total'] > 0 ? round(($stats['non_compliant'] / $stats['total']) * 100) : 0 ?>%</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Not Applicable</div>
            <div class="stat-value"><?= $stats['not_applicable'] ?></div>
        </div>
    </div>

    <h3 style="margin-top: 30px;">Assessment Details</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Control</th>
                <th>Response</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($responses as $response): ?>
            <tr>
                <td><?= $e($response['control_code']) ?></td>
                <td class="<?= in_array($response['response'], ['compliant', 'yes']) ? 'compliant' : (in_array($response['response'], ['non_compliant', 'no']) ? 'non-compliant' : '') ?>">
                    <?= ucfirst(str_replace('_', ' ', $response['response'])) ?>
                </td>
                <td><?= $e($response['notes'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc; text-align: center; font-size: 9pt; color: #666;">
        <p><strong><?= $e($settings['site_name'] ?? 'CMMC Compliance Suite') ?></strong></p>
        <p>Assessed by: <?= $e($assessment['assessed_by']) ?> | Status: <?= ucfirst($assessment['status']) ?></p>
    </div>
</body>
</html>
