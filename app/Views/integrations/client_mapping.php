<?php
$page_title = 'Client Mapping';
$current_page = 'integrations';

ob_start();
?>

<div class="page-header">
    <h1>Client Mapping</h1>
    <p class="page-subtitle">Map external clients from Autotask and ITGlue to OneComply</p>
    <div class="page-actions">
        <a href="<?= $url('integrations') ?>" class="btn btn-secondary">Back to Integrations</a>
    </div>
</div>

<?php if (!$autotask_enabled && !$itglue_enabled): ?>
    <div class="alert alert-warning">
        <span class="alert-icon">⚠️</span>
        No integrations are enabled. Please configure <a href="<?= $url('integrations') ?>">Autotask or ITGlue</a> first.
    </div>
<?php else: ?>
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3 style="margin: 0;">Instructions</h3>
        </div>
        <div class="card-body">
            <ol style="margin: 0; padding-left: 20px;">
                <li><strong>Fetch Clients:</strong> Click "Fetch Clients" to retrieve all clients from your connected integrations.</li>
                <li><strong>Review Mappings:</strong> For each external client, choose an action:
                    <ul style="margin-top: 8px;">
                        <li><strong>Ignore:</strong> Skip this client entirely</li>
                        <li><strong>Map to Existing:</strong> Link to an existing OneComply client</li>
                        <li><strong>Create New:</strong> Import as a new OneComply client</li>
                    </ul>
                </li>
                <li><strong>Save Mappings:</strong> Click "Save Mappings" to store your choices.</li>
                <li><strong>Sync:</strong> Click "Sync Now" to execute the import based on your saved mappings.</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">External Clients</h3>
            <div style="display: flex; gap: 10px;">
                <form action="<?= $url('integrations/fetch-clients') ?>" method="POST" style="display: inline;">
                    <?= $csrf() ?>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('This will fetch all clients from Autotask and ITGlue. Continue?');">
                        Fetch Clients
                    </button>
                </form>
                <button type="button" class="btn btn-success" id="sync-btn" onclick="syncMappedClients()">
                    Sync Now
                </button>
            </div>
        </div>

        <?php if (empty($mappings)): ?>
            <div class="card-body">
                <p style="text-align: center; color: #666; margin: 40px 0;">
                    No clients found. Click "Fetch Clients" to retrieve clients from your integrations.
                </p>
            </div>
        <?php else: ?>
            <form action="<?= $url('integrations/save-mappings') ?>" method="POST" id="mapping-form">
                <?= $csrf() ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Source</th>
                            <th>External Client Name</th>
                            <th style="width: 200px;">Action</th>
                            <th>Map to OneComply Client</th>
                            <th style="width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mappings as $mapping): ?>
                        <tr>
                            <td>
                                <?php if ($mapping['provider'] === 'autotask'): ?>
                                    <span class="badge" style="background: #FF6B35; color: white;">Autotask</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #004E89; color: white;">ITGlue</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= $e($mapping['external_name']) ?></strong>
                                <div style="font-size: 0.85em; color: #666;">
                                    ID: <?= $e($mapping['external_id']) ?>
                                </div>
                            </td>
                            <td>
                                <select name="mapping_action[<?= $mapping['id'] ?>]" class="form-control" onchange="toggleClientSelect(<?= $mapping['id'] ?>)">
                                    <option value="ignore" <?= $mapping['mapping_action'] === 'ignore' ? 'selected' : '' ?>>Ignore</option>
                                    <option value="map_existing" <?= $mapping['mapping_action'] === 'map_existing' ? 'selected' : '' ?>>Map to Existing</option>
                                    <option value="create_new" <?= $mapping['mapping_action'] === 'create_new' ? 'selected' : '' ?>>Create New</option>
                                </select>
                            </td>
                            <td>
                                <select name="onecomply_client_id[<?= $mapping['id'] ?>]" class="form-control client-select" id="client-select-<?= $mapping['id'] ?>" <?= $mapping['mapping_action'] !== 'map_existing' ? 'disabled' : '' ?>>
                                    <option value="">-- Select Client --</option>
                                    <?php foreach ($onecomply_clients as $client): ?>
                                        <option value="<?= $client['id'] ?>" <?= $mapping['onecomply_client_id'] == $client['id'] ? 'selected' : '' ?>>
                                            <?= $e($client['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <?php if ($mapping['synced']): ?>
                                    <span class="badge badge-success">Synced</span>
                                    <div style="font-size: 0.75em; color: #666;">
                                        <?= date('M d, Y', strtotime($mapping['synced_at'])) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="card-footer" style="text-align: right; padding: 15px; border-top: 1px solid #e0e0e0;">
                    <button type="submit" class="btn btn-primary">Save Mappings</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Hidden CSRF token for AJAX requests -->
<input type="hidden" id="csrf-token" value="<?= $csrfToken() ?>">

<script>
function toggleClientSelect(mappingId) {
    const actionSelect = document.querySelector(`select[name="mapping_action[${mappingId}]"]`);
    const clientSelect = document.getElementById(`client-select-${mappingId}`);

    if (actionSelect.value === 'map_existing') {
        clientSelect.disabled = false;
        clientSelect.required = true;
    } else {
        clientSelect.disabled = true;
        clientSelect.required = false;
    }
}

function syncMappedClients() {
    if (!confirm('This will create new clients and update existing clients based on your saved mappings. Continue?')) {
        return;
    }

    const btn = document.getElementById('sync-btn');
    const csrfToken = document.getElementById('csrf-token').value;

    btn.disabled = true;
    btn.textContent = 'Syncing...';

    fetch('<?= $url('integrations/sync-mapped-clients') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '_csrf_token': csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.textContent = 'Sync Now';
        }
    })
    .catch(error => {
        alert('Network error: ' + error);
        btn.disabled = false;
        btn.textContent = 'Sync Now';
    });
}
</script>

<style>
.card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e0e0e0;
}

.card-body {
    padding: 20px;
}

.card-footer {
    background: #f8f9fa;
}

.data-table select.form-control {
    width: 100%;
    min-width: 150px;
}

.data-table td {
    vertical-align: middle;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
}

.alert-icon {
    font-size: 1.2em;
}
</style>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
