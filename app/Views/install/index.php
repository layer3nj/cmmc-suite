<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMMC Compliance Suite - Installation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .installer {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .progress {
            display: flex;
            padding: 20px;
            background: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .progress-step {
            flex: 1;
            text-align: center;
            padding: 10px;
            position: relative;
            opacity: 0.5;
        }

        .progress-step.active {
            opacity: 1;
        }

        .progress-step.completed {
            opacity: 1;
        }

        .progress-step .number {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #cbd5e0;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .progress-step.active .number {
            background: #667eea;
        }

        .progress-step.completed .number {
            background: #48bb78;
            content: '✓';
        }

        .progress-step .label {
            font-size: 12px;
            color: #4a5568;
        }

        .content {
            padding: 40px;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .step h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2d3748;
        }

        .step p {
            color: #718096;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group small {
            display: block;
            margin-top: 4px;
            color: #718096;
            font-size: 12px;
        }

        .check-list {
            list-style: none;
        }

        .check-item {
            padding: 12px;
            margin-bottom: 8px;
            background: #f7fafc;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .check-item.passed {
            background: #f0fff4;
            border-left: 4px solid #48bb78;
        }

        .check-item.failed {
            background: #fff5f5;
            border-left: 4px solid #f56565;
        }

        .check-item .status {
            font-weight: 600;
        }

        .check-item.passed .status {
            color: #48bb78;
        }

        .check-item.failed .status {
            color: #f56565;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #f0fff4;
            color: #22543d;
            border-left: 4px solid #48bb78;
        }

        .alert-error {
            background: #fff5f5;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }

        .alert-info {
            background: #ebf8ff;
            color: #2c5282;
            border-left: 4px solid #4299e1;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }

        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="installer">
        <div class="header">
            <h1>CMMC Compliance Suite</h1>
            <p>Web-Based Installation Wizard</p>
        </div>

        <div class="progress">
            <div class="progress-step active" data-step="1">
                <div class="number">1</div>
                <div class="label">Requirements</div>
            </div>
            <div class="progress-step" data-step="2">
                <div class="number">2</div>
                <div class="label">Database</div>
            </div>
            <div class="progress-step" data-step="3">
                <div class="number">3</div>
                <div class="label">Migrations</div>
            </div>
            <div class="progress-step" data-step="4">
                <div class="number">4</div>
                <div class="label">SAML Setup</div>
            </div>
            <div class="progress-step" data-step="5">
                <div class="number">5</div>
                <div class="label">Admin User</div>
            </div>
            <div class="progress-step" data-step="6">
                <div class="number">6</div>
                <div class="label">Complete</div>
            </div>
        </div>

        <div class="content">
            <!-- Step 1: Requirements Check -->
            <div class="step active" data-step="1">
                <h2>System Requirements</h2>
                <p>Checking your server configuration...</p>

                <div id="requirements-result"></div>

                <div class="buttons">
                    <div></div>
                    <button class="btn btn-primary" id="btn-check-requirements">Check Requirements</button>
                </div>
            </div>

            <!-- Step 2: Database Configuration -->
            <div class="step" data-step="2">
                <h2>Database Configuration</h2>
                <p>Configure your database connection. Both MySQL and PostgreSQL are supported.</p>

                <div id="db-message"></div>

                <form id="db-form">
                    <div class="form-group">
                        <label>Database Driver</label>
                        <select name="driver" id="db-driver" required>
                            <option value="mysql">MySQL / MariaDB</option>
                            <option value="pgsql">PostgreSQL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Host</label>
                        <input type="text" name="host" value="localhost" required>
                        <small>Usually 'localhost' or '127.0.0.1'</small>
                    </div>

                    <div class="form-group">
                        <label>Port</label>
                        <input type="text" name="port" id="db-port" value="3306" required>
                        <small>Default MySQL: 3306, PostgreSQL: 5432</small>
                    </div>

                    <div class="form-group">
                        <label>Database Name</label>
                        <input type="text" name="database" required>
                        <small>The database must already exist</small>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password">
                        <small>Leave blank if no password</small>
                    </div>
                </form>

                <div class="buttons">
                    <button class="btn btn-secondary" onclick="prevStep()">Previous</button>
                    <button class="btn btn-primary" id="btn-test-db">Test Connection</button>
                </div>
            </div>

            <!-- Step 3: Migrations -->
            <div class="step" data-step="3">
                <h2>Database Setup</h2>
                <p>Create database tables and load initial compliance data.</p>

                <div id="migration-message"></div>

                <div class="alert alert-info">
                    This will create all necessary tables and load:
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <li>CMMC 2.0 ML1-ML3 controls</li>
                        <li>NIST SP 800-171 (110 practices)</li>
                        <li>STIG references</li>
                        <li>Control mappings</li>
                    </ul>
                </div>

                <div id="migration-progress"></div>

                <div class="buttons">
                    <button class="btn btn-secondary" onclick="prevStep()">Previous</button>
                    <button class="btn btn-primary" id="btn-migrate">Setup Database</button>
                </div>
            </div>

            <!-- Step 4: SAML Configuration -->
            <div class="step" data-step="4">
                <h2>SAML SSO Configuration</h2>
                <p>Configure single sign-on with Microsoft Entra (optional).</p>

                <div id="saml-message"></div>

                <form id="saml-form">
                    <div class="checkbox-group">
                        <input type="checkbox" name="saml_enabled" id="saml-enabled">
                        <label for="saml-enabled">Enable SAML SSO</label>
                    </div>

                    <div id="saml-config" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Service Provider Information:</strong>
                            <div class="code-block">
Entity ID: <span id="sp-entity-id"></span>
ACS URL: <span id="sp-acs-url"></span>
                            </div>
                            Copy these values into your IdP configuration.
                        </div>

                        <div class="form-group">
                            <label>IdP Metadata URL</label>
                            <input type="url" name="idp_metadata_url" id="idp-metadata-url">
                            <small>Microsoft Entra App Federation Metadata URL</small>
                        </div>

                        <div style="text-align: center; margin: 20px 0; color: #718096;">— OR —</div>

                        <div class="form-group">
                            <label>IdP Metadata XML</label>
                            <textarea name="idp_metadata_xml" rows="6" id="idp-metadata-xml" placeholder="Paste XML metadata here..."></textarea>
                            <small>Upload or paste the IdP metadata XML</small>
                        </div>
                    </div>
                </form>

                <div class="buttons">
                    <button class="btn btn-secondary" onclick="prevStep()">Previous</button>
                    <button class="btn btn-primary" id="btn-save-saml">Continue</button>
                </div>
            </div>

            <!-- Step 5: Admin User -->
            <div class="step" data-step="5">
                <h2>Create Admin User</h2>
                <p>Create a local administrator account for initial access.</p>

                <div id="admin-message"></div>

                <form id="admin-form">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Display Name</label>
                        <input type="text" name="display_name" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required minlength="8">
                        <small>Minimum 8 characters</small>
                    </div>
                </form>

                <div class="buttons">
                    <button class="btn btn-secondary" onclick="prevStep()">Previous</button>
                    <button class="btn btn-primary" id="btn-create-admin">Create Admin</button>
                </div>
            </div>

            <!-- Step 6: Complete -->
            <div class="step" data-step="6">
                <h2>Installation Complete!</h2>
                <p>Your CMMC Compliance Suite has been successfully installed.</p>

                <div class="alert alert-success">
                    <strong>Success!</strong> The application is now ready to use.
                </div>

                <div class="alert alert-info">
                    <strong>Next Steps:</strong>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <li>Log in with the admin account you created</li>
                        <li>Review the dashboard and compliance controls</li>
                        <li>Create your first assessment</li>
                        <li>Configure integrations (Autotask, ITGlue)</li>
                    </ul>
                </div>

                <div class="buttons">
                    <div></div>
                    <button class="btn btn-primary" id="btn-goto-login">Go to Login</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const maxSteps = 6;

        // Update progress indicator
        function updateProgress() {
            document.querySelectorAll('.progress-step').forEach(step => {
                const stepNum = parseInt(step.dataset.step);
                step.classList.remove('active', 'completed');
                if (stepNum === currentStep) {
                    step.classList.add('active');
                } else if (stepNum < currentStep) {
                    step.classList.add('completed');
                    step.querySelector('.number').textContent = '✓';
                } else {
                    step.querySelector('.number').textContent = stepNum;
                }
            });
        }

        // Navigate to step
        function gotoStep(step) {
            if (step < 1 || step > maxSteps) return;

            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.querySelector(`.step[data-step="${step}"]`).classList.add('active');

            currentStep = step;
            updateProgress();
        }

        function nextStep() {
            gotoStep(currentStep + 1);
        }

        function prevStep() {
            gotoStep(currentStep - 1);
        }

        // Helpers
        function showMessage(containerId, message, type = 'info') {
            const container = document.getElementById(containerId);
            container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        }

        function clearMessage(containerId) {
            document.getElementById(containerId).innerHTML = '';
        }

        // Step 1: Check Requirements
        document.getElementById('btn-check-requirements').addEventListener('click', async function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner"></span> Checking...';

            try {
                const response = await fetch('install/check', {
                    method: 'POST'
                });
                const data = await response.json();

                let html = '<ul class="check-list">';
                for (const [key, check] of Object.entries(data.checks)) {
                    const status = check.passed ? 'passed' : 'failed';
                    const statusText = check.passed ? '✓ Pass' : '✗ Fail';
                    html += `
                        <li class="check-item ${status}">
                            <span>${check.name}: ${check.value}</span>
                            <span class="status">${statusText}</span>
                        </li>
                    `;
                }
                html += '</ul>';

                document.getElementById('requirements-result').innerHTML = html;

                if (data.success) {
                    this.textContent = 'Next';
                    this.onclick = nextStep;
                } else {
                    showMessage('requirements-result', 'Some requirements failed. Please fix them before continuing.', 'error');
                }
            } catch (error) {
                showMessage('requirements-result', 'Error checking requirements: ' + error.message, 'error');
            } finally {
                this.disabled = false;
                if (this.querySelector('.spinner')) {
                    this.textContent = 'Check Requirements';
                }
            }
        });

        // Database driver change
        document.getElementById('db-driver').addEventListener('change', function() {
            const port = document.getElementById('db-port');
            port.value = this.value === 'mysql' ? '3306' : '5432';
        });

        // Step 2: Test Database
        document.getElementById('btn-test-db').addEventListener('click', async function() {
            clearMessage('db-message');

            const form = document.getElementById('db-form');
            const formData = new FormData(form);

            this.disabled = true;
            this.innerHTML = '<span class="spinner"></span> Testing...';

            try {
                const response = await fetch('install/database', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showMessage('db-message', data.message, 'success');
                    this.textContent = 'Next';
                    this.onclick = nextStep;
                } else {
                    showMessage('db-message', data.message, 'error');
                    this.disabled = false;
                    this.textContent = 'Test Connection';
                }
            } catch (error) {
                showMessage('db-message', 'Error: ' + error.message, 'error');
                this.disabled = false;
                this.textContent = 'Test Connection';
            }
        });

        // Step 3: Run Migrations
        document.getElementById('btn-migrate').addEventListener('click', async function() {
            clearMessage('migration-message');

            this.disabled = true;
            this.innerHTML = '<span class="spinner"></span> Setting up database...';

            try {
                const response = await fetch('install/migrate', {
                    method: 'POST'
                });
                const data = await response.json();

                if (data.success) {
                    showMessage('migration-message', data.message, 'success');

                    let progressHtml = '<p><strong>Migrations:</strong></p><ul class="check-list">';
                    data.migrations.forEach(m => {
                        progressHtml += `<li class="check-item passed">${m.migration}</li>`;
                    });
                    progressHtml += '</ul>';

                    progressHtml += '<p style="margin-top: 20px;"><strong>Seeders:</strong></p><ul class="check-list">';
                    data.seeders.forEach(s => {
                        progressHtml += `<li class="check-item passed">${s.seeder}</li>`;
                    });
                    progressHtml += '</ul>';

                    document.getElementById('migration-progress').innerHTML = progressHtml;

                    this.textContent = 'Next';
                    this.onclick = nextStep;
                } else {
                    showMessage('migration-message', data.message, 'error');
                    this.disabled = false;
                    this.textContent = 'Setup Database';
                }
            } catch (error) {
                showMessage('migration-message', 'Error: ' + error.message, 'error');
                this.disabled = false;
                this.textContent = 'Setup Database';
            }
        });

        // Step 4: SAML Configuration
        document.getElementById('saml-enabled').addEventListener('change', function() {
            const config = document.getElementById('saml-config');
            config.style.display = this.checked ? 'block' : 'none';

            if (this.checked) {
                // Generate SP metadata URLs
                const baseUrl = window.location.origin + window.location.pathname.replace('/install', '');
                document.getElementById('sp-entity-id').textContent = baseUrl;
                document.getElementById('sp-acs-url').textContent = baseUrl + '/saml/acs';
            }
        });

        document.getElementById('btn-save-saml').addEventListener('click', async function() {
            clearMessage('saml-message');

            const form = document.getElementById('saml-form');
            const formData = new FormData(form);
            formData.set('saml_enabled', document.getElementById('saml-enabled').checked);
            formData.set('entity_id', document.getElementById('sp-entity-id').textContent);
            formData.set('acs_url', document.getElementById('sp-acs-url').textContent);

            this.disabled = true;
            this.innerHTML = '<span class="spinner"></span> Saving...';

            try {
                const response = await fetch('install/saml', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    nextStep();
                } else {
                    showMessage('saml-message', data.message, 'error');
                }
            } catch (error) {
                showMessage('saml-message', 'Error: ' + error.message, 'error');
            } finally {
                this.disabled = false;
                this.textContent = 'Continue';
            }
        });

        // Step 5: Create Admin
        document.getElementById('btn-create-admin').addEventListener('click', async function() {
            clearMessage('admin-message');

            const form = document.getElementById('admin-form');
            const formData = new FormData(form);

            this.disabled = true;
            this.innerHTML = '<span class="spinner"></span> Creating...';

            try {
                const response = await fetch('install/admin', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    // Finalize installation
                    const finalResponse = await fetch('install/finalize', {
                        method: 'POST'
                    });
                    const finalData = await finalResponse.json();

                    if (finalData.success) {
                        nextStep();
                    } else {
                        showMessage('admin-message', finalData.message, 'error');
                    }
                } else {
                    showMessage('admin-message', data.message, 'error');
                    this.disabled = false;
                    this.textContent = 'Create Admin';
                }
            } catch (error) {
                showMessage('admin-message', 'Error: ' + error.message, 'error');
                this.disabled = false;
                this.textContent = 'Create Admin';
            }
        });

        // Step 6: Go to Login
        document.getElementById('btn-goto-login').addEventListener('click', function() {
            const baseUrl = window.location.origin + window.location.pathname.replace('/install', '');
            window.location.href = baseUrl + '/login';
        });
    </script>
</body>
</html>
