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

<?php if ($framework === 'CMMC' && !empty($ml_counts)): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Filter by Maturity Level</h3>
    </div>
    <div style="padding: 15px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="<?= $url('controls/cmmc') ?>"
           class="btn btn-sm <?= empty($filters['ml_level']) ? 'btn-primary' : 'btn-secondary' ?>">
            All Levels (<?= array_sum($ml_counts) ?>)
        </a>
        <?php for ($level = 1; $level <= 3; $level++): ?>
            <a href="<?= $url('controls/cmmc?ml_level=' . $level) ?>"
               class="btn btn-sm <?= ($filters['ml_level'] ?? '') == $level ? 'btn-primary' : 'btn-secondary' ?>">
                ML<?= $level ?> (<?= $ml_counts[$level] ?? 0 ?>)
            </a>
        <?php endfor; ?>
        <?php if (!empty($filters['ml_level'])): ?>
            <span style="margin-left: 10px; color: #666;">
                Showing ML<?= $filters['ml_level'] ?> controls only
            </span>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($category_counts)): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Filter by Category</h3>
    </div>
    <div style="padding: 15px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <a href="<?= $url('controls/' . strtolower($framework)) ?>"
           class="btn btn-sm <?= empty($filters['category']) ? 'btn-primary' : 'btn-secondary' ?>"
           style="min-width: 90px;">
            All Categories
        </a>
        <?php foreach ($category_counts as $cat): ?>
            <?php
            $abbr = $category_abbreviations[$cat['category']] ?? substr($cat['category'], 0, 2);
            $isActive = ($filters['category'] ?? '') === $cat['category'];
            ?>
            <a href="<?= $url('controls/' . strtolower($framework) . '?category=' . urlencode($cat['category'])) ?>"
               class="btn btn-sm <?= $isActive ? 'btn-primary' : 'btn-secondary' ?>"
               title="<?= $e($cat['category']) ?>"
               style="min-width: 70px;">
                <?= $e($abbr) ?> (<?= $cat['count'] ?>)
            </a>
        <?php endforeach; ?>
        <?php if (!empty($filters['category'])): ?>
            <span style="margin-left: 10px; color: #666;">
                Showing <?= $e($filters['category']) ?> controls only
            </span>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

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
                    <?php if (in_array($framework, ['CMMC', 'NIST800171'])): ?>
                    <th>Category</th>
                    <?php endif; ?>
                    <th>Title</th>
                    <?php if ($framework === 'CMMC'): ?>
                    <th>ML Level</th>
                    <th>SPRS Points</th>
                    <th>Partial Credit</th>
                    <?php endif; ?>
                    <?php if ($framework === 'NIST800171'): ?>
                    <th>SPRS Points</th>
                    <th>Partial Credit</th>
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
                    <?php if (in_array($framework, ['CMMC', 'NIST800171'])): ?>
                    <td>
                        <?php
                        $categoryAbbr = $category_abbreviations[$control['category'] ?? ''] ?? '—';
                        ?>
                        <span class="badge badge-info" title="<?= $e($control['category'] ?? 'Unknown') ?>">
                            <?= $e($categoryAbbr) ?>
                        </span>
                    </td>
                    <?php endif; ?>
                    <td><?= $e($control['title']) ?></td>
                    <?php if ($framework === 'CMMC'): ?>
                    <td>
                        <?php
                        $mlBadge = 'badge-info';
                        if ($control['ml_level'] == 1) $mlBadge = 'badge-success';
                        elseif ($control['ml_level'] == 2) $mlBadge = 'badge-warning';
                        elseif ($control['ml_level'] == 3) $mlBadge = 'badge-danger';
                        ?>
                        <span class="badge <?= $mlBadge ?>">ML<?= $control['ml_level'] ?></span>
                    </td>
                    <td>
                        <strong><?= $control['sprs_score'] ?? 3 ?></strong> pts
                    </td>
                    <td>
                        <?php if (isset($control['partial_credit']) && $control['partial_credit'] == 1): ?>
                            <span class="badge badge-success">✓ Yes</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">No</span>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <?php if ($framework === 'NIST800171'): ?>
                    <td>
                        <strong><?= $control['sprs_score'] ?? 3 ?></strong> pts
                    </td>
                    <td>
                        <?php if (isset($control['partial_credit']) && $control['partial_credit'] == 1): ?>
                            <span class="badge badge-success">✓ Yes</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">No</span>
                        <?php endif; ?>
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
