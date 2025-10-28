<?php
$page_title = 'Assessments';
$current_page = 'assessments';

ob_start();
?>

<div class="page-header">
    <h1>Assessments</h1>
    <p class="page-subtitle">Compliance Assessments & Gap Analysis</p>
    <div class="page-actions">
        <a href="<?= $url('assessments/create') ?>" class="btn btn-primary">New Assessment</a>
    </div>
</div>

<?php if (!$current_customer): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        Please <a href="<?= $url('customers') ?>">select a customer</a> to view assessments.
    </div>
<?php elseif (empty($assessments)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No assessments found. <a href="<?= $url('assessments/create') ?>">Create your first assessment</a>.
    </div>
<?php else: ?>
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Framework</th>
                    <th>Status</th>
                    <th>Assessed Date</th>
                    <th>Completed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assessments as $assessment): ?>
                <tr>
                    <td>
                        <strong><?= $e($assessment['framework'] ?? 'Unknown') ?> Assessment #<?= $e($assessment['id']) ?></strong>
                        <?php if (!empty($assessment['scope'])): ?>
                            <br><small><?= $e($assessment['scope']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge"><?= $e($assessment['framework'] ?? 'N/A') ?></span></td>
                    <td>
                        <?php if ($assessment['status'] === 'published'): ?>
                            <span class="badge badge-success">Published</span>
                        <?php elseif ($assessment['status'] === 'in_progress'): ?>
                            <span class="badge badge-warning">In Progress</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $assessment['assessed_at'] ? date('M d, Y', strtotime($assessment['assessed_at'])) : '-' ?></td>
                    <td>
                        <?php
                        $progress = ($assessment['controls_completed'] / max($assessment['total_controls'], 1)) * 100;
                        ?>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= $progress ?>%"><?= round($progress) ?>%</div>
                        </div>
                    </td>
                    <td>
                        <a href="<?= $url('assessments/' . $assessment['id']) ?>" class="btn btn-sm btn-primary">Continue</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
