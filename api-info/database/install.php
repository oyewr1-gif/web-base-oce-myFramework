<?php
/**
 * Skrip Installer Otomatis Standalone REST API (api-info)
 * Dapat dijalankan langsung di server terisolasi via CLI: php database/install.php
 */

define('API_PATH', dirname(__DIR__));

require_once API_PATH . '/core/ApiEnv.php';

// Muat .env lokal api-info
if (file_exists(API_PATH . '/.env')) {
    ApiEnv::load(API_PATH . '/.env');
}

$dbConfig = require API_PATH . '/config/database.php';

$host     = $dbConfig['host'] ?? 'localhost';
$port     = $dbConfig['port'] ?? 3306;
$database = $dbConfig['database'] ?? 'cms_framework';
$username = $dbConfig['username'] ?? 'root';
$password = $dbConfig['password'] ?? '';
$charset  = $dbConfig['charset'] ?? 'utf8mb4';

$isCli = (php_sapi_name() === 'cli');

function apiOutput(string $msg, bool $isCli, string $type = 'info') {
    if ($isCli) {
        $prefix = match($type) {
            'success' => "[SUKSES] ",
            'error'   => "[ERROR] ",
            default   => "[INFO] "
        };
        echo $prefix . $msg . PHP_EOL;
    } else {
        $color = match($type) {
            'success' => '#15803d',
            'error'   => '#b91c1c',
            default   => '#1d4ed8'
        };
        echo "<div style='font-family:sans-serif;margin:8px 0;padding:12px;background:#f8fafc;border-left:4px solid {$color};color:#1e293b;'>{$msg}</div>";
    }
}

if (!$isCli) {
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><title>REST API Database Installer</title>"
        . "<style>body{font-family:sans-serif;padding:30px;max-width:800px;margin:auto;background:#f1f5f9;}</style></head><body>"
        . "<h2>🚀 REST API Database Installer</h2>";
}

try {
    apiOutput("Menghubungkan ke MySQL server ({$host}:{$port}) user '{$username}'...", $isCli);
    $pdo = new PDO("mysql:host={$host};port={$port};charset={$charset}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    apiOutput("Berhasil terhubung ke server MySQL!", $isCli, 'success');

    // 1. Buat database jika belum ada
    apiOutput("Membuat database `{$database}`...", $isCli);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$database}`");

    // 2. Jalankan schema
    apiOutput("Mengeksekusi skema database dari api-info/database/schema.sql...", $isCli);
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schemaSql);
    apiOutput("Tabel database API berhasil dibuat!", $isCli, 'success');

    // 3. Jalankan seeder
    apiOutput("Mengisi seeder dan token pengujian...", $isCli);
    $adminPasswordHash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`)
        VALUES (1, 'admin', 'Administrator Utama', 'admin@cms.local', :admin_pass, 'admin')
        ON DUPLICATE KEY UPDATE `password` = VALUES(`password`)");
    $stmt->execute(['admin_pass' => $adminPasswordHash]);

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

    apiOutput("Database REST API berhasil disiapkan!", $isCli, 'success');
    apiOutput("Testing Token: test-token-cms-info-2026", $isCli, 'success');

} catch (Exception $e) {
    apiOutput("Gagal: " . $e->getMessage(), $isCli, 'error');
}

if (!$isCli) {
    echo "<p><a href='../index.php'>&larr; Buka Endpoint API</a></p></body></html>";
}
