<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $e($page_title) . ' - ' : '' ?><?= $e($app_name ?? 'Layer3 | Trident Cyber OneComply') ?></title>
    <link rel="stylesheet" href="<?= $asset('css/app.css') ?>?v=<?= filemtime(BASE_PATH . '/public/assets/css/app.css') ?>">
    <?php if (!empty($primary_color) || !empty($secondary_color)): ?>
    <style>
        :root {
            <?php if (!empty($primary_color)): ?>
            --primary: <?= $e($primary_color) ?>;
            --primary-dark: <?= $e($primary_color) ?>dd;
            <?php endif; ?>
            <?php if (!empty($secondary_color)): ?>
            --secondary: <?= $e($secondary_color) ?>;
            <?php endif; ?>
        }
    </style>
    <?php endif; ?>
    <?= isset($extra_css) ? $extra_css : '' ?>
</head>
<body>
    <div class="app-container">
        <!-- Top Navigation -->
        <nav class="top-nav">
            <div class="nav-brand">
                <a href="<?= $url('/') ?>">
                    <?php if (!empty($app_logo)): ?>
                        <img src="<?= $url($app_logo) ?>" alt="<?= $e($app_name ?? 'Layer3 | Trident Cyber OneComply') ?>" style="height: 30px; vertical-align: middle; margin-right: 8px;">
                    <?php endif; ?>
                    <?= $e($app_name ?? 'Layer3 | Trident Cyber OneComply') ?>
                </a>
            </div>

            <div class="nav-center">
                <?php
                $currentCustomer = \App\Core\Session::get('current_customer_name');
                if ($currentCustomer):
                ?>
                <div class="customer-selector">
                    <span class="customer-icon">🏢</span>
                    <span class="customer-name"><?= $e($currentCustomer) ?></span>
                    <a href="<?= $url('customers') ?>" class="change-customer">Change</a>
                </div>
                <?php endif; ?>
            </div>

            <div class="nav-right">
                <div class="user-menu">
                    <span class="user-icon">👤</span>
                    <span class="user-name"><?= $e(\App\Core\Session::get('user_name', 'User')) ?></span>
                    <span class="user-role">(<?= $e(\App\Core\Session::get('user_role', 'viewer')) ?>)</span>
                    <a href="<?= $url('logout') ?>" class="logout-link">Logout</a>
                </div>
            </div>
        </nav>

        <div class="app-body">
            <!-- Sidebar Navigation -->
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <a href="<?= $url('/') ?>" class="nav-item <?= ($current_page ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label">Dashboard</span>
                    </a>

                    <a href="<?= $url('customers') ?>" class="nav-item <?= ($current_page ?? '') === 'customers' ? 'active' : '' ?>">
                        <span class="nav-icon">🏢</span>
                        <span class="nav-label">Customers</span>
                    </a>

                    <a href="<?= $url('controls') ?>" class="nav-item <?= ($current_page ?? '') === 'controls' ? 'active' : '' ?>">
                        <span class="nav-icon">📋</span>
                        <span class="nav-label">Controls</span>
                    </a>

                    <a href="<?= $url('assessments') ?>" class="nav-item <?= ($current_page ?? '') === 'assessments' ? 'active' : '' ?>">
                        <span class="nav-icon">✅</span>
                        <span class="nav-label">Assessments</span>
                    </a>

                    <a href="<?= $url('sprs') ?>" class="nav-item <?= ($current_page ?? '') === 'sprs' ? 'active' : '' ?>">
                        <span class="nav-icon">📈</span>
                        <span class="nav-label">SPRS Scoring</span>
                    </a>

                    <a href="<?= $url('poam') ?>" class="nav-item <?= ($current_page ?? '') === 'poam' ? 'active' : '' ?>">
                        <span class="nav-icon">📝</span>
                        <span class="nav-label">POA&M</span>
                    </a>

                    <a href="<?= $url('documents') ?>" class="nav-item <?= ($current_page ?? '') === 'documents' ? 'active' : '' ?>">
                        <span class="nav-icon">📁</span>
                        <span class="nav-label">Documents</span>
                    </a>

                    <a href="<?= $url('reports') ?>" class="nav-item <?= ($current_page ?? '') === 'reports' ? 'active' : '' ?>">
                        <span class="nav-icon">📄</span>
                        <span class="nav-label">Reports</span>
                    </a>

                    <a href="<?= $url('integrations') ?>" class="nav-item <?= ($current_page ?? '') === 'integrations' ? 'active' : '' ?>">
                        <span class="nav-icon">🔗</span>
                        <span class="nav-label">Integrations</span>
                    </a>

                    <?php if (\App\Middleware\AuthMiddleware::checkPermission('admin')): ?>
                    <div class="nav-divider"></div>

                    <a href="<?= $url('admin') ?>" class="nav-item <?= ($current_page ?? '') === 'admin' ? 'active' : '' ?>">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-label">Admin</span>
                    </a>
                    <?php endif; ?>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="main-content">
                <?php if ($success = $success()): ?>
                    <div class="alert alert-success">
                        <span class="alert-icon">✓</span>
                        <?= $e($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage = $error()): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">✗</span>
                        <?= $e($errorMessage) ?>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>

                <!-- Footer -->
                <footer class="app-footer">
                    &copy; <?= date('Y') ?> Layer3 &amp; Trident Cyber. All rights reserved.
                </footer>
            </main>
        </div>
    </div>

    <script src="<?= $asset('js/app.js') ?>"></script>
    <?= isset($extra_js) ? $extra_js : '' ?>
</body>
</html>
