<?php
/**
 * Controller Home
 * Halaman utama portal publik CMS
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $postModel = $this->model('Post');
        $catModel = $this->model('Category');
        $settingModel = $this->model('Setting');

        $posts = $postModel->allWithRelations('published', 9);
        $categories = $catModel->allWithCount();
        $settings = $settingModel->allAsKeyVal();

        $this->view('home/index', [
            'pageTitle'   => ($settings['site_title'] ?? 'Modern PHP MVC CMS') . ' - Beranda',
            'siteTitle'   => $settings['site_title'] ?? 'Modern PHP MVC CMS',
            'siteTagline' => $settings['site_tagline'] ?? 'Framework Mandiri Cepat & Ringan',
            'footerText'  => $settings['footer_text'] ?? '© 2026 CMS Framework.',
            'posts'       => $posts,
            'categories'  => $categories
        ], 'layouts/frontend');
    }
}
