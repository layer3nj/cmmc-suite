<?php
$page_title = 'Add Customer';
$current_page = 'customers';

ob_start();
?>

<div class="page-header">
    <h1>Add Customer</h1>
    <p class="page-subtitle">Create a new customer account</p>
</div>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<div class="card">
    <form action="<?= $url('customers') ?>" method="POST">
        <?= $csrf() ?>

        <div class="form-group">
            <label>Customer Name *</label>
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

        <div class="form-group">
            <label>
                <input type="checkbox" name="active" value="1" checked>
                Active
            </label>
        </div>

        <div class="form-actions">
            <a href="<?= $url('customers') ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Customer</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
