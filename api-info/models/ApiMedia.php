<?php
/**
 * Model ApiMedia
 * Mengelola tabel media pada REST API
 */
class ApiMedia extends ApiModel
{
    protected string $table = 'media';

    /**
     * Pastikan tabel media dan kolom-kolomnya ada di database REST API.
     * Membuat tabel secara otomatis jika belum ada di server database produksi.
     */
    public function ensureTableExists(): bool
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `media` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `filename` VARCHAR(255) NOT NULL,
                `original_name` VARCHAR(255) NOT NULL,
                `file_path` VARCHAR(255) NOT NULL,
                `mime_type` VARCHAR(100) NOT NULL,
                `file_size` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->db->exec($sql);

            // Verifikasi dan tambahkan kolom jika tabel dibuat dengan versi terdahulu
            $cols = $this->db->query("SHOW COLUMNS FROM `media`")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('original_name', $cols)) {
                $this->db->exec("ALTER TABLE `media` ADD COLUMN `original_name` VARCHAR(255) NOT NULL DEFAULT '' AFTER `filename`");
            }
            if (!in_array('mime_type', $cols)) {
                $this->db->exec("ALTER TABLE `media` ADD COLUMN `mime_type` VARCHAR(100) NOT NULL DEFAULT 'application/octet-stream' AFTER `file_path`");
            }
            if (!in_array('file_size', $cols)) {
                $this->db->exec("ALTER TABLE `media` ADD COLUMN `file_size` INT NOT NULL DEFAULT 0 AFTER `mime_type`");
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function allOrdered(): array
    {
        try {
            return $this->all('id DESC');
        } catch (\Throwable $e) {
            $this->ensureTableExists();
            try {
                return $this->all('id DESC');
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }
}
