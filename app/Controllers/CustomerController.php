<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Core\Csrf;
use App\Middleware\AuthMiddleware;
use App\Services\AuditLogger;

/**
 * Customer Controller - Multi-tenant Management
 */
class CustomerController
{
    private $db;

    public function __construct()
    {
        global $app;
        $this->db = $app->getDatabase();
    }

    public function index(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $customers = $this->db->fetchAll(
            "SELECT * FROM customers WHERE active = 1 ORDER BY name ASC"
        );

        $content = View::render('customers/index', [
            'customers' => $customers,
        ]);

        return new Response($content);
    }

    public function create(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $content = View::render('customers/create');
        return new Response($content);
    }

    public function store(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/customers/create');
        }

        $name = trim($request->post('name'));
        $contactEmail = trim($request->post('contact_email'));
        $contactPhone = trim($request->post('contact_phone'));
        $address = trim($request->post('address'));

        if (empty($name)) {
            Session::flash('error', 'Customer name is required.');
            return Response::redirect($request->baseUrl() . '/customers/create');
        }

        $customerId = $this->db->insert('customers', [
            'name' => $name,
            'contact_email' => $contactEmail,
            'contact_phone' => $contactPhone,
            'address' => $address,
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('create', 'customer', $customerId, [
            'name' => $name
        ], $request->ip());

        Session::flash('success', 'Customer created successfully.');
        return Response::redirect($request->baseUrl() . '/customers/' . $customerId);
    }

    public function show(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $customer = $this->db->fetchOne(
            "SELECT * FROM customers WHERE id = ?",
            [$id]
        );

        if (!$customer) {
            return new Response('Customer not found', 404);
        }

        // Get customer statistics
        $stats = [
            'assessments_count' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM assessments WHERE customer_id = ?",
                [$id]
            ),
            'poam_count' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM poam_items WHERE customer_id = ? AND status IN ('open', 'in_progress')",
                [$id]
            ),
            'documents_count' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM documents WHERE customer_id = ?",
                [$id]
            ),
        ];

        $content = View::render('customers/show', [
            'customer' => $customer,
            'stats' => $stats,
        ]);

        return new Response($content);
    }

    public function edit(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $customer = $this->db->fetchOne(
            "SELECT * FROM customers WHERE id = ?",
            [$id]
        );

        if (!$customer) {
            return new Response('Customer not found', 404);
        }

        $content = View::render('customers/edit', [
            'customer' => $customer,
        ]);

        return new Response($content);
    }

    public function update(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/customers/' . $id . '/edit');
        }

        $customer = $this->db->fetchOne(
            "SELECT * FROM customers WHERE id = ?",
            [$id]
        );

        if (!$customer) {
            return new Response('Customer not found', 404);
        }

        $data = [
            'name' => trim($request->post('name')),
            'contact_email' => trim($request->post('contact_email')),
            'contact_phone' => trim($request->post('contact_phone')),
            'address' => trim($request->post('address')),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->update('customers', $data, 'id = :id', [':id' => $id]);

        AuditLogger::logChange('customer', $id, $customer, $data, $request->ip());

        Session::flash('success', 'Customer updated successfully.');
        return Response::redirect($request->baseUrl() . '/customers/' . $id);
    }

    public function delete(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Soft delete - mark as inactive
        $this->db->update('customers', ['active' => 0], 'id = :id', [':id' => $id]);

        AuditLogger::log('delete', 'customer', $id, null, $request->ip());

        Session::flash('success', 'Customer deleted successfully.');
        return Response::redirect($request->baseUrl() . '/customers');
    }

    public function select(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();

        $customer = $this->db->fetchOne(
            "SELECT * FROM customers WHERE id = ? AND active = 1",
            [$id]
        );

        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            return Response::redirect($request->baseUrl() . '/customers');
        }

        Session::set('current_customer_id', $id);
        Session::set('current_customer_name', $customer['name']);

        Session::flash('success', 'Switched to ' . $customer['name']);
        return Response::redirect($request->baseUrl() . '/');
    }
}
