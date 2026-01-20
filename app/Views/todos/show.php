<?php
$page_title = 'Edit To-Do Item';
$current_page = 'todos';

ob_start();
?>

<div class="page-header">
    <h1>Edit To-Do Item</h1>
    <div class="page-actions">
        <a href="<?= $url('todos') ?>" class="btn btn-secondary">Back to List</a>
        <form method="POST" action="<?= $url('todos/' . $item['id'] . '/delete') ?>"
              style="display: inline;"
              onsubmit="return confirm('Are you sure you want to delete this to-do item?');">
            <?= $csrf() ?>
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $url('todos/' . $item['id']) ?>">
            <?= $csrf() ?>

            <div class="form-group">
                <label for="customer_id">Client *</label>
                <select name="customer_id" id="customer_id" class="form-control" required>
                    <option value="">-- Select Client --</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['id'] ?>"
                            <?= ($item['customer_id'] == $customer['id']) ? 'selected' : '' ?>>
                            <?= $e($customer['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($error('customer_id')): ?>
                    <span class="error-message"><?= $error('customer_id') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" name="title" id="title" class="form-control"
                       value="<?= $e($item['title']) ?>" required maxlength="255">
                <?php if ($error('title')): ?>
                    <span class="error-message"><?= $error('title') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"
                          rows="4"><?= $e($item['description']) ?></textarea>
                <?php if ($error('description')): ?>
                    <span class="error-message"><?= $error('description') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="pending" <?= $item['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= $item['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="completed" <?= $item['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="low" <?= $item['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= $item['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= $item['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date" class="form-control"
                           value="<?= $e($item['due_date']) ?>">
                </div>

                <?php if ($item['completed_at']): ?>
                    <div class="form-group">
                        <label>Completed At</label>
                        <input type="text" class="form-control"
                               value="<?= date('M d, Y g:i A', strtotime($item['completed_at'])) ?>" readonly>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update To-Do Item</button>
                <a href="<?= $url('todos') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h3>Details</h3>
    </div>
    <div class="card-body">
        <div class="detail-row">
            <strong>Created:</strong>
            <?= date('M d, Y g:i A', strtotime($item['created_at'])) ?>
        </div>
        <div class="detail-row">
            <strong>Last Updated:</strong>
            <?= date('M d, Y g:i A', strtotime($item['updated_at'])) ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
?>
