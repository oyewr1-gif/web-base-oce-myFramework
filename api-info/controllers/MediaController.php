<?php
/**
 * Controller Media REST API
 * Mengelola upload dan galeri media file via REST API
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

        $file = $_FILES['media_file'] ?? ($_FILES['file'] ?? null);
        if (!$file || empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            ApiResponse::error("File tidak ditemukan atau terjadi kesalahan saat upload.", 400);
            return;
        }

        $uploadDir = defined('ROOT_PATH') && is_dir(ROOT_PATH . '/public/uploads') 
            ? ROOT_PATH . '/public/uploads/' 
            : API_PATH . '/uploads/';

        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'svg', 'zip', 'doc', 'docx'];
        if (!in_array($extension, $allowed)) {
            ApiResponse::error("Ekstensi file .{$extension} tidak diizinkan.", 422);
            return;
        }

        // Slugify base name
        $safeBase = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $originalName));
        $safeBase = trim($safeBase, '-');
        $newFilename = $safeBase . '-' . time() . '-' . bin2hex(random_bytes(3)) . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $mediaModel = new ApiMedia();
            $data = [
                'filename'      => $newFilename,
                'original_name' => $file['name'],
                'file_path'     => 'uploads/' . $newFilename,
                'mime_type'     => $file['type'] ?? 'application/octet-stream',
                'file_size'     => (int)$file['size']
            ];

            $id = $mediaModel->insert($data);
            $data['id'] = (int)$id;

            ApiResponse::success($data, "File berhasil diunggah.", 201);
            return;
        }

        ApiResponse::error("Gagal memindahkan file yang diunggah ke folder penyimpanan.", 500);
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
        $uploadDir = defined('ROOT_PATH') && is_dir(ROOT_PATH . '/public/uploads') 
            ? ROOT_PATH . '/public/' 
            : API_PATH . '/';
        $fullPath = $uploadDir . $item['file_path'];
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $mediaModel->delete($id);
        ApiResponse::success(null, "File media berhasil dihapus.");
    }
}
