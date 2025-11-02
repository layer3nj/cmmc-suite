<?php
$page_title = 'Add Client';
$current_page = 'clients';

ob_start();
?>

<div class="page-header">
    <h1>Add Client</h1>
    <p class="page-subtitle">Create a new client account</p>
</div>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<div class="card">
    <form action="<?= $url('clients') ?>" method="POST">
        <?= $csrf() ?>

        <div class="form-group">
            <label>Client Name *</label>
            <input type="text" name="name" value="<?= $old('name') ?>" required class="form-control">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Contact Email</label>
                <input type="email" name="contact_email" value="<?= $old('contact_email') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>Contact Phone</label>
                <input type="tel" name="contact_phone" value="<?= $old('contact_phone') ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3" class="form-control"><?= $old('address') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Autotask Company ID</label>
                <input type="text" name="autotask_company_id" value="<?= $old('autotask_company_id') ?>" class="form-control">
                <small>Optional: Link to Autotask PSA company</small>
            </div>
            <div class="form-group">
                <label>ITGlue Organization ID</label>
                <input type="text" name="itglue_organization_id" value="<?= $old('itglue_organization_id') ?>" class="form-control">
                <small>Optional: Link to ITGlue organization</small>
            </div>
        </div>

        <div class="card" style="margin-top: 20px; background: #f5f5f5;">
            <div class="card-header">
                <h3>Framework Assignments</h3>
            </div>
            <div class="form-group">
                <label>Select Compliance Frameworks *</label>
                <small style="display: block; margin-bottom: 10px;">Choose which frameworks apply to this client</small>
                <?php foreach ($frameworks as $framework): ?>
                <div style="margin-bottom: 8px;">
                    <label style="display: flex; align-items: center; font-weight: normal;">
                        <input type="checkbox" name="frameworks[]" value="<?= $e($framework) ?>" style="margin-right: 8px;">
                        <span><?= $e($framework) ?></span>
                        <input type="radio" name="primary_framework" value="<?= $e($framework) ?>" style="margin-left: 15px; margin-right: 5px;">
                        <small style="color: #666;">Primary</small>
                    </label>
                </div>
                <?php endforeach; ?>
                <small style="color: #666; margin-top: 10px; display: block;">
                    Check the frameworks that apply and select one as the primary framework for scoring.
                </small>
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="active" value="1" checked>
                Active
            </label>
        </div>

        <div class="form-actions">
            <a href="<?= $url('clients') ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Client</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
