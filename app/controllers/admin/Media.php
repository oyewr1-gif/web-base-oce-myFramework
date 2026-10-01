<?php
/**
 * Controller Admin Media
 * Mengelola upload dan galeri file media
 */
class MediaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index(): void
    {
        $mediaModel = $this->model('Media');
        $mediaList = $mediaModel->all('id DESC');

        $this->view('admin/media/index', [
            'pageTitle' => 'Media & File Manager',
            'mediaList' => $mediaList
        ], 'layouts/admin');
    }

    public function upload(): void
    {
        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('admin/media');
                return;
            }

            $file = $this->request->file('media_file');
            if (!$file) {
                flash('error', 'Silakan pilih file yang ingin diunggah.');
                $this->redirect('admin/media');
                return;
            }

            $mediaModel = $this->model('Media');
            try {
                $result = $mediaModel->upload($file);
                if ($result) {
                    flash('success', 'File berhasil diunggah!');
                } else {
                    flash('error', 'Gagal memproses unggahan file.');
                }
            } catch (Exception $e) {
                flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        $this->redirect('admin/media');
    }

    public function delete(string $id = ''): void
    {
        $id = (int)$id;
        $mediaModel = $this->model('Media');
        $item = $mediaModel->find($id);

        if ($item) {
            // Hapus file fisik jika ada di server
            $fullPath = ROOT_PATH . '/public/' . $item['file_path'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            $mediaModel->delete($id);
            flash('success', 'File media berhasil dihapus.');
        } else {
            flash('error', 'File tidak ditemukan.');
        }

        $this->redirect('admin/media');
    }
}
