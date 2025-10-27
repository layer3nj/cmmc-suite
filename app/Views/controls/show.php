<?php
$page_title = $control['code'] . ' - ' . $control['title'];
$current_page = 'controls';

ob_start();
?>

<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="<?= $url('controls') ?>">Controls</a> /
            <a href="<?= $url('controls/' . strtolower($control['framework'])) ?>"><?= $e($control['framework']) ?></a> /
            <?= $e($control['code']) ?>
        </div>
        <h1><?= $e($control['code']) ?></h1>
        <p class="page-subtitle"><?= $e($control['title']) ?></p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Control Details</h3>
        <?php if (isset($control['ml_level'])): ?>
            <span class="badge badge-info">CMMC ML<?= $control['ml_level'] ?></span>
        <?php endif; ?>
        <?php if (isset($control['stig_severity'])): ?>
            <span class="badge badge-<?= $control['stig_severity'] === 'high' ? 'danger' : ($control['stig_severity'] === 'medium' ? 'warning' : 'secondary') ?>">
                <?= ucfirst($control['stig_severity']) ?>
            </span>
        <?php endif; ?>
    </div>

    <table class="info-table">
        <tr>
            <th>Framework</th>
            <td><?= $e($control['framework']) ?></td>
        </tr>
        <tr>
            <th>Control Code</th>
            <td><code><?= $e($control['code']) ?></code></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><?= $e($control['title']) ?></td>
        </tr>
        <?php if (!empty($control['description'])): ?>
        <tr>
            <th>Description</th>
            <td><?= $e($control['description']) ?></td>
        </tr>
        <?php endif; ?>
        <?php if (isset($control['stig_version'])): ?>
        <tr>
            <th>STIG Version</th>
            <td><?= $e($control['stig_version']) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<?php if (!empty($mappings['maps_to']) || !empty($mappings['mapped_from'])): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Related Controls</h3>
    </div>

    <?php if (!empty($mappings['maps_to'])): ?>
    <h4 style="padding: 15px; margin: 0; background: #f7fafc;">Maps To</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>Framework</th>
                <th>Control Code</th>
                <th>Title</th>
                <th>Relationship</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mappings['maps_to'] as $mapping): ?>
            <tr>
                <td><?= $e($mapping['target_framework']) ?></td>
                <td><code><?= $e($mapping['target_code']) ?></code></td>
                <td><?= $e($mapping['target_title']) ?></td>
                <td><span class="badge badge-secondary"><?= ucwords(str_replace('_', ' ', $mapping['relation_type'])) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (!empty($mappings['mapped_from'])): ?>
    <h4 style="padding: 15px; margin: 0; background: #f7fafc;">Mapped From</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>Framework</th>
                <th>Control Code</th>
                <th>Title</th>
                <th>Relationship</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mappings['mapped_from'] as $mapping): ?>
            <tr>
                <td><?= $e($mapping['source_framework']) ?></td>
                <td><code><?= $e($mapping['source_code']) ?></code></td>
                <td><?= $e($mapping['source_title']) ?></td>
                <td><span class="badge badge-secondary"><?= ucwords(str_replace('_', ' ', $mapping['relation_type'])) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="form-actions" style="margin-top: 20px;">
    <a href="<?= $url('controls/' . strtolower($control['framework'])) ?>" class="btn btn-secondary">Back to Controls</a>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
