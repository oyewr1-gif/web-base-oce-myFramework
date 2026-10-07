<?php
/**
 * Core Database Auto-Initializer
 * Menangani pembuatan database, skema tabel, dan seeder secara otomatis
 * saat aplikasi pertama kali dijalankan di server produksi.
 */
class DatabaseInitializer
{
    /**
     * Memastikan Database dan Tabel tersedia di MySQL server.
     */
    public static function initialize(array $config, string $schemaPath): PDO
    {
        $host     = $config['host'] ?? 'localhost';
        $port     = $config['port'] ?? 3306;
        $database = $config['database'] ?? 'cms_framework';
        $username = $config['username'] ?? 'info';
        $password = $config['password'] ?? 'Info123*';
        $charset  = $config['charset'] ?? 'utf8mb4';

        // 1. Hubungkan ke MySQL server tanpa menentukan database terlebih dahulu
        $dsnNoDb = "mysql:host={$host};port={$port};charset={$charset}";
        $pdo = new PDO($dsnNoDb, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // 2. Buat database jika belum ada & pilih database tersebut
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$database}`");

        // 3. Impor skema tabel dari berkas schema.sql jika tabel 'users' belum ada
        if (!self::tableExists($pdo, 'users')) {
            if (file_exists($schemaPath)) {
                $schemaSql = file_get_contents($schemaPath);
                $pdo->exec($schemaSql);
            }
        }

        // 4. Kondisional Seeding: Jika tabel users kosong (0 data), isi data seeder awal
        self::seedIfEmpty($pdo);

        return $pdo;
    }

    /**
     * Memeriksa keberadaan tabel tertentu dalam database yang aktif
     */
    public static function tableExists(PDO $pdo, string $tableName): bool
    {
        try {
            $result = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($tableName));
            return $result && $result->rowCount() > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Mengisi data seeder secara kondisional jika tabel users kosong
     */
    public static function seedIfEmpty(PDO $pdo): void
    {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) AS total FROM `users`");
            $row = $stmt->fetch();
            if ($row && intval($row['total']) > 0) {
                return; // Sudah ada user, jangan seed ulang!
            }

            // Jalankan seeder admin & data awal
            $adminPasswordHash  = password_hash('admin123', PASSWORD_BCRYPT);
            $editorPasswordHash = password_hash('editor123', PASSWORD_BCRYPT);

            $stmtUser = $pdo->prepare("INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`)
                VALUES 
                (1, 'admin', 'Administrator Utama', 'admin@cms.local', :admin_pass, 'admin'),
                (2, 'editor', 'Redaksi Konten', 'editor@cms.local', :editor_pass, 'editor')
                ON DUPLICATE KEY UPDATE `password` = VALUES(`password`)");
            $stmtUser->execute([
                'admin_pass'  => $adminPasswordHash,
                'editor_pass' => $editorPasswordHash
            ]);

            // Seeder kategori
            $pdo->exec("INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
                (1, 'Teknologi', 'teknologi', 'Artikel dan berita seputar perkembangan dunia teknologi dan pemrograman.'),
                (2, 'Tutorial', 'tutorial', 'Panduan teknis langkah demi langkah untuk developer.'),
                (3, 'Pengumuman', 'pengumuman', 'Informasi dan pembaruan seputar situs web.')
                ON DUPLICATE KEY UPDATE `name`=VALUES(`name`)");

            // Seeder posts
            $pdo->exec("INSERT INTO `posts` (`id`, `user_id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `status`, `views`) VALUES
                (1, 1, 1, 'Selamat Datang di Framework CMS PHP MVC', 'selamat-datang-di-framework-cms-php-mvc', 'Framework baru yang ringan, zero-dependency, dan didesain khusus untuk performa tinggi.', '<p>Selamat datang di kerangka <strong>Framework CMS PHP MVC</strong> baru Anda!</p><p>Framework ini dibangun murni menggunakan PHP native tanpa dependensi eksternal dari Composer. Seluruh core engine mulai dari Autoloader, Router berkonvensi, Base Model PDO, View Engine dengan Master Layout, hingga Custom CSS Library dibuat secara modular dan terstruktur rapi.</p><p>Anda dapat mengelola artikel ini melalui panel admin di menu <em>Posts</em>.</p>', 'published', 15),
                (2, 1, 2, 'Panduan Mengakses Standalone REST API (api-info)', 'panduan-mengakses-standalone-rest-api-api-info', 'Pelajari cara mengonsumsi endpoint data JSON melalui aplikasi terpisah api-info.', '<p>Aplikasi REST API diletakkan terpisah pada folder <code>api-info/</code> di dalam proyek ini.</p><p>Dengan konfigurasi database mandiri dan arsitektur token authentication, Anda dapat menghubungkan aplikasi mobile, frontend SPA (React/Vue), atau pihak ketiga tanpa mengganggu sistem CMS web utama.</p>', 'published', 8)
                ON DUPLICATE KEY UPDATE `title`=VALUES(`title`)");

            // Seeder settings
            $pdo->exec("INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
                (1, 'site_title', 'Modern PHP MVC CMS'),
                (2, 'site_tagline', 'Framework Ringan, Cepat, dan Mandiri'),
                (3, 'admin_email', 'admin@cms.local'),
                (4, 'footer_text', '© 2026 Modern PHP MVC CMS. Hak Cipta Dilindungi.')
                ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`)");

            // Seeder token API
            $pdo->exec("INSERT INTO `api_tokens` (`id`, `user_id`, `token`, `name`) VALUES
                (1, 1, 'test-token-cms-info-2026', 'Master Testing Token')
                ON DUPLICATE KEY UPDATE `token`=VALUES(`token`)");

        } catch (Throwable $e) {
            // Sembunyikan error seeder jika terjadi kendala minor
        }
    }
}
