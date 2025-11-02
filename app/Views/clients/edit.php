<?php
$page_title = 'Edit Client';
$current_page = 'clients';

// Create array of currently assigned frameworks for easy checking
$currentFrameworks = [];
$primaryFramework = null;
foreach ($assigned_frameworks as $af) {
    $currentFrameworks[] = $af['framework'];
    if ($af['is_primary']) {
        $primaryFramework = $af['framework'];
    }
}

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('clients') ?>">Clients</a> / <a href="<?= $url('clients/' . $client['id']) ?>"><?= $e($client['name']) ?></a> / Edit
    </div>
    <h1>Edit Client</h1>
</div>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<div class="card">
    <form action="<?= $url('clients/' . $client['id']) ?>" method="POST">
        <?= $csrf() ?>

        <div class="form-group">
            <label>Client Name *</label>
            <input type="text" name="name" value="<?= $e($client['name']) ?>" required class="form-control">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Contact Email</label>
                <input type="email" name="contact_email" value="<?= $e($client['contact_email'] ?? '') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>Contact Phone</label>
                <input type="tel" name="contact_phone" value="<?= $e($client['contact_phone'] ?? '') ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3" class="form-control"><?= $e($client['address'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Autotask Company ID</label>
                <input type="text" name="autotask_company_id" value="<?= $e($client['autotask_company_id'] ?? '') ?>" class="form-control">
                <small>Optional: Link to Autotask PSA company</small>
            </div>
            <div class="form-group">
                <label>ITGlue Organization ID</label>
                <input type="text" name="itglue_organization_id" value="<?= $e($client['itglue_organization_id'] ?? '') ?>" class="form-control">
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
                        <input type="checkbox" name="frameworks[]" value="<?= $e($framework) ?>"
                               <?= in_array($framework, $currentFrameworks) ? 'checked' : '' ?>
                               style="margin-right: 8px;">
                        <span><?= $e($framework) ?></span>
                        <input type="radio" name="primary_framework" value="<?= $e($framework) ?>"
                               <?= $framework === $primaryFramework ? 'checked' : '' ?>
                               style="margin-left: 15px; margin-right: 5px;">
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
                <input type="checkbox" name="active" value="1" <?= $client['active'] ? 'checked' : '' ?>>
                Active
            </label>
        </div>

        <div class="form-actions">
            <a href="<?= $url('clients/' . $client['id']) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Client</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
