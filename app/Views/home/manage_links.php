<?php
$page_title = 'Manage Quick Links';
$current_page = 'dashboard';

ob_start();
?>

<div class="page-header">
    <h1>Manage Quick Links</h1>
    <p class="page-subtitle">Add, edit, or remove quick access links on the dashboard</p>
    <div class="page-actions">
        <a href="<?= $url('/') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<!-- Add New Link Form -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <h3>Add New Quick Link</h3>
    </div>
    <form action="<?= $url('home/links/create') ?>" method="POST" style="padding: 20px;">
        <?= $csrf() ?>

        <div class="form-row">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" required class="form-control" placeholder="e.g., DattoRMM">
            </div>
            <div class="form-group">
                <label>Icon</label>
                <input type="text" name="icon" class="form-control" placeholder="🔗" maxlength="10">
                <small>Enter an emoji or leave blank for default</small>
            </div>
        </div>

        <div class="form-group">
            <label>URL *</label>
            <input type="url" name="url" required class="form-control" placeholder="https://example.com">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="2" class="form-control" placeholder="Brief description of this link"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Display Order</label>
                <input type="number" name="display_order" value="0" class="form-control" min="0">
                <small>Lower numbers appear first</small>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked>
                    Active
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Link</button>
        </div>
    </form>
</div>

<!-- Existing Links -->
<div class="card">
    <div class="card-header">
        <h3>Existing Quick Links</h3>
        <?php if (!empty($links)): ?>
        <button onclick="deleteSelected()" class="btn btn-sm btn-danger" id="delete-selected-btn" style="display: none;">
            Delete Selected
        </button>
        <?php endif; ?>
    </div>

    <?php if (empty($links)): ?>
        <div class="alert alert-info" style="margin: 20px;">
            <span class="alert-icon">ℹ️</span>
            No quick links configured yet. Add one using the form above.
        </div>
    <?php else: ?>
        <form id="bulk-delete-form" action="<?= $url('home/links/delete-multiple') ?>" method="POST">
            <?= $csrf() ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                        </th>
                        <th>Order</th>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($links as $link): ?>
                    <tr id="link-<?= $link['id'] ?>">
                        <td>
                            <input type="checkbox" name="link_ids[]" value="<?= $link['id'] ?>" class="link-checkbox" onchange="updateDeleteButton()">
                        </td>
                        <td><?= $e($link['display_order']) ?></td>
                        <td style="font-size: 24px;"><?= $e($link['icon'] ?? '🔗') ?></td>
                        <td><strong><?= $e($link['title']) ?></strong></td>
                        <td>
                            <a href="<?= $e($link['url']) ?>" target="_blank" rel="noopener noreferrer" style="font-size: 12px;">
                                <?= $e(substr($link['url'], 0, 50)) ?><?= strlen($link['url']) > 50 ? '...' : '' ?>
                            </a>
                        </td>
                        <td><?= $e(substr($link['description'] ?? '', 0, 60)) ?><?= strlen($link['description'] ?? '') > 60 ? '...' : '' ?></td>
                        <td>
                            <?= $link['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>' ?>
                        </td>
                        <td>
                            <button type="button" onclick="editLink(<?= $link['id'] ?>)" class="btn btn-sm btn-secondary">Edit</button>
                            <form action="<?= $url('home/links/' . $link['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this link?');">
                                <?= $csrf() ?>
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr id="edit-<?= $link['id'] ?>" style="display: none;">
                        <td colspan="8" style="background: #f8f9fa; padding: 20px;">
                            <form action="<?= $url('home/links/' . $link['id'] . '/update') ?>" method="POST">
                                <?= $csrf() ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Title *</label>
                                        <input type="text" name="title" value="<?= $e($link['title']) ?>" required class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Icon</label>
                                        <input type="text" name="icon" value="<?= $e($link['icon'] ?? '') ?>" class="form-control" maxlength="10">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>URL *</label>
                                    <input type="url" name="url" value="<?= $e($link['url']) ?>" required class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" rows="2" class="form-control"><?= $e($link['description'] ?? '') ?></textarea>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Display Order</label>
                                        <input type="number" name="display_order" value="<?= $e($link['display_order']) ?>" class="form-control" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="is_active" value="1" <?= $link['is_active'] ? 'checked' : '' ?>>
                                            Active
                                        </label>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" onclick="cancelEdit(<?= $link['id'] ?>)" class="btn btn-secondary">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Link</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php endif; ?>
</div>

<script>
function editLink(id) {
    document.getElementById('link-' + id).style.display = 'none';
    document.getElementById('edit-' + id).style.display = 'table-row';
}

function cancelEdit(id) {
    document.getElementById('link-' + id).style.display = 'table-row';
    document.getElementById('edit-' + id).style.display = 'none';
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.link-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    updateDeleteButton();
}

function updateDeleteButton() {
    const checkboxes = document.querySelectorAll('.link-checkbox:checked');
    const deleteBtn = document.getElementById('delete-selected-btn');

    if (checkboxes.length > 0) {
        deleteBtn.style.display = 'inline-block';
        deleteBtn.textContent = `Delete Selected (${checkboxes.length})`;
    } else {
        deleteBtn.style.display = 'none';
    }
}

function deleteSelected() {
    const checkboxes = document.querySelectorAll('.link-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one link to delete.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${checkboxes.length} selected link(s)? This action cannot be undone.`)) {
        document.getElementById('bulk-delete-form').submit();
    }
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
