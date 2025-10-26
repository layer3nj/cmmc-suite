<?php
$page_title = 'Dashboard';
$current_page = 'dashboard';

ob_start();
?>

<div class="dashboard">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p class="page-subtitle">Compliance Overview</p>
    </div>

    <?php if (!$current_customer): ?>
        <div class="alert alert-info">
            <span class="alert-icon">ℹ️</span>
            Please <a href="<?= $url('customers') ?>">select a customer</a> to view compliance metrics.
        </div>
    <?php else: ?>

    <!-- Metrics Cards -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon sprs-icon">📊</div>
            <div class="metric-content">
                <div class="metric-label">Current SPRS Score</div>
                <div class="metric-value <?= $metrics['current_sprs'] >= 0 ? 'text-success' : 'text-danger' ?>">
                    <?= $metrics['current_sprs'] ?>
                </div>
                <div class="metric-sublabel">out of 110 points</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">🎯</div>
            <div class="metric-content">
                <div class="metric-label">Projected SPRS Score</div>
                <div class="metric-value text-info">
                    <?= $metrics['projected_sprs'] ?>
                </div>
                <div class="metric-sublabel">with remediation</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">📋</div>
            <div class="metric-content">
                <div class="metric-label">Open POA&M Items</div>
                <div class="metric-value <?= $metrics['open_poam'] > 0 ? 'text-warning' : 'text-success' ?>">
                    <?= $metrics['open_poam'] ?>
                </div>
                <div class="metric-sublabel">action items pending</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">✓</div>
            <div class="metric-content">
                <div class="metric-label">Total Controls</div>
                <div class="metric-value"><?= $metrics['total_controls'] ?></div>
                <div class="metric-sublabel">CMMC controls</div>
            </div>
        </div>
    </div>

    <!-- ML Level Progress -->
    <div class="card">
        <div class="card-header">
            <h2>CMMC Maturity Level Progress</h2>
        </div>
        <div class="card-body">
            <div class="progress-list">
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label">ML1 - Foundational</span>
                        <span class="progress-value"><?= $metrics['ml1_percent'] ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $metrics['ml1_percent'] ?>%"></div>
                    </div>
                </div>

                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label">ML2 - Advanced</span>
                        <span class="progress-value"><?= $metrics['ml2_percent'] ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $metrics['ml2_percent'] ?>%"></div>
                    </div>
                </div>

                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label">ML3 - Expert</span>
                        <span class="progress-value"><?= $metrics['ml3_percent'] ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $metrics['ml3_percent'] ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Recent Assessments -->
        <div class="card">
            <div class="card-header">
                <h2>Recent Assessments</h2>
                <a href="<?= $url('assessments') ?>" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_assessments)): ?>
                    <p class="text-muted">No assessments yet. <a href="<?= $url('assessments/create') ?>">Create one</a></p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Assessor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_assessments as $assessment): ?>
                            <tr>
                                <td>
                                    <a href="<?= $url('assessments/' . $assessment['id']) ?>">
                                        <?= date('M j, Y', strtotime($assessment['created_at'])) ?>
                                    </a>
                                </td>
                                <td><?= $e($assessment['assessor_name']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $assessment['status'] === 'published' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($assessment['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Milestones -->
        <div class="card">
            <div class="card-header">
                <h2>Upcoming POA&M Milestones</h2>
                <a href="<?= $url('poam') ?>" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_milestones)): ?>
                    <p class="text-muted">No upcoming milestones.</p>
                <?php else: ?>
                    <div class="milestone-list">
                        <?php foreach ($upcoming_milestones as $milestone): ?>
                        <div class="milestone-item">
                            <div class="milestone-date">
                                <?= date('M j', strtotime($milestone['planned_completion_date'])) ?>
                            </div>
                            <div class="milestone-content">
                                <a href="<?= $url('poam/' . $milestone['id']) ?>" class="milestone-title">
                                    <?= $e($milestone['title']) ?>
                                </a>
                                <div class="milestone-meta">
                                    <?php if ($milestone['control_code']): ?>
                                        <span class="badge"><?= $e($milestone['control_code']) ?></span>
                                    <?php endif; ?>
                                    <span class="badge badge-<?= $milestone['status'] === 'in_progress' ? 'info' : 'default' ?>">
                                        <?= ucfirst(str_replace('_', ' ', $milestone['status'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
?>
