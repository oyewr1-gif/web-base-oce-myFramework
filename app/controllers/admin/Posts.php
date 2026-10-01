<?php
/**
 * Controller Admin Posts
 * Mengelola CRUD Artikel dan Halaman
 */
class PostsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index(): void
    {
        $postModel = $this->model('Post');
        $posts = $postModel->allWithRelations();

        $this->view('admin/posts/index', [
            'pageTitle' => 'Manajemen Artikel',
            'posts'     => $posts
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $catModel = $this->model('Category');
        $categories = $catModel->all('name ASC');

        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('admin/posts/create');
                return;
            }

            $title = trim((string)$this->request->post('title'));
            $slug  = trim((string)$this->request->post('slug'));
            $content = (string)$this->request->post('content');
            $excerpt = trim((string)$this->request->post('excerpt'));
            $categoryId = (int)$this->request->post('category_id');
            $status = in_array($this->request->post('status'), ['draft', 'published']) ? $this->request->post('status') : 'draft';

            if (empty($title) || empty($content)) {
                flash('error', 'Judul dan konten artikel wajib diisi.');
                $this->redirect('admin/posts/create');
                return;
            }

            if (empty($slug)) {
                $slug = slugify($title);
            } else {
                $slug = slugify($slug);
            }

            // Cek keunikan slug
            $postModel = $this->model('Post');
            if ($postModel->findBySlugWithRelations($slug)) {
                $slug = $slug . '-' . time();
            }

            // Handle thumbnail upload jika ada
            $thumbnailPath = null;
            $thumbFile = $this->request->file('thumbnail');
            if ($thumbFile && $thumbFile['error'] === UPLOAD_ERR_OK) {
                $mediaModel = $this->model('Media');
                try {
                    $media = $mediaModel->upload($thumbFile);
                    if ($media) {
                        $thumbnailPath = $media['filename'];
                    }
                } catch (Exception $e) {
                    flash('error', 'Gagal upload thumbnail: ' . $e->getMessage());
                    $this->redirect('admin/posts/create');
                    return;
                }
            }

            $currentUser = current_user();

            $data = [
                'user_id'     => $currentUser['id'] ?? 1,
                'category_id' => $categoryId > 0 ? $categoryId : null,
                'title'       => $title,
                'slug'        => $slug,
                'excerpt'     => $excerpt ?: substr(strip_tags($content), 0, 150) . '...',
                'content'     => $content,
                'thumbnail'   => $thumbnailPath,
                'status'      => $status,
                'views'       => 0
            ];

            $postModel->insert($data);
            flash('success', 'Artikel baru berhasil disimpan!');
            $this->redirect('admin/posts');
            return;
        }

        $this->view('admin/posts/create', [
            'pageTitle'  => 'Tulis Artikel Baru',
            'categories' => $categories
        ], 'layouts/admin');
    }

    public function edit(string $id = ''): void
    {
        $id = (int)$id;
        $postModel = $this->model('Post');
        $post = $postModel->find($id);

        if (!$post) {
            flash('error', 'Artikel tidak ditemukan.');
            $this->redirect('admin/posts');
            return;
        }

        $catModel = $this->model('Category');
        $categories = $catModel->all('name ASC');

        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('admin/posts/edit/' . $id);
                return;
            }

            $title = trim((string)$this->request->post('title'));
            $slug  = trim((string)$this->request->post('slug'));
            $content = (string)$this->request->post('content');
            $excerpt = trim((string)$this->request->post('excerpt'));
            $categoryId = (int)$this->request->post('category_id');
            $status = in_array($this->request->post('status'), ['draft', 'published']) ? $this->request->post('status') : 'draft';

            if (empty($title) || empty($content)) {
                flash('error', 'Judul dan konten artikel wajib diisi.');
                $this->redirect('admin/posts/edit/' . $id);
                return;
            }

            $slug = empty($slug) ? slugify($title) : slugify($slug);

            // Handle upload thumbnail baru jika diunggah
            $thumbnailPath = $post['thumbnail'];
            $thumbFile = $this->request->file('thumbnail');
            if ($thumbFile && $thumbFile['error'] === UPLOAD_ERR_OK) {
                $mediaModel = $this->model('Media');
                try {
                    $media = $mediaModel->upload($thumbFile);
                    if ($media) {
                        $thumbnailPath = $media['filename'];
                    }
                } catch (Exception $e) {
                    flash('error', 'Gagal upload thumbnail: ' . $e->getMessage());
                    $this->redirect('admin/posts/edit/' . $id);
                    return;
                }
            }

            $updateData = [
                'category_id' => $categoryId > 0 ? $categoryId : null,
                'title'       => $title,
                'slug'        => $slug,
                'excerpt'     => $excerpt ?: substr(strip_tags($content), 0, 150) . '...',
                'content'     => $content,
                'thumbnail'   => $thumbnailPath,
                'status'      => $status
            ];

            $postModel->update($id, $updateData);
            flash('success', 'Perubahan artikel berhasil disimpan!');
            $this->redirect('admin/posts');
            return;
        }

        $this->view('admin/posts/edit', [
            'pageTitle'  => 'Edit Artikel: ' . $post['title'],
            'post'       => $post,
            'categories' => $categories
        ], 'layouts/admin');
    }

    public function delete(string $id = ''): void
    {
        $id = (int)$id;
        $postModel = $this->model('Post');
        $post = $postModel->find($id);

        if ($post) {
            $postModel->delete($id);
            flash('success', 'Artikel "' . $post['title'] . '" berhasil dihapus.');
        } else {
            flash('error', 'Artikel tidak ditemukan.');
        }

        $this->redirect('admin/posts');
    }
}
