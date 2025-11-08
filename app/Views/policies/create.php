<?php
$page_title = 'Create Policy Template';
$current_page = 'policies';

ob_start();
?>

<div class="page-header">
    <h1>Create Policy Template</h1>
    <p class="page-subtitle">Add a new boilerplate policy template</p>
</div>

<div class="card">
    <form action="<?= $url('policies') ?>" method="POST">
        <?= $csrf() ?>

        <div class="form-group">
            <label>Policy Title *</label>
            <input type="text" name="title" required class="form-control" placeholder="e.g., Access Control Policy">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Category *</label>
                <input type="text" name="category" required class="form-control" placeholder="e.g., Access Control, Incident Response">
                <small>Used to group related policies</small>
            </div>
            <div class="form-group">
                <label>Version</label>
                <input type="text" name="version" value="1.0" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>Last Reviewed Date</label>
            <input type="date" name="last_reviewed_date" class="form-control">
        </div>

        <div class="form-group">
            <label>Applicable Frameworks</label>
            <small style="display: block; margin-bottom: 10px;">Select which frameworks this policy applies to (leave all unchecked for all frameworks)</small>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                <?php foreach ($frameworks as $framework): ?>
                <label style="font-weight: normal;">
                    <input type="checkbox" name="frameworks[]" value="<?= $e($framework) ?>">
                    <?= $e($framework) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" class="form-control" placeholder="Brief description of what this policy covers"></textarea>
        </div>

        <div class="form-group">
            <label>Policy Content *</label>
            <textarea name="content" rows="20" required class="form-control" placeholder="Enter the full policy text here..."></textarea>
            <small>This is the template content that can be customized per client</small>
        </div>

        <div class="form-actions">
            <a href="<?= $url('policies') ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Policy</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
