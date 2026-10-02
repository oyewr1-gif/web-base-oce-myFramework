<?php
/**
 * Controller Home
 * Halaman utama portal publik CMS
 * Murni mengonsumsi data dari REST API (api-info) tanpa akses langsung ke Database
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $api = new ApiClient();

        $postsResult = $api->getPosts(9, 1);
        $posts       = $postsResult['data'] ?? [];
        $categories  = $api->getCategories();
        $settings    = $api->getSettings();

        $this->view('home/index', [
            'pageTitle'   => ($settings['site_title'] ?? 'Modern PHP MVC CMS') . ' - Beranda',
            'siteTitle'   => $settings['site_title'] ?? 'Modern PHP MVC CMS',
            'siteTagline' => $settings['site_tagline'] ?? 'Framework Mandiri Cepat & Ringan',
            'footerText'  => $settings['footer_text'] ?? '© 2026 CMS Framework.',
            'posts'       => $posts,
            'recentPosts' => array_slice($posts, 0, 5),
            'categories'  => $categories
        ], 'layouts/frontend');
    }
}
