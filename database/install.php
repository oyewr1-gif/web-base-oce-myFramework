<?php
/**
 * Skrip Installer Otomatis Database CMS
 * Dapat dijalankan via CLI: php database/install.php
 * Atau via Web Browser jika dipanggil langsung
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CORE_PATH', ROOT_PATH . '/core');

require_once CORE_PATH . '/Env.php';
require_once CORE_PATH . '/Helper.php';
Env::load(ROOT_PATH . '/.env');

$dbConfig = require APP_PATH . '/config/database.php';

$host     = $dbConfig['host'] ?? 'localhost';
$port     = $dbConfig['port'] ?? 3306;
$database = $dbConfig['database'] ?? 'cms_framework';
$username = $dbConfig['username'] ?? 'info';
$password = $dbConfig['password'] ?? 'Info123*';
$charset  = $dbConfig['charset'] ?? 'utf8mb4';

$isCli = (php_sapi_name() === 'cli');

function output(string $msg, bool $isCli, string $type = 'info') {
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
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><title>CMS Database Installer</title>"
        . "<style>body{font-family:sans-serif;padding:30px;max-width:800px;margin:auto;background:#f1f5f9;}</style></head><body>"
        . "<h2>🛠️ Framework CMS Database Installer</h2>";
}

try {
    output("Mencoba menghubungkan ke MySQL server ({$host}:{$port}) dengan user '{$username}'...", $isCli);
    $pdo = new PDO("mysql:host={$host};port={$port};charset={$charset}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    output("Berhasil terhubung ke server MySQL!", $isCli, 'success');

    // 1. Buat database jika belum ada
    output("Membuat database `{$database}` (jika belum ada)...", $isCli);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$database}`");
    output("Database `{$database}` siap digunakan.", $isCli, 'success');

    // 2. Jalankan skema tabel
    output("Mengeksekusi skema tabel dari database/schema.sql...", $isCli);
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schemaSql);
    output("Semua tabel (users, categories, posts, media, settings, api_tokens) berhasil dibuat!", $isCli, 'success');

    // 3. Masukkan data seeder dengan password hash dinamis
    output("Memasukkan data awal (seeder)...", $isCli);
    $adminPasswordHash = password_hash('admin123', PASSWORD_BCRYPT);
    $editorPasswordHash = password_hash('editor123', PASSWORD_BCRYPT);

    // Seeder user admin & editor
    $stmt = $pdo->prepare("INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`)
        VALUES 
        (1, 'admin', 'Administrator Utama', 'admin@cms.local', :admin_pass, 'admin'),
        (2, 'editor', 'Redaksi Konten', 'editor@cms.local', :editor_pass, 'editor')
        ON DUPLICATE KEY UPDATE `password` = VALUES(`password`)");
    $stmt->execute([
        'admin_pass' => $adminPasswordHash,
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

    output("Data seeder berhasil dimasukkan!", $isCli, 'success');
    output("----------------------------------------------------------------", $isCli);
    output("Akun Admin CMS:", $isCli, 'success');
    output("Email    : admin@cms.local", $isCli);
    output("Username : admin", $isCli);
    output("Password : admin123", $isCli);
    output("----------------------------------------------------------------", $isCli);
    output("Token REST API Default:", $isCli, 'success');
    output("Bearer Token: test-token-cms-info-2026", $isCli);
    output("----------------------------------------------------------------", $isCli);
    output("Instalasi Selesai! Framework siap dijalankan.", $isCli, 'success');

} catch (Exception $e) {
    output("Gagal melakukan instalasi: " . $e->getMessage(), $isCli, 'error');
}

if (!$isCli) {
    echo "<p><a href='../public/'>&larr; Buka Website CMS</a> | <a href='../public/auth/login'>Login ke Admin</a></p></body></html>";
}
