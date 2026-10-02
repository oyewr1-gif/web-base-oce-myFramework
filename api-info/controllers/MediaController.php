<?php
/**
 * Controller Media REST API
 * Mengelola upload dan galeri media file via REST API
 * Dirancang tangguh untuk server lokal maupun server produksi terisolasi
 */
class MediaController extends ApiController
{
    public function index(): void
    {
        $this->requireAuth();
        $mediaModel = new ApiMedia();
        $mediaList = $mediaModel->allOrdered();

        ApiResponse::success($mediaList, "Daftar media berhasil diambil.");
    }

    public function upload(): void
    {
        $this->requireAuth();

        // 1. Dukungan registrasi metadata jika berkas fisik telah disimpan oleh front-end di public/uploads
        $preFilename = $this->input('filename');
        if (!empty($preFilename)) {
            $mediaModel = new ApiMedia();
            $data = [
                'filename'      => $preFilename,
                'original_name' => $this->input('original_name', $preFilename),
                'file_path'     => $this->input('file_path', 'uploads/' . $preFilename),
                'mime_type'     => $this->input('mime_type', 'application/octet-stream'),
                'file_size'     => (int)$this->input('file_size', 0)
            ];

            try {
                $id = $mediaModel->insert($data);
                $data['id'] = (int)$id;

                ApiResponse::success($data, "Metadata media berhasil didaftarkan.", 201);
                return;
            } catch (\Throwable $e) {
                // Buat tabel jika belum ada di database dan periksa kolom, lalu coba ulangi insert
                $mediaModel->ensureTableExists();
                try {
                    $id = $mediaModel->insert($data);
                    $data['id'] = (int)$id;

                    ApiResponse::success($data, "Metadata media berhasil didaftarkan.", 201);
                    return;
                } catch (\Throwable $e2) {
                    ApiResponse::error("Gagal mencatat media ke database API: " . $e2->getMessage(), 500);
                    return;
                }
            }
        }

        // 2. Unggah berkas fisik jika dikirimkan via form multipart
        $file = $_FILES['media_file'] ?? ($_FILES['file'] ?? null);
        if (!$file || empty($file['name'])) {
            ApiResponse::error("File tidak ditemukan dalam request upload.", 400);
            return;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE   => "Ukuran berkas melebihi batas upload_max_filesize pada php.ini server.",
                UPLOAD_ERR_FORM_SIZE  => "Ukuran berkas melebihi batas MAX_FILE_SIZE pada form HTML.",
                UPLOAD_ERR_PARTIAL    => "Berkas hanya terunggah sebagian.",
                UPLOAD_ERR_NO_FILE    => "Tidak ada berkas yang diunggah.",
                UPLOAD_ERR_NO_TMP_DIR => "Folder sementara (upload_tmp_dir) tidak ditemukan di server produksi.",
                UPLOAD_ERR_CANT_WRITE => "Gagal menulis berkas ke disk server (periksa izin /tmp atau disk penuh).",
                UPLOAD_ERR_EXTENSION  => "Unggahan berkas dihentikan oleh ekstensi PHP server."
            ];
            $msg = $uploadErrors[$file['error']] ?? ("Terjadi kesalahan saat upload berkas (Kode error: " . $file['error'] . ").");
            ApiResponse::error($msg, 400);
            return;
        }

        // Tentukan kandidat folder upload:
        // 1. ROOT_PATH/public/uploads (jika front-end dan API berada dalam 1 server)
        // 2. API_PATH/uploads (jika API berjalan terisolasi pada server mandiri)
        $candidates = [];
        if (defined('ROOT_PATH') && is_dir(ROOT_PATH . '/public')) {
            $candidates[] = ROOT_PATH . '/public/uploads';
        }
        $candidates[] = API_PATH . '/uploads';

        $uploadDir = null;
        $isRootPublic = false;

        foreach ($candidates as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            if (is_dir($dir) && is_writable($dir)) {
                $uploadDir = rtrim(str_replace('\\', '/', $dir), '/') . '/';
                $isRootPublic = (defined('ROOT_PATH') && realpath($dir) === realpath(ROOT_PATH . '/public/uploads'));
                break;
            }
        }

        // Jika tidak ada folder yang writable, pilih target utama dan berikan pesan solutif
        if ($uploadDir === null) {
            $targetDir = $candidates[0];
            $displayPath = realpath($targetDir) ?: $targetDir;
            if (!is_dir($targetDir)) {
                ApiResponse::error("Folder penyimpanan '{$targetDir}' belum ada dan tidak dapat dibuat otomatis oleh PHP. Silakan buat folder dan atur hak aksesnya di server: 'mkdir -p {$targetDir} && chmod -R 775 {$targetDir} && chown -R www-data:www-data {$targetDir}'", 500);
                return;
            } else {
                ApiResponse::error("Folder penyimpanan '{$displayPath}' tidak memiliki izin tulis (write permission) untuk user web server. Silakan jalankan perintah berikut di terminal server produksi: 'sudo chown -R www-data:www-data {$displayPath} && sudo chmod -R 775 {$displayPath}'", 500);
                return;
            }
        }

        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'svg', 'zip', 'doc', 'docx'];
        if (!in_array($extension, $allowed)) {
            ApiResponse::error("Ekstensi berkas .{$extension} tidak diizinkan. Format yang didukung: " . implode(', ', $allowed), 422);
            return;
        }

        // Slugify nama berkas
        $safeBase = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $originalName));
        $safeBase = trim($safeBase, '-') ?: 'file';
        $newFilename = $safeBase . '-' . time() . '-' . bin2hex(random_bytes(3)) . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            @chmod($targetPath, 0664);

            // Bangun URL absolut berkas
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
            $subDir = rtrim(dirname($scriptName), '/\\');
            $apiBaseUrl = $protocol . $host . ($subDir === '/' || $subDir === '\\' ? '' : $subDir);

            $fileUrl = $apiBaseUrl . '/uploads/' . $newFilename;
            $filePath = $isRootPublic ? 'uploads/' . $newFilename : $fileUrl;

            $mediaModel = new ApiMedia();
            $data = [
                'filename'      => $newFilename,
                'original_name' => $file['name'],
                'file_path'     => $filePath,
                'file_url'      => $fileUrl,
                'mime_type'     => $file['type'] ?? 'application/octet-stream',
                'file_size'     => (int)$file['size']
            ];

            try {
                $id = $mediaModel->insert([
                    'filename'      => $data['filename'],
                    'original_name' => $data['original_name'],
                    'file_path'     => $data['file_path'],
                    'mime_type'     => $data['mime_type'],
                    'file_size'     => $data['file_size']
                ]);
                $data['id'] = (int)$id;

                ApiResponse::success($data, "Berkas berhasil diunggah.", 201);
                return;
            } catch (\Throwable $e) {
                $mediaModel->ensureTableExists();
                try {
                    $id = $mediaModel->insert([
                        'filename'      => $data['filename'],
                        'original_name' => $data['original_name'],
                        'file_path'     => $data['file_path'],
                        'mime_type'     => $data['mime_type'],
                        'file_size'     => $data['file_size']
                    ]);
                    $data['id'] = (int)$id;

                    ApiResponse::success($data, "Berkas berhasil diunggah.", 201);
                    return;
                } catch (\Throwable $e2) {
                    ApiResponse::error("Gagal mencatat berkas ke database API: " . $e2->getMessage(), 500);
                    return;
                }
            }
        }

        $lastError = error_get_last();
        $errorDetail = !empty($lastError['message']) ? " (Detail: " . $lastError['message'] . ")" : "";
        ApiResponse::error("Gagal memindahkan file yang diunggah ke folder penyimpanan '{$uploadDir}'.{$errorDetail} Periksa kepemilikan user dan hak izin tulis folder pada server.", 500);
    }

    public function delete($id = ''): void
    {
        $this->requireAuth();
        $id = (int)$id;

        $mediaModel = new ApiMedia();
        $item = $mediaModel->find($id);

        if (!$item) {
            ApiResponse::error("File media tidak ditemukan.", 404);
            return;
        }

        // Hapus berkas fisik jika ada
        $candidates = [];
        if (defined('ROOT_PATH')) {
            $candidates[] = ROOT_PATH . '/public/' . ltrim($item['file_path'], '/');
            $candidates[] = ROOT_PATH . '/public/uploads/' . basename($item['filename']);
        }
        $candidates[] = API_PATH . '/' . ltrim($item['file_path'], '/');
        $candidates[] = API_PATH . '/uploads/' . basename($item['filename']);

        foreach ($candidates as $path) {
            if (file_exists($path) && is_file($path)) {
                @unlink($path);
                break;
            }
        }

        try {
            $mediaModel->delete($id);
            ApiResponse::success(null, "File media berhasil dihapus.");
        } catch (\Throwable $e) {
            ApiResponse::error("Gagal menghapus catatan media dari database API: " . $e->getMessage(), 500);
        }
    }
}
