<?php
/**
 * Controller Dashboard REST API
 * Menyediakan agregasi metrik dan ringkasan untuk admin CMS
 */
class DashboardController extends ApiController
{
    public function index(): void
    {
        $this->stats();
    }

    public function stats(): void
    {
        $this->requireAuth();

        $postModel = new ApiPost();
        $catModel = new ApiCategory();
        $mediaModel = new ApiMedia();
        $userModel = new ApiUser();

        $stats = [
            'total_posts'      => $postModel->countAll(),
            'published_posts'  => $postModel->countPublished(),
            'total_categories' => count($catModel->all()),
            'total_media'      => count($mediaModel->all()),
            'total_users'      => count($userModel->all()),
        ];

        $recentPosts = $postModel->allWithRelations(null, 5, 0);

        ApiResponse::success([
            'stats'        => $stats,
            'recent_posts' => $recentPosts
        ], "Statistik dashboard berhasil diambil.");
    }
}
