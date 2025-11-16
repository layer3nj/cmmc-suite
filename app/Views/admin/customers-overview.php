<?php
$page_title = 'Customers Overview';
$current_page = 'admin';

ob_start();
?>

<style>
/* Customer Overview Styles */
.admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.overview-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 20px 30px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.overview-stats {
    display: flex;
    gap: 30px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.overview-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-box {
    position: relative;
}

.search-box input {
    padding: 10px 40px 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    width: 300px;
    font-size: 14px;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.customers-table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.customers-table table {
    width: 100%;
    border-collapse: collapse;
}

.customers-table thead {
    background: #f8f9fa;
}

.customers-table th {
    padding: 15px 20px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.customers-table td {
    padding: 20px;
    border-top: 1px solid #f0f0f0;
    vertical-align: top;
}

.customers-table tbody tr {
    cursor: pointer;
    transition: background-color 0.2s;
}

.customers-table tbody tr:hover {
    background-color: #f8f9ff;
}

.company-name {
    font-weight: 600;
    color: #333;
    font-size: 15px;
    margin-bottom: 5px;
}

.company-alt-name {
    font-size: 13px;
    color: #999;
}

.module-pills {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.module-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    background: #10b981;
    color: white;
    gap: 6px;
}

.module-pill.disabled {
    background: #e0e0e0;
    color: #999;
}

.policy-stats {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.policy-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

.policy-badge.approved {
    background: #d1fae5;
    color: #065f46;
}

.policy-badge.in-review {
    background: #fed7aa;
    color: #92400e;
}

.policy-badge.draft {
    background: #fef3c7;
    color: #92400e;
}

.policy-badge.outdated {
    background: #fecaca;
    color: #991b1b;
}

.compliance-phases {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.phase-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    background: #e5e7eb;
    color: #4b5563;
}

.user-stats {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.user-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.user-badge.sso {
    background: #e5e7eb;
    color: #4b5563;
}

.user-badge.scim {
    background: #e5e7eb;
    color: #4b5563;
}

.user-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.user-badge.inactive {
    background: #e5e7eb;
    color: #6b7280;
}

.actions-cell {
    display: flex;
    gap: 10px;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    background: #f3f4f6;
    color: #6b7280;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #e5e7eb;
    color: #374151;
}

.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 200px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -100px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 12px;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

.info-icon {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #667eea;
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    margin-left: 5px;
    cursor: help;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #999;
}
</style>

<!-- Admin Header Section -->
<div class="admin-header">
    <h1 style="margin: 0 0 10px 0; font-size: 28px;">Customers Overview</h1>
    <p style="margin: 0; opacity: 0.9;">Manage and monitor all customer accounts</p>
</div>

<!-- Overview Bar -->
<div class="overview-bar">
    <div class="overview-stats">
        <div class="stat-item">
            <span class="stat-label">Total Customers:</span>
            <span class="stat-value" id="total-customers"><?= $stats['total'] ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-label">New (last 30 days):</span>
            <span class="stat-value" id="new-customers"><?= $stats['new_last_30_days'] ?></span>
        </div>
    </div>

    <div class="overview-actions">
        <div class="search-box">
            <input type="text" id="customer-search" placeholder="Search customers..." autocomplete="off">
            <span class="search-icon">🔍</span>
        </div>
        <a href="<?= $url('clients/create') ?>" class="btn btn-primary">+ Add Customer</a>
    </div>
</div>

<!-- Customers Table -->
<div class="customers-table">
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Company</th>
                <th style="width: 15%;">
                    Modules
                    <span class="tooltip">
                        <span class="info-icon">i</span>
                        <span class="tooltiptext">
                            Policies: Document management<br>
                            Training: Security awareness<br>
                            Compliance: Framework tracking<br>
                            Risk: Risk assessments
                        </span>
                    </span>
                </th>
                <th style="width: 15%;">Policies</th>
                <th style="width: 20%;">Compliance</th>
                <th style="width: 15%;">Users</th>
                <th style="width: 15%;">Actions</th>
            </tr>
        </thead>
        <tbody id="customers-tbody">
            <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="6" class="no-results">
                        No customers found. <a href="<?= $url('clients/create') ?>">Add your first customer</a>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $customer): ?>
                <tr onclick="window.location='<?= $url('clients/' . $customer['id']) ?>'">
                    <td>
                        <div class="company-name"><?= $e($customer['name']) ?></div>
                        <?php if (!empty($customer['external_id']) && strpos($customer['external_id'], 'autotask_') === 0): ?>
                            <div class="company-alt-name">Autotask ID: <?= $e(str_replace('autotask_', '', $customer['external_id'])) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="module-pills">
                            <?php foreach ($customer['modules'] as $module): ?>
                                <span class="module-pill <?= $module['enabled'] ? '' : 'disabled' ?>">
                                    <?php
                                    $icons = ['policies' => '📄', 'training' => '🎓', 'compliance' => '✓', 'risk' => '⚠️'];
                                    echo $icons[$module['module_name']] ?? '•';
                                    ?>
                                    <?= ucfirst($module['module_name']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td>
                        <div class="policy-stats">
                            <span class="policy-badge approved"><?= $customer['policies']['approved'] ?> Approved</span>
                            <span class="policy-badge in-review"><?= $customer['policies']['in_review'] ?> in Review</span>
                            <span class="policy-badge draft"><?= $customer['policies']['draft'] ?> Draft</span>
                            <?php if ($customer['policies']['outdated'] > 0): ?>
                                <span class="policy-badge outdated"><?= $customer['policies']['outdated'] ?> Outdated</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="compliance-phases">
                            <?php foreach ($customer['compliance'] as $phase => $percentage): ?>
                                <span class="phase-badge">Phase <?= $phase ?>: <?= number_format($percentage, 0) ?>%</span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td>
                        <div class="user-stats">
                            <?php if ($customer['users']['sso_enforced']): ?>
                                <span class="user-badge sso">SSO Enforced</span>
                            <?php endif; ?>
                            <?php if ($customer['users']['scim_enabled']): ?>
                                <span class="user-badge scim">SCM Enabled</span>
                            <?php endif; ?>
                            <span class="user-badge active"><?= $customer['users']['active'] ?> Active</span>
                            <?php if ($customer['users']['never_logged_in'] > 0): ?>
                                <span class="user-badge inactive"><?= $customer['users']['never_logged_in'] ?> never logged in</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="actions-cell" onclick="event.stopPropagation();">
                            <div class="tooltip">
                                <button class="action-btn" onclick="window.location='<?= $url('clients/' . $customer['id'] . '/users') ?>'">
                                    👥
                                </button>
                                <span class="tooltiptext">Manage Users</span>
                            </div>
                            <div class="tooltip">
                                <button class="action-btn" onclick="syncCustomer(<?= $customer['id'] ?>)">
                                    🔄
                                </button>
                                <span class="tooltiptext">Sync/Refresh</span>
                            </div>
                            <div class="tooltip">
                                <button class="action-btn" onclick="deleteCustomer(<?= $customer['id'] ?>, '<?= $e($customer['name']) ?>')">
                                    🗑️
                                </button>
                                <span class="tooltiptext">Delete</span>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Search functionality with debounce
let searchTimeout;
const searchInput = document.getElementById('customer-search');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const searchTerm = this.value.trim();

    searchTimeout = setTimeout(() => {
        searchCustomers(searchTerm);
    }, 300);
});

function searchCustomers(term) {
    if (term === '') {
        // Reload page to show all customers
        window.location.reload();
        return;
    }

    fetch('<?= $url('admin/customers/search') ?>?q=' + encodeURIComponent(term))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTable(data.customers);
            }
        })
        .catch(error => {
            console.error('Search failed:', error);
        });
}

function updateTable(customers) {
    const tbody = document.getElementById('customers-tbody');

    if (customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="no-results">No customers found matching your search.</td></tr>';
        return;
    }

    tbody.innerHTML = customers.map(customer => `
        <tr onclick="window.location='<?= $url('clients/') ?>${customer.id}'">
            <td>
                <div class="company-name">${escapeHtml(customer.name)}</div>
                ${customer.external_id && customer.external_id.startsWith('autotask_') ?
                    `<div class="company-alt-name">Autotask ID: ${escapeHtml(customer.external_id.replace('autotask_', ''))}</div>` : ''}
            </td>
            <td>
                <div class="module-pills">
                    ${customer.modules.map(module => `
                        <span class="module-pill ${module.enabled ? '' : 'disabled'}">
                            ${getModuleIcon(module.module_name)} ${capitalize(module.module_name)}
                        </span>
                    `).join('')}
                </div>
            </td>
            <td>
                <div class="policy-stats">
                    <span class="policy-badge approved">${customer.policies.approved} Approved</span>
                    <span class="policy-badge in-review">${customer.policies.in_review} in Review</span>
                    <span class="policy-badge draft">${customer.policies.draft} Draft</span>
                    ${customer.policies.outdated > 0 ? `<span class="policy-badge outdated">${customer.policies.outdated} Outdated</span>` : ''}
                </div>
            </td>
            <td>
                <div class="compliance-phases">
                    ${Object.entries(customer.compliance).map(([phase, percentage]) => `
                        <span class="phase-badge">Phase ${phase}: ${Math.round(percentage)}%</span>
                    `).join('')}
                </div>
            </td>
            <td>
                <div class="user-stats">
                    ${customer.users.sso_enforced ? '<span class="user-badge sso">SSO Enforced</span>' : ''}
                    ${customer.users.scim_enabled ? '<span class="user-badge scim">SCM Enabled</span>' : ''}
                    <span class="user-badge active">${customer.users.active} Active</span>
                    ${customer.users.never_logged_in > 0 ? `<span class="user-badge inactive">${customer.users.never_logged_in} never logged in</span>` : ''}
                </div>
            </td>
            <td>
                <div class="actions-cell" onclick="event.stopPropagation();">
                    <div class="tooltip">
                        <button class="action-btn" onclick="window.location='<?= $url('clients/') ?>${customer.id}/users'">👥</button>
                        <span class="tooltiptext">Manage Users</span>
                    </div>
                    <div class="tooltip">
                        <button class="action-btn" onclick="syncCustomer(${customer.id})">🔄</button>
                        <span class="tooltiptext">Sync/Refresh</span>
                    </div>
                    <div class="tooltip">
                        <button class="action-btn" onclick="deleteCustomer(${customer.id}, '${escapeHtml(customer.name)}')">🗑️</button>
                        <span class="tooltiptext">Delete</span>
                    </div>
                </div>
            </td>
        </tr>
    `).join('');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function getModuleIcon(moduleName) {
    const icons = {
        'policies': '📄',
        'training': '🎓',
        'compliance': '✓',
        'risk': '⚠️'
    };
    return icons[moduleName] || '•';
}

function syncCustomer(customerId) {
    if (confirm('Sync this customer with external systems?')) {
        // TODO: Implement sync functionality
        alert('Sync functionality will be implemented based on your integration settings.');
    }
}

function deleteCustomer(customerId, customerName) {
    if (confirm(`Are you sure you want to delete "${customerName}"? This action cannot be undone.`)) {
        // TODO: Implement delete functionality
        alert('Delete functionality will be implemented with proper confirmation.');
    }
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
