<?php
$page_title = 'Edit Policy Template';
$current_page = 'policies';

$selectedFrameworks = !empty($policy['frameworks']) ? explode(',', $policy['frameworks']) : [];
$selectedFrameworks = array_map('trim', $selectedFrameworks);

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('policies') ?>">Policy Templates</a> / <a href="<?= $url('policies/' . $policy['id']) ?>"><?= $e($policy['title']) ?></a> / Edit
    </div>
    <h1>Edit Policy Template</h1>
</div>

<div class="card">
    <form action="<?= $url('policies/' . $policy['id']) ?>" method="POST">
        <?= $csrf() ?>

        <div class="form-group">
            <label>Policy Title *</label>
            <input type="text" name="title" value="<?= $e($policy['title']) ?>" required class="form-control">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Category *</label>
                <input type="text" name="category" value="<?= $e($policy['category']) ?>" required class="form-control">
                <small>Used to group related policies</small>
            </div>
            <div class="form-group">
                <label>Version</label>
                <input type="text" name="version" value="<?= $e($policy['version'] ?? '1.0') ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>Last Reviewed Date</label>
            <input type="date" name="last_reviewed_date" value="<?= $e($policy['last_reviewed_date'] ?? '') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Applicable Frameworks</label>
            <small style="display: block; margin-bottom: 10px;">Select which frameworks this policy applies to (leave all unchecked for all frameworks)</small>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                <?php foreach ($frameworks as $framework): ?>
                <label style="font-weight: normal;">
                    <input type="checkbox" name="frameworks[]" value="<?= $e($framework) ?>"
                           <?= in_array($framework, $selectedFrameworks) ? 'checked' : '' ?>>
                    <?= $e($framework) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" class="form-control"><?= $e($policy['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Policy Content *</label>
            <textarea name="content" rows="20" required class="form-control"><?= $e($policy['content']) ?></textarea>
            <small>This is the template content that can be customized per client</small>
        </div>

        <div class="form-actions">
            <a href="<?= $url('policies/' . $policy['id']) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Policy</button>
            <form action="<?= $url('policies/' . $policy['id'] . '/delete') ?>" method="POST" style="display: inline; margin-left: auto;" onsubmit="return confirm('Delete this policy template?');">
                <?= $csrf() ?>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
