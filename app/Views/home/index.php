<?php
$page_title = 'Dashboard';
$current_page = 'dashboard';

ob_start();
?>

<div class="page-header">
    <h1>Welcome to <?= $e($app_name ?? 'Layer3 | Trident Cyber OneComply') ?></h1>
    <?php if ($current_client): ?>
        <p class="page-subtitle">Currently viewing: <strong><?= $e($current_client) ?></strong>
            <a href="<?= $url('clients') ?>" style="margin-left: 10px;">(Change Client)</a>
        </p>
    <?php else: ?>
        <p class="page-subtitle">Please <a href="<?= $url('clients') ?>">select a client</a> to view compliance data</p>
    <?php endif; ?>
</div>

<?php if ($current_client && !empty($recent_activity)): ?>
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <h3>Quick Stats for <?= $e($current_client) ?></h3>
        <a href="<?= $url('dashboard') ?>" class="btn btn-sm btn-primary">View Full Compliance Dashboard</a>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Open POA&M Items</div>
            <div class="stat-value text-warning"><?= $recent_activity['open_poam_count'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Documents</div>
            <div class="stat-value"><?= $recent_activity['total_documents'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Recent Assessments</div>
            <div class="stat-value"><?= count($recent_activity['recent_assessments']) ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Quick Access Links</h3>
        <?php if (\App\Middleware\AuthMiddleware::checkPermission('admin')): ?>
            <a href="<?= $url('home/links') ?>" class="btn btn-sm btn-secondary">Manage Links</a>
        <?php endif; ?>
    </div>

    <?php if (empty($quick_links)): ?>
        <div class="alert alert-info" style="margin: 20px;">
            <span class="alert-icon">ℹ️</span>
            No quick links configured yet.
            <?php if (\App\Middleware\AuthMiddleware::checkPermission('admin')): ?>
                <a href="<?= $url('home/links') ?>">Add your first link</a>.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px;">
            <?php foreach ($quick_links as $link): ?>
                <a href="<?= $e($link['url']) ?>" target="_blank" rel="noopener noreferrer"
                   class="quick-link-card"
                   style="text-decoration: none; color: inherit; border: 1px solid #ddd; border-radius: 8px; padding: 20px; transition: all 0.2s; display: block;">
                    <div style="font-size: 48px; margin-bottom: 10px;"><?= $e($link['icon'] ?? '🔗') ?></div>
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px; color: var(--primary, #667eea);">
                        <?= $e($link['title']) ?>
                    </div>
                    <?php if ($link['description']): ?>
                        <div style="font-size: 14px; color: #666;">
                            <?= $e($link['description']) ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.quick-link-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
    border-color: var(--primary, #667eea) !important;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    padding: 20px;
}

.stat-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #333;
}

.stat-value.text-warning {
    color: #f39c12;
}
</style>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Compliance Tools</h3>
    </div>
    <div style="padding: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <a href="<?= $url('clients') ?>" class="btn btn-primary" style="padding: 15px; text-align: center;">
                🏢 Clients
            </a>
            <a href="<?= $url('assessments') ?>" class="btn btn-primary" style="padding: 15px; text-align: center;">
                ✅ Assessments
            </a>
            <a href="<?= $url('poam') ?>" class="btn btn-primary" style="padding: 15px; text-align: center;">
                📝 POA&M
            </a>
            <a href="<?= $url('controls') ?>" class="btn btn-primary" style="padding: 15px; text-align: center;">
                📋 Controls
            </a>
            <a href="<?= $url('documents') ?>" class="btn btn-primary" style="padding: 15px; text-align: center;">
                📁 Documents
            </a>
            <a href="<?= $url('reports') ?>" class="btn btn-primary" style="padding: 15px; text-align: center;">
                📄 Reports
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
