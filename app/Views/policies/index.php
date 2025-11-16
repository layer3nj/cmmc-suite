<?php
$page_title = 'Policy Templates';
$current_page = 'policies';

ob_start();
?>

<div class="page-header">
    <h1>Policy Templates</h1>
    <p class="page-subtitle">Boilerplate compliance policies applicable across all frameworks</p>
    <div class="page-actions">
        <?php if (\App\Middleware\AuthMiddleware::checkPermission('admin')): ?>
        <a href="<?= $url('policy-templates/create') ?>" class="btn btn-primary">Add Policy Template</a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Filter by Category</h3>
    </div>
    <div style="padding: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= $url('policy-templates') ?>" class="btn btn-sm <?= empty($selected_category) ? 'btn-primary' : 'btn-secondary' ?>">All</a>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= $url('policy-templates?category=' . urlencode($cat['category'])) ?>"
                   class="btn btn-sm <?= ($selected_category ?? '') === $cat['category'] ? 'btn-primary' : 'btn-secondary' ?>">
                    <?= $e($cat['category']) ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($policies)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No policy templates found. <a href="<?= $url('policy-templates/create') ?>">Create your first policy template</a>.
    </div>
<?php else: ?>
    <?php
    // Group policies by category
    $grouped = [];
    foreach ($policies as $policy) {
        $grouped[$policy['category']][] = $policy;
    }
    ?>

    <?php foreach ($grouped as $category => $categoryPolicies): ?>
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3><?= $e($category) ?></h3>
            <span class="badge"><?= count($categoryPolicies) ?> policies</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Policy Title</th>
                    <th>Applicable Frameworks</th>
                    <th>Version</th>
                    <th>Last Reviewed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categoryPolicies as $policy): ?>
                <tr>
                    <td>
                        <strong><?= $e($policy['title']) ?></strong>
                        <?php if (!empty($policy['description'])): ?>
                            <br><small style="color: #666;"><?= $e(substr($policy['description'], 0, 100)) ?><?= strlen($policy['description']) > 100 ? '...' : '' ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($policy['frameworks'])): ?>
                            <?php foreach (explode(',', $policy['frameworks']) as $fw): ?>
                                <span class="badge badge-info" style="margin: 2px;"><?= $e(trim($fw)) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">All frameworks</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $e($policy['version'] ?? '1.0') ?></td>
                    <td><?= $policy['last_reviewed_date'] ? date('M d, Y', strtotime($policy['last_reviewed_date'])) : '-' ?></td>
                    <td>
                        <a href="<?= $url('policy-templates/' . $policy['id']) ?>" class="btn btn-sm btn-primary">View</a>
                        <?php if (\App\Middleware\AuthMiddleware::checkPermission('admin')): ?>
                        <a href="<?= $url('policy-templates/' . $policy['id'] . '/edit') ?>" class="btn btn-sm btn-secondary">Edit</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
