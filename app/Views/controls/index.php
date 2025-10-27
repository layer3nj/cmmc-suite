<?php
$page_title = 'Controls';
$current_page = 'controls';

ob_start();
?>

<div class="page-header">
    <h1>Compliance Controls</h1>
    <p class="page-subtitle">CMMC 2.0, NIST SP 800-171, and STIG Controls</p>
</div>

<div class="tabs">
    <a href="<?= $url('controls/cmmc') ?>" class="tab <?= $framework === 'CMMC' ? 'active' : '' ?>">
        CMMC 2.0
    </a>
    <a href="<?= $url('controls/nist800171') ?>" class="tab <?= $framework === 'NIST800171' ? 'active' : '' ?>">
        NIST SP 800-171
    </a>
    <a href="<?= $url('controls/stig') ?>" class="tab <?= $framework === 'STIG' ? 'active' : '' ?>">
        DISA STIG
    </a>
</div>

<?php if (empty($controls)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No controls found. Please run database migrations.
    </div>
<?php else: ?>
    <?php
    // Display friendly framework name
    $frameworkNames = [
        'CMMC' => 'CMMC 2.0',
        'NIST800171' => 'NIST SP 800-171',
        'STIG' => 'DISA STIG'
    ];
    $frameworkDisplay = $frameworkNames[$framework] ?? $framework;
    ?>
    <div class="card">
        <div class="card-header">
            <h3><?= $frameworkDisplay ?> Controls</h3>
            <span class="badge"><?= count($controls) ?> controls</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <?php if ($framework === 'cmmc'): ?>
                    <th>ML Level</th>
                    <?php endif; ?>
                    <?php if ($framework === 'stig'): ?>
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
                    <?php if ($framework === 'cmmc'): ?>
                    <td>
                        <span class="badge badge-info">ML<?= $control['ml_level'] ?></span>
                    </td>
                    <?php endif; ?>
                    <?php if ($framework === 'stig'): ?>
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
