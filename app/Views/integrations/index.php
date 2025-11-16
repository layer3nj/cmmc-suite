<?php
$page_title = 'Integrations';
$current_page = 'integrations';

ob_start();
?>

<div class="page-header">
    <h1>Integrations</h1>
    <p class="page-subtitle">Connect with Autotask and ITGlue</p>
    <div class="page-actions">
        <a href="<?= $url('integrations/client-mapping') ?>" class="btn btn-primary">Manage Client Mappings</a>
    </div>
</div>

<div class="integrations-grid">
    <!-- Autotask Integration -->
    <div class="integration-card">
        <div class="integration-header">
            <div class="integration-icon">🔧</div>
            <div>
                <h3>Autotask PSA</h3>
                <p>Sync customers and create tickets from POA&M items</p>
            </div>
        </div>

        <?php if ($autotask_connected): ?>
            <div class="integration-status connected">
                <span class="status-indicator"></span>
                Connected
            </div>
            <div class="integration-info">
                <div class="info-row">
                    <span class="label">API URL:</span>
                    <span class="value"><?= $e($autotask_config['api_url'] ?? '') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Username:</span>
                    <span class="value"><?= $e($autotask_config['username'] ?? '') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Last Sync:</span>
                    <span class="value"><?= $autotask_last_sync ? date('M d, Y H:i', strtotime($autotask_last_sync)) : 'Never' ?></span>
                </div>
            </div>
            <div class="integration-actions">
                <form action="<?= $url('integrations/autotask/sync') ?>" method="POST" style="display: inline;" class="sync-form" data-sync-type="autotask">
                    <?= $csrf() ?>
                    <button type="submit" class="btn btn-primary">Sync Now</button>
                </form>
                <button class="btn btn-secondary" onclick="document.getElementById('autotask-form').style.display='block'">Configure</button>
            </div>
        <?php else: ?>
            <div class="integration-status disconnected">
                <span class="status-indicator"></span>
                Not Connected
            </div>
            <div class="integration-actions">
                <button class="btn btn-primary" onclick="document.getElementById('autotask-form').style.display='block'">Connect</button>
            </div>
        <?php endif; ?>

        <div id="autotask-form" class="integration-form" style="display: none;">
            <form action="<?= $url('integrations/autotask/connect') ?>" method="POST">
                <?= $csrf() ?>
                <div class="form-group">
                    <label>API URL</label>
                    <input type="url" name="api_url" value="<?= $e($autotask_config['api_url'] ?? '') ?>" required class="form-control" placeholder="https://webservices2.autotask.net/atservicesrest">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= $e($autotask_config['username'] ?? '') ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Secret</label>
                    <input type="password" name="secret" required class="form-control" placeholder="API secret">
                </div>
                <div class="form-group">
                    <label>Integration Code</label>
                    <input type="text" name="integration_code" value="<?= $e($autotask_config['api_key'] ?? '') ?>" required class="form-control">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('autotask-form').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Configuration</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ITGlue Integration -->
    <div class="integration-card">
        <div class="integration-header">
            <div class="integration-icon">📚</div>
            <div>
                <h3>ITGlue</h3>
                <p>Sync documentation and compliance evidence</p>
            </div>
        </div>

        <?php if ($itglue_connected): ?>
            <div class="integration-status connected">
                <span class="status-indicator"></span>
                Connected
            </div>
            <div class="integration-info">
                <div class="info-row">
                    <span class="label">API URL:</span>
                    <span class="value"><?= $e($itglue_config['api_url'] ?? '') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Last Sync:</span>
                    <span class="value"><?= $itglue_last_sync ? date('M d, Y H:i', strtotime($itglue_last_sync)) : 'Never' ?></span>
                </div>
            </div>
            <div class="integration-actions">
                <form action="<?= $url('integrations/itglue/sync') ?>" method="POST" style="display: inline;" class="sync-form" data-sync-type="itglue">
                    <?= $csrf() ?>
                    <button type="submit" class="btn btn-primary">Sync Now</button>
                </form>
                <button class="btn btn-secondary" onclick="document.getElementById('itglue-form').style.display='block'">Configure</button>
            </div>
        <?php else: ?>
            <div class="integration-status disconnected">
                <span class="status-indicator"></span>
                Not Connected
            </div>
            <div class="integration-actions">
                <button class="btn btn-primary" onclick="document.getElementById('itglue-form').style.display='block'">Connect</button>
            </div>
        <?php endif; ?>

        <div id="itglue-form" class="integration-form" style="display: none;">
            <form action="<?= $url('integrations/itglue/connect') ?>" method="POST">
                <?= $csrf() ?>
                <div class="form-group">
                    <label>API URL</label>
                    <input type="url" name="api_url" value="<?= $e($itglue_config['api_url'] ?? 'https://api.itglue.com') ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>API Key</label>
                    <input type="password" name="api_key" required class="form-control" placeholder="ITGlue API key">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('itglue-form').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Configuration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fix CSRF token issue - ensure correct field is submitted
document.addEventListener('DOMContentLoaded', function() {
    const syncForms = document.querySelectorAll('.sync-form');

    syncForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            console.log('Sync form submitting for:', form.dataset.syncType);

            // Find the correct CSRF field
            const csrfField = form.querySelector('input[name="_csrf_token"]');

            if (!csrfField) {
                console.error('CSRF token field _csrf_token not found!');
                alert('Security token missing. Please refresh the page.');
                e.preventDefault();
                return false;
            }

            if (!csrfField.value || csrfField.value === '') {
                console.error('CSRF token value is empty!');
                alert('Security token is empty. Please refresh the page.');
                e.preventDefault();
                return false;
            }

            console.log('CSRF token found:', csrfField.value.substring(0, 16) + '...');

            // Remove any duplicate csrf_token fields (without underscore)
            const wrongFields = form.querySelectorAll('input[name="csrf_token"]');
            wrongFields.forEach(field => {
                console.log('Removing duplicate csrf_token field (no underscore)');
                field.remove();
            });

            console.log('Form validated, submitting...');
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
