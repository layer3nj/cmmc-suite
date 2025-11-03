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
        <form id="create-user-form" action="<?= $url('admin/users') ?>" method="POST">
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

            <div class="form-group">
                <label><strong>Client Access</strong></label>
                <small style="display: block; margin-bottom: 10px;">Select which clients this user can access and their permission level. Leave all unchecked for access to all clients (based on role).</small>
                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; padding: 15px; border-radius: 4px; background: #f9fafb;">
                    <?php if (empty($clients)): ?>
                        <p style="color: #888;">No clients available yet. Create clients first.</p>
                    <?php else: ?>
                        <?php foreach ($clients as $client): ?>
                        <div style="display: flex; align-items: center; margin-bottom: 10px; padding: 8px; background: white; border-radius: 4px;">
                            <label style="flex: 1; margin: 0; display: flex; align-items: center;">
                                <input type="checkbox" class="client-checkbox" value="<?= $client['id'] ?>" style="margin-right: 10px;">
                                <strong><?= $e($client['name']) ?></strong>
                            </label>
                            <select class="client-access-level form-control" data-client-id="<?= $client['id'] ?>" disabled style="width: 150px;">
                                <option value="read-only">Read-Only</option>
                                <option value="read-write">Read-Write</option>
                            </select>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="client_access" id="client-access-data">
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="cancelUserForm()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitUserForm()">Create User</button>
            </div>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Client Access</th>
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
                <td>
                    <?php if (empty($user['client_access'])): ?>
                        <span style="color: #888; font-size: 12px;">All clients</span>
                    <?php else: ?>
                        <div style="max-width: 300px;">
                            <?php foreach ($user['client_access'] as $access): ?>
                                <span class="badge badge-info" style="margin: 2px; font-size: 11px;">
                                    <?= $e($access['client_name']) ?>
                                    <small>(<?= $access['access_level'] === 'read-write' ? 'RW' : 'RO' ?>)</small>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td><?= $user['last_login_at'] ? date('M d, Y H:i', strtotime($user['last_login_at'])) : 'Never' ?></td>
                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                <td>
                    <?php if ($user['id'] !== $current_user_id): ?>
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

<!-- Notification Toast -->
<div id="notification-toast" style="display: none; position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 16px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease-out;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="notification-message" style="font-weight: 500;"></span>
    </div>
</div>

<style>
@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}
</style>

<script>
// Handle client checkbox changes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.client-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const clientId = this.value;
            const accessLevelSelect = document.querySelector(`.client-access-level[data-client-id="${clientId}"]`);

            if (this.checked) {
                accessLevelSelect.disabled = false;
            } else {
                accessLevelSelect.disabled = true;
            }
        });
    });
});

// Show notification
function showNotification(message, type = 'success') {
    const toast = document.getElementById('notification-toast');
    const messageEl = document.getElementById('notification-message');

    // Set colors based on type
    if (type === 'success') {
        toast.style.background = '#10b981';
    } else if (type === 'error') {
        toast.style.background = '#ef4444';
    }

    messageEl.textContent = message;
    toast.style.display = 'block';
    toast.style.animation = 'slideInRight 0.3s ease-out';

    // Auto hide after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 300);
    }, 3000);
}

// Cancel user form
function cancelUserForm() {
    document.getElementById('user-form').style.display = 'none';
    document.getElementById('create-user-form').reset();
}

// Build client access JSON data
function buildClientAccessData() {
    const checkboxes = document.querySelectorAll('.client-checkbox:checked');
    const clientAccessData = [];

    checkboxes.forEach(checkbox => {
        const clientId = checkbox.value;
        const accessLevelSelect = document.querySelector(`.client-access-level[data-client-id="${clientId}"]`);
        const accessLevel = accessLevelSelect.value;

        clientAccessData.push({
            client_id: parseInt(clientId),
            access_level: accessLevel
        });
    });

    return clientAccessData;
}

// Submit user form via AJAX
function submitUserForm() {
    const form = document.getElementById('create-user-form');
    const formData = new FormData(form);

    // Build and add client access data
    const clientAccessData = buildClientAccessData();
    formData.set('client_access', JSON.stringify(clientAccessData));

    // Get the form action URL
    const url = form.action;

    // Disable submit button during request
    const submitBtn = event.target;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';

    // Submit via AJAX
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('User created successfully!', 'success');

            // Reset form and hide it
            form.reset();
            document.getElementById('user-form').style.display = 'none';

            // Reload page after short delay to show updated user list
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Failed to create user', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create User';
        }
    })
    .catch(error => {
        showNotification('An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create User';
    });
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
