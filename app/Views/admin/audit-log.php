<?php
$page_title = 'Audit Log';
$current_page = 'admin';

ob_start();
?>

<div class="page-header">
    <h1>Audit Log</h1>
    <p class="page-subtitle">System activity and change history</p>
</div>

<div class="tabs">
    <a href="<?= $url('admin') ?>" class="tab">Users</a>
    <a href="<?= $url('admin/settings') ?>" class="tab">Settings</a>
    <a href="<?= $url('admin/audit-log') ?>" class="tab active">Audit Log</a>
    <a href="<?= $url('admin/import') ?>" class="tab">Import Data</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Recent Activity (Last 100 entries)</h3>
    </div>

    <?php if (empty($logs)): ?>
        <div class="empty-state">
            <p>No audit log entries found.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Entity Type</th>
                        <th>Entity ID</th>
                        <th>IP Address</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= $e(date('Y-m-d H:i:s', strtotime($log['ts']))) ?></td>
                            <td><?= $e($log['user_name'] ?? 'System') ?></td>
                            <td>
                                <span class="badge badge-<?= $log['action'] === 'delete' ? 'danger' : ($log['action'] === 'create' ? 'success' : 'info') ?>">
                                    <?= $e(ucfirst($log['action'])) ?>
                                </span>
                            </td>
                            <td><?= $e($log['entity_type']) ?></td>
                            <td><?= $e($log['entity_id'] ?? '-') ?></td>
                            <td><?= $e($log['ip_addr']) ?></td>
                            <td>
                                <?php if (!empty($log['details'])): ?>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="showDetails(<?= $e($log['id']) ?>)">View</button>
                                    <div id="details-<?= $e($log['id']) ?>" style="display: none;">
                                        <pre style="font-size: 12px; max-width: 400px; overflow-x: auto;"><?= $e($log['details']) ?></pre>
                                    </div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function showDetails(id) {
    const detailsEl = document.getElementById('details-' + id);
    if (detailsEl) {
        detailsEl.style.display = detailsEl.style.display === 'none' ? 'block' : 'none';
    }
}
</script>

<style>
.badge {
    display: inline-block;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 4px;
    text-transform: uppercase;
}
.badge-success {
    background: #10b981;
    color: white;
}
.badge-info {
    background: #3b82f6;
    color: white;
}
.badge-danger {
    background: #ef4444;
    color: white;
}
</style>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
