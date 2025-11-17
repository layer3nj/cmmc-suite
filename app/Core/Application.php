<?php

namespace App\Core;

use App\Core\Router;
use App\Core\Database;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

/**
 * Main Application Class
 */
class Application
{
    private Router $router;
    private Request $request;
    private Config $config;
    private ?Database $db = null;
    private bool $isInstalled;

    public function __construct(bool $isInstalled)
    {
        $this->isInstalled = $isInstalled;
        $this->config = new Config($isInstalled);
        $this->request = new Request();
        $this->router = new Router($this->request);

        // Initialize database if installed
        if ($isInstalled) {
            try {
                $this->db = new Database($this->config);

                // Load and share settings globally with all views
                $settingsService = new \App\Services\SettingsService($this->db);
                $settings = $settingsService->getAll();
                View::share('app_settings', $settings);
                View::share('app_name', $settings['site_name'] ?? 'CMMC Compliance Suite');
                View::share('app_logo', $settings['logo_path'] ?? null);
                View::share('primary_color', $settings['primary_color'] ?? '#667eea');
                View::share('secondary_color', $settings['secondary_color'] ?? '#764ba2');
            } catch (\Exception $e) {
                error_log('Database connection failed: ' . $e->getMessage());
            }
        }

        $this->registerRoutes();
    }

    public function run(): void
    {
        try {
            $response = $this->router->dispatch();

            if ($response instanceof Response) {
                $response->send();
            }
        } catch (\Exception $e) {
            error_log('Router dispatch error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function registerRoutes(): void
    {
        // Installer routes (always available)
        $this->router->get('/install', 'App\Controllers\InstallController@index');
        $this->router->post('/install/check', 'App\Controllers\InstallController@check');
        $this->router->post('/install/database', 'App\Controllers\InstallController@database');
        $this->router->post('/install/migrate', 'App\Controllers\InstallController@migrate');
        $this->router->post('/install/saml', 'App\Controllers\InstallController@saml');
        $this->router->post('/install/admin', 'App\Controllers\InstallController@admin');
        $this->router->post('/install/finalize', 'App\Controllers\InstallController@finalize');

        // Only register app routes if installed
        if ($this->isInstalled) {
            // Authentication routes
            $this->router->get('/login', 'App\Controllers\AuthController@showLogin');
            $this->router->post('/login', 'App\Controllers\AuthController@login');
            $this->router->get('/logout', 'App\Controllers\AuthController@logout');
            $this->router->post('/saml/acs', 'App\Controllers\SamlController@acs');
            $this->router->get('/saml/login', 'App\Controllers\SamlController@login');
            $this->router->get('/saml/metadata', 'App\Controllers\SamlController@metadata');

            // Home Dashboard with Quick Links
            $this->router->get('/', 'App\Controllers\HomeController@index');
            $this->router->get('/home/links', 'App\Controllers\HomeController@manageLinks');
            $this->router->post('/home/links/create', 'App\Controllers\HomeController@createLink');
            $this->router->post('/home/links/{id}/update', 'App\Controllers\HomeController@updateLink');
            $this->router->post('/home/links/{id}/delete', 'App\Controllers\HomeController@deleteLink');
            $this->router->post('/home/links/delete-multiple', 'App\Controllers\HomeController@deleteMultiple');

            // Compliance Dashboard
            $this->router->get('/dashboard', 'App\Controllers\DashboardController@index');
            $this->router->get('/compliance', 'App\Controllers\DashboardController@index');

            // Clients (multi-tenant) - New routes
            $this->router->get('/clients', 'App\Controllers\ClientController@index');
            $this->router->get('/clients/create', 'App\Controllers\ClientController@create');
            $this->router->post('/clients', 'App\Controllers\ClientController@store');
            $this->router->get('/clients/{id}', 'App\Controllers\ClientController@show');
            $this->router->get('/clients/{id}/edit', 'App\Controllers\ClientController@edit');
            $this->router->post('/clients/{id}', 'App\Controllers\ClientController@update');
            $this->router->post('/clients/{id}/delete', 'App\Controllers\ClientController@delete');
            $this->router->get('/clients/{id}/select', 'App\Controllers\ClientController@select');

            // Customers (multi-tenant) - Legacy routes (redirect to clients)
            $this->router->get('/customers', 'App\Controllers\ClientController@index');
            $this->router->get('/customers/create', 'App\Controllers\ClientController@create');
            $this->router->post('/customers', 'App\Controllers\ClientController@store');
            $this->router->get('/customers/{id}', 'App\Controllers\ClientController@show');
            $this->router->get('/customers/{id}/edit', 'App\Controllers\ClientController@edit');
            $this->router->post('/customers/{id}', 'App\Controllers\ClientController@update');
            $this->router->post('/customers/{id}/delete', 'App\Controllers\ClientController@delete');
            $this->router->get('/customers/{id}/select', 'App\Controllers\ClientController@select');

            // Controls
            $this->router->get('/controls', 'App\Controllers\ControlController@index');
            $this->router->get('/controls/{framework}/export-pdf', 'App\Controllers\ControlController@exportPdf');
            $this->router->get('/controls/{framework}', 'App\Controllers\ControlController@byFramework');
            $this->router->get('/controls/{framework}/{code}', 'App\Controllers\ControlController@show');
            $this->router->post('/controls/{framework}/{code}/update', 'App\Controllers\ControlController@update');

            // Assessments
            $this->router->get('/assessments', 'App\Controllers\AssessmentController@index');
            $this->router->get('/assessments/create', 'App\Controllers\AssessmentController@create');
            $this->router->post('/assessments', 'App\Controllers\AssessmentController@store');
            $this->router->get('/assessments/{id}', 'App\Controllers\AssessmentController@show');
            $this->router->post('/assessments/{id}/findings', 'App\Controllers\AssessmentController@updateFindings');
            $this->router->post('/assessments/{id}/publish', 'App\Controllers\AssessmentController@publish');
            $this->router->post('/assessments/{id}/delete', 'App\Controllers\AssessmentController@delete');

            // Risk Assessments
            $this->router->get('/risk-assessments', 'App\Controllers\RiskAssessmentController@index');
            $this->router->get('/risk-assessments/create', 'App\Controllers\RiskAssessmentController@create');
            $this->router->post('/risk-assessments', 'App\Controllers\RiskAssessmentController@store');
            $this->router->get('/risk-assessments/{id}/questionnaire', 'App\Controllers\RiskAssessmentController@questionnaire');
            $this->router->post('/risk-assessments/{id}/save-response', 'App\Controllers\RiskAssessmentController@saveResponse');
            $this->router->post('/risk-assessments/{id}/complete', 'App\Controllers\RiskAssessmentController@complete');
            $this->router->get('/risk-assessments/{id}/results', 'App\Controllers\RiskAssessmentController@results');
            $this->router->post('/risk-assessments/{id}/delete', 'App\Controllers\RiskAssessmentController@delete');

            // SPRS Scoring
            $this->router->get('/sprs', 'App\Controllers\SprsController@index');
            $this->router->get('/sprs/export', 'App\Controllers\SprsController@export');

            // POA&M
            $this->router->get('/poam', 'App\Controllers\PoamController@index');
            $this->router->get('/poam/create', 'App\Controllers\PoamController@create');
            $this->router->post('/poam', 'App\Controllers\PoamController@store');
            $this->router->get('/poam/{id}', 'App\Controllers\PoamController@show');
            $this->router->get('/poam/{id}/edit', 'App\Controllers\PoamController@edit');
            $this->router->post('/poam/{id}', 'App\Controllers\PoamController@update');
            $this->router->post('/poam/{id}/delete', 'App\Controllers\PoamController@delete');
            $this->router->post('/poam/generate', 'App\Controllers\PoamController@generate');
            $this->router->get('/poam/export/csv', 'App\Controllers\PoamController@exportCsv');
            $this->router->get('/poam/export/pdf', 'App\Controllers\PoamController@exportPdf');

            // Reports
            $this->router->get('/reports', 'App\Controllers\ReportController@index');
            $this->router->get('/reports/cmmc', 'App\Controllers\ReportController@cmmc');
            $this->router->get('/reports/nist', 'App\Controllers\ReportController@nist');
            $this->router->get('/reports/stig', 'App\Controllers\ReportController@stig');
            $this->router->get('/reports/sprs', 'App\Controllers\ReportController@sprs');
            $this->router->post('/reports/generate', 'App\Controllers\ReportController@generate');

            // Documents/Evidence
            $this->router->get('/documents', 'App\Controllers\DocumentController@index');
            $this->router->post('/documents/upload', 'App\Controllers\DocumentController@upload');
            $this->router->get('/documents/{id}/download', 'App\Controllers\DocumentController@download');
            $this->router->post('/documents/{id}/delete', 'App\Controllers\DocumentController@delete');

            // Policy Templates (Global)
            $this->router->get('/policy-templates', 'App\Controllers\PolicyController@index');
            $this->router->get('/policy-templates/create', 'App\Controllers\PolicyController@create');
            $this->router->post('/policy-templates', 'App\Controllers\PolicyController@store');
            $this->router->get('/policy-templates/{id}', 'App\Controllers\PolicyController@show');
            $this->router->get('/policy-templates/{id}/edit', 'App\Controllers\PolicyController@edit');
            $this->router->post('/policy-templates/{id}', 'App\Controllers\PolicyController@update');
            $this->router->post('/policy-templates/{id}/delete', 'App\Controllers\PolicyController@delete');

            // Client Policies (Customer-Specific)
            $this->router->get('/policies', 'App\Controllers\ClientPolicyController@index');
            $this->router->get('/policies/create', 'App\Controllers\ClientPolicyController@create');
            $this->router->post('/policies', 'App\Controllers\ClientPolicyController@store');
            $this->router->post('/policies/copy-template', 'App\Controllers\ClientPolicyController@copyFromTemplate');
            $this->router->post('/policies/{id}/submit-review', 'App\Controllers\ClientPolicyController@submitForReview');
            $this->router->post('/policies/{id}/approve', 'App\Controllers\ClientPolicyController@approve');

            // Integrations
            $this->router->get('/integrations', 'App\Controllers\IntegrationController@index');
            $this->router->post('/integrations/autotask/connect', 'App\Controllers\IntegrationController@autotaskConnect');
            $this->router->post('/integrations/autotask/sync', 'App\Controllers\IntegrationController@autotaskSync');
            $this->router->post('/integrations/itglue/connect', 'App\Controllers\IntegrationController@itglueConnect');
            $this->router->post('/integrations/itglue/sync', 'App\Controllers\IntegrationController@itglueSync');
            $this->router->get('/integrations/client-mapping', 'App\Controllers\IntegrationController@clientMapping');
            $this->router->post('/integrations/fetch-clients', 'App\Controllers\IntegrationController@fetchClients');
            $this->router->post('/integrations/save-mappings', 'App\Controllers\IntegrationController@saveMappings');
            $this->router->post('/integrations/sync-mapped-clients', 'App\Controllers\IntegrationController@syncMappedClients');

            // Bulk Operations
            $this->router->get('/bulk', 'App\Controllers\BulkOperationsController@index');
            $this->router->post('/bulk/clone-assessment', 'App\Controllers\BulkOperationsController@cloneAssessment');
            $this->router->post('/bulk/batch-update-poam', 'App\Controllers\BulkOperationsController@batchUpdatePoam');
            $this->router->get('/bulk/get-poam-items', 'App\Controllers\BulkOperationsController@getPoamItems');

            // Admin
            $this->router->get('/admin', 'App\Controllers\AdminController@index');
            $this->router->get('/admin/users', 'App\Controllers\AdminController@users');
            $this->router->post('/admin/users', 'App\Controllers\AdminController@createUser');
            $this->router->post('/admin/users/{id}/update', 'App\Controllers\AdminController@updateUser');
            $this->router->post('/admin/users/{id}/delete', 'App\Controllers\AdminController@deleteUser');
            $this->router->get('/admin/settings', 'App\Controllers\AdminController@settings');
            $this->router->post('/admin/settings', 'App\Controllers\AdminController@updateSettings');
            $this->router->get('/admin/audit-log', 'App\Controllers\AdminController@auditLog');
            $this->router->get('/admin/import', 'App\Controllers\AdminController@showImport');
            $this->router->post('/admin/import', 'App\Controllers\AdminController@import');
            $this->router->post('/admin/run-migrations', 'App\Controllers\AdminController@runMigrations');
            $this->router->post('/admin/run-seeders', 'App\Controllers\AdminController@runSeeders');
            $this->router->post('/admin/update-sprs-scores', 'App\Controllers\AdminController@updateSprsScores');
            $this->router->post('/admin/update-categories', 'App\Controllers\AdminController@updateCategories');

            // Admin Dashboard - Customers Overview
            $this->router->get('/admin/customers', 'App\Controllers\AdminDashboardController@customersOverview');
            $this->router->get('/admin/customers/search', 'App\Controllers\AdminDashboardController@searchCustomers');
        }
    }

    public function getDatabase(): ?Database
    {
        return $this->db;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
