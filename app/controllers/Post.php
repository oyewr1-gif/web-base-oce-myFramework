<?php
/**
 * Controller Post (Publik)
 * Menampilkan detail artikel dan filter per kategori
 */
class PostController extends Controller
{
    public function read(string $slug = ''): void
    {
        if (empty($slug)) {
            Response::notFound("Artikel tidak ditentukan.");
            return;
        }

        $postModel = $this->model('Post');
        $settingModel = $this->model('Setting');
        $catModel = $this->model('Category');

        $post = $postModel->findBySlugWithRelations($slug);

        if (!$post || $post['status'] !== 'published') {
            Response::notFound("Artikel tidak ditemukan atau belum dipublikasikan.");
            return;
        }

        // Naikkan hit counter
        $postModel->incrementViews((int)$post['id']);

        $settings = $settingModel->allAsKeyVal();
        $categories = $catModel->allWithCount();

        $this->view('post/read', [
            'pageTitle'   => $post['title'] . ' - ' . ($settings['site_title'] ?? 'CMS'),
            'siteTitle'   => $settings['site_title'] ?? 'CMS',
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

        $catModel = $this->model('Category');
        $postModel = $this->model('Post');
        $settingModel = $this->model('Setting');

        $category = $catModel->findBySlug($slug);
        if (!$category) {
            Response::notFound("Kategori tidak ditemukan.");
            return;
        }

        $posts = $postModel->getByCategory((int)$category['id']);
        $categories = $catModel->allWithCount();
        $settings = $settingModel->allAsKeyVal();

        $this->view('home/index', [
            'pageTitle'   => 'Kategori: ' . $category['name'] . ' - ' . ($settings['site_title'] ?? 'CMS'),
            'siteTitle'   => $settings['site_title'] ?? 'CMS',
            'siteTagline' => 'Artikel dalam kategori: ' . $category['name'],
            'footerText'  => $settings['footer_text'] ?? '© 2026 CMS Framework.',
            'posts'       => $posts,
            'categories'  => $categories,
            'activeCategory' => $category
        ], 'layouts/frontend');
    }
}
