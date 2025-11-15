<?php
$page_title = 'New Risk Assessment';
$current_page = 'risk-assessments';

ob_start();
?>

<div class="page-header">
    <h1>New Risk Assessment</h1>
    <p class="page-subtitle">Create a comprehensive cybersecurity risk assessment</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Assessment Details</h3>
    </div>
    <form action="<?= $url('risk-assessments') ?>" method="POST">
        <?= $csrf() ?>

        <div class="form-group">
            <label for="title">Assessment Title *</label>
            <input type="text"
                   id="title"
                   name="title"
                   class="form-control"
                   placeholder="e.g., Q1 2025 Cybersecurity Risk Assessment"
                   required>
            <small>Give this assessment a descriptive name</small>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description"
                      name="description"
                      class="form-control"
                      rows="4"
                      placeholder="Optional: Add context, scope, or objectives for this assessment"></textarea>
            <small>Optional: Describe the purpose and scope of this assessment</small>
        </div>

        <div style="background: #f7fafc; padding: 15px; border-left: 4px solid #667eea; margin: 20px 0;">
            <h4 style="margin-top: 0;">What to Expect</h4>
            <p>This risk assessment includes:</p>
            <ul style="margin-bottom: 0;">
                <li><strong>70+ Questions</strong> covering all major security domains</li>
                <li><strong>Categories:</strong> Access Control, Network Security, Data Protection, Incident Response, and more</li>
                <li><strong>Risk Scoring:</strong> Each response is evaluated for likelihood and impact</li>
                <li><strong>Actionable Results:</strong> Prioritized list of risks with remediation guidance</li>
            </ul>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Assessment & Begin Questionnaire</button>
            <a href="<?= $url('risk-assessments') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
