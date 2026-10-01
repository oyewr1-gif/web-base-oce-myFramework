<?php
/**
 * Controller Admin Categories
 * Mengelola kategori konten artikel
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
        $catModel = $this->model('Category');
        $categories = $catModel->allWithCount();

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

            $slug = empty($slug) ? slugify($name) : slugify($slug);

            $catModel = $this->model('Category');
            if ($catModel->findBySlug($slug)) {
                $slug = $slug . '-' . time();
            }

            $catModel->insert([
                'name'        => $name,
                'slug'        => $slug,
                'description' => $desc
            ]);

            flash('success', "Kategori '{$name}' berhasil ditambahkan.");
        }

        $this->redirect('admin/categories');
    }

    public function delete(string $id = ''): void
    {
        $id = (int)$id;
        $catModel = $this->model('Category');
        $cat = $catModel->find($id);

        if ($cat) {
            $catModel->delete($id);
            flash('success', "Kategori '{$cat['name']}' berhasil dihapus.");
        } else {
            flash('error', 'Kategori tidak ditemukan.');
        }

        $this->redirect('admin/categories');
    }
}
