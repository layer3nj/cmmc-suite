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
        <h3>Seed Framework Controls</h3>
    </div>

    <form action="<?= $url('admin/import') ?>" method="POST" class="form-section">
        <?= $csrf() ?>
        <input type="hidden" name="import_type" value="controls">
        <input type="hidden" name="source" value="seed">

        <div class="form-group">
            <p><strong>Load all compliance framework controls into the database:</strong></p>
            <ul style="margin: 10px 0; padding-left: 25px;">
                <li>CMMC 2.0 (~110 controls)</li>
                <li>NIST SP 800-171 Rev 2 (~110 controls)</li>
                <li>DISA STIG (~50 controls)</li>
                <li>HIPAA Security Rule (~52 controls)</li>
                <li>FTC Safeguards Rule (~15 controls)</li>
                <li>PCI-DSS v4.0 (~52 key controls)</li>
                <li>SOC 2 Trust Services (~45 controls)</li>
                <li>ISO/IEC 27001:2022 (~50 controls)</li>
            </ul>
            <p><strong>Total: ~484 controls across all frameworks</strong></p>
            <p><strong>Note:</strong> This is safe to run multiple times. Existing controls will not be duplicated.</p>
        </div>

        <button type="submit" class="btn btn-primary" onclick="return confirm('Load all framework controls? This may take a moment.')">Seed Database with All Controls</button>
    </form>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Database Migrations</h3>
    </div>

    <form action="<?= $url('admin/run-migrations') ?>" method="POST" class="form-section">
        <?= $csrf() ?>

        <div class="form-group">
            <p>Run pending database migrations to update the schema with new features and improvements.</p>
            <p><strong>Note:</strong> This is safe to run multiple times. Already-applied migrations will be skipped automatically.</p>
        </div>

        <button type="submit" class="btn btn-primary" onclick="return confirm('Run all pending database migrations?')">Run Migrations</button>
    </form>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Run Database Seeders</h3>
    </div>

    <form action="<?= $url('admin/run-seeders') ?>" method="POST" class="form-section">
        <?= $csrf() ?>

        <div class="form-group">
            <p>Run all database seeders to populate data such as policy templates, sample data, and other predefined content.</p>
            <p><strong>Includes:</strong></p>
            <ul style="margin: 10px 0; padding-left: 25px;">
                <li>CMMC ML2 Policy Templates (14 comprehensive policies)</li>
                <li>Sample assessment data</li>
                <li>Additional framework-specific content</li>
            </ul>
            <p><strong>Note:</strong> Seeders will skip items that already exist in the database. Safe to run multiple times.</p>
        </div>

        <button type="submit" class="btn btn-primary" onclick="return confirm('Run all database seeders?')">Run Seeders</button>
    </form>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Update SPRS Scores</h3>
    </div>

    <form action="<?= $url('admin/update-sprs-scores') ?>" method="POST" class="form-section">
        <?= $csrf() ?>

        <div class="form-group">
            <p>Update SPRS scoring for all NIST 800-171 and CMMC controls with accurate point weights based on the DoD Assessment Methodology.</p>
            <p><strong>Scoring breakdown:</strong></p>
            <ul style="margin: 10px 0; padding-left: 25px;">
                <li><strong>5 points</strong> - High-risk controls (MFA, encryption, incident response, privileged access)</li>
                <li><strong>3 points</strong> - Medium-risk controls (most controls)</li>
                <li><strong>1 point</strong> - Low-risk controls (awareness/training, some procedural)</li>
            </ul>
            <p><strong>Note:</strong> This will overwrite existing SPRS scores. Use this if migration 023 didn't set scores correctly.</p>
        </div>

        <button type="submit" class="btn btn-warning" onclick="return confirm('Update all SPRS scores? This will overwrite current values.')">Update SPRS Scores</button>
    </form>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Update Control Categories</h3>
    </div>

    <form action="<?= $url('admin/update-categories') ?>" method="POST" class="form-section">
        <?= $csrf() ?>

        <div class="form-group">
            <p>Update control categories for all NIST 800-171 and CMMC controls to organize them by domain/family.</p>
            <p><strong>Categories include:</strong></p>
            <ul style="margin: 10px 0; padding-left: 25px; columns: 2;">
                <li>Access Control (AC)</li>
                <li>Awareness and Training (AT)</li>
                <li>Audit and Accountability (AU)</li>
                <li>Configuration Management (CM)</li>
                <li>Identification and Authentication (IA)</li>
                <li>Incident Response (IR)</li>
                <li>Maintenance (MA)</li>
                <li>Media Protection (MP)</li>
                <li>Personnel Security (PS)</li>
                <li>Physical Protection (PE)</li>
                <li>Risk Assessment (RA)</li>
                <li>Security Assessment (CA)</li>
                <li>System and Communications Protection (SC)</li>
                <li>System and Information Integrity (SI)</li>
            </ul>
            <p><strong>Note:</strong> This will overwrite existing categories. Use this if migration 025 didn't set categories correctly.</p>
        </div>

        <button type="submit" class="btn btn-warning" onclick="return confirm('Update all control categories? This will overwrite current values.')">Update Categories</button>
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
