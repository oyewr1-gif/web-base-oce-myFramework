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
}
