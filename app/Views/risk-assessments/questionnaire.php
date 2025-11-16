<?php
$page_title = 'Risk Assessment Questionnaire - ' . $assessment['title'];
$current_page = 'risk-assessments';

ob_start();
?>

<div class="page-header">
    <h1><?= $e($assessment['title']) ?></h1>
    <p class="page-subtitle">Cybersecurity Risk Assessment Questionnaire</p>
</div>

<!-- Progress Bar -->
<div class="card" style="margin-bottom: 20px;">
    <div style="padding: 15px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <strong>Progress:</strong>
            <span><?= $answered_questions ?> of <?= $total_questions ?> questions answered (<?= $progress ?>%)</span>
        </div>
        <div style="background: #e5e7eb; height: 24px; border-radius: 4px; overflow: hidden;">
            <div style="background: #667eea; height: 100%; width: <?= $progress ?>%; transition: width 0.3s;"></div>
        </div>
    </div>
</div>

<style>
.question-card {
    margin-bottom: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: white;
}
.question-header {
    padding: 15px;
    background: #f7fafc;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.question-body {
    padding: 20px;
}
.risk-matrix {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 15px;
}
.risk-indicator {
    padding: 8px 12px;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
    margin-top: 10px;
}
.risk-low { background: #d1fae5; color: #065f46; }
.risk-medium { background: #fef3c7; color: #92400e; }
.risk-high { background: #fed7aa; color: #9a3412; }
.risk-critical { background: #fee2e2; color: #991b1b; }
.category-section {
    margin-bottom: 40px;
}
.category-header {
    background: #667eea;
    color: white;
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
}
</style>

<?php foreach ($questions_by_category as $category => $questions): ?>
<div class="category-section">
    <div class="category-header">
        <h2 style="margin: 0; font-size: 1.4em;">📋 <?= $e($category) ?></h2>
        <p style="margin: 5px 0 0 0; opacity: 0.9;"><?= count($questions) ?> questions</p>
    </div>

    <?php foreach ($questions as $question): ?>
        <?php
        $response = $responses[$question['id']] ?? null;
        $answered = $response !== null;
        ?>
        <div class="question-card" id="question-<?= $question['id'] ?>">
            <div class="question-header">
                <div style="flex: 1;">
                    <strong style="color: #4a5568;"><?= $e($question['subcategory'] ?? $category) ?></strong>
                    <?php if ($answered): ?>
                        <span class="badge badge-success" style="margin-left: 10px;">✓ Answered</span>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="badge badge-info">Weight: <?= $question['weight'] ?></span>
                </div>
            </div>
            <div class="question-body">
                <p style="font-size: 1.1em; margin-bottom: 15px; font-weight: 500;">
                    <?= $e($question['question_text']) ?>
                </p>

                <?php if (!empty($question['guidance'])): ?>
                <div style="background: #f7fafc; padding: 12px; border-left: 3px solid #667eea; margin-bottom: 15px; font-size: 0.95em;">
                    <strong>💡 Guidance:</strong> <?= $e($question['guidance']) ?>
                </div>
                <?php endif; ?>

                <!-- Response Options -->
                <div style="margin-bottom: 15px;">
                    <label><strong>Response:</strong></label>
                    <div style="display: flex; gap: 10px; margin-top: 8px;">
                        <label style="flex: 1;">
                            <input type="radio"
                                   name="response_<?= $question['id'] ?>"
                                   value="yes"
                                   <?= ($response && $response['response_value'] === 'yes') ? 'checked' : '' ?>
                                   onchange="updateResponse(<?= $question['id'] ?>, 'yes')">
                            <span class="badge badge-success">✓ Yes</span>
                        </label>
                        <label style="flex: 1;">
                            <input type="radio"
                                   name="response_<?= $question['id'] ?>"
                                   value="no"
                                   <?= ($response && $response['response_value'] === 'no') ? 'checked' : '' ?>
                                   onchange="updateResponse(<?= $question['id'] ?>, 'no')">
                            <span class="badge badge-danger">✗ No</span>
                        </label>
                        <label style="flex: 1;">
                            <input type="radio"
                                   name="response_<?= $question['id'] ?>"
                                   value="partial"
                                   <?= ($response && $response['response_value'] === 'partial') ? 'checked' : '' ?>
                                   onchange="updateResponse(<?= $question['id'] ?>, 'partial')">
                            <span class="badge badge-warning">⚠ Partial</span>
                        </label>
                        <label style="flex: 1;">
                            <input type="radio"
                                   name="response_<?= $question['id'] ?>"
                                   value="na"
                                   <?= ($response && $response['response_value'] === 'na') ? 'checked' : '' ?>
                                   onchange="updateResponse(<?= $question['id'] ?>, 'na')">
                            <span class="badge badge-secondary">N/A</span>
                        </label>
                    </div>
                </div>

                <!-- Risk Assessment (shown only if No or Partial) -->
                <div id="risk-assessment-<?= $question['id'] ?>"
                     style="display: <?= ($response && in_array($response['response_value'], ['no', 'partial'])) ? 'block' : 'none' ?>;">

                    <div class="risk-matrix">
                        <div>
                            <label><strong>Likelihood (1-5):</strong></label>
                            <select class="form-control"
                                    id="likelihood-<?= $question['id'] ?>"
                                    onchange="calculateRisk(<?= $question['id'] ?>)">
                                <option value="1" <?= ($response && $response['likelihood'] == 1) ? 'selected' : '' ?>>1 - Rare</option>
                                <option value="2" <?= ($response && $response['likelihood'] == 2) ? 'selected' : '' ?>>2 - Unlikely</option>
                                <option value="3" <?= ($response && $response['likelihood'] == 3) ? 'selected' : '' ?>>3 - Possible</option>
                                <option value="4" <?= ($response && $response['likelihood'] == 4) ? 'selected' : '' ?>>4 - Likely</option>
                                <option value="5" <?= ($response && $response['likelihood'] == 5) ? 'selected' : '' ?>>5 - Almost Certain</option>
                            </select>
                        </div>
                        <div>
                            <label><strong>Impact (1-5):</strong></label>
                            <select class="form-control"
                                    id="impact-<?= $question['id'] ?>"
                                    onchange="calculateRisk(<?= $question['id'] ?>)">
                                <option value="1" <?= ($response && $response['impact'] == 1) ? 'selected' : '' ?>>1 - Insignificant</option>
                                <option value="2" <?= ($response && $response['impact'] == 2) ? 'selected' : '' ?>>2 - Minor</option>
                                <option value="3" <?= ($response && $response['impact'] == 3) ? 'selected' : '' ?>>3 - Moderate</option>
                                <option value="4" <?= ($response && $response['impact'] == 4) ? 'selected' : '' ?>>4 - Major</option>
                                <option value="5" <?= ($response && $response['impact'] == 5) ? 'selected' : '' ?>>5 - Catastrophic</option>
                            </select>
                        </div>
                    </div>

                    <div id="risk-indicator-<?= $question['id'] ?>" class="risk-indicator" style="display: <?= $response ? 'block' : 'none' ?>;">
                        <!-- Risk level will be shown here -->
                    </div>

                    <div style="margin-top: 15px;">
                        <label><strong>Notes / Remediation Plan:</strong></label>
                        <textarea class="form-control"
                                  id="notes-<?= $question['id'] ?>"
                                  rows="3"
                                  placeholder="Add notes about this risk or planned remediation steps..."
                                  onchange="saveNotes(<?= $question['id'] ?>)"><?= $e($response['notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>

<div class="form-actions">
    <form action="<?= $url('risk-assessments/' . $assessment['id'] . '/complete') ?>" method="POST" onsubmit="return confirmComplete();">
        <?= $csrf() ?>
        <button type="submit" class="btn btn-primary" style="font-size: 1.1em; padding: 12px 24px;">
            ✓ Complete Assessment & View Results
        </button>
    </form>
    <a href="<?= $url('risk-assessments') ?>" class="btn btn-secondary">Save & Continue Later</a>
</div>

<script>
function updateResponse(questionId, value) {
    // Show/hide risk assessment section
    const riskSection = document.getElementById('risk-assessment-' + questionId);
    if (value === 'no' || value === 'partial') {
        riskSection.style.display = 'block';
        // Trigger risk calculation if values are set
        calculateRisk(questionId);
    } else {
        riskSection.style.display = 'none';
        // For Yes/NA, save with low risk
        saveResponse(questionId, value, 1, 1);
    }
}

function calculateRisk(questionId) {
    const responseValue = document.querySelector('input[name="response_' + questionId + '"]:checked')?.value;
    if (!responseValue || (responseValue !== 'no' && responseValue !== 'partial')) {
        return;
    }

    const likelihood = parseInt(document.getElementById('likelihood-' + questionId).value);
    const impact = parseInt(document.getElementById('impact-' + questionId).value);

    saveResponse(questionId, responseValue, likelihood, impact);
}

function saveResponse(questionId, responseValue, likelihood, impact) {
    const notes = document.getElementById('notes-' + questionId)?.value || '';

    fetch('<?= $url('risk-assessments/' . $assessment['id'] . '/save-response') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            _csrf_token: '<?= $csrfToken() ?>',
            question_id: questionId,
            response_value: responseValue,
            likelihood: likelihood,
            impact: impact,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show risk level
            const riskIndicator = document.getElementById('risk-indicator-' + questionId);
            if (riskIndicator && data.risk_level) {
                const riskLabels = {
                    'low': 'Low Risk',
                    'medium': 'Medium Risk',
                    'high': 'High Risk',
                    'critical': 'Critical Risk'
                };
                riskIndicator.className = 'risk-indicator risk-' + data.risk_level;
                riskIndicator.textContent = 'Risk Level: ' + riskLabels[data.risk_level];
                riskIndicator.style.display = 'block';
            }

            // Mark as answered
            const questionCard = document.getElementById('question-' + questionId);
            const header = questionCard.querySelector('.question-header > div:first-child');
            if (!header.querySelector('.badge-success')) {
                header.innerHTML += ' <span class="badge badge-success" style="margin-left: 10px;">✓ Answered</span>';
            }

            // Refresh progress (simple reload - you could make this AJAX too)
            location.reload();
        }
    });
}

function saveNotes(questionId) {
    // Trigger save with current values
    const responseValue = document.querySelector('input[name="response_' + questionId + '"]:checked')?.value;
    if (responseValue && (responseValue === 'no' || responseValue === 'partial')) {
        calculateRisk(questionId);
    }
}

function confirmComplete() {
    const total = <?= $total_questions ?>;
    const answered = <?= $answered_questions ?>;

    if (answered < total) {
        return confirm(`You have answered ${answered} out of ${total} questions. Complete the assessment anyway?`);
    }
    return confirm('Mark this assessment as complete? You can still view and update it later.');
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
