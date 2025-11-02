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
 * Home Controller - Dashboard Homepage with Quick Links
 */
class HomeController
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
        $currentClient = Session::get('current_customer_name');
        $currentClientId = Session::get('current_customer_id');

        // Get quick links
        $quickLinks = $this->db->fetchAll(
            "SELECT * FROM quick_links WHERE is_active = 1 ORDER BY display_order ASC, title ASC"
        );

        // Get recent activity summary if client is selected
        $recentActivity = [];
        if ($currentClientId) {
            $recentActivity = [
                'recent_assessments' => $this->db->fetchAll(
                    "SELECT * FROM assessments WHERE customer_id = ? ORDER BY created_at DESC LIMIT 3",
                    [$currentClientId]
                ),
                'open_poam_count' => $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM poam_items WHERE customer_id = ? AND status IN ('open', 'in_progress')",
                    [$currentClientId]
                ),
                'total_documents' => $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM documents WHERE customer_id = ?",
                    [$currentClientId]
                ),
            ];
        }

        $content = View::render('home/index', [
            'quick_links' => $quickLinks,
            'current_client' => $currentClient,
            'current_client_id' => $currentClientId,
            'recent_activity' => $recentActivity,
        ]);

        return new Response($content);
    }

    public function manageLinks(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        $links = $this->db->fetchAll(
            "SELECT * FROM quick_links ORDER BY display_order ASC, title ASC"
        );

        $content = View::render('home/manage_links', [
            'links' => $links,
        ]);

        return new Response($content);
    }

    public function createLink(Request $request): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/home/links');
        }

        $data = [
            'title' => trim($request->post('title')),
            'url' => trim($request->post('url')),
            'description' => trim($request->post('description')),
            'icon' => trim($request->post('icon')) ?: '🔗',
            'display_order' => (int)$request->post('display_order', 0),
            'is_active' => $request->post('is_active', '1') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $linkId = $this->db->insert('quick_links', $data);

        AuditLogger::log('create', 'quick_link', $linkId, [
            'title' => $data['title'],
            'url' => $data['url'],
        ], $request->ip());

        Session::flash('success', 'Quick link created successfully.');
        return Response::redirect($request->baseUrl() . '/home/links');
    }

    public function updateLink(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            Session::flash('error', 'Invalid security token.');
            return Response::redirect($request->baseUrl() . '/home/links');
        }

        $data = [
            'title' => trim($request->post('title')),
            'url' => trim($request->post('url')),
            'description' => trim($request->post('description')),
            'icon' => trim($request->post('icon')) ?: '🔗',
            'display_order' => (int)$request->post('display_order', 0),
            'is_active' => $request->post('is_active', '1') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->update('quick_links', $data, 'id = :id', [':id' => $id]);

        AuditLogger::log('update', 'quick_link', $id, $data, $request->ip());

        Session::flash('success', 'Quick link updated successfully.');
        return Response::redirect($request->baseUrl() . '/home/links');
    }

    public function deleteLink(Request $request, string $id): Response
    {
        $authCheck = AuthMiddleware::handle($request);
        if ($authCheck) return $authCheck;

        $roleCheck = AuthMiddleware::requireRole('admin');
        if ($roleCheck) return $roleCheck;

        Session::start();

        if (!Csrf::validate($request)) {
            return Response::json(['success' => false, 'message' => 'Invalid security token']);
        }

        $this->db->query("DELETE FROM quick_links WHERE id = ?", [$id]);

        AuditLogger::log('delete', 'quick_link', $id, null, $request->ip());

        Session::flash('success', 'Quick link deleted successfully.');
        return Response::redirect($request->baseUrl() . '/home/links');
    }
}
