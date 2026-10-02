<?php
/**
 * Controller Post (Publik)
 * Menampilkan detail artikel dan filter per kategori
 * Murni mengonsumsi data dari REST API (api-info) tanpa akses langsung ke Database
 */
class PostController extends Controller
{
    public function read(string $slug = ''): void
    {
        if (empty($slug)) {
            Response::notFound("Artikel tidak ditentukan.");
            return;
        }

        $api = new ApiClient();
        $post = $api->getPost($slug);

        if (!$post || ($post['status'] ?? '') !== 'published') {
            Response::notFound("Artikel tidak ditemukan atau belum dipublikasikan.");
            return;
        }

        $categories = $api->getCategories();
        $settings   = $api->getSettings();

        $this->view('post/read', [
            'pageTitle'   => $post['title'] . ' - ' . ($settings['site_title'] ?? 'CMS'),
            'siteTitle'   => $settings['site_title'] ?? 'CMS',
            'siteLogo'    => $settings['site_logo'] ?? '',
            'footerText'  => $settings['footer_text'] ?? '© 2026 CMS Framework.',
            'post'        => $post,
            'categories'  => $categories
        ], 'layouts/frontend');
    }

    public function category(string $slug = ''): void
    {
        if (empty($slug)) {
            $this->redirect('');
            return;
        }

        $api = new ApiClient();
        $category = $api->getCategory($slug);

        if (!$category) {
            Response::notFound("Kategori tidak ditemukan.");
            return;
        }

        $postsResult = $api->getPosts(12, 1, $slug);
        $posts       = $postsResult['data'] ?? [];
        $categories  = $api->getCategories();
        $settings    = $api->getSettings();

        $this->view('home/index', [
            'pageTitle'      => 'Kategori: ' . $category['name'] . ' - ' . ($settings['site_title'] ?? 'CMS'),
            'siteTitle'      => $settings['site_title'] ?? 'CMS',
            'siteTagline'    => 'Artikel dalam kategori: ' . $category['name'],
            'siteLogo'       => $settings['site_logo'] ?? '',
            'footerText'     => $settings['footer_text'] ?? '© 2026 CMS Framework.',
            'posts'          => $posts,
            'categories'     => $categories,
            'activeCategory' => $category
        ], 'layouts/frontend');
    }
}
