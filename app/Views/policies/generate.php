<?php
$page_title = 'Generate from Template: ' . $policy['title'];
$current_page = 'policies';

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('policies') ?>">Policy Templates</a> / Generate from Template
    </div>
    <h1>Generate from Template</h1>
    <p class="page-subtitle"><?= $e($policy['title']) ?></p>
</div>

<div class="two-column-layout">
    <div class="main-column">
        <div class="card">
            <div class="card-header">
                <h3>Select Clients</h3>
            </div>
            <div class="card-body">
                <form id="generate-form">
                    <?= $csrf() ?>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="select-all-clients" onchange="toggleAllClients(this)">
                            Select All Clients
                        </label>
                    </div>

                    <div class="client-list" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                        <?php foreach ($clients as $client): ?>
                            <div style="margin-bottom: 10px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" name="client_ids[]" value="<?= $client['id'] ?>" class="client-checkbox" style="margin-right: 10px;">
                                    <span style="font-weight: 500;"><?= $e($client['name']) ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="save_as_document" value="1" id="save-as-document">
                            Save as Document (will create a document for each client)
                        </label>
                        <small class="form-text">
                            If unchecked, documents will be generated but not saved (preview mode)
                        </small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="generate-btn">
                            Generate Documents
                        </button>
                        <a href="<?= $url('policies/' . $policy['id']) ?>" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="sidebar-column">
        <div class="card">
            <div class="card-header">
                <h3>Available Variables</h3>
            </div>
            <div class="card-body" style="font-size: 13px;">
                <p style="margin-bottom: 15px;">
                    This template will automatically replace variables with actual client data when generated.
                </p>

                <?php foreach ($available_vars as $category => $vars): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 10px; color: var(--primary);">
                            <?= $e($category) ?>
                        </h4>
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 4px;">
                            <?php foreach ($vars as $var => $description): ?>
                                <div style="margin-bottom: 8px;">
                                    <code style="color: #e83e8c; background: white; padding: 2px 6px; border-radius: 3px;">
                                        <?= $e($var) ?>
                                    </code>
                                    <br>
                                    <small style="color: #666; margin-left: 4px;"><?= $e($description) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="alert alert-info" style="font-size: 12px; padding: 10px; margin-top: 15px;">
                    <strong>Tip:</strong> Variables in the template content will be replaced with actual values when you generate the document.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.two-column-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 20px;
    margin-top: 20px;
}

.main-column {
    min-width: 0; /* Prevents grid blowout */
}

.sidebar-column {
    position: sticky;
    top: 20px;
    height: fit-content;
}

@media (max-width: 1024px) {
    .two-column-layout {
        grid-template-columns: 1fr;
    }

    .sidebar-column {
        position: static;
    }
}

.client-list label:hover {
    background: #f8f9fa;
    padding: 5px;
    margin: -5px;
    border-radius: 4px;
}
</style>

<script>
function toggleAllClients(checkbox) {
    document.querySelectorAll('.client-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

document.getElementById('generate-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const selectedClients = formData.getAll('client_ids[]');

    if (selectedClients.length === 0) {
        alert('Please select at least one client');
        return;
    }

    const saveAsDoc = document.getElementById('save-as-document').checked;
    const action = saveAsDoc ? 'generate and save' : 'preview';

    if (!confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} documents for ${selectedClients.length} client(s)?`)) {
        return;
    }

    const btn = document.getElementById('generate-btn');
    btn.disabled = true;
    btn.textContent = 'Generating...';

    fetch('<?= $url('policies/' . $policy['id'] . '/generate') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            if (document.getElementById('save-as-document').checked) {
                // Redirect to policies after saving
                window.location.href = '<?= $url('policies/' . $policy['id']) ?>';
            } else {
                // Reset form for preview mode
                this.reset();
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Network error: ' + error);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Generate Documents';
    });
});
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
