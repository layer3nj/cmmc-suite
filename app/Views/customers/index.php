<?php
$page_title = 'Customers';
$current_page = 'customers';

ob_start();
?>

<div class="page-header">
    <h1>Customers</h1>
    <p class="page-subtitle">Multi-Tenant Customer Management</p>
    <div class="page-actions">
        <a href="<?= $url('customers/create') ?>" class="btn btn-primary">Add Customer</a>
    </div>
</div>

<?php if (empty($customers)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No customers found. <a href="<?= $url('customers/create') ?>">Create your first customer</a>.
    </div>
<?php else: ?>
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Email</th>
                    <th>Autotask ID</th>
                    <th>ITGlue ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td>
                        <strong><?= $e($customer['name']) ?></strong>
                    </td>
                    <td><?= $e($customer['contact_email'] ?? '-') ?></td>
                    <td><?= $e($customer['autotask_company_id'] ?? '-') ?></td>
                    <td><?= $e($customer['itglue_organization_id'] ?? '-') ?></td>
                    <td>
                        <?php if ($customer['active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= $url('customers/' . $customer['id'] . '/select') ?>" class="btn btn-sm btn-primary">Select</a>
                        <a href="<?= $url('customers/' . $customer['id']) ?>" class="btn btn-sm btn-secondary">View</a>
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
