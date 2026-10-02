<?php
/**
 * Controller Admin Posts (Headless REST API Client)
 * Mengelola CRUD Artikel dan Halaman via REST API (api-info)
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
        $api = new ApiClient();
        $posts = $api->getAdminPosts();

        $this->view('admin/posts/index', [
            'pageTitle' => 'Manajemen Artikel',
            'posts'     => $posts
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $api = new ApiClient();
        $categories = $api->getCategories();

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

            $slug = empty($slug) ? slugify($title) : slugify($slug);

            // Handle upload thumbnail via API jika ada file
            $thumbnailPath = null;
            $thumbFile = $this->request->file('thumbnail');
            if ($thumbFile && $thumbFile['error'] === UPLOAD_ERR_OK) {
                $uploadRes = $api->uploadMedia($thumbFile);
                if ($uploadRes && !empty($uploadRes['filename'])) {
                    $thumbnailPath = $uploadRes['filename'];
                }
            }

            $data = [
                'category_id' => $categoryId > 0 ? $categoryId : null,
                'title'       => $title,
                'slug'        => $slug,
                'excerpt'     => $excerpt ?: substr(strip_tags($content), 0, 150) . '...',
                'content'     => $content,
                'thumbnail'   => $thumbnailPath,
                'status'      => $status
            ];

            $created = $api->createPost($data);
            if ($created) {
                flash('success', 'Artikel baru berhasil disimpan!');
                $this->redirect('admin/posts');
                return;
            } else {
                flash('error', $api->getLastError() ?: 'Gagal menyimpan artikel baru.');
                $this->redirect('admin/posts/create');
                return;
            }
        }

        $this->view('admin/posts/create', [
            'pageTitle'  => 'Tulis Artikel Baru',
            'categories' => $categories
        ], 'layouts/admin');
    }

    public function edit(string $id = ''): void
    {
        $id = (int)$id;
        $api = new ApiClient();
        $post = $api->getPost((string)$id, false);

        if (!$post) {
            flash('error', 'Artikel tidak ditemukan.');
            $this->redirect('admin/posts');
            return;
        }

        $categories = $api->getCategories();

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
            $thumbnailPath = $post['thumbnail'] ?? null;
            $thumbFile = $this->request->file('thumbnail');
            if ($thumbFile && $thumbFile['error'] === UPLOAD_ERR_OK) {
                $uploadRes = $api->uploadMedia($thumbFile);
                if ($uploadRes && !empty($uploadRes['filename'])) {
                    $thumbnailPath = $uploadRes['filename'];
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

            $updated = $api->updatePost($id, $updateData);
            if ($updated) {
                flash('success', 'Perubahan artikel berhasil disimpan!');
                $this->redirect('admin/posts');
                return;
            } else {
                flash('error', $api->getLastError() ?: 'Gagal memperbarui artikel.');
                $this->redirect('admin/posts/edit/' . $id);
                return;
            }
        }

        $this->view('admin/posts/edit', [
            'pageTitle'  => 'Edit Artikel: ' . ($post['title'] ?? ''),
            'post'       => $post,
            'categories' => $categories
        ], 'layouts/admin');
    }

    public function delete(string $id = ''): void
    {
        $id = (int)$id;
        $api = new ApiClient();

        if ($id > 0 && $api->deletePost($id)) {
            flash('success', 'Artikel berhasil dihapus.');
        } else {
            flash('error', $api->getLastError() ?: 'Gagal menghapus artikel.');
        }

        $this->redirect('admin/posts');
    }
}
