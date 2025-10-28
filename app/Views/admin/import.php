<?php
$page_title = 'Import Data';
$current_page = 'admin';

ob_start();
?>

<div class="page-header">
    <h1>Import Data</h1>
    <p class="page-subtitle">Import controls, assessments, and other data from external sources</p>
</div>

<div class="tabs">
    <a href="<?= $url('admin') ?>" class="tab">Users</a>
    <a href="<?= $url('admin/settings') ?>" class="tab">Settings</a>
    <a href="<?= $url('admin/audit-log') ?>" class="tab">Audit Log</a>
    <a href="<?= $url('admin/import') ?>" class="tab active">Import Data</a>
</div>

<?php if ($successMessage = $success()): ?>
    <div class="alert alert-success"><?= $e($successMessage) ?></div>
<?php endif; ?>

<?php if ($infoMessage = $info()): ?>
    <div class="alert alert-info"><?= $e($infoMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Import Controls</h3>
    </div>

    <form action="<?= $url('admin/import') ?>" method="POST" enctype="multipart/form-data" class="form-section">
        <?= $csrf() ?>
        <input type="hidden" name="import_type" value="controls">

        <div class="form-group">
            <label>Data Source</label>
            <select name="source" class="form-control" required>
                <option value="">Select source...</option>
                <option value="cmmc">CMMC 2.0 (Official)</option>
                <option value="nist800171">NIST SP 800-171 Rev 2</option>
                <option value="nist80053">NIST SP 800-53 Rev 5</option>
                <option value="csv">CSV File</option>
            </select>
        </div>

        <div class="form-group">
            <label>Import File (CSV format)</label>
            <input type="file" name="import_file" accept=".csv" class="form-control">
            <small>Upload a CSV file with columns: framework, code, title, description, level</small>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="overwrite" value="1">
                Overwrite existing controls
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Import Controls</button>
    </form>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Import Customers</h3>
    </div>

    <form action="<?= $url('admin/import') ?>" method="POST" enctype="multipart/form-data" class="form-section">
        <?= $csrf() ?>
        <input type="hidden" name="import_type" value="customers">

        <div class="form-group">
            <label>Customer Data File (CSV format)</label>
            <input type="file" name="import_file" accept=".csv" class="form-control" required>
            <small>Upload a CSV file with columns: name, contact_name, contact_email, address, phone</small>
        </div>

        <button type="submit" class="btn btn-primary">Import Customers</button>
    </form>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>CSV Format Examples</h3>
    </div>

    <div class="form-section">
        <h4>Controls CSV Format:</h4>
        <pre style="background: #f7fafc; padding: 15px; border-radius: 4px; overflow-x: auto;">framework,code,title,description,level
CMMC,AC.1.001,Access Control Policy,Establish and maintain an access control policy,1
NIST800171,3.1.1,Access Control,Limit system access to authorized users,Basic</pre>

        <h4 style="margin-top: 20px;">Customers CSV Format:</h4>
        <pre style="background: #f7fafc; padding: 15px; border-radius: 4px; overflow-x: auto;">name,contact_name,contact_email,address,phone
Acme Corp,John Doe,john@acme.com,"123 Main St, City, ST 12345",555-1234
Example Inc,Jane Smith,jane@example.com,"456 Oak Ave, Town, ST 67890",555-5678</pre>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
