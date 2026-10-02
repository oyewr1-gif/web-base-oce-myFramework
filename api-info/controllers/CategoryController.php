<?php
/**
 * Controller Category REST API
 */
class CategoryController extends ApiController
{
    public function index(): void
    {
        $catModel = new ApiCategory();
        $categories = $catModel->allWithCount();

        ApiResponse::success($categories, "Daftar kategori berhasil diambil.");
    }

    public function read($idOrSlug = ''): void
    {
        $catModel = new ApiCategory();
        if (is_numeric($idOrSlug)) {
            $category = $catModel->find((int)$idOrSlug);
        } else {
            $category = $catModel->firstWhere("slug = :slug", ['slug' => $idOrSlug]);
        }

        if (!$category) {
            ApiResponse::error("Kategori tidak ditemukan.", 404);
            return;
        }

        ApiResponse::success($category, "Detail kategori berhasil diambil.");
    }

    public function create(): void
    {
        $this->requireAuth();

        $name = trim((string)$this->input('name'));
        $slug = trim((string)$this->input('slug'));
        $desc = trim((string)$this->input('description'));

        if (empty($name)) {
            ApiResponse::error("Nama kategori wajib diisi.", 422);
            return;
        }

        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        }
        $slug = trim($slug, '-');

        $catModel = new ApiCategory();
        if ($catModel->firstWhere("slug = :slug", ['slug' => $slug])) {
            $slug .= '-' . time();
        }

        $id = $catModel->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $desc
        ]);

        $created = $catModel->find($id);
        ApiResponse::success($created, "Kategori berhasil dibuat.", 201);
    }

    public function update($id = ''): void
    {
        $this->requireAuth();
        $id = (int)$id;

        $catModel = new ApiCategory();
        $cat = $catModel->find($id);
        if (!$cat) {
            ApiResponse::error("Kategori tidak ditemukan.", 404);
            return;
        }

        $updateData = [];
        if ($this->input('name') !== null) {
            $updateData['name'] = trim((string)$this->input('name'));
        }
        if ($this->input('slug') !== null) {
            $updateData['slug'] = trim((string)$this->input('slug'));
        }
        if ($this->input('description') !== null) {
            $updateData['description'] = trim((string)$this->input('description'));
        }

        if (empty($updateData)) {
            ApiResponse::error("Tidak ada data yang dikirim.", 422);
            return;
        }

        $catModel->update($id, $updateData);
        $updated = $catModel->find($id);
        ApiResponse::success($updated, "Kategori berhasil diperbarui.");
    }

    public function delete($id = ''): void
    {
        $this->requireAuth();
        $id = (int)$id;

        $catModel = new ApiCategory();
        $cat = $catModel->find($id);
        if (!$cat) {
            ApiResponse::error("Kategori tidak ditemukan.", 404);
            return;
        }

        $catModel->delete($id);
        ApiResponse::success(null, "Kategori '{$cat['name']}' berhasil dihapus.");
    }
}
