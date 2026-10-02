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

            // Simpan berkas fisik langsung ke front-end public/uploads/
            $upload = save_uploaded_media($file);
            if (!$upload['success']) {
                flash('error', $upload['error']);
                $this->redirect('admin/media');
                return;
            }

            // Catat metadata media ke database via REST API
            $api = new ApiClient();
            $result = $api->registerMedia([
                'filename'      => $upload['filename'],
                'original_name' => $upload['original_name'],
                'file_path'     => $upload['file_path'],
                'mime_type'     => $upload['mime_type'],
                'file_size'     => $upload['file_size']
            ]);

            if ($result !== null) {
                flash('success', 'File media berhasil diunggah!');
            } else {
                flash('warning', 'Berkas fisik berhasil disimpan di front-end (public/uploads), namun sinkronisasi catatan ke database API mengalami kendala: ' . ($api->getLastError() ?: 'Kesalahan API.'));
            }
        }

        $this->redirect('admin/media');
    }

    public function delete(string $id = ''): void
    {
        $id = (int)$id;
        $api = new ApiClient();

        // 1. Ambil daftar media untuk hapus file fisik lokal jika ada
        $mediaList = $api->getMedia();
        $target = null;
        foreach ($mediaList as $item) {
            if ((int)$item['id'] === $id) {
                $target = $item;
                break;
            }
        }

        if ($target && !empty($target['filename'])) {
            $localFile = upload_dir() . basename($target['filename']);
            if (file_exists($localFile) && is_file($localFile)) {
                @unlink($localFile);
            }
        }

        if ($id > 0 && $api->deleteMedia($id)) {
            flash('success', 'File media berhasil dihapus.');
        } else {
            flash('error', $api->getLastError() ?: 'Gagal menghapus file media.');
        }

        $this->redirect('admin/media');
    }
}
