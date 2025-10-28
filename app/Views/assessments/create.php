<?php
$page_title = 'Create Assessment';
$current_page = 'assessments';

ob_start();
?>

<div class="page-header">
    <h1>Create Assessment</h1>
    <p class="page-subtitle">Start a new compliance assessment</p>
</div>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<form action="<?= $url('assessments') ?>" method="POST">
    <?= $csrf() ?>

    <div class="card">
        <div class="card-header">
            <h3>Assessment Details</h3>
        </div>

        <div class="form-group">
            <label for="framework">Framework <span class="required">*</span></label>
            <select name="framework" id="framework" class="form-control" required>
                <option value="">Select framework...</option>
                <optgroup label="DoD / Government">
                    <option value="CMMC" <?= $old('framework') === 'CMMC' ? 'selected' : '' ?>>CMMC 2.0</option>
                    <option value="NIST800171" <?= $old('framework') === 'NIST800171' ? 'selected' : '' ?>>NIST SP 800-171</option>
                    <option value="NIST80053" <?= $old('framework') === 'NIST80053' ? 'selected' : '' ?>>NIST SP 800-53</option>
                    <option value="STIG" <?= $old('framework') === 'STIG' ? 'selected' : '' ?>>DISA STIG</option>
                </optgroup>
                <optgroup label="Healthcare">
                    <option value="HIPAA" <?= $old('framework') === 'HIPAA' ? 'selected' : '' ?>>HIPAA Security Rule</option>
                </optgroup>
                <optgroup label="Financial">
                    <option value="FTC-SAFEGUARDS" <?= $old('framework') === 'FTC-SAFEGUARDS' ? 'selected' : '' ?>>FTC Safeguards Rule</option>
                    <option value="PCI-DSS" <?= $old('framework') === 'PCI-DSS' ? 'selected' : '' ?>>PCI-DSS v4.0</option>
                </optgroup>
                <optgroup label="Audit & Certification">
                    <option value="SOC2" <?= $old('framework') === 'SOC2' ? 'selected' : '' ?>>SOC 2 (Trust Services)</option>
                    <option value="ISO27001" <?= $old('framework') === 'ISO27001' ? 'selected' : '' ?>>ISO 27001:2022</option>
                </optgroup>
            </select>
            <?php if ($fieldError = $error('framework')): ?>
                <span class="field-error"><?= $e($fieldError) ?></span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="assessment_type">Assessment Type <span class="required">*</span></label>
            <select name="assessment_type" id="assessment_type" class="form-control" required>
                <option value="">Select type...</option>
                <option value="self" <?= $old('assessment_type') === 'self' ? 'selected' : '' ?>>Self-Assessment</option>
                <option value="internal" <?= $old('assessment_type') === 'internal' ? 'selected' : '' ?>>Internal Audit</option>
                <option value="external" <?= $old('assessment_type') === 'external' ? 'selected' : '' ?>>External Audit</option>
                <option value="c3pao" <?= $old('assessment_type') === 'c3pao' ? 'selected' : '' ?>>C3PAO Assessment</option>
            </select>
            <?php if ($fieldError = $error('assessment_type')): ?>
                <span class="field-error"><?= $e($fieldError) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="scope">Scope</label>
            <input type="text" name="scope" id="scope" value="<?= $e($old('scope')) ?>" class="form-control" placeholder="e.g., Corporate Network, Production Environment">
            <small>Define the boundaries of this assessment</small>
        </div>

        <div class="form-group">
            <label for="target_level">Target Level</label>
            <select name="target_level" id="target_level" class="form-control">
                <option value="">Not applicable</option>
                <option value="1" <?= $old('target_level') === '1' ? 'selected' : '' ?>>Level 1 (Foundational)</option>
                <option value="2" <?= $old('target_level') === '2' ? 'selected' : '' ?>>Level 2 (Advanced)</option>
                <option value="3" <?= $old('target_level') === '3' ? 'selected' : '' ?>>Level 3 (Expert)</option>
            </select>
            <small>For CMMC assessments only</small>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" rows="5" class="form-control" placeholder="Add any relevant notes or context for this assessment"><?= $e($old('notes')) ?></textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Create Assessment</button>
        <a href="<?= $url('assessments') ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
