<?php
$page_title = 'Evidence Collection';
$current_page = 'evidence';

ob_start();
?>

<div class="page-header">
    <h1>Evidence Collection & Management</h1>
    <p class="page-subtitle">Track and manage compliance evidence</p>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="showUploadModal()">Upload Evidence</button>
    </div>
</div>

<?php if ($successMessage = $success()): ?>
    <div class="alert alert-success"><?= $e($successMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage = $error()): ?>
    <div class="alert alert-error"><?= $e($errorMessage) ?></div>
<?php endif; ?>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Evidence</div>
        <div class="stat-value"><?= $stats['total'] ?></div>
    </div>
    <div class="stat-card success">
        <div class="stat-label">Valid Evidence</div>
        <div class="stat-value"><?= $stats['valid'] ?></div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #fd7e14;">
        <div class="stat-label">Expiring Soon (30 days)</div>
        <div class="stat-value"><?= $stats['expiring_soon'] ?></div>
    </div>
    <div class="stat-card danger">
        <div class="stat-label">Expired</div>
        <div class="stat-value"><?= $stats['expired'] ?></div>
    </div>
    <?php if ($stats['pending_approval'] > 0): ?>
    <div class="stat-card" style="border-left: 4px solid #6c757d;">
        <div class="stat-label">Pending Approval</div>
        <div class="stat-value"><?= $stats['pending_approval'] ?></div>
    </div>
    <?php endif; ?>
</div>

<!-- Evidence Library -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Evidence Library</h3>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <input type="text" id="search-input" placeholder="Search by description, control, or filename..." style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">

            <select id="status-filter" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <select id="validity-filter" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">All Validity</option>
                <option value="valid">Valid</option>
                <option value="expiring_soon">Expiring Soon</option>
                <option value="expired">Expired</option>
            </select>
        </div>

        <!-- Evidence Table -->
        <div style="overflow-x: auto;">
            <table class="data-table" id="evidence-table">
                <thead>
                    <tr>
                        <th>Control</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>File</th>
                        <th>Expiration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($evidence)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #666; padding: 40px;">
                            No evidence uploaded yet. Click "Upload Evidence" to get started.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($evidence as $item): ?>
                    <tr class="evidence-row"
                        data-status="<?= $e($item['status']) ?>"
                        data-validity="<?= $e($item['validity_status']) ?>"
                        data-search="<?= $e(strtolower($item['control_code'] . ' ' . $item['description'] . ' ' . $item['file_name'])) ?>">
                        <td>
                            <strong><?= $e($item['control_code']) ?></strong>
                            <br>
                            <small style="color: #666;"><?= $e($item['control_framework']) ?></small>
                        </td>
                        <td><?= $e(ucfirst(str_replace('_', ' ', $item['evidence_type']))) ?></td>
                        <td><?= $e($item['description']) ?></td>
                        <td>
                            <a href="<?= $url('evidence/' . $item['id'] . '/download') ?>" style="color: #007bff; text-decoration: none;">
                                📎 <?= $e($item['file_name']) ?>
                            </a>
                            <br>
                            <small style="color: #666;"><?= number_format($item['file_size'] / 1024, 1) ?> KB</small>
                        </td>
                        <td>
                            <?php if ($item['expiration_date']): ?>
                                <span class="validity-badge validity-<?= $item['validity_status'] ?>">
                                    <?= date('M d, Y', strtotime($item['expiration_date'])) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #666;">No expiration</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $item['status'] === 'approved' ? 'success' : ($item['status'] === 'rejected' ? 'danger' : 'secondary') ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($item['status'] === 'pending' && \App\Middleware\AuthMiddleware::checkPermission('admin')): ?>
                                <button onclick="approveEvidence(<?= $item['id'] ?>)" class="btn btn-sm btn-success" title="Approve">
                                    ✓
                                </button>
                                <button onclick="rejectEvidence(<?= $item['id'] ?>)" class="btn btn-sm btn-danger" title="Reject">
                                    ✗
                                </button>
                            <?php endif; ?>
                            <button onclick="deleteEvidence(<?= $item['id'] ?>)" class="btn btn-sm btn-secondary" title="Delete">
                                🗑️
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Upload Evidence</h3>
            <button onclick="closeUploadModal()" class="close-btn">×</button>
        </div>
        <form id="upload-form" enctype="multipart/form-data" onsubmit="uploadEvidence(event)">
            <?= $csrf() ?>

            <div class="form-group">
                <label for="control_code">Control Code *</label>
                <input type="text" id="control_code" name="control_code" class="form-control" required placeholder="e.g., AC.1.001">
            </div>

            <div class="form-group">
                <label for="control_framework">Framework *</label>
                <select id="control_framework" name="control_framework" class="form-control" required>
                    <option value="">Select Framework</option>
                    <option value="CMMC">CMMC</option>
                    <option value="NIST800171">NIST 800-171</option>
                    <option value="NIST80053">NIST 800-53</option>
                    <option value="ISO27001">ISO 27001</option>
                </select>
            </div>

            <div class="form-group">
                <label for="evidence_type">Evidence Type *</label>
                <select id="evidence_type" name="evidence_type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="policy">Policy Document</option>
                    <option value="procedure">Procedure</option>
                    <option value="screenshot">Screenshot</option>
                    <option value="certificate">Certificate</option>
                    <option value="log">System Log</option>
                    <option value="report">Audit Report</option>
                    <option value="configuration">Configuration File</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" class="form-control" required rows="3" placeholder="Describe what this evidence demonstrates..."></textarea>
            </div>

            <div class="form-group">
                <label for="evidence_file">File * (Max 10MB)</label>
                <input type="file" id="evidence_file" name="evidence_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.gif,.docx,.xlsx,.txt">
                <small class="form-text">Supported: PDF, images, Word, Excel, text files</small>
            </div>

            <div class="form-group">
                <label for="expiration_date">Expiration Date (Optional)</label>
                <input type="date" id="expiration_date" name="expiration_date" class="form-control">
                <small class="form-text">When this evidence expires or needs renewal</small>
            </div>

            <div class="form-group">
                <label for="notes">Notes (Optional)</label>
                <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="upload-btn">Upload</button>
                <button type="button" class="btn btn-secondary" onclick="closeUploadModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 0;
    border-radius: 8px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
}

.close-btn {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #666;
    line-height: 1;
    padding: 0;
    width: 30px;
    height: 30px;
}

.close-btn:hover {
    color: #000;
}

.modal-content form {
    padding: 20px;
}

.validity-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.validity-valid {
    background: #d4edda;
    color: #155724;
}

.validity-expiring_soon {
    background: #fff3cd;
    color: #856404;
}

.validity-expired {
    background: #f8d7da;
    color: #721c24;
}

.stat-card.success {
    border-left: 4px solid #28a745;
}

.stat-card.danger {
    border-left: 4px solid #dc3545;
}
</style>

<script>
function showUploadModal() {
    document.getElementById('upload-modal').style.display = 'flex';
}

function closeUploadModal() {
    document.getElementById('upload-modal').style.display = 'none';
    document.getElementById('upload-form').reset();
}

function uploadEvidence(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const uploadBtn = document.getElementById('upload-btn');

    uploadBtn.disabled = true;
    uploadBtn.textContent = 'Uploading...';

    fetch('<?= $url('evidence/upload') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Evidence uploaded successfully');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Network error: ' + error);
    })
    .finally(() => {
        uploadBtn.disabled = false;
        uploadBtn.textContent = 'Upload';
    });
}

function approveEvidence(id) {
    if (!confirm('Approve this evidence?')) return;

    const formData = new FormData();
    formData.append('_csrf_token', '<?= $csrfToken() ?>');
    formData.append('id', id);

    fetch('<?= $url('evidence/approve') ?>', {
        method: 'POST',
        body: formData
    })
    .then(() => window.location.reload())
    .catch(error => alert('Error: ' + error));
}

function rejectEvidence(id) {
    const reason = prompt('Enter rejection reason:');
    if (!reason) return;

    const formData = new FormData();
    formData.append('_csrf_token', '<?= $csrfToken() ?>');
    formData.append('id', id);
    formData.append('rejection_reason', reason);

    fetch('<?= $url('evidence/reject') ?>', {
        method: 'POST',
        body: formData
    })
    .then(() => window.location.reload())
    .catch(error => alert('Error: ' + error));
}

function deleteEvidence(id) {
    if (!confirm('Delete this evidence? This cannot be undone.')) return;

    const formData = new FormData();
    formData.append('_csrf_token', '<?= $csrfToken() ?>');
    formData.append('id', id);

    fetch('<?= $url('evidence/delete') ?>', {
        method: 'POST',
        body: formData
    })
    .then(() => window.location.reload())
    .catch(error => alert('Error: ' + error));
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const validityFilter = document.getElementById('validity-filter');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const validityValue = validityFilter.value;

        document.querySelectorAll('.evidence-row').forEach(row => {
            const searchMatch = !searchTerm || row.dataset.search.includes(searchTerm);
            const statusMatch = !statusValue || row.dataset.status === statusValue;
            const validityMatch = !validityValue || row.dataset.validity === validityValue;

            row.style.display = (searchMatch && statusMatch && validityMatch) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    validityFilter.addEventListener('change', filterTable);
});
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
