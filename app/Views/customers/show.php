<?php
$page_title = $customer['name'];
$current_page = 'customers';

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('customers') ?>">Customers</a> / <?= $e($customer['name']) ?>
    </div>
    <h1><?= $e($customer['name']) ?></h1>
    <div class="page-actions">
        <a href="<?= $url('customers/' . $customer['id'] . '/edit') ?>" class="btn btn-secondary">Edit</a>
        <a href="<?= $url('customers/' . $customer['id'] . '/select') ?>" class="btn btn-primary">Select Customer</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Customer Details</h3>
        <?php if ($customer['active']): ?>
            <span class="badge badge-success">Active</span>
        <?php else: ?>
            <span class="badge badge-secondary">Inactive</span>
        <?php endif; ?>
    </div>

    <table class="info-table">
        <tr>
            <th>Customer Name</th>
            <td><?= $e($customer['name']) ?></td>
        </tr>
        <tr>
            <th>Contact Email</th>
            <td><?= $e($customer['contact_email'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Contact Phone</th>
            <td><?= $e($customer['contact_phone'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?= $customer['address'] ? nl2br($e($customer['address'])) : '-' ?></td>
        </tr>
        <tr>
            <th>Autotask Company ID</th>
            <td><?= $e($customer['autotask_company_id'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>ITGlue Organization ID</th>
            <td><?= $e($customer['itglue_organization_id'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Created</th>
            <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
        </tr>
        <?php if (isset($customer['updated_at']) && $customer['updated_at']): ?>
        <tr>
            <th>Last Updated</th>
            <td><?= date('M d, Y', strtotime($customer['updated_at'])) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Compliance Summary</h3>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Assessments</div>
            <div class="stat-value"><?= $stats['total_assessments'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Open POA&M Items</div>
            <div class="stat-value text-warning"><?= $stats['open_poam'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Documents</div>
            <div class="stat-value"><?= $stats['total_documents'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Latest SPRS Score</div>
            <div class="stat-value <?= $stats['latest_sprs'] >= 0 ? 'text-success' : 'text-danger' ?>">
                <?= $stats['latest_sprs'] ?? 'N/A' ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($recent_assessments)): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Recent Assessments</h3>
        <a href="<?= $url('assessments?customer=' . $customer['id']) ?>" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Framework</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_assessments as $assessment): ?>
            <tr>
                <td><?= $e($assessment['name']) ?></td>
                <td><span class="badge"><?= $e($assessment['framework']) ?></span></td>
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
                    <a href="<?= $url('assessments/' . $assessment['id']) ?>" class="btn btn-sm btn-primary">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($recent_poam)): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Active POA&M Items</h3>
        <a href="<?= $url('poam?customer=' . $customer['id']) ?>" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Control</th>
                <th>Weakness</th>
                <th>Responsible Party</th>
                <th>Target Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_poam as $item): ?>
            <tr>
                <td><code><?= $e($item['control_code']) ?></code></td>
                <td><?= $e(substr($item['weakness_description'], 0, 60)) ?>...</td>
                <td><?= $e($item['responsible_party']) ?></td>
                <td><?= date('M d, Y', strtotime($item['planned_completion_date'])) ?></td>
                <td>
                    <?php if ($item['status'] === 'open'): ?>
                        <span class="badge badge-warning">Open</span>
                    <?php elseif ($item['status'] === 'in_progress'): ?>
                        <span class="badge badge-info">In Progress</span>
                    <?php else: ?>
                        <span class="badge badge-success">Completed</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="form-actions" style="margin-top: 20px;">
    <a href="<?= $url('customers') ?>" class="btn btn-secondary">Back to Customers</a>
    <form action="<?= $url('customers/' . $customer['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this customer? This will also delete all associated assessments, POA&M items, and documents.');">
        <?= $csrf() ?>
        <button type="submit" class="btn btn-danger">Delete Customer</button>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
