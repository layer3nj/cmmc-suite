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

    <?php if (empty($client_frameworks)): ?>
        <div class="alert alert-warning">
            <span class="alert-icon">⚠️</span>
            No frameworks assigned to this client. <a href="<?= $url('clients/' . $current_customer['id'] . '/edit') ?>">Assign frameworks</a> to view compliance metrics.
        </div>
    <?php else: ?>

    <!-- Framework Metrics -->
    <?php foreach ($metrics['frameworks'] as $framework => $frameworkMetrics): ?>
        <?php
        $frameworkName = str_replace('-', ' ', $framework);
        $isPrimary = false;
        foreach ($client_frameworks as $cf) {
            if ($cf['framework'] === $framework && $cf['is_primary']) {
                $isPrimary = true;
                break;
            }
        }
        ?>

        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header" style="background: <?= $isPrimary ? '#10b981' : '#f8f9fa' ?>; color: <?= $isPrimary ? 'white' : 'inherit' ?>;">
                <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                    <?= $e($frameworkName) ?>
                    <?php if ($isPrimary): ?>
                        <span class="badge" style="background: white; color: #10b981;">Primary</span>
                    <?php endif; ?>
                </h2>
            </div>

            <?php if ($framework === 'CMMC'): ?>
                <!-- CMMC Specific Metrics -->
                <div class="metrics-grid" style="padding: 20px;">
                    <div class="metric-card">
                        <div class="metric-icon sprs-icon">📊</div>
                        <div class="metric-content">
                            <div class="metric-label">Current SPRS Score</div>
                            <div class="metric-value <?= $frameworkMetrics['current_sprs'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $frameworkMetrics['current_sprs'] ?>
                            </div>
                            <div class="metric-sublabel">out of 110 points</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">🎯</div>
                        <div class="metric-content">
                            <div class="metric-label">Projected SPRS Score</div>
                            <div class="metric-value text-info">
                                <?= $frameworkMetrics['projected_sprs'] ?>
                            </div>
                            <div class="metric-sublabel">with remediation</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">🏆</div>
                        <div class="metric-content">
                            <div class="metric-label">Target Maturity Level</div>
                            <div class="metric-value" style="font-size: 1.5em;">
                                <?= $e($frameworkMetrics['maturity_level']) ?>
                            </div>
                            <div class="metric-sublabel"><?= $frameworkMetrics['total_controls'] ?> controls</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h3>Maturity Level Progress</h3>
                    <div class="progress-list">
                        <div class="progress-item">
                            <div class="progress-header">
                                <span class="progress-label">ML1 - Foundational</span>
                                <span class="progress-value"><?= $frameworkMetrics['ml1_percent'] ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $frameworkMetrics['ml1_percent'] ?>%"></div>
                            </div>
                        </div>

                        <div class="progress-item">
                            <div class="progress-header">
                                <span class="progress-label">ML2 - Advanced</span>
                                <span class="progress-value"><?= $frameworkMetrics['ml2_percent'] ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $frameworkMetrics['ml2_percent'] ?>%"></div>
                            </div>
                        </div>

                        <div class="progress-item">
                            <div class="progress-header">
                                <span class="progress-label">ML3 - Expert</span>
                                <span class="progress-value"><?= $frameworkMetrics['ml3_percent'] ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $frameworkMetrics['ml3_percent'] ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($framework === 'NIST80053' && !empty($frameworkMetrics['families'])): ?>
                <!-- NIST 800-53 Specific Metrics with Families -->
                <div class="metrics-grid" style="padding: 20px;">
                    <div class="metric-card">
                        <div class="metric-icon">✅</div>
                        <div class="metric-content">
                            <div class="metric-label">Controls Met</div>
                            <div class="metric-value text-success">
                                <?= $frameworkMetrics['met'] ?>
                            </div>
                            <div class="metric-sublabel">of <?= $frameworkMetrics['total_controls'] ?></div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">❌</div>
                        <div class="metric-content">
                            <div class="metric-label">Controls Not Met</div>
                            <div class="metric-value text-danger">
                                <?= $frameworkMetrics['not_met'] ?>
                            </div>
                            <div class="metric-sublabel">require action</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">📈</div>
                        <div class="metric-content">
                            <div class="metric-label">Completion</div>
                            <div class="metric-value text-info">
                                <?= $frameworkMetrics['completion_percent'] ?>%
                            </div>
                            <div class="metric-sublabel">overall progress</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h3>Control Families Progress</h3>
                    <div class="progress-list">
                        <?php foreach ($frameworkMetrics['families'] as $family): ?>
                            <?php
                            $familyPercent = $family['total'] > 0 ? round(($family['met'] / $family['total']) * 100) : 0;
                            ?>
                            <div class="progress-item">
                                <div class="progress-header">
                                    <span class="progress-label"><?= $e($family['family']) ?></span>
                                    <span class="progress-value"><?= $family['met'] ?>/<?= $family['total'] ?> (<?= $familyPercent ?>%)</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $familyPercent ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- Generic Framework Metrics -->
                <div class="metrics-grid" style="padding: 20px;">
                    <div class="metric-card">
                        <div class="metric-icon">✅</div>
                        <div class="metric-content">
                            <div class="metric-label">Controls Met</div>
                            <div class="metric-value text-success">
                                <?= $frameworkMetrics['met'] ?>
                            </div>
                            <div class="metric-sublabel">of <?= $frameworkMetrics['total_controls'] ?></div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">❌</div>
                        <div class="metric-content">
                            <div class="metric-label">Controls Not Met</div>
                            <div class="metric-value text-danger">
                                <?= $frameworkMetrics['not_met'] ?>
                            </div>
                            <div class="metric-sublabel">require action</div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">📈</div>
                        <div class="metric-content">
                            <div class="metric-label">Completion</div>
                            <div class="metric-value text-info">
                                <?= $frameworkMetrics['completion_percent'] ?>%
                            </div>
                            <div class="metric-sublabel">overall progress</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <!-- Common Metrics -->
    <div class="metrics-grid" style="margin-bottom: 20px;">
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
    </div>

    <?php endif; ?>

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
