<?php
$page_title = 'Settings';
$current_page = 'admin';

ob_start();
?>

<div class="page-header">
    <h1>System Settings</h1>
    <p class="page-subtitle">Configure application settings</p>
</div>

<div class="tabs">
    <a href="<?= $url('admin') ?>" class="tab">Users</a>
    <a href="<?= $url('admin/settings') ?>" class="tab active">Settings</a>
    <a href="<?= $url('admin/audit-log') ?>" class="tab">Audit Log</a>
    <a href="<?= $url('admin/import') ?>" class="tab">Import Data</a>
</div>

<?php if ($successMessage = $success()): ?>
    <div class="alert alert-success"><?= $e($successMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<form action="<?= $url('admin/settings') ?>" method="POST">
    <?= $csrf() ?>

    <div class="card">
        <div class="card-header">
            <h3>Application Settings</h3>
        </div>

        <div class="form-group">
            <label>Application Name</label>
            <input type="text" name="app_name" value="<?= $e($settings['app_name'] ?? 'CMMC Compliance Suite') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Timezone</label>
            <select name="timezone" class="form-control">
                <option value="UTC" <?= ($settings['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                <option value="America/New_York" <?= ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>Eastern Time</option>
                <option value="America/Chicago" <?= ($settings['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' ?>>Central Time</option>
                <option value="America/Denver" <?= ($settings['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' ?>>Mountain Time</option>
                <option value="America/Los_Angeles" <?= ($settings['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time</option>
            </select>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3>Security Settings</h3>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="force_https" value="1" <?= !empty($settings['force_https']) ? 'checked' : '' ?>>
                Force HTTPS
            </label>
            <small style="display: block; margin-top: 5px;">Redirect all HTTP requests to HTTPS</small>
        </div>

        <div class="form-group">
            <label>Session Idle Timeout (minutes)</label>
            <input type="number" name="session_idle_timeout" value="<?= $e($settings['session_idle_timeout'] ?? '30') ?>" min="5" max="1440" class="form-control">
            <small>How long a user can be idle before being logged out</small>
        </div>

        <div class="form-group">
            <label>Session Absolute Timeout (hours)</label>
            <input type="number" name="session_absolute_timeout" value="<?= $e($settings['session_absolute_timeout'] ?? '2') ?>" min="1" max="24" class="form-control">
            <small>Maximum session duration regardless of activity</small>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="rate_limit_enabled" value="1" <?= !empty($settings['rate_limit_enabled']) ? 'checked' : '' ?>>
                Enable Rate Limiting
            </label>
            <small style="display: block; margin-top: 5px;">Limit failed login attempts (5 per 15 minutes)</small>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3>File Upload Settings</h3>
        </div>

        <div class="form-group">
            <label>Maximum Upload Size (MB)</label>
            <input type="number" name="max_upload_size" value="<?= $e($settings['max_upload_size'] ?? '10') ?>" min="1" max="100" class="form-control">
        </div>

        <div class="form-group">
            <label>Allowed File Types</label>
            <input type="text" name="allowed_file_types" value="<?= $e($settings['allowed_file_types'] ?? 'pdf,doc,docx,xls,xlsx,txt,png,jpg,jpeg') ?>" class="form-control">
            <small>Comma-separated list of file extensions</small>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3>Email Settings</h3>
        </div>

        <div class="form-group">
            <label>SMTP Host</label>
            <input type="text" name="smtp_host" value="<?= $e($settings['smtp_host'] ?? '') ?>" class="form-control" placeholder="smtp.example.com">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>SMTP Port</label>
                <input type="number" name="smtp_port" value="<?= $e($settings['smtp_port'] ?? '587') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>SMTP Encryption</label>
                <select name="smtp_encryption" class="form-control">
                    <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                    <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>SMTP Username</label>
                <input type="text" name="smtp_username" value="<?= $e($settings['smtp_username'] ?? '') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>SMTP Password</label>
                <input type="password" name="smtp_password" placeholder="Enter to change" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>From Email Address</label>
            <input type="email" name="from_email" value="<?= $e($settings['from_email'] ?? '') ?>" class="form-control" placeholder="noreply@example.com">
        </div>
    </div>

    <div class="form-actions" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </div>
</form>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
