<?php
$page_title = 'Controls';
$current_page = 'controls';

ob_start();
?>

<div class="page-header">
    <h1>Compliance Controls</h1>
    <p class="page-subtitle">Browse and export controls for multiple compliance frameworks</p>
</div>

<div class="tabs">
    <a href="<?= $url('controls/cmmc') ?>" class="tab <?= $framework === 'CMMC' ? 'active' : '' ?>">
        CMMC 2.0
    </a>
    <a href="<?= $url('controls/nist800171') ?>" class="tab <?= $framework === 'NIST800171' ? 'active' : '' ?>">
        NIST 800-171
    </a>
    <a href="<?= $url('controls/stig') ?>" class="tab <?= $framework === 'STIG' ? 'active' : '' ?>">
        DISA STIG
    </a>
    <a href="<?= $url('controls/hipaa') ?>" class="tab <?= $framework === 'HIPAA' ? 'active' : '' ?>">
        HIPAA
    </a>
    <a href="<?= $url('controls/ftc-safeguards') ?>" class="tab <?= $framework === 'FTC-SAFEGUARDS' ? 'active' : '' ?>">
        FTC Safeguards
    </a>
    <a href="<?= $url('controls/pci-dss') ?>" class="tab <?= $framework === 'PCI-DSS' ? 'active' : '' ?>">
        PCI-DSS
    </a>
    <a href="<?= $url('controls/soc2') ?>" class="tab <?= $framework === 'SOC2' ? 'active' : '' ?>">
        SOC 2
    </a>
    <a href="<?= $url('controls/iso27001') ?>" class="tab <?= $framework === 'ISO27001' ? 'active' : '' ?>">
        ISO 27001
    </a>
</div>

<?php if (empty($controls)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No controls found for this framework. Please run database migrations or seed the database with controls.
    </div>
<?php else: ?>
    <?php
    // Display friendly framework name
    $frameworkNames = [
        'CMMC' => 'CMMC 2.0',
        'NIST800171' => 'NIST SP 800-171',
        'STIG' => 'DISA STIG',
        'HIPAA' => 'HIPAA Security Rule',
        'FTC-SAFEGUARDS' => 'FTC Safeguards Rule',
        'PCI-DSS' => 'PCI-DSS v4.0',
        'SOC2' => 'SOC 2 Trust Services',
        'ISO27001' => 'ISO/IEC 27001:2022',
    ];
    $frameworkDisplay = $frameworkNames[$framework] ?? $framework;
    ?>
    <div class="card">
        <div class="card-header">
            <h3><?= $frameworkDisplay ?> Controls</h3>
            <div style="display: flex; gap: 10px; align-items: center;">
                <span class="badge"><?= count($controls) ?> controls</span>
                <a href="<?= $url('controls/' . strtolower($framework) . '/export-pdf') ?>"
                   class="btn btn-sm btn-primary"
                   target="_blank"
                   title="Open print-friendly view (use browser's Print to PDF)">
                    📄 Export as PDF
                </a>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <?php if ($framework === 'CMMC'): ?>
                    <th>ML Level</th>
                    <?php endif; ?>
                    <?php if ($framework === 'STIG'): ?>
                    <th>Severity</th>
                    <?php endif; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($controls as $control): ?>
                <tr>
                    <td><code><?= $e($control['code']) ?></code></td>
                    <td><?= $e($control['title']) ?></td>
                    <?php if ($framework === 'CMMC'): ?>
                    <td>
                        <span class="badge badge-info">ML<?= $control['ml_level'] ?></span>
                    </td>
                    <?php endif; ?>
                    <?php if ($framework === 'STIG'): ?>
                    <td>
                        <span class="badge badge-<?= $control['stig_severity'] === 'high' ? 'danger' : ($control['stig_severity'] === 'medium' ? 'warning' : 'secondary') ?>">
                            <?= ucfirst($control['stig_severity']) ?>
                        </span>
                    </td>
                    <?php endif; ?>
                    <td>
                        <a href="<?= $url('controls/' . strtolower($framework) . '/' . urlencode($control['code'])) ?>" class="btn btn-sm btn-secondary">View</a>
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
