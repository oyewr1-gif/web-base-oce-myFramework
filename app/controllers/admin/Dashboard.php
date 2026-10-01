<?php
/**
 * Controller Admin Dashboard
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
        $postModel = $this->model('Post');
        $catModel = $this->model('Category');
        $mediaModel = $this->model('Media');
        $userModel = $this->model('User');

        $stats = [
            'total_posts'      => $postModel->count(),
            'published_posts'  => $postModel->count("status = 'published'"),
            'total_categories' => $catModel->count(),
            'total_media'      => $mediaModel->count(),
            'total_users'      => $userModel->count(),
        ];

        $recentPosts = $postModel->allWithRelations(null, 5);

        $this->view('admin/dashboard/index', [
            'pageTitle'   => 'Ringkasan Dashboard',
            'stats'       => $stats,
            'recentPosts' => $recentPosts
        ], 'layouts/admin');
    }
}
