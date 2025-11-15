<?php
$page_title = 'Risk Assessment Results - ' . $assessment['title'];
$current_page = 'risk-assessments';

ob_start();
?>

<div class="page-header">
    <h1>📊 Risk Assessment Results</h1>
    <p class="page-subtitle"><?= $e($assessment['title']) ?> - <?= $e($assessment['customer_name']) ?></p>
</div>

<!-- Executive Summary -->
<div class="card">
    <div class="card-header">
        <h3>Executive Summary</h3>
        <div>
            <span class="badge badge-info">Overall Risk Score: <?= number_format($assessment['overall_risk_score'], 1) ?> / 25</span>
            <?php if ($assessment['completed_at']): ?>
                <span style="margin-left: 10px; color: #666;">
                    Completed: <?= date('M j, Y g:i A', strtotime($assessment['completed_at'])) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    <div style="padding: 20px;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
            <div style="text-align: center; padding: 20px; background: #fee2e2; border-radius: 6px;">
                <div style="font-size: 2.5em; font-weight: bold; color: #991b1b;">
                    <?= $risk_summary['critical_risks'] ?? 0 ?>
                </div>
                <div style="font-weight: 500; color: #991b1b;">Critical Risks</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #fed7aa; border-radius: 6px;">
                <div style="font-size: 2.5em; font-weight: bold; color: #9a3412;">
                    <?= $risk_summary['high_risks'] ?? 0 ?>
                </div>
                <div style="font-weight: 500; color: #9a3412;">High Risks</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #fef3c7; border-radius: 6px;">
                <div style="font-size: 2.5em; font-weight: bold; color: #92400e;">
                    <?= $risk_summary['medium_risks'] ?? 0 ?>
                </div>
                <div style="font-weight: 500; color: #92400e;">Medium Risks</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #d1fae5; border-radius: 6px;">
                <div style="font-size: 2.5em; font-weight: bold; color: #065f46;">
                    <?= $risk_summary['low_risks'] ?? 0 ?>
                </div>
                <div style="font-weight: 500; color: #065f46;">Low Risks</div>
            </div>
        </div>

        <?php if (!empty($assessment['description'])): ?>
        <div style="background: #f7fafc; padding: 15px; border-left: 4px solid #667eea; margin-bottom: 20px;">
            <strong>Assessment Description:</strong><br>
            <?= nl2br($e($assessment['description'])) ?>
        </div>
        <?php endif; ?>

        <div style="padding: 15px; background: #fffbeb; border-left: 4px solid #f59e0b;">
            <strong>📌 Recommendation:</strong>
            <?php
            $criticalCount = $risk_summary['critical_risks'] ?? 0;
            $highCount = $risk_summary['high_risks'] ?? 0;

            if ($criticalCount > 0) {
                echo "Address <strong>{$criticalCount} critical risk(s)</strong> immediately. These represent severe vulnerabilities that could lead to significant security incidents.";
            } elseif ($highCount > 0) {
                echo "Prioritize remediation of <strong>{$highCount} high risk(s)</strong>. These should be addressed within 30 days.";
            } else {
                echo "Focus on medium risks to further strengthen your security posture. Continue monitoring and maintain good security practices.";
            }
            ?>
        </div>
    </div>
</div>

<!-- Top Risks Requiring Immediate Attention -->
<?php if (!empty($topRisks)): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>⚠️ Top Risks Requiring Immediate Attention</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Risk</th>
                <th>Risk Level</th>
                <th>Score</th>
                <th>L×I</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topRisks as $risk): ?>
            <tr>
                <td><?= $e($risk['category']) ?></td>
                <td><?= $e($risk['question_text']) ?></td>
                <td>
                    <?php
                    $badgeClass = 'badge-secondary';
                    if ($risk['risk_level'] === 'critical') $badgeClass = 'badge-danger';
                    elseif ($risk['risk_level'] === 'high') $badgeClass = 'badge-danger';
                    elseif ($risk['risk_level'] === 'medium') $badgeClass = 'badge-warning';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($risk['risk_level']) ?></span>
                </td>
                <td><strong><?= $risk['risk_score'] ?></strong></td>
                <td><?= $risk['likelihood'] ?> × <?= $risk['impact'] ?></td>
                <td><?= $e($risk['notes'] ?: '—') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Detailed Results by Category -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Detailed Results by Category</h3>
    </div>

    <?php foreach ($responses_by_category as $category => $categoryResponses): ?>
        <div style="border-bottom: 1px solid #e5e7eb; padding: 20px;">
            <h4 style="margin-top: 0; color: #4a5568;">📋 <?= $e($category) ?></h4>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Question</th>
                        <th>Response</th>
                        <th>Risk Level</th>
                        <th>Score</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoryResponses as $response): ?>
                    <tr>
                        <td>
                            <?= $e($response['question_text']) ?>
                            <?php if (!empty($response['subcategory'])): ?>
                                <br><small style="color: #666;"><?= $e($response['subcategory']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $responseBadge = 'badge-secondary';
                            $responseText = ucfirst($response['response_value']);
                            if ($response['response_value'] === 'yes') {
                                $responseBadge = 'badge-success';
                                $responseText = '✓ Yes';
                            } elseif ($response['response_value'] === 'no') {
                                $responseBadge = 'badge-danger';
                                $responseText = '✗ No';
                            } elseif ($response['response_value'] === 'partial') {
                                $responseBadge = 'badge-warning';
                                $responseText = '⚠ Partial';
                            }
                            ?>
                            <span class="badge <?= $responseBadge ?>"><?= $responseText ?></span>
                        </td>
                        <td>
                            <?php
                            $riskBadge = 'badge-secondary';
                            if ($response['risk_level'] === 'critical') $riskBadge = 'badge-danger';
                            elseif ($response['risk_level'] === 'high') $riskBadge = 'badge-danger';
                            elseif ($response['risk_level'] === 'medium') $riskBadge = 'badge-warning';
                            elseif ($response['risk_level'] === 'low') $riskBadge = 'badge-success';
                            ?>
                            <span class="badge <?= $riskBadge ?>"><?= ucfirst($response['risk_level']) ?></span>
                        </td>
                        <td>
                            <?= $response['risk_score'] ?>
                            <small style="color: #666;">(<?= $response['likelihood'] ?>×<?= $response['impact'] ?>)</small>
                        </td>
                        <td>
                            <?php if (!empty($response['notes'])): ?>
                                <?= $e(substr($response['notes'], 0, 100)) ?><?= strlen($response['notes']) > 100 ? '...' : '' ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>

<!-- Actions -->
<div class="form-actions" style="margin-top: 20px;">
    <a href="<?= $url('risk-assessments/' . $assessment['id'] . '/questionnaire') ?>" class="btn btn-secondary">Edit Responses</a>
    <a href="<?= $url('risk-assessments') ?>" class="btn btn-secondary">Back to Assessments</a>
    <button onclick="window.print()" class="btn btn-primary">Print Report</button>
</div>

<style>
@media print {
    .nav, .sidebar, .form-actions, .btn { display: none !important; }
    .card { page-break-inside: avoid; }
}
</style>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
