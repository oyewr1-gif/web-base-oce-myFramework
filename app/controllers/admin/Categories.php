<?php
/**
 * Controller Admin Categories (Headless REST API Client)
 * Mengelola kategori konten artikel via REST API
 */
class CategoriesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index(): void
    {
        $api = new ApiClient();
        $categories = $api->getCategories();

        $this->view('admin/categories/index', [
            'pageTitle'  => 'Manajemen Kategori',
            'categories' => $categories
        ], 'layouts/admin');
    }

    public function create(): void
    {
        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('admin/categories');
                return;
            }

            $name = trim((string)$this->request->post('name'));
            $slug = trim((string)$this->request->post('slug'));
            $desc = trim((string)$this->request->post('description'));

            if (empty($name)) {
                flash('error', 'Nama kategori wajib diisi.');
                $this->redirect('admin/categories');
                return;
            }

            $api = new ApiClient();
            $result = $api->createCategory([
                'name'        => $name,
                'slug'        => $slug,
                'description' => $desc
            ]);

            if ($result) {
                flash('success', "Kategori '{$name}' berhasil ditambahkan.");
            } else {
                flash('error', $api->getLastError() ?: 'Gagal menambahkan kategori.');
            }
        }

        $this->redirect('admin/categories');
    }

    public function delete(string $id = ''): void
    {
        $id = (int)$id;
        $api = new ApiClient();

        if ($id > 0 && $api->deleteCategory($id)) {
            flash('success', 'Kategori berhasil dihapus.');
        } else {
            flash('error', $api->getLastError() ?: 'Gagal menghapus kategori.');
        }

        $this->redirect('admin/categories');
    }
}
