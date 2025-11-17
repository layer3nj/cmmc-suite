<?php
$page_title = 'Compliance Gap Analysis';
$current_page = 'compliance-gap';

ob_start();
?>

<div class="page-header">
    <h1>Compliance Gap Analysis</h1>
    <p class="page-subtitle">Identify gaps and prioritize remediation efforts</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Select Framework for Analysis</h3>
    </div>
    <div class="card-body">
        <form id="gap-analysis-form" onsubmit="analyzeGaps(event)">
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="framework">Framework *</label>
                    <select id="framework" name="framework" class="form-control" required onchange="updateTargetLevel()">
                        <option value="">Select Framework</option>
                        <option value="CMMC">CMMC</option>
                        <option value="NIST800171">NIST 800-171</option>
                        <option value="NIST80053">NIST 800-53</option>
                        <option value="ISO27001">ISO 27001</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="target_level">Target Maturity Level (CMMC only)</label>
                    <select id="target_level" name="target_level" class="form-control" disabled>
                        <option value="">All Levels</option>
                        <option value="1">Level 1</option>
                        <option value="2">Level 2</option>
                        <option value="3">Level 3</option>
                        <option value="4">Level 4</option>
                        <option value="5">Level 5</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" id="analyze-btn">
                    Analyze Gaps
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Results Container -->
<div id="results-container" style="display: none; margin-top: 20px;">
    <!-- Statistics -->
    <div class="stats-grid" id="stats-section">
        <div class="stat-card">
            <div class="stat-label">Total Controls</div>
            <div class="stat-value" id="stat-total">0</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Compliant</div>
            <div class="stat-value" id="stat-compliant">0</div>
            <div class="stat-subtitle" id="stat-compliance-rate">0%</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-label">Non-Compliant</div>
            <div class="stat-value" id="stat-non-compliant">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Not Assessed</div>
            <div class="stat-value" id="stat-not-assessed">0</div>
        </div>
    </div>

    <!-- Priority Breakdown -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3>Priority Breakdown</h3>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div style="text-align: center; padding: 20px; background: #fff3cd; border-radius: 4px;">
                    <div style="font-size: 32px; font-weight: bold; color: #dc3545;" id="priority-high">0</div>
                    <div style="color: #666; margin-top: 5px;">High Priority Gaps</div>
                </div>
                <div style="text-align: center; padding: 20px; background: #fff3cd; border-radius: 4px;">
                    <div style="font-size: 32px; font-weight: bold; color: #fd7e14;" id="priority-medium">0</div>
                    <div style="color: #666; margin-top: 5px;">Medium Priority Gaps</div>
                </div>
                <div style="text-align: center; padding: 20px; background: #e7f3ff; border-radius: 4px;">
                    <div style="font-size: 32px; font-weight: bold; color: #0066cc;" id="priority-low">0</div>
                    <div style="color: #666; margin-top: 5px;">Low Priority Gaps</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gap Details -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Gap Details</h3>
            <button onclick="exportGaps()" class="btn btn-secondary btn-sm">
                Export to CSV
            </button>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Priority</th>
                        <th>Control</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody id="gaps-table-body">
                    <tr>
                        <td colspan="6" style="text-align: center; color: #666; padding: 40px;">
                            Select a framework and click "Analyze Gaps" to begin
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.priority-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.priority-high {
    background: #dc3545;
    color: white;
}

.priority-medium {
    background: #fd7e14;
    color: white;
}

.priority-low {
    background: #0066cc;
    color: white;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.status-non-compliant {
    background: #f8d7da;
    color: #721c24;
}

.status-not-assessed {
    background: #e7f3ff;
    color: #004085;
}

.stat-card.success {
    border-left: 4px solid #28a745;
}

.stat-card.danger {
    border-left: 4px solid #dc3545;
}
</style>

<script>
let currentAnalysisData = null;

function updateTargetLevel() {
    const framework = document.getElementById('framework').value;
    const targetLevel = document.getElementById('target_level');

    if (framework === 'CMMC') {
        targetLevel.disabled = false;
    } else {
        targetLevel.disabled = true;
        targetLevel.value = '';
    }
}

function analyzeGaps(event) {
    event.preventDefault();

    const framework = document.getElementById('framework').value;
    const targetLevel = document.getElementById('target_level').value;
    const analyzeBtn = document.getElementById('analyze-btn');

    analyzeBtn.disabled = true;
    analyzeBtn.textContent = 'Analyzing...';

    const params = new URLSearchParams({
        framework: framework,
        target_level: targetLevel || ''
    });

    fetch('<?= $url('compliance-gap/analyze') ?>?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentAnalysisData = data;
                displayResults(data);
            } else {
                alert('Error: ' + (data.error || 'Analysis failed'));
            }
        })
        .catch(error => {
            alert('Network error: ' + error);
        })
        .finally(() => {
            analyzeBtn.disabled = false;
            analyzeBtn.textContent = 'Analyze Gaps';
        });
}

function displayResults(data) {
    // Show results container
    document.getElementById('results-container').style.display = 'block';

    // Update statistics
    document.getElementById('stat-total').textContent = data.stats.total_controls;
    document.getElementById('stat-compliant').textContent = data.stats.compliant;
    document.getElementById('stat-non-compliant').textContent = data.stats.non_compliant;
    document.getElementById('stat-not-assessed').textContent = data.stats.not_assessed;
    document.getElementById('stat-compliance-rate').textContent = data.compliance_rate + '%';

    // Update priority breakdown
    document.getElementById('priority-high').textContent = data.stats.high_priority;
    document.getElementById('priority-medium').textContent = data.stats.medium_priority;
    document.getElementById('priority-low').textContent = data.stats.low_priority;

    // Update gaps table
    const tbody = document.getElementById('gaps-table-body');
    tbody.innerHTML = '';

    if (data.gaps.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #28a745; padding: 40px; font-weight: 600;">🎉 No compliance gaps found! All controls are compliant.</td></tr>';
    } else {
        data.gaps.forEach(gap => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <span class="priority-badge priority-${gap.priority}">
                        ${gap.priority}
                    </span>
                </td>
                <td><strong>${escapeHtml(gap.control_code)}</strong></td>
                <td>${escapeHtml(gap.title)}</td>
                <td>${escapeHtml(gap.category)}</td>
                <td>
                    <span class="status-badge status-${gap.status.replace('_', '-')}">
                        ${gap.status.replace('_', ' ')}
                    </span>
                </td>
                <td>${escapeHtml(gap.notes || '-')}</td>
            `;
            tbody.appendChild(row);
        });
    }

    // Scroll to results
    document.getElementById('results-container').scrollIntoView({ behavior: 'smooth' });
}

function exportGaps() {
    if (!currentAnalysisData) {
        alert('Please run an analysis first');
        return;
    }

    const framework = currentAnalysisData.framework;
    const targetLevel = currentAnalysisData.target_level || '';

    const params = new URLSearchParams({
        framework: framework,
        target_level: targetLevel
    });

    window.location.href = '<?= $url('compliance-gap/export') ?>?' + params.toString();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
