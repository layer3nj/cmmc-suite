<?php
$page_title = $policy['title'];
$current_page = 'policies';

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('policies') ?>">Policy Templates</a> / <?= $e($policy['title']) ?>
    </div>
    <h1><?= $e($policy['title']) ?></h1>
    <div class="page-actions">
        <?php if (\App\Middleware\AuthMiddleware::checkPermission('admin')): ?>
        <a href="<?= $url('policies/' . $policy['id'] . '/edit') ?>" class="btn btn-secondary">Edit</a>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Policy Information</h3>
    </div>
    <table class="info-table">
        <tr>
            <th>Category</th>
            <td><?= $e($policy['category']) ?></td>
        </tr>
        <tr>
            <th>Version</th>
            <td><?= $e($policy['version'] ?? '1.0') ?></td>
        </tr>
        <tr>
            <th>Applicable Frameworks</th>
            <td>
                <?php if (!empty($policy['frameworks'])): ?>
                    <?php foreach (explode(',', $policy['frameworks']) as $fw): ?>
                        <span class="badge badge-info"><?= $e(trim($fw)) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    All frameworks
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Last Reviewed</th>
            <td><?= $policy['last_reviewed_date'] ? date('M d, Y', strtotime($policy['last_reviewed_date'])) : 'Not reviewed' ?></td>
        </tr>
        <?php if (!empty($policy['description'])): ?>
        <tr>
            <th>Description</th>
            <td><?= nl2br($e($policy['description'])) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Policy Content</h3>
    </div>
    <div style="padding: 20px; line-height: 1.8; white-space: pre-wrap; font-family: inherit;">
        <?= nl2br($e($policy['content'])) ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
