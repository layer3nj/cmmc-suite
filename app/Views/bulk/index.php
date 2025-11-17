<?php
$page_title = 'Bulk Operations';
$current_page = 'bulk';

ob_start();
?>

<div class="page-header">
    <h1>Bulk Operations</h1>
    <p class="page-subtitle">Perform operations across multiple clients efficiently</p>
</div>

<div class="tabs">
    <button class="tab-button active" onclick="switchTab('clone-assessment')">Clone Assessment</button>
    <button class="tab-button" onclick="switchTab('batch-poam')">Batch POA&M Update</button>
</div>

<!-- Clone Assessment Tab -->
<div id="clone-assessment" class="tab-content active">
    <div class="card">
        <div class="card-header">
            <h3>Clone Assessment to Multiple Clients</h3>
        </div>
        <div class="card-body">
            <p style="margin-bottom: 20px;">
                Select a source assessment and clone it to multiple target clients. This is useful for applying the same assessment template to similar clients.
            </p>

            <form id="clone-assessment-form">
                <?= $csrf() ?>

                <div class="form-group">
                    <label for="source-assessment">Source Assessment *</label>
                    <select id="source-assessment" name="source_assessment_id" class="form-control" required>
                        <option value="">-- Select Assessment to Clone --</option>
                        <?php foreach ($assessments as $assessment): ?>
                            <option value="<?= $assessment['id'] ?>">
                                <?= $e($assessment['client_name']) ?> -
                                <?= $e($assessment['framework']) ?>
                                <?= $assessment['target_level'] ? ' ML' . $assessment['target_level'] : '' ?>
                                (<?= date('M d, Y', strtotime($assessment['created_at'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Target Clients * (Select multiple)</label>
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                        <?php foreach ($clients as $client): ?>
                            <div style="margin-bottom: 8px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" name="target_client_ids[]" value="<?= $client['id'] ?>" style="margin-right: 8px;">
                                    <?= $e($client['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <small class="form-text">The assessment will be created as a draft for each selected client</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="clone-btn">
                        Clone Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batch POA&M Update Tab -->
<div id="batch-poam" class="tab-content">
    <div class="card">
        <div class="card-header">
            <h3>Batch Update POA&M Items</h3>
        </div>
        <div class="card-body">
            <p style="margin-bottom: 20px;">
                Select multiple POA&M items and update a field for all of them at once.
            </p>

            <!-- Filters -->
            <div class="filter-bar" style="display: flex; gap: 15px; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <div style="flex: 1;">
                    <label for="filter-client">Filter by Client</label>
                    <select id="filter-client" class="form-control">
                        <option value="">All Clients</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= $e($client['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label for="filter-status">Filter by Status</label>
                    <select id="filter-status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="closed">Closed</option>
                        <option value="deferred">Deferred</option>
                    </select>
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="loadPoamItems()">
                        Load Items
                    </button>
                </div>
            </div>

            <!-- POA&M Items Table -->
            <div id="poam-items-container" style="margin-bottom: 20px;">
                <p class="text-muted" style="text-align: center; padding: 40px;">
                    Click "Load Items" to view POA&M items
                </p>
            </div>

            <!-- Update Form -->
            <form id="batch-poam-form" style="display: none;">
                <?= $csrf() ?>

                <div class="form-group">
                    <label for="update-field">Field to Update *</label>
                    <select id="update-field" name="update_field" class="form-control" required>
                        <option value="">-- Select Field --</option>
                        <option value="status">Status</option>
                        <option value="responsible_party">Responsible Party</option>
                        <option value="planned_completion_date">Planned Completion Date</option>
                        <option value="priority">Priority</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="update-value">New Value *</label>
                    <input type="text" id="update-value" name="update_value" class="form-control" required>
                    <small class="form-text">For status: open, in_progress, closed, deferred. For dates: YYYY-MM-DD format</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="batch-update-btn">
                        Update Selected Items
                    </button>
                    <span id="selected-count" class="text-muted" style="margin-left: 15px;"></span>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.tabs {
    display: flex;
    gap: 5px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.tab-button {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    color: #666;
    transition: all 0.2s;
}

.tab-button:hover {
    color: var(--primary);
    background: #f8f9fa;
}

.tab-button.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.filter-bar label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 5px;
}

.poam-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.poam-table th,
.poam-table td {
    padding: 10px;
    border: 1px solid #e0e0e0;
    text-align: left;
}

.poam-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.poam-table tr:hover {
    background: #f8f9fa;
}
</style>

<script>
function switchTab(tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabId).classList.add('active');
    event.target.classList.add('active');
}

// Clone Assessment Form Handler
document.getElementById('clone-assessment-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const targetClients = formData.getAll('target_client_ids[]');

    if (targetClients.length === 0) {
        alert('Please select at least one target client');
        return;
    }

    if (!confirm(`Clone this assessment to ${targetClients.length} client(s)?`)) {
        return;
    }

    const btn = document.getElementById('clone-btn');
    btn.disabled = true;
    btn.textContent = 'Cloning...';

    fetch('<?= $url('bulk/clone-assessment') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            this.reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Network error: ' + error);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Clone Assessment';
    });
});

// Load POA&M Items
function loadPoamItems() {
    const clientId = document.getElementById('filter-client').value;
    const status = document.getElementById('filter-status').value;
    const container = document.getElementById('poam-items-container');

    container.innerHTML = '<p class="text-muted" style="text-align: center; padding: 40px;">Loading...</p>';

    const params = new URLSearchParams();
    if (clientId) params.append('client_id', clientId);
    if (status) params.append('status', status);

    fetch('<?= $url('bulk/get-poam-items') ?>?' + params)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.items.length > 0) {
            let html = `
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="poam-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="select-all" onchange="toggleAllPoam(this)">
                                </th>
                                <th>Client</th>
                                <th>Control</th>
                                <th>Weakness</th>
                                <th>Status</th>
                                <th>Responsible</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.items.forEach(item => {
                const weakness = item.weakness.length > 60 ? item.weakness.substring(0, 60) + '...' : item.weakness;
                html += `
                    <tr>
                        <td><input type="checkbox" class="poam-checkbox" value="${item.id}"></td>
                        <td>${item.client_name}</td>
                        <td><code>${item.control_code || 'N/A'}</code></td>
                        <td>${weakness}</td>
                        <td><span class="badge">${item.status}</span></td>
                        <td>${item.responsible_party || '-'}</td>
                        <td>${item.planned_completion_date || '-'}</td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            container.innerHTML = html;
            document.getElementById('batch-poam-form').style.display = 'block';
            updateSelectedCount();
        } else {
            container.innerHTML = '<p class="text-muted" style="text-align: center; padding: 40px;">No POA&M items found</p>';
            document.getElementById('batch-poam-form').style.display = 'none';
        }
    })
    .catch(error => {
        container.innerHTML = '<p class="text-danger" style="text-align: center; padding: 40px;">Error loading items</p>';
    });
}

function toggleAllPoam(checkbox) {
    document.querySelectorAll('.poam-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const selected = document.querySelectorAll('.poam-checkbox:checked').length;
    document.getElementById('selected-count').textContent = `${selected} item(s) selected`;
}

// Add event listener to individual checkboxes when they're created
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('poam-checkbox')) {
        updateSelectedCount();
    }
});

// Batch POA&M Update Form Handler
document.getElementById('batch-poam-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const selectedItems = Array.from(document.querySelectorAll('.poam-checkbox:checked'))
        .map(cb => cb.value);

    if (selectedItems.length === 0) {
        alert('Please select at least one POA&M item');
        return;
    }

    const formData = new FormData(this);
    selectedItems.forEach(id => formData.append('poam_ids[]', id));

    if (!confirm(`Update ${selectedItems.length} POA&M item(s)?`)) {
        return;
    }

    const btn = document.getElementById('batch-update-btn');
    btn.disabled = true;
    btn.textContent = 'Updating...';

    fetch('<?= $url('bulk/batch-update-poam') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadPoamItems(); // Reload to show updated items
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Network error: ' + error);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Update Selected Items';
    });
});
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
