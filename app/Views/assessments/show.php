<?php
$page_title = 'Assessment - ' . ($assessment['framework'] ?? 'Unknown');
$current_page = 'assessments';

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('assessments') ?>">Assessments</a> / Assessment #<?= $e($assessment['id']) ?>
    </div>
    <h1><?= $e($assessment['framework'] ?? 'Unknown Framework') ?> Assessment</h1>
    <div class="page-actions">
        <?php if ($assessment['status'] === 'draft'): ?>
            <button type="button" class="btn btn-primary" onclick="publishAssessment()">Publish Assessment</button>
        <?php else: ?>
            <span class="badge badge-success">Published</span>
        <?php endif; ?>
        <a href="<?= $url('assessments') ?>" class="btn btn-secondary">Back to Assessments</a>
    </div>
</div>

<?php if ($successMessage = $success()): ?>
    <div class="alert alert-success"><?= $e($successMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Assessment Details</h3>
    </div>

    <table class="info-table">
        <tr>
            <th>Framework</th>
            <td><?= $e($assessment['framework'] ?? 'Not specified') ?></td>
        </tr>
        <?php if (!empty($assessment['assessment_type'])): ?>
        <tr>
            <th>Assessment Type</th>
            <td><?= $e(ucfirst($assessment['assessment_type'])) ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($assessment['scope'])): ?>
        <tr>
            <th>Scope</th>
            <td><?= $e($assessment['scope']) ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($assessment['target_level'])): ?>
        <tr>
            <th>Target Level</th>
            <td>Level <?= $e($assessment['target_level']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Assessor</th>
            <td><?= $e($assessment['assessor_name'] ?? 'Unknown') ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <?php if ($assessment['status'] === 'published'): ?>
                    <span class="badge badge-success">Published</span>
                <?php elseif ($assessment['status'] === 'in_progress'): ?>
                    <span class="badge badge-warning">In Progress</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Draft</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Created</th>
            <td><?= date('M d, Y g:i A', strtotime($assessment['created_at'])) ?></td>
        </tr>
        <?php if (!empty($assessment['assessed_at'])): ?>
        <tr>
            <th>Assessed Date</th>
            <td><?= date('M d, Y g:i A', strtotime($assessment['assessed_at'])) ?></td>
        </tr>
        <?php endif; ?>
        <?php if (isset($assessment['sprs_score']) && $assessment['sprs_score'] !== null): ?>
        <tr>
            <th>SPRS Score</th>
            <td>
                <strong class="<?= $assessment['sprs_score'] >= 0 ? 'text-success' : 'text-danger' ?>">
                    <?= $e($assessment['sprs_score']) ?>
                </strong>
            </td>
        </tr>
        <?php endif; ?>
        <?php if ($assessment['notes']): ?>
        <tr>
            <th>Notes</th>
            <td><?= nl2br($e($assessment['notes'])) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Compliance Statistics</h3>
    </div>

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
</div>

<form action="<?= $url('assessments/' . $assessment['id'] . '/findings') ?>" method="POST" id="findings-form">
    <?= $csrf() ?>

    <?php foreach ($findings as $domain => $domainFindings): ?>
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3><?= $e($domain) ?></h3>
            <span class="badge"><?= count($domainFindings) ?> controls</span>
        </div>

        <div class="card-body">
            <?php foreach ($domainFindings as $finding): ?>
            <div class="control-item">
                <div class="control-header">
                    <div>
                        <div class="control-code"><?= $e($finding['control_code']) ?></div>
                        <div class="control-title"><?= $e($finding['title']) ?></div>
                    </div>
                    <div>
                        <select name="findings[<?= $e($finding['id']) ?>][status]" class="form-control" style="width: 200px;">
                            <option value="not_met" <?= $finding['status'] === 'not_met' ? 'selected' : '' ?>>Not Met</option>
                            <option value="partially_met" <?= $finding['status'] === 'partially_met' ? 'selected' : '' ?>>Partially Met</option>
                            <option value="met" <?= $finding['status'] === 'met' ? 'selected' : '' ?>>Met</option>
                            <option value="not_applicable" <?= $finding['status'] === 'not_applicable' ? 'selected' : '' ?>>Not Applicable</option>
                        </select>
                    </div>
                </div>
                <div class="control-description"><?= $e($finding['description']) ?></div>

                <div class="form-group" style="margin-top: 10px;">
                    <label style="font-size: 13px;">Finding Notes</label>
                    <textarea name="findings[<?= $e($finding['id']) ?>][notes]" rows="2" class="form-control" placeholder="Add notes, evidence, or explanation..."><?= $e($finding['notes'] ?? '') ?></textarea>
                </div>

                <?php if ($finding['ml_level']): ?>
                <div class="control-meta">
                    <span>Maturity Level: <?= $e($finding['ml_level']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if ($assessment['status'] !== 'published'): ?>
    <div class="form-actions" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary">Save Findings</button>
        <a href="<?= $url('assessments') ?>" class="btn btn-secondary">Cancel</a>
    </div>
    <?php endif; ?>
</form>

<?php if ($assessment['status'] === 'draft'): ?>
<div class="form-actions" style="margin-top: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
    <form action="<?= $url('assessments/' . $assessment['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.');">
        <?= $csrf() ?>
        <button type="submit" class="btn btn-danger">Delete Assessment</button>
    </form>
</div>
<?php endif; ?>

<script>
function publishAssessment() {
    if (!confirm('Are you sure you want to publish this assessment? Once published, findings cannot be modified.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= $url('assessments/' . $assessment['id'] . '/publish') ?>';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_csrf_token';
    csrfInput.value = '<?= \App\Core\Csrf::generateToken() ?>';

    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
