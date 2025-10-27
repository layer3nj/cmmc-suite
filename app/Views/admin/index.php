<?php
$page_title = 'Administration';
$current_page = 'admin';

ob_start();
?>

<div class="page-header">
    <h1>Administration</h1>
    <p class="page-subtitle">User management and system settings</p>
</div>

<div class="tabs">
    <a href="<?= $url('admin') ?>" class="tab active">Users</a>
    <a href="<?= $url('admin/settings') ?>" class="tab">Settings</a>
    <a href="<?= $url('admin/audit-log') ?>" class="tab">Audit Log</a>
    <a href="<?= $url('admin/import') ?>" class="tab">Import Data</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>User Management</h3>
        <button class="btn btn-primary" onclick="document.getElementById('user-form').style.display='block'">Add User</button>
    </div>

    <div id="user-form" class="card-body" style="display: none; border-top: 1px solid #e2e8f0; margin-top: 20px;">
        <h4>Create New User</h4>
        <form action="<?= $url('admin/users') ?>" method="POST">
            <?= $csrf() ?>
            <div class="form-row">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Display Name</label>
                    <input type="text" name="display_name" required class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required class="form-control">
                        <option value="viewer">Viewer (Read-only)</option>
                        <option value="contributor">Contributor (Can edit)</option>
                        <option value="auditor">Auditor (Full access, no delete)</option>
                        <option value="admin">Administrator (Full access)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Initial Password</label>
                    <input type="password" name="password" required minlength="8" class="form-control">
                    <small>Minimum 8 characters</small>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('user-form').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Last Login</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><strong><?= $e($user['display_name']) ?></strong></td>
                <td><?= $e($user['email']) ?></td>
                <td>
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge badge-danger">Admin</span>
                    <?php elseif ($user['role'] === 'auditor'): ?>
                        <span class="badge badge-warning">Auditor</span>
                    <?php elseif ($user['role'] === 'contributor'): ?>
                        <span class="badge badge-info">Contributor</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Viewer</span>
                    <?php endif; ?>
                </td>
                <td><?= $user['last_login_at'] ? date('M d, Y H:i', strtotime($user['last_login_at'])) : 'Never' ?></td>
                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                <td>
                    <?php if ($user['id'] !== Session::get('user_id')): ?>
                        <form action="<?= $url('admin/users/' . $user['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this user?');">
                            <?= $csrf() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">Current User</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>System Information</h3>
    </div>
    <table class="info-table">
        <tr>
            <th>Application Version</th>
            <td>1.0.0</td>
        </tr>
        <tr>
            <th>PHP Version</th>
            <td><?= PHP_VERSION ?></td>
        </tr>
        <tr>
            <th>Database Driver</th>
            <td><?= $e($db_driver) ?></td>
        </tr>
        <tr>
            <th>SAML SSO</th>
            <td><?= $saml_enabled ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-secondary">Disabled</span>' ?></td>
        </tr>
        <tr>
            <th>Total Customers</th>
            <td><?= $stats['total_customers'] ?></td>
        </tr>
        <tr>
            <th>Total Assessments</th>
            <td><?= $stats['total_assessments'] ?></td>
        </tr>
        <tr>
            <th>Total POA&M Items</th>
            <td><?= $stats['total_poam'] ?></td>
        </tr>
    </table>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
