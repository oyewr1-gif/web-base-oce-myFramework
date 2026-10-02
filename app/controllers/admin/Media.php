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
            
            // PRIORITAS 1: Simpan langsung ke front-end public/uploads jika folder lokal tersedia & writable
            $localUploadDir = defined('ROOT_PATH') ? ROOT_PATH . '/public/uploads/' : (APP_PATH . '/../public/uploads/');
            if (!is_dir($localUploadDir)) {
                @mkdir($localUploadDir, 0775, true);
            }

            $savedLocally = false;
            $result = null;

            if (is_dir($localUploadDir) && is_writable($localUploadDir)) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $safeBase = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', pathinfo($file['name'], PATHINFO_FILENAME)));
                $safeBase = trim($safeBase, '-') ?: 'media';
                $newFilename = $safeBase . '-' . time() . '-' . bin2hex(random_bytes(3)) . '.' . $ext;
                $targetPath = $localUploadDir . $newFilename;

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    @chmod($targetPath, 0664);
                    $result = $api->registerMedia([
                        'filename'      => $newFilename,
                        'original_name' => $file['name'],
                        'file_path'     => 'uploads/' . $newFilename,
                        'mime_type'     => $file['type'] ?? 'application/octet-stream',
                        'file_size'     => (int)$file['size']
                    ]);
                    $savedLocally = ($result !== null);
                }
            }

            // PRIORITAS 2: Jika penyimpanan lokal gagal atau terpisah, teruskan via cURL ke API
            if (!$savedLocally) {
                $result = $api->uploadMedia($file);
            }

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
