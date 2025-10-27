<?php
$page_title = 'POA&M';
$current_page = 'poam';

ob_start();
?>

<div class="page-header">
    <h1>Plan of Action & Milestones (POA&M)</h1>
    <p class="page-subtitle">Track remediation efforts for compliance gaps</p>
    <div class="page-actions">
        <a href="<?= $url('poam/generate') ?>" class="btn btn-secondary">Generate from Assessment</a>
        <a href="<?= $url('poam/create') ?>" class="btn btn-primary">Add POA&M Item</a>
    </div>
</div>

<?php if (!$current_customer): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        Please <a href="<?= $url('customers') ?>">select a customer</a> to view POA&M items.
    </div>
<?php elseif (empty($poam_items)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No POA&M items found. <a href="<?= $url('poam/generate') ?>">Generate from assessment</a> or <a href="<?= $url('poam/create') ?>">create manually</a>.
    </div>
<?php else: ?>
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Items</div>
            <div class="stat-value"><?= count($poam_items) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Open</div>
            <div class="stat-value text-warning"><?= count(array_filter($poam_items, fn($i) => $i['status'] === 'open')) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">In Progress</div>
            <div class="stat-value text-info"><?= count(array_filter($poam_items, fn($i) => $i['status'] === 'in_progress')) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Completed</div>
            <div class="stat-value text-success"><?= count(array_filter($poam_items, fn($i) => $i['status'] === 'completed')) ?></div>
        </div>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Control</th>
                    <th>Weakness</th>
                    <th>Corrective Action</th>
                    <th>Responsible Party</th>
                    <th>Target Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($poam_items as $item): ?>
                <tr>
                    <td><code><?= $e($item['control_code']) ?></code></td>
                    <td><?= $e($item['weakness_description']) ?></td>
                    <td><?= $e(substr($item['corrective_action'], 0, 50)) ?>...</td>
                    <td><?= $e($item['responsible_party']) ?></td>
                    <td>
                        <?php
                        $target = strtotime($item['planned_completion_date']);
                        $isOverdue = $target < time() && $item['status'] !== 'completed';
                        ?>
                        <span class="<?= $isOverdue ? 'text-danger' : '' ?>">
                            <?= date('M d, Y', $target) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($item['status'] === 'completed'): ?>
                            <span class="badge badge-success">Completed</span>
                        <?php elseif ($item['status'] === 'in_progress'): ?>
                            <span class="badge badge-info">In Progress</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Open</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= $url('poam/' . $item['id']) ?>" class="btn btn-sm btn-secondary">View</a>
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
