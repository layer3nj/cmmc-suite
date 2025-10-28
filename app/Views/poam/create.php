<?php
$page_title = 'Create POA&M Item';
$current_page = 'poam';

ob_start();
?>

<div class="page-header">
    <h1>Create POA&M Item</h1>
    <p class="page-subtitle">Plan of Action & Milestones</p>
</div>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<form action="<?= $url('poam') ?>" method="POST">
    <?= $csrf() ?>

    <div class="card">
        <div class="card-header">
            <h3>Control Information</h3>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="control_framework">Control Framework</label>
                <select name="control_framework" id="control_framework" class="form-control">
                    <option value="">Select framework...</option>
                    <option value="CMMC" <?= $old('control_framework') === 'CMMC' ? 'selected' : '' ?>>CMMC 2.0</option>
                    <option value="NIST800171" <?= $old('control_framework') === 'NIST800171' ? 'selected' : '' ?>>NIST SP 800-171</option>
                    <option value="NIST80053" <?= $old('control_framework') === 'NIST80053' ? 'selected' : '' ?>>NIST SP 800-53</option>
                    <option value="STIG" <?= $old('control_framework') === 'STIG' ? 'selected' : '' ?>>DISA STIG</option>
                </select>
            </div>

            <div class="form-group">
                <label for="control_code">Control Code</label>
                <input type="text" name="control_code" id="control_code" value="<?= $e($old('control_code')) ?>" class="form-control" placeholder="e.g., AC.L2-3.1.1">
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3>Weakness & Remediation</h3>
        </div>

        <div class="form-group">
            <label for="weakness_description">Weakness Description <span class="required">*</span></label>
            <textarea name="weakness_description" id="weakness_description" rows="4" class="form-control" required placeholder="Describe the identified weakness or deficiency..."><?= $e($old('weakness_description')) ?></textarea>
            <?php if ($fieldError = $error('weakness_description')): ?>
                <span class="field-error"><?= $e($fieldError) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="remediation_plan">Remediation Plan <span class="required">*</span></label>
            <textarea name="remediation_plan" id="remediation_plan" rows="4" class="form-control" required placeholder="Describe the planned corrective actions..."><?= $e($old('remediation_plan')) ?></textarea>
            <?php if ($fieldError = $error('remediation_plan')): ?>
                <span class="field-error"><?= $e($fieldError) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="resources_required">Resources Required</label>
            <input type="text" name="resources_required" id="resources_required" value="<?= $e($old('resources_required')) ?>" class="form-control" placeholder="e.g., Budget, Personnel, Tools">
            <small>Describe the resources needed to implement the remediation</small>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3>Assignment & Timeline</h3>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="responsible_party">Responsible Party <span class="required">*</span></label>
                <input type="text" name="responsible_party" id="responsible_party" value="<?= $e($old('responsible_party')) ?>" class="form-control" required placeholder="Name or Role">
                <?php if ($fieldError = $error('responsible_party')): ?>
                    <span class="field-error"><?= $e($fieldError) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="milestone_changes">Milestone Changes</label>
                <input type="text" name="milestone_changes" id="milestone_changes" value="<?= $e($old('milestone_changes')) ?>" class="form-control" placeholder="Key milestones or phases">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="original_detection_date">Original Detection Date</label>
                <input type="date" name="original_detection_date" id="original_detection_date" value="<?= $e($old('original_detection_date') ?: date('Y-m-d')) ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="planned_completion_date">Planned Completion Date <span class="required">*</span></label>
                <input type="date" name="planned_completion_date" id="planned_completion_date" value="<?= $e($old('planned_completion_date')) ?>" class="form-control" required>
                <?php if ($fieldError = $error('planned_completion_date')): ?>
                    <span class="field-error"><?= $e($fieldError) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="status">Status <span class="required">*</span></label>
            <select name="status" id="status" class="form-control" required>
                <option value="open" <?= $old('status', 'open') === 'open' ? 'selected' : '' ?>>Open</option>
                <option value="in_progress" <?= $old('status') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="closed" <?= $old('status') === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </div>
    </div>

    <div class="form-actions" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary">Create POA&M Item</button>
        <a href="<?= $url('poam') ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
