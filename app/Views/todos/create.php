<?php
$page_title = 'Create To-Do Item';
$current_page = 'todos';

ob_start();
?>

<div class="page-header">
    <h1>Create To-Do Item</h1>
    <div class="page-actions">
        <a href="<?= $url('todos') ?>" class="btn btn-secondary">Back to List</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $url('todos') ?>">
            <?= $csrf() ?>

            <div class="form-group">
                <label for="customer_id">Client *</label>
                <select name="customer_id" id="customer_id" class="form-control" required>
                    <option value="">-- Select Client --</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['id'] ?>" <?= old('customer_id') == $customer['id'] ? 'selected' : '' ?>>
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
                       value="<?= $e(old('title')) ?>" required maxlength="255">
                <?php if ($error('title')): ?>
                    <span class="error-message"><?= $error('title') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"
                          rows="4"><?= $e(old('description')) ?></textarea>
                <?php if ($error('description')): ?>
                    <span class="error-message"><?= $error('description') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="low" <?= old('priority') === 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= old('priority') === 'medium' || !old('priority') ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= old('priority') === 'high' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date" class="form-control"
                           value="<?= $e(old('due_date')) ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create To-Do Item</button>
                <a href="<?= $url('todos') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
?>
