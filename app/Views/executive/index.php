<?php
$page_title = 'Executive Dashboard';
$current_page = 'executive';

ob_start();
?>

<div class="page-header">
    <h1>Executive Compliance Dashboard</h1>
    <p class="page-subtitle">Multi-client compliance overview and analytics</p>
</div>

<!-- Overall Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Clients</div>
        <div class="stat-value"><?= $overall_stats['total_clients'] ?></div>
    </div>
    <div class="stat-card success">
        <div class="stat-label">Compliant Clients</div>
        <div class="stat-value"><?= $overall_stats['compliant_clients'] ?></div>
        <div class="stat-subtitle">
            <?php
            $percentage = $overall_stats['total_clients'] > 0
                ? round(($overall_stats['compliant_clients'] / $overall_stats['total_clients']) * 100)
                : 0;
            echo $percentage . '%';
            ?>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Assessments</div>
        <div class="stat-value"><?= $overall_stats['total_assessments'] ?></div>
    </div>
    <div class="stat-card <?= $overall_stats['overdue_poam_items'] > 0 ? 'danger' : '' ?>">
        <div class="stat-label">Overdue POA&M Items</div>
        <div class="stat-value"><?= $overall_stats['overdue_poam_items'] ?></div>
        <div class="stat-subtitle"><?= $overall_stats['open_poam_items'] ?> total open</div>
    </div>
</div>

<!-- At-Risk Clients Alert -->
<?php if (!empty($at_risk_clients)): ?>
<div class="alert alert-warning" style="margin-top: 20px;">
    <strong>⚠️ Attention Required:</strong> <?= count($at_risk_clients) ?> client(s) need immediate attention
</div>
<?php endif; ?>

<!-- Main Grid -->
<div class="dashboard-grid" style="margin-top: 20px;">
    <!-- Clients Table -->
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header">
            <h3>Client Compliance Status</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Latest Assessment</th>
                        <th>Days Since</th>
                        <th>Open POA&M</th>
                        <th>Overdue POA&M</th>
                        <th>Documents</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                    <tr>
                        <td>
                            <a href="<?= $url('clients/' . $client['id']) ?>">
                                <strong><?= $e($client['name']) ?></strong>
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-<?= $client['status_class'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $client['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($client['latest_assessment']): ?>
                                <?= $e($client['latest_assessment']['framework']) ?>
                                <?= $client['latest_assessment']['target_level'] ? ' ML' . $client['latest_assessment']['target_level'] : '' ?>
                            <?php else: ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $client['days_since_assessment'] !== null ? $client['days_since_assessment'] . ' days' : '-' ?>
                        </td>
                        <td class="text-center"><?= $client['open_poam'] ?></td>
                        <td class="text-center">
                            <span class="<?= $client['overdue_poam'] > 0 ? 'text-danger font-weight-bold' : '' ?>">
                                <?= $client['overdue_poam'] ?>
                            </span>
                        </td>
                        <td class="text-center"><?= $client['document_count'] ?></td>
                        <td>
                            <a href="<?= $url('clients/' . $client['id']) ?>" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Framework Stats -->
    <div class="card">
        <div class="card-header">
            <h3>Compliance by Framework</h3>
        </div>
        <div class="card-body">
            <?php foreach ($framework_stats as $framework): ?>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <strong><?= $e($framework['framework']) ?></strong>
                        <span><?= $framework['client_count'] ?> clients</span>
                    </div>
                    <div class="progress-bar">
                        <?php
                        $percentage = $framework['total_assessments'] > 0
                            ? round(($framework['published_count'] / $framework['total_assessments']) * 100)
                            : 0;
                        ?>
                        <div class="progress-fill" style="width: <?= $percentage ?>%;"></div>
                    </div>
                    <small class="text-muted"><?= $framework['published_count'] ?> of <?= $framework['total_assessments'] ?> assessments published</small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Activity</h3>
        </div>
        <div class="card-body">
            <?php foreach ($recent_activity as $activity): ?>
                <div class="activity-item">
                    <span class="activity-icon"><?= $activity['icon'] ?></span>
                    <div class="activity-content">
                        <div><?= $e($activity['description']) ?></div>
                        <small class="text-muted"><?= date('M d, Y', strtotime($activity['date'])) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

.progress-bar {
    background: #e0e0e0;
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 5px;
}

.progress-fill {
    background: var(--primary);
    height: 100%;
    transition: width 0.3s ease;
}

.activity-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    font-size: 20px;
}

.activity-content {
    flex: 1;
}

.stat-card.success {
    border-left: 4px solid #28a745;
}

.stat-card.danger {
    border-left: 4px solid #dc3545;
}

.text-danger {
    color: #dc3545;
}

.font-weight-bold {
    font-weight: 600;
}
</style>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
