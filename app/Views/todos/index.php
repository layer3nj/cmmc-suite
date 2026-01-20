<?php
$page_title = 'To-Do List';
$current_page = 'todos';

ob_start();
?>

<div class="page-header">
    <h1>Client To-Do List</h1>
    <p class="page-subtitle">Manage tasks and action items by client</p>
    <div class="page-actions">
        <a href="<?= $url('todos/create') ?>" class="btn btn-primary">Add To-Do Item</a>
    </div>
</div>

<!-- Statistics -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon">📋</div>
        <div class="metric-content">
            <div class="metric-value"><?= $stats['total'] ?></div>
            <div class="metric-label">Total Items</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon">⏳</div>
        <div class="metric-content">
            <div class="metric-value"><?= $stats['pending'] ?></div>
            <div class="metric-label">Pending</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon">🔄</div>
        <div class="metric-content">
            <div class="metric-value"><?= $stats['in_progress'] ?></div>
            <div class="metric-label">In Progress</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon">✅</div>
        <div class="metric-content">
            <div class="metric-value"><?= $stats['completed'] ?></div>
            <div class="metric-label">Completed</div>
        </div>
    </div>
</div>

<!-- Grid View of Todos by Client -->
<?php if (empty($todosByCustomer)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No clients found. Please add clients first.
    </div>
<?php else: ?>
    <div class="todos-grid">
        <?php foreach ($todosByCustomer as $customerData): ?>
            <?php
            $customer = $customerData['customer'];
            $todos = $customerData['todos'];
            $customerTodoCount = count($todos);
            $completedCount = count(array_filter($todos, fn($t) => $t['status'] === 'completed'));
            ?>

            <div class="client-todo-card">
                <div class="client-todo-header">
                    <h3 class="client-name"><?= $e($customer['name']) ?></h3>
                    <div class="client-stats">
                        <span class="todo-count"><?= $customerTodoCount ?> tasks</span>
                        <?php if ($customerTodoCount > 0): ?>
                            <span class="completion-badge">
                                <?= $completedCount ?>/<?= $customerTodoCount ?> done
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="client-todo-body">
                    <?php if (empty($todos)): ?>
                        <div class="empty-todos">
                            <p>No tasks for this client</p>
                        </div>
                    <?php else: ?>
                        <div class="todo-list">
                            <?php foreach ($todos as $todo): ?>
                                <div class="todo-item <?= $todo['status'] === 'completed' ? 'completed' : '' ?>">
                                    <div class="todo-header">
                                        <div class="todo-title-row">
                                            <span class="priority-indicator priority-<?= $e($todo['priority']) ?>"
                                                  title="<?= ucfirst($e($todo['priority'])) ?> priority"></span>
                                            <h4 class="todo-title"><?= $e($todo['title']) ?></h4>
                                        </div>
                                        <div class="todo-status">
                                            <?php if ($todo['status'] === 'completed'): ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php elseif ($todo['status'] === 'in_progress'): ?>
                                                <span class="badge badge-primary">In Progress</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($todo['description'])): ?>
                                        <p class="todo-description"><?= $e(substr($todo['description'], 0, 100)) ?><?= strlen($todo['description']) > 100 ? '...' : '' ?></p>
                                    <?php endif; ?>

                                    <div class="todo-footer">
                                        <?php if ($todo['due_date']): ?>
                                            <?php
                                            $dueDate = strtotime($todo['due_date']);
                                            $today = strtotime('today');
                                            $isOverdue = $dueDate < $today && $todo['status'] !== 'completed';
                                            $isDueSoon = $dueDate <= strtotime('+3 days') && $dueDate >= $today && $todo['status'] !== 'completed';
                                            ?>
                                            <span class="todo-due-date <?= $isOverdue ? 'overdue' : ($isDueSoon ? 'due-soon' : '') ?>">
                                                📅 Due: <?= date('M d, Y', $dueDate) ?>
                                            </span>
                                        <?php endif; ?>

                                        <div class="todo-actions">
                                            <a href="<?= $url('todos/' . $todo['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>

                                            <?php if ($todo['status'] !== 'completed'): ?>
                                                <form method="POST" action="<?= $url('todos/' . $todo['id'] . '/status') ?>"
                                                      style="display: inline;" class="status-form">
                                                    <?= $csrf() ?>
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Mark this task as completed?')">
                                                        ✓ Complete
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
?>
