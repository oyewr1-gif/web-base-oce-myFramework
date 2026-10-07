<?php
/**
 * Database Auto-Initializer Mandiri untuk REST API (api-info)
 * Menangani pembuatan database, skema tabel, dan seeder otomatis di server produksi.
 */
class ApiDatabaseInitializer
{
    /**
     * Memastikan Database dan Tabel tersedia di MySQL server untuk REST API.
     */
    public static function initialize(array $config, string $schemaPath): PDO
    {
        $host     = $config['host'] ?? 'localhost';
        $port     = $config['port'] ?? 3306;
        $database = $config['database'] ?? 'cms_framework';
        $username = $config['username'] ?? 'info';
        $password = $config['password'] ?? 'Info123*';
        $charset  = $config['charset'] ?? 'utf8mb4';

        // 1. Hubungkan ke MySQL server tanpa menentukan dbname dulu
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
     * Memeriksa keberadaan tabel tertentu
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
     * Mengisi data seeder awal jika tabel users kosong
     */
    public static function seedIfEmpty(PDO $pdo): void
    {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) AS total FROM `users`");
            $row = $stmt->fetch();
            if ($row && intval($row['total']) > 0) {
                return; // Data user sudah ada
            }

            $adminPasswordHash = password_hash('admin123', PASSWORD_BCRYPT);
            $stmtUser = $pdo->prepare("INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`)
                VALUES (1, 'admin', 'Administrator Utama', 'admin@cms.local', :admin_pass, 'admin')
                ON DUPLICATE KEY UPDATE `password` = VALUES(`password`)");
            $stmtUser->execute(['admin_pass' => $adminPasswordHash]);

            $pdo->exec("INSERT INTO `api_tokens` (`id`, `user_id`, `token`, `name`) VALUES
                (1, 1, 'test-token-cms-info-2026', 'Master Testing Token')
                ON DUPLICATE KEY UPDATE `token`=VALUES(`token`)");

            $pdo->exec("INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
                (1, 'Teknologi', 'teknologi', 'Kategori teknologi'),
                (2, 'Tutorial', 'tutorial', 'Kategori tutorial')
                ON DUPLICATE KEY UPDATE `name`=VALUES(`name`)");

            $pdo->exec("INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
                (1, 'site_title', 'Modern PHP MVC CMS'),
                (2, 'site_tagline', 'Framework Mandiri Cepat & Ringan'),
                (3, 'footer_text', '© 2026 CMS Framework.')
                ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`)");

        } catch (Throwable $e) {
            // Abaikan kendala seeder minor
        }
    }
}
