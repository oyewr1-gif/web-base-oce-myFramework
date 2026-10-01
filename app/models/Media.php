<?php
/**
 * Model Media
 * Mengelola file gambar dan dokumen yang diupload
 */
class Media extends Model
{
    protected string $table = 'media';

    /**
     * Upload file dan simpan informasinya ke database
     */
    public function upload(array $file): ?array
    {
        if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = ROOT_PATH . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validasi ekstensi
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'svg'];
        if (!in_array($extension, $allowed)) {
            throw new Exception("Ekstensi file .{$extension} tidak diizinkan.");
        }

        // Generate nama file unik yang aman
        $safeBase = slugify($originalName);
        $newFilename = $safeBase . '-' . time() . '-' . bin2hex(random_bytes(3)) . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $data = [
                'filename'      => $newFilename,
                'original_name' => $file['name'],
                'file_path'     => 'uploads/' . $newFilename,
                'mime_type'     => $file['type'] ?? 'application/octet-stream',
                'file_size'     => (int)$file['size']
            ];

            $id = $this->insert($data);
            $data['id'] = $id;
            return $data;
        }

        return null;
    }
}
