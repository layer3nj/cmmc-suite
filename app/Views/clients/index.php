<?php
$page_title = 'Clients';
$current_page = 'clients';

ob_start();
?>

<div class="page-header">
    <h1>Clients</h1>
    <p class="page-subtitle">Multi-Tenant Client Management</p>
    <div class="page-actions">
        <a href="<?= $url('clients/create') ?>" class="btn btn-primary">Add Client</a>
    </div>
</div>

<?php if (empty($clients)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No clients found. <a href="<?= $url('clients/create') ?>">Create your first client</a>.
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
                <?php foreach ($clients as $client): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <?php if (!empty($client['logo_path'])): ?>
                                <img src="<?= $url($client['logo_path']) ?>" alt="<?= $e($client['name']) ?>" style="max-height: 40px; max-width: 100px;">
                            <?php endif; ?>
                            <strong><?= $e($client['name']) ?></strong>
                        </div>
                    </td>
                    <td><?= $e($client['contact_email'] ?? '-') ?></td>
                    <td><?= $e($client['autotask_company_id'] ?? '-') ?></td>
                    <td><?= $e($client['itglue_organization_id'] ?? '-') ?></td>
                    <td>
                        <?php if ($client['active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= $url('clients/' . $client['id'] . '/select') ?>" class="btn btn-sm btn-primary">Select</a>
                        <a href="<?= $url('clients/' . $client['id']) ?>" class="btn btn-sm btn-secondary">View</a>
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
