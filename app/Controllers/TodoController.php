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
 * To-Do List Controller
 */
class TodoController
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

        Session::start();

        // Get all customers and their todos for grid view
        $customers = $this->db->fetchAll(
            "SELECT * FROM customers WHERE active = 1 ORDER BY name ASC"
        );

        // Get all todos
        $allTodos = $this->db->fetchAll(
            "SELECT t.*, c.name as customer_name
             FROM todos t
             JOIN customers c ON t.customer_id = c.id
             ORDER BY
                CASE t.priority
                    WHEN 'high' THEN 1
                    WHEN 'medium' THEN 2
                    WHEN 'low' THEN 3
                END,
                t.due_date ASC"
        );

        // Organize todos by customer
        $todosByCustomer = [];
        foreach ($customers as $customer) {
            $todosByCustomer[$customer['id']] = [
                'customer' => $customer,
                'todos' => array_filter($allTodos, function($todo) use ($customer) {
                    return $todo['customer_id'] == $customer['id'];
                })
            ];
        }

        // Get statistics
        $stats = [
            'total' => count($allTodos),
            'pending' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM todos WHERE status = 'pending'"
            ),
            'in_progress' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM todos WHERE status = 'in_progress'"
            ),
            'completed' => $this->db->fetchColumn(
                "SELECT COUNT(*) FROM todos WHERE status = 'completed'"
            ),
        ];

        $content = View::render('todos/index', [
            'todosByCustomer' => $todosByCustomer,
            'customers' => $customers,
            'stats' => $stats,
        ]);

        return new Response($content);
    }

    public function create(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        // Get all customers for dropdown
        $customers = $this->db->fetchAll(
            "SELECT * FROM customers WHERE active = 1 ORDER BY name ASC"
        );

        $content = View::render('todos/create', [
            'customers' => $customers,
        ]);
        return new Response($content);
    }

    public function store(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/todos/create');
        }

        $data = [
            'customer_id' => (int)$request->post('customer_id'),
            'title' => trim($request->post('title')),
            'description' => trim($request->post('description')),
            'status' => 'pending',
            'priority' => $request->post('priority') ?: 'medium',
            'due_date' => $request->post('due_date') ?: null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $todoId = $this->db->insert('todos', $data);

        AuditLogger::log('create', 'todo', $todoId, [
            'title' => $data['title'],
            'customer_id' => $data['customer_id']
        ], $request->ip());

        Session::flash('success', 'To-Do item created successfully.');
        return Response::redirect($request->baseUrl() . '/todos');
    }

    public function show(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        Session::start();

        $item = $this->db->fetchOne(
            "SELECT t.*, c.name as customer_name
             FROM todos t
             JOIN customers c ON t.customer_id = c.id
             WHERE t.id = ?",
            [$id]
        );

        if (!$item) {
            return new Response('To-Do item not found', 404);
        }

        // Get all customers for the edit form
        $customers = $this->db->fetchAll(
            "SELECT * FROM customers WHERE active = 1 ORDER BY name ASC"
        );

        $content = View::render('todos/show', [
            'item' => $item,
            'customers' => $customers,
        ]);

        return new Response($content);
    }

    public function update(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/todos/' . $id);
        }

        $item = $this->db->fetchOne("SELECT * FROM todos WHERE id = ?", [$id]);

        if (!$item) {
            return new Response('To-Do item not found', 404);
        }

        $data = [
            'customer_id' => (int)$request->post('customer_id'),
            'title' => trim($request->post('title')),
            'description' => trim($request->post('description')),
            'status' => $request->post('status'),
            'priority' => $request->post('priority'),
            'due_date' => $request->post('due_date') ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Set completed_at if status changed to completed
        if ($data['status'] === 'completed' && $item['status'] !== 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        } elseif ($data['status'] !== 'completed') {
            $data['completed_at'] = null;
        }

        $this->db->update('todos', $data, 'id = :id', [':id' => $id]);

        AuditLogger::logChange('todo', $id, $item, $data, $request->ip());

        Session::flash('success', 'To-Do item updated successfully.');
        return Response::redirect($request->baseUrl() . '/todos');
    }

    public function delete(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $this->db->delete('todos', 'id = :id', [':id' => $id]);

        AuditLogger::log('delete', 'todo', $id, null, $request->ip());

        Session::flash('success', 'To-Do item deleted successfully.');
        return Response::redirect($request->baseUrl() . '/todos');
    }

    public function updateStatus(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $permCheck = AuthMiddleware::requireRole('contributor');
        if ($permCheck) return $permCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $status = $request->post('status');

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Set completed_at if status is completed
        if ($status === 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        } else {
            $data['completed_at'] = null;
        }

        $this->db->update('todos', $data, 'id = :id', [':id' => $id]);

        return Response::json(['success' => true, 'message' => 'Status updated successfully']);
    }
}
