<?php
$page_title = 'POA&M Item: ' . ($item['title'] ?? 'Details');
$current_page = 'poam';

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('poam') ?>">POA&M</a> / <?= $e($item['title'] ?? 'Item Details') ?>
    </div>
    <h1>POA&M Item Details</h1>
    <div class="page-actions">
        <a href="<?= $url('poam/' . $item['id'] . '/edit') ?>" class="btn btn-secondary">Edit</a>
        <a href="<?= $url('poam') ?>" class="btn btn-primary">Back to List</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><?= $e($item['title']) ?></h3>
        <?php if ($item['status'] === 'closed'): ?>
            <span class="badge badge-success">Closed</span>
        <?php elseif ($item['status'] === 'in_progress'): ?>
            <span class="badge badge-info">In Progress</span>
        <?php elseif ($item['status'] === 'deferred'): ?>
            <span class="badge badge-secondary">Deferred</span>
        <?php else: ?>
            <span class="badge badge-warning">Open</span>
        <?php endif; ?>
    </div>

    <table class="info-table">
        <tr>
            <th style="width: 200px;">Control Framework</th>
            <td><?= $e($item['control_framework'] ?? 'N/A') ?></td>
        </tr>
        <tr>
            <th>Control Code</th>
            <td><code><?= $e($item['control_code'] ?? 'N/A') ?></code></td>
        </tr>
        <?php if ($control): ?>
        <tr>
            <th>Control Title</th>
            <td><?= $e($control['title'] ?? '') ?></td>
        </tr>
        <tr>
            <th>Control Description</th>
            <td><?= $e($control['description'] ?? '') ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Weakness</th>
            <td><?= nl2br($e($item['weakness'] ?? '')) ?></td>
        </tr>
        <tr>
            <th>Corrective Action</th>
            <td><?= nl2br($e($item['corrective_action'] ?? '')) ?></td>
        </tr>
        <tr>
            <th>Milestones</th>
            <td><?= nl2br($e($item['milestones'] ?? 'None specified')) ?></td>
        </tr>
        <tr>
            <th>Responsible Party</th>
            <td><?= $e($item['responsible_party'] ?? 'Not assigned') ?></td>
        </tr>
        <tr>
            <th>Resources</th>
            <td><?= $e($item['resources'] ?? 'None specified') ?></td>
        </tr>
        <tr>
            <th>Start Date</th>
            <td><?= $item['start_date'] ? date('M d, Y', strtotime($item['start_date'])) : 'Not set' ?></td>
        </tr>
        <tr>
            <th>Planned Completion Date</th>
            <td>
                <?php if ($item['planned_completion_date']): ?>
                    <?php
                    $target = strtotime($item['planned_completion_date']);
                    $isOverdue = $target < time() && !in_array($item['status'], ['closed']);
                    ?>
                    <span class="<?= $isOverdue ? 'text-danger font-weight-bold' : '' ?>">
                        <?= date('M d, Y', $target) ?>
                        <?= $isOverdue ? ' (OVERDUE)' : '' ?>
                    </span>
                <?php else: ?>
                    Not set
                <?php endif; ?>
            </td>
        </tr>
        <?php if ($item['actual_completion_date']): ?>
        <tr>
            <th>Actual Completion Date</th>
            <td><?= date('M d, Y', strtotime($item['actual_completion_date'])) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Status</th>
            <td><?= ucfirst(str_replace('_', ' ', $item['status'])) ?></td>
        </tr>
        <tr>
            <th>Residual Risk</th>
            <td><?= $e($item['residual_risk'] ?? 'Not assessed') ?></td>
        </tr>
        <?php if ($item['comments']): ?>
        <tr>
            <th>Comments</th>
            <td><?= nl2br($e($item['comments'])) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Created</th>
            <td><?= date('M d, Y H:i', strtotime($item['created_at'])) ?></td>
        </tr>
        <?php if ($item['updated_at'] && $item['updated_at'] !== $item['created_at']): ?>
        <tr>
            <th>Last Updated</th>
            <td><?= date('M d, Y H:i', strtotime($item['updated_at'])) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="form-actions" style="margin-top: 20px;">
    <a href="<?= $url('poam') ?>" class="btn btn-secondary">Back to POA&M List</a>
    <a href="<?= $url('poam/' . $item['id'] . '/edit') ?>" class="btn btn-primary">Edit Item</a>
    <form action="<?= $url('poam/' . $item['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this POA&M item?');">
        <?= $csrf() ?>
        <button type="submit" class="btn btn-danger">Delete Item</button>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
