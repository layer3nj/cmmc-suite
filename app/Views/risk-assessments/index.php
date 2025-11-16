<?php
$page_title = 'Risk Assessments';
$current_page = 'risk-assessments';

ob_start();
?>

<div class="page-header">
    <h1>🎯 Risk Assessments</h1>
    <p class="page-subtitle">Evaluate and manage cybersecurity risks for <?= $e($customer['name']) ?></p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Assessments</h3>
        <a href="<?= $url('risk-assessments/create') ?>" class="btn btn-primary">+ New Risk Assessment</a>
    </div>

    <?php if (empty($assessments)): ?>
        <div style="padding: 40px; text-align: center; color: #666;">
            <p style="font-size: 1.1em; margin-bottom: 20px;">No risk assessments yet.</p>
            <p>Start your first cybersecurity risk assessment to identify vulnerabilities and prioritize remediation efforts.</p>
            <a href="<?= $url('risk-assessments/create') ?>" class="btn btn-primary" style="margin-top: 15px;">Create First Assessment</a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Risk Score</th>
                    <th>High Risks</th>
                    <th>Medium Risks</th>
                    <th>Low Risks</th>
                    <th>Created By</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assessments as $assessment): ?>
                <tr>
                    <td>
                        <strong><?= $e($assessment['title']) ?></strong>
                        <?php if (!empty($assessment['description'])): ?>
                            <br><small style="color: #666;"><?= $e(substr($assessment['description'], 0, 80)) ?><?= strlen($assessment['description']) > 80 ? '...' : '' ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $statusBadge = 'badge-secondary';
                        $statusText = ucfirst(str_replace('_', ' ', $assessment['status']));
                        if ($assessment['status'] === 'completed') $statusBadge = 'badge-success';
                        elseif ($assessment['status'] === 'in_progress') $statusBadge = 'badge-warning';
                        ?>
                        <span class="badge <?= $statusBadge ?>"><?= $statusText ?></span>
                    </td>
                    <td>
                        <?php if ($assessment['overall_risk_score'] > 0): ?>
                            <strong><?= number_format($assessment['overall_risk_score'], 1) ?></strong> / 25
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($assessment['high_risks'] > 0): ?>
                            <span class="badge badge-danger"><?= $assessment['high_risks'] ?></span>
                        <?php else: ?>
                            <span style="color: #999;">0</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($assessment['medium_risks'] > 0): ?>
                            <span class="badge badge-warning"><?= $assessment['medium_risks'] ?></span>
                        <?php else: ?>
                            <span style="color: #999;">0</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($assessment['low_risks'] > 0): ?>
                            <span class="badge badge-success"><?= $assessment['low_risks'] ?></span>
                        <?php else: ?>
                            <span style="color: #999;">0</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $e($assessment['created_by_name'] ?? 'Unknown') ?></td>
                    <td><?= date('M j, Y', strtotime($assessment['created_at'])) ?></td>
                    <td>
                        <?php if ($assessment['status'] === 'completed'): ?>
                            <a href="<?= $url('risk-assessments/' . $assessment['id'] . '/results') ?>" class="btn btn-sm btn-primary">View Results</a>
                        <?php else: ?>
                            <a href="<?= $url('risk-assessments/' . $assessment['id'] . '/questionnaire') ?>" class="btn btn-sm btn-secondary">Continue</a>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-danger" onclick="deleteAssessment(<?= $assessment['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function deleteAssessment(id) {
    if (!confirm('Are you sure you want to delete this risk assessment? This cannot be undone.')) {
        return;
    }

    fetch('<?= $url('risk-assessments') ?>/' + id + '/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_csrf_token=<?= $csrfToken() ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
