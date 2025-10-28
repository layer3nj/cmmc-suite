<?php
$page_title = 'Edit Customer';
$current_page = 'customers';

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('customers') ?>">Customers</a> / <a href="<?= $url('customers/' . $customer['id']) ?>"><?= $e($customer['name']) ?></a> / Edit
    </div>
    <h1>Edit Customer</h1>
</div>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<div class="card">
    <form action="<?= $url('customers/' . $customer['id']) ?>" method="POST">
        <?= $csrf() ?>

        <div class="form-group">
            <label>Customer Name *</label>
            <input type="text" name="name" value="<?= $e($customer['name']) ?>" required class="form-control">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Contact Email</label>
                <input type="email" name="contact_email" value="<?= $e($customer['contact_email'] ?? '') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>Contact Phone</label>
                <input type="tel" name="contact_phone" value="<?= $e($customer['contact_phone'] ?? '') ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3" class="form-control"><?= $e($customer['address'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Autotask Company ID</label>
                <input type="text" name="autotask_company_id" value="<?= $e($customer['autotask_company_id'] ?? '') ?>" class="form-control">
                <small>Optional: Link to Autotask PSA company</small>
            </div>
            <div class="form-group">
                <label>ITGlue Organization ID</label>
                <input type="text" name="itglue_organization_id" value="<?= $e($customer['itglue_organization_id'] ?? '') ?>" class="form-control">
                <small>Optional: Link to ITGlue organization</small>
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="active" value="1" <?= $customer['active'] ? 'checked' : '' ?>>
                Active
            </label>
        </div>

        <div class="form-actions">
            <a href="<?= $url('customers/' . $customer['id']) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Customer</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
