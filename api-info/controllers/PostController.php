<?php
/**
 * Controller Post REST API
 */
class PostController extends ApiController
{
    public function index(): void
    {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;
        $category = $_GET['category'] ?? ($_GET['category_slug'] ?? ($_GET['category_id'] ?? null));
        $isAdmin = !empty($_GET['admin']) || !empty($_GET['all']);

        $postModel = new ApiPost();

        if ($isAdmin) {
            $this->requireAuth();
            $status = isset($_GET['status']) && in_array($_GET['status'], ['draft', 'published']) ? $_GET['status'] : null;
            $posts = $postModel->allWithRelations($status, $limit, $offset);
            $totalItems = $postModel->countAll($status);

            ApiResponse::success($posts, 'Daftar semua artikel (Admin) berhasil diambil.', 200, [
                'page'        => $page,
                'limit'       => $limit,
                'status'      => $status,
                'total_items' => $totalItems,
                'total_pages' => ceil($totalItems / max(1, $limit))
            ]);
            return;
        }

        $posts = $postModel->allPublished($limit, $offset, $category);
        $totalItems = $postModel->countPublished($category);

        ApiResponse::success($posts, 'Daftar artikel berhasil diambil.', 200, [
            'page'        => $page,
            'limit'       => $limit,
            'category'    => $category,
            'total_items' => $totalItems,
            'total_pages' => ceil($totalItems / max(1, $limit))
        ]);
    }

    public function read($idOrSlug = ''): void
    {
        if (empty($idOrSlug)) {
            ApiResponse::error("Parameter ID atau Slug artikel wajib disertakan.", 400);
            return;
        }

        $postModel = new ApiPost();
        // Jangan tambah views jika diakses untuk edit oleh admin (ada query ?no_view=1)
        $increment = empty($_GET['no_view']);
        $post = $postModel->findDetail($idOrSlug, $increment);

        if (!$post) {
            ApiResponse::error("Artikel tidak ditemukan.", 404);
            return;
        }

        ApiResponse::success($post, 'Detail artikel berhasil diambil.');
    }

    public function create(): void
    {
        $user = $this->requireAuth();

        $title = trim((string)$this->input('title'));
        $content = (string)$this->input('content');
        $slug = trim((string)$this->input('slug'));
        $categoryId = (int)$this->input('category_id');
        $status = in_array($this->input('status'), ['draft', 'published']) ? $this->input('status') : 'published';
        $thumbnail = trim((string)$this->input('thumbnail', ''));

        if (empty($title) || empty($content)) {
            ApiResponse::error("Field 'title' dan 'content' wajib diisi.", 422);
            return;
        }

        $slug = empty($slug) ? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title)) : $slug;
        $slug = trim($slug, '-');

        $postModel = new ApiPost();
        // Cek jika slug sudah ada, beri suffix timestamp
        if ($postModel->firstWhere("slug = :s", ['s' => $slug])) {
            $slug .= '-' . time();
        }

        $insertData = [
            'user_id'     => (int)$user['user_id'],
            'category_id' => $categoryId > 0 ? $categoryId : null,
            'title'       => $title,
            'slug'        => $slug,
            'excerpt'     => trim((string)$this->input('excerpt', substr(strip_tags($content), 0, 150) . '...')),
            'content'     => $content,
            'status'      => $status,
            'thumbnail'   => !empty($thumbnail) ? $thumbnail : null,
            'views'       => 0
        ];

        $postId = $postModel->insert($insertData);
        $created = $postModel->findDetail($postId, false);

        ApiResponse::success($created, "Artikel berhasil dibuat.", 201);
    }

    public function update($id = ''): void
    {
        $this->requireAuth();
        $id = (int)$id;

        if ($id <= 0) {
            ApiResponse::error("ID artikel tidak valid.", 400);
            return;
        }

        $postModel = new ApiPost();
        $post = $postModel->find($id);

        if (!$post) {
            ApiResponse::error("Artikel dengan ID {$id} tidak ditemukan.", 404);
            return;
        }

        $updateData = [];
        $fields = ['title', 'slug', 'excerpt', 'content', 'status', 'category_id', 'thumbnail'];
        foreach ($fields as $field) {
            if ($this->input($field) !== null) {
                $val = $this->input($field);
                if ($field === 'category_id') {
                    $val = (int)$val > 0 ? (int)$val : null;
                }
                $updateData[$field] = $val;
            }
        }

        if (empty($updateData)) {
            ApiResponse::error("Tidak ada data yang dikirim untuk diupdate.", 422);
            return;
        }

        $postModel->update($id, $updateData);
        $updated = $postModel->findDetail($id, false);

        ApiResponse::success($updated, "Artikel berhasil diperbarui.");
    }

    public function delete($id = ''): void
    {
        $this->requireAuth();
        $id = (int)$id;

        if ($id <= 0) {
            ApiResponse::error("ID artikel tidak valid.", 400);
            return;
        }

        $postModel = new ApiPost();
        $post = $postModel->find($id);

        if (!$post) {
            ApiResponse::error("Artikel dengan ID {$id} tidak ditemukan.", 404);
            return;
        }

        $postModel->delete($id);
        ApiResponse::success(null, "Artikel '{$post['title']}' berhasil dihapus.");
    }
}
