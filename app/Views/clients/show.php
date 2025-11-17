<?php
$page_title = $client['name'];
$current_page = 'clients';

ob_start();
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= $url('clients') ?>">Clients</a> / <?= $e($client['name']) ?>
    </div>
    <div style="display: flex; align-items: center; gap: 20px;">
        <?php if (!empty($client['logo_path'])): ?>
            <img src="<?= $url($client['logo_path']) ?>" alt="<?= $e($client['name']) ?> Logo" style="max-height: 60px; max-width: 200px;">
        <?php endif; ?>
        <h1><?= $e($client['name']) ?></h1>
    </div>
    <div class="page-actions">
        <div class="dropdown" style="position: relative; display: inline-block;">
            <button class="btn btn-secondary" onclick="toggleReportMenu(event)">
                Generate Report ▼
            </button>
            <div id="report-menu" class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 100%; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); margin-top: 5px; min-width: 200px; z-index: 1000;">
                <a href="<?= $url('reports/ssp/' . $client['id']) ?>" target="_blank" class="dropdown-item" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">
                    System Security Plan (SSP)
                </a>
                <a href="<?= $url('reports/poam/' . $client['id']) ?>" target="_blank" class="dropdown-item" style="display: block; padding: 10px 15px; color: #333; text-decoration: none;">
                    POA&M Report
                </a>
            </div>
        </div>
        <a href="<?= $url('clients/' . $client['id'] . '/edit') ?>" class="btn btn-secondary">Edit</a>
        <a href="<?= $url('clients/' . $client['id'] . '/select') ?>" class="btn btn-primary">Select Client</a>
    </div>
</div>

<style>
.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>

<script>
function toggleReportMenu(event) {
    event.stopPropagation();
    const menu = document.getElementById('report-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('report-menu');
    if (menu) {
        menu.style.display = 'none';
    }
});
</script>

<div class="card">
    <div class="card-header">
        <h3>Client Details</h3>
        <?php if ($client['active']): ?>
            <span class="badge badge-success">Active</span>
        <?php else: ?>
            <span class="badge badge-secondary">Inactive</span>
        <?php endif; ?>
    </div>

    <table class="info-table">
        <tr>
            <th>Client Name</th>
            <td><?= $e($client['name']) ?></td>
        </tr>
        <tr>
            <th>Contact Email</th>
            <td><?= $e($client['contact_email'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Contact Phone</th>
            <td><?= $e($client['contact_phone'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?= $client['address'] ? nl2br($e($client['address'])) : '-' ?></td>
        </tr>
        <tr>
            <th>Autotask Company ID</th>
            <td>
                <?= $e($client['autotask_company_id'] ?? '-') ?>
                <?php
                // Debug output
                error_log('DEBUG Autotask Button Visibility:');
                error_log('  client[autotask_company_id]: ' . ($client['autotask_company_id'] ?? 'NULL'));
                error_log('  autotask_config exists: ' . (!empty($autotask_config) ? 'YES' : 'NO'));
                if (!empty($autotask_config)) {
                    error_log('  autotask_config[api_url]: ' . ($autotask_config['api_url'] ?? 'NULL'));
                }
                ?>
                <?php if (!empty($client['autotask_company_id']) && !empty($autotask_config)): ?>
                    <?php
                    // Extract Autotask instance from API URL (e.g., webservices2.autotask.net -> ww2)
                    $autotaskUrl = $autotask_config['api_url'] ?? '';
                    error_log('  Checking regex match against: ' . $autotaskUrl);
                    if (preg_match('/webservices(\d+)\.autotask\.net/', $autotaskUrl, $matches)) {
                        error_log('  Regex matched! Instance: ww' . $matches[1]);
                        $instance = 'ww' . $matches[1];
                        $autotaskLink = "https://{$instance}.autotask.net/Autotask/AutotaskExtend/ExecuteCommand.aspx?Code=OpenAccount&AccountID={$client['autotask_company_id']}";
                    ?>
                        <a href="<?= $e($autotaskLink) ?>" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: 10px;">
                            View in Autotask
                        </a>
                    <?php } else {
                        error_log('  Regex did NOT match');
                    } ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>ITGlue Organization ID</th>
            <td>
                <?= $e($client['itglue_organization_id'] ?? '-') ?>
                <?php
                // Debug output
                error_log('DEBUG ITGlue Button Visibility:');
                error_log('  client[itglue_organization_id]: ' . ($client['itglue_organization_id'] ?? 'NULL'));
                error_log('  itglue_config exists: ' . (!empty($itglue_config) ? 'YES' : 'NO'));
                ?>
                <?php if (!empty($client['itglue_organization_id']) && !empty($itglue_config)): ?>
                    <?php
                    error_log('  ITGlue button should be visible');
                    $itglueLink = "https://app.itglue.com/{$client['itglue_organization_id']}/overview";
                    ?>
                    <a href="<?= $e($itglueLink) ?>" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: 10px;">
                        View in ITGlue
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Assigned Frameworks</th>
            <td>
                <?php if (!empty($assigned_frameworks)): ?>
                    <?php foreach ($assigned_frameworks as $af): ?>
                        <span class="badge <?= $af['is_primary'] ? 'badge-primary' : 'badge-secondary' ?>" style="margin-right: 5px;">
                            <?= $e($af['framework']) ?>
                            <?= $af['is_primary'] ? ' (Primary)' : '' ?>
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-muted">No frameworks assigned</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Created</th>
            <td><?= date('M d, Y', strtotime($client['created_at'])) ?></td>
        </tr>
        <?php if (isset($client['updated_at']) && $client['updated_at']): ?>
        <tr>
            <th>Last Updated</th>
            <td><?= date('M d, Y', strtotime($client['updated_at'])) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Compliance Summary</h3>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Assessments</div>
            <div class="stat-value"><?= $stats['total_assessments'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Open POA&M Items</div>
            <div class="stat-value text-warning"><?= $stats['open_poam'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Documents</div>
            <div class="stat-value"><?= $stats['total_documents'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Latest SPRS Score</div>
            <div class="stat-value <?= $stats['latest_sprs'] >= 0 ? 'text-success' : 'text-danger' ?>">
                <?= $stats['latest_sprs'] ?? 'N/A' ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($recent_assessments)): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Recent Assessments</h3>
        <a href="<?= $url('assessments?customer=' . $client['id']) ?>" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Framework</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_assessments as $assessment): ?>
            <tr>
                <td>
                    <?php
                    $framework = $assessment['framework'] ?? 'Assessment';
                    $level = $assessment['target_level'] ? " ML{$assessment['target_level']}" : '';
                    $type = $assessment['assessment_type'] ?? 'Assessment';
                    echo $e("{$framework}{$level} {$type}");
                    ?>
                </td>
                <td><span class="badge"><?= $e($assessment['framework'] ?? 'N/A') ?></span></td>
                <td>
                    <?php if ($assessment['status'] === 'published'): ?>
                        <span class="badge badge-success">Published</span>
                    <?php elseif ($assessment['status'] === 'in_progress'): ?>
                        <span class="badge badge-warning">In Progress</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Draft</span>
                    <?php endif; ?>
                </td>
                <td><?= $assessment['assessed_at'] ? date('M d, Y', strtotime($assessment['assessed_at'])) : '-' ?></td>
                <td>
                    <a href="<?= $url('assessments/' . $assessment['id']) ?>" class="btn btn-sm btn-primary">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($recent_poam)): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Active POA&M Items</h3>
        <a href="<?= $url('poam?customer=' . $client['id']) ?>" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Control</th>
                <th>Weakness</th>
                <th>Responsible Party</th>
                <th>Target Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_poam as $item): ?>
            <tr>
                <td><code><?= $e($item['control_code'] ?? 'N/A') ?></code></td>
                <td><?= $e(substr($item['weakness'] ?? '', 0, 60)) ?><?= strlen($item['weakness'] ?? '') > 60 ? '...' : '' ?></td>
                <td><?= $e($item['responsible_party'] ?? '-') ?></td>
                <td><?= $item['planned_completion_date'] ? date('M d, Y', strtotime($item['planned_completion_date'])) : '-' ?></td>
                <td>
                    <?php if ($item['status'] === 'open'): ?>
                        <span class="badge badge-warning">Open</span>
                    <?php elseif ($item['status'] === 'in_progress'): ?>
                        <span class="badge badge-info">In Progress</span>
                    <?php elseif ($item['status'] === 'closed'): ?>
                        <span class="badge badge-success">Closed</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Deferred</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="form-actions" style="margin-top: 20px;">
    <a href="<?= $url('clients') ?>" class="btn btn-secondary">Back to Clients</a>
    <form action="<?= $url('clients/' . $client['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this client? This will also delete all associated assessments, POA&M items, and documents.');">
        <?= $csrf() ?>
        <button type="submit" class="btn btn-danger">Delete Client</button>
    </form>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
