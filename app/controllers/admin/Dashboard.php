<?php
/**
 * Controller Admin Dashboard (Headless REST API Client)
 * Mengambil ringkasan metrik dan artikel terbaru via REST API
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index(): void
    {
        $api = new ApiClient();
        $dashboardData = $api->getDashboardStats();

        $stats = $dashboardData['stats'] ?? [
            'total_posts'      => 0,
            'published_posts'  => 0,
            'total_categories' => 0,
            'total_media'      => 0,
            'total_users'      => 0
        ];

        $recentPosts = $dashboardData['recent_posts'] ?? [];

        $this->view('admin/dashboard/index', [
            'pageTitle'   => 'Ringkasan Dashboard',
            'stats'       => $stats,
            'recentPosts' => $recentPosts
        ], 'layouts/admin');
    }
}
