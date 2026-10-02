<?php
/**
 * Controller Admin Media (Headless REST API Client)
 * Mengelola upload dan galeri file media via REST API (api-info)
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
        $api = new ApiClient();
        $mediaList = $api->getMedia();

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
            if (!$file || empty($file['name'])) {
                flash('error', 'Silakan pilih file yang ingin diunggah.');
                $this->redirect('admin/media');
                return;
            }

            $api = new ApiClient();
            $result = $api->uploadMedia($file);

            if ($result) {
                flash('success', 'File berhasil diunggah!');
            } else {
                flash('error', $api->getLastError() ?: 'Gagal memproses unggahan file via API.');
            }
        }

        $this->redirect('admin/media');
    }

    public function delete(string $id = ''): void
    {
        $id = (int)$id;
        $api = new ApiClient();

        if ($id > 0 && $api->deleteMedia($id)) {
            flash('success', 'File media berhasil dihapus.');
        } else {
            flash('error', $api->getLastError() ?: 'Gagal menghapus file media.');
        }

        $this->redirect('admin/media');
    }
}
