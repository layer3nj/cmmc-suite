<?php
$page_title = 'Edit POA&M Item';
$current_page = 'poam';

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('poam') ?>">POA&M</a> / <a href="<?= $url('poam/' . $item['id']) ?>"><?= $e(substr($item['title'], 0, 50)) ?></a> / Edit
    </div>
    <h1>Edit POA&M Item</h1>
</div>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<div class="card">
    <form action="<?= $url('poam/' . $item['id']) ?>" method="POST">
        <?= $csrf() ?>

        <div class="form-row">
            <div class="form-group">
                <label>Control Framework</label>
                <select name="control_framework" class="form-control">
                    <option value="">Select Framework</option>
                    <option value="CMMC" <?= ($item['control_framework'] ?? '') === 'CMMC' ? 'selected' : '' ?>>CMMC</option>
                    <option value="NIST800171" <?= ($item['control_framework'] ?? '') === 'NIST800171' ? 'selected' : '' ?>>NIST 800-171</option>
                    <option value="STIG" <?= ($item['control_framework'] ?? '') === 'STIG' ? 'selected' : '' ?>>STIG</option>
                    <option value="HIPAA" <?= ($item['control_framework'] ?? '') === 'HIPAA' ? 'selected' : '' ?>>HIPAA</option>
                    <option value="FTC-SAFEGUARDS" <?= ($item['control_framework'] ?? '') === 'FTC-SAFEGUARDS' ? 'selected' : '' ?>>FTC Safeguards</option>
                    <option value="PCI-DSS" <?= ($item['control_framework'] ?? '') === 'PCI-DSS' ? 'selected' : '' ?>>PCI-DSS</option>
                    <option value="SOC2" <?= ($item['control_framework'] ?? '') === 'SOC2' ? 'selected' : '' ?>>SOC 2</option>
                    <option value="ISO27001" <?= ($item['control_framework'] ?? '') === 'ISO27001' ? 'selected' : '' ?>>ISO 27001</option>
                </select>
            </div>
            <div class="form-group">
                <label>Control Code</label>
                <input type="text" name="control_code" value="<?= $e($item['control_code'] ?? '') ?>" class="form-control" placeholder="e.g., AC.L1-3.1.1">
            </div>
        </div>

        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" value="<?= $e($item['title']) ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label>Weakness Description</label>
            <textarea name="weakness" rows="4" class="form-control"><?= $e($item['weakness'] ?? '') ?></textarea>
            <small>Describe the identified security weakness or gap</small>
        </div>

        <div class="form-group">
            <label>Corrective Action</label>
            <textarea name="corrective_action" rows="4" class="form-control"><?= $e($item['corrective_action'] ?? '') ?></textarea>
            <small>Describe the planned corrective actions to address the weakness</small>
        </div>

        <div class="form-group">
            <label>Milestones</label>
            <textarea name="milestones" rows="3" class="form-control"><?= $e($item['milestones'] ?? '') ?></textarea>
            <small>List key milestones for completing this POA&M item</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Responsible Party</label>
                <input type="text" name="responsible_party" value="<?= $e($item['responsible_party'] ?? '') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>Resources</label>
                <input type="text" name="resources" value="<?= $e($item['resources'] ?? '') ?>" class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" value="<?= $e($item['start_date'] ?? '') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>Planned Completion Date</label>
                <input type="date" name="planned_completion_date" value="<?= $e($item['planned_completion_date'] ?? '') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>Actual Completion Date</label>
                <input type="date" name="actual_completion_date" value="<?= $e($item['actual_completion_date'] ?? '') ?>" class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="open" <?= $item['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="in_progress" <?= $item['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="closed" <?= $item['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                    <option value="deferred" <?= $item['status'] === 'deferred' ? 'selected' : '' ?>>Deferred</option>
                </select>
            </div>
            <div class="form-group">
                <label>Residual Risk</label>
                <select name="residual_risk" class="form-control">
                    <option value="">Not Assessed</option>
                    <option value="Low" <?= ($item['residual_risk'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
                    <option value="Medium" <?= ($item['residual_risk'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="High" <?= ($item['residual_risk'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Comments</label>
            <textarea name="comments" rows="3" class="form-control"><?= $e($item['comments'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="<?= $url('poam/' . $item['id']) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update POA&M Item</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
