<?php
$page_title = 'Reports';
$current_page = 'reports';

ob_start();
?>

<div class="page-header">
    <h1>Reports</h1>
    <p class="page-subtitle">Generate compliance reports and exports</p>
</div>

<?php if (!$current_customer): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        Please <a href="<?= $url('customers') ?>">select a customer</a> to generate reports.
    </div>
<?php else: ?>
    <div class="reports-grid">
        <div class="report-card">
            <div class="report-icon">📊</div>
            <h3>CMMC Assessment Report</h3>
            <p>Generate a comprehensive CMMC 2.0 compliance report with findings and remediation plans.</p>
            <div class="report-actions">
                <a href="<?= $url('reports/cmmc?format=pdf') ?>" class="btn btn-primary">Generate PDF</a>
                <a href="<?= $url('reports/cmmc?format=csv') ?>" class="btn btn-secondary">Export CSV</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-icon">🔒</div>
            <h3>NIST SP 800-171 Report</h3>
            <p>Detailed NIST SP 800-171 assessment report including SPRS score calculation.</p>
            <div class="report-actions">
                <a href="<?= $url('reports/nist?format=pdf') ?>" class="btn btn-primary">Generate PDF</a>
                <a href="<?= $url('reports/nist?format=csv') ?>" class="btn btn-secondary">Export CSV</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-icon">🛡️</div>
            <h3>STIG Compliance Report</h3>
            <p>DISA Security Technical Implementation Guide compliance status report.</p>
            <div class="report-actions">
                <a href="<?= $url('reports/stig?format=pdf') ?>" class="btn btn-primary">Generate PDF</a>
                <a href="<?= $url('reports/stig?format=csv') ?>" class="btn btn-secondary">Export CSV</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-icon">📈</div>
            <h3>SPRS Score Report</h3>
            <p>DoD Supplier Performance Risk System score breakdown and trending.</p>
            <div class="report-actions">
                <a href="<?= $url('reports/sprs?format=pdf') ?>" class="btn btn-primary">Generate PDF</a>
                <a href="<?= $url('reports/sprs?format=csv') ?>" class="btn btn-secondary">Export CSV</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-icon">📋</div>
            <h3>POA&M Export</h3>
            <p>Export all Plan of Action & Milestones items for remediation tracking.</p>
            <div class="report-actions">
                <a href="<?= $url('poam/export/pdf') ?>" class="btn btn-primary">Generate PDF</a>
                <a href="<?= $url('poam/export/csv') ?>" class="btn btn-secondary">Export CSV</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-icon">📄</div>
            <h3>Evidence Package</h3>
            <p>Complete compliance evidence package including all documentation and artifacts.</p>
            <div class="report-actions">
                <a href="<?= $url('reports/evidence?format=zip') ?>" class="btn btn-primary">Generate ZIP</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
