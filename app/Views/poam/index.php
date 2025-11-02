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

<?php if (!isset($current_customer) || !$current_customer): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        Please <a href="<?= $url('clients') ?>">select a client</a> to view POA&M items.
    </div>
<?php elseif (empty($items)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No POA&M items found. <a href="<?= $url('poam/create') ?>">Create your first POA&M item</a>.
    </div>
<?php else: ?>
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Items</div>
            <div class="stat-value"><?= $stats['total'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Open</div>
            <div class="stat-value text-warning"><?= $stats['open'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">In Progress</div>
            <div class="stat-value text-info"><?= $stats['in_progress'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Completed</div>
            <div class="stat-value text-success"><?= $stats['closed'] ?></div>
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
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><code><?= $e($item['control_code'] ?? 'N/A') ?></code></td>
                    <td><?= $e(substr($item['weakness'] ?? '', 0, 60)) ?><?= strlen($item['weakness'] ?? '') > 60 ? '...' : '' ?></td>
                    <td><?= $e(substr($item['corrective_action'] ?? '', 0, 50)) ?><?= strlen($item['corrective_action'] ?? '') > 50 ? '...' : '' ?></td>
                    <td><?= $e($item['responsible_party'] ?? '-') ?></td>
                    <td>
                        <?php if ($item['planned_completion_date']): ?>
                            <?php
                            $target = strtotime($item['planned_completion_date']);
                            $isOverdue = $target < time() && !in_array($item['status'], ['closed', 'completed']);
                            ?>
                            <span class="<?= $isOverdue ? 'text-danger' : '' ?>">
                                <?= date('M d, Y', $target) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">Not set</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($item['status'] === 'closed'): ?>
                            <span class="badge badge-success">Closed</span>
                        <?php elseif ($item['status'] === 'in_progress'): ?>
                            <span class="badge badge-info">In Progress</span>
                        <?php elseif ($item['status'] === 'deferred'): ?>
                            <span class="badge badge-secondary">Deferred</span>
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
