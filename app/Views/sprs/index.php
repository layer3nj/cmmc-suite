<?php
$page_title = 'SPRS Score';
$current_page = 'sprs';

ob_start();
?>

<div class="page-header">
    <h1>SPRS Score Calculator</h1>
    <p class="page-subtitle">DoD NIST SP 800-171 Assessment Methodology</p>
    <div class="page-actions">
        <a href="<?= $url('sprs/export') ?>" class="btn btn-secondary">Export CSV</a>
    </div>
</div>

<?php if (!$current_customer): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        Please <a href="<?= $url('customers') ?>">select a customer</a> to view SPRS scores.
    </div>
<?php elseif (!$assessment): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No published NIST SP 800-171 assessment found. <a href="<?= $url('assessments/create') ?>">Create an assessment</a> first.
    </div>
<?php else: ?>
    <div class="score-display">
        <div class="score-card primary">
            <div class="score-label">Current SPRS Score</div>
            <div class="score-value <?= $scores['current_score'] >= 0 ? 'positive' : 'negative' ?>">
                <?= $scores['current_score'] ?>
            </div>
            <div class="score-sublabel">out of 110 possible points</div>
            <div class="score-detail">
                <?= $scores['current_deductions'] ?> points deducted
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Score Breakdown by Domain</h3>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Domain</th>
                    <th>Practices</th>
                    <th>Met</th>
                    <th>Partially Met</th>
                    <th>Not Met</th>
                    <th>N/A</th>
                    <th>Deductions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($breakdown as $domain): ?>
                <tr>
                    <td><strong><?= $e($domain['domain']) ?></strong></td>
                    <td><?= $domain['total'] ?></td>
                    <td class="text-success"><?= $domain['met'] ?></td>
                    <td class="text-warning"><?= $domain['partially_met'] ?></td>
                    <td class="text-danger"><?= $domain['not_met'] ?></td>
                    <td class="text-muted"><?= $domain['not_applicable'] ?></td>
                    <td><span class="badge badge-danger">-<?= $domain['deductions'] ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th><?= array_sum(array_column($breakdown, 'total')) ?></th>
                    <th class="text-success"><?= array_sum(array_column($breakdown, 'met')) ?></th>
                    <th class="text-warning"><?= array_sum(array_column($breakdown, 'partially_met')) ?></th>
                    <th class="text-danger"><?= array_sum(array_column($breakdown, 'not_met')) ?></th>
                    <th class="text-muted"><?= array_sum(array_column($breakdown, 'not_applicable')) ?></th>
                    <th><span class="badge badge-danger">-<?= $scores['current_deductions'] ?></span></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="alert alert-info">
        <strong>About SPRS Scoring:</strong><br>
        The Supplier Performance Risk System (SPRS) score is calculated using the DoD Assessment Methodology.
        Each Not Met practice deducts 5 points, each Partially Met deducts 3 points.
        The minimum score is -203 (all 110 practices Not Met: -110×5 + 110×(-3) = -203).
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
