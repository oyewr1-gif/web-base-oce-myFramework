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

    public function read($id = ''): void
    {
        $id = (int)$id;
        $catModel = new ApiCategory();
        $category = $catModel->find($id);

        if (!$category) {
            ApiResponse::error("Kategori tidak ditemukan.", 404);
            return;
        }

        ApiResponse::success($category, "Detail kategori berhasil diambil.");
    }
}
