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

            // Dashboard
            $this->router->get('/', 'App\Controllers\DashboardController@index');
            $this->router->get('/dashboard', 'App\Controllers\DashboardController@index');

            // Customers (multi-tenant)
            $this->router->get('/customers', 'App\Controllers\CustomerController@index');
            $this->router->get('/customers/create', 'App\Controllers\CustomerController@create');
            $this->router->post('/customers', 'App\Controllers\CustomerController@store');
            $this->router->get('/customers/{id}', 'App\Controllers\CustomerController@show');
            $this->router->get('/customers/{id}/edit', 'App\Controllers\CustomerController@edit');
            $this->router->post('/customers/{id}', 'App\Controllers\CustomerController@update');
            $this->router->post('/customers/{id}/delete', 'App\Controllers\CustomerController@delete');
            $this->router->get('/customers/{id}/select', 'App\Controllers\CustomerController@select');

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
            $this->router->get('/assessments/{id}/export-pdf', 'App\Controllers\AssessmentController@exportPdf');
            $this->router->post('/assessments/{id}/findings', 'App\Controllers\AssessmentController@updateFindings');
            $this->router->post('/assessments/{id}/publish', 'App\Controllers\AssessmentController@publish');
            $this->router->post('/assessments/{id}/delete', 'App\Controllers\AssessmentController@delete');

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

            // Integrations
            $this->router->get('/integrations', 'App\Controllers\IntegrationController@index');
            $this->router->post('/integrations/autotask/connect', 'App\Controllers\IntegrationController@autotaskConnect');
            $this->router->post('/integrations/autotask/sync', 'App\Controllers\IntegrationController@autotaskSync');
            $this->router->post('/integrations/itglue/connect', 'App\Controllers\IntegrationController@itglueConnect');
            $this->router->post('/integrations/itglue/sync', 'App\Controllers\IntegrationController@itglueSync');

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
