<?php
/**
 * Core Database Connection (PDO Singleton)
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require APP_PATH . '/config/database.php';
            
            $driver = $config['driver'] ?? 'mysql';
            $host = $config['host'] ?? 'localhost';
            $port = $config['port'] ?? 3306;
            $database = $config['database'] ?? 'cms_framework';
            $username = $config['username'] ?? 'info';
            $password = $config['password'] ?? 'Info123*';
            $charset = $config['charset'] ?? 'utf8mb4';

            try {
                if ($driver === 'sqlite') {
                    $dsn = "sqlite:" . ($config['database'] ?? (APP_PATH . '/database.sqlite'));
                    self::$instance = new PDO($dsn);
                } else {
                    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
                    self::$instance = new PDO($dsn, $username, $password, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]);
                }
            } catch (PDOException $e) {
                $isPlaceholder = in_array($username, ['your_db_username', 'root_placeholder']) 
                              || in_array($password, ['your_db_password']);

                $html = "<div style='font-family:system-ui,-apple-system,sans-serif;max-width:700px;margin:40px auto;padding:24px;background:#fff1f2;color:#9f1239;border:1px solid #fecdd3;border-radius:12px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);'>";
                
                if ($isPlaceholder) {
                    $html .= "<h3 style='margin-top:0;display:flex;align-items:center;gap:8px;'>⚠️ Kredensial Database Belum Diperbarui di Server</h3>"
                        . "<p style='font-size:15px;line-height:1.6;'>Aplikasi mendeteksi bahwa berkas <code>.env</code> masih menggunakan kredensial contoh template (<strong>{$username}</strong>).</p>"
                        . "<div style='background:#ffffff;padding:16px;border-radius:8px;border:1px solid #ffe4e6;margin:16px 0;color:#374151;font-size:14px;'>"
                        . "<strong>💡 Langkah Solusi Cepat:</strong>"
                        . "<ol style='margin:8px 0 0 16px;padding:0;line-height:1.8;'>"
                        . "<li>Buka file <code>.env</code> di direktori root server produksi Anda.</li>"
                        . "<li>Ubah nilai berikut sesuai akun MySQL server Anda:<br>"
                        . "<pre style='background:#f3f4f6;padding:10px;border-radius:6px;margin:6px 0;font-size:13px;overflow-x:auto;'>DB_HOST={$host}\nDB_DATABASE={$database}\nDB_USERNAME=nama_user_mysql_anda\nDB_PASSWORD=password_user_mysql_anda</pre></li>"
                        . "<li>Pastikan user MySQL tersebut telah diberikan hak akses penuh (GRANT ALL PRIVILEGES) ke database <code>{$database}</code>.</li>"
                        . "<li>Simpan file <code>.env</code> dan muat ulang halaman ini.</li>"
                        . "</ol>"
                        . "</div>";
                } else {
                    $html .= "<h3 style='margin-top:0;'>⚠️ Database Connection Error</h3>"
                        . "<p style='font-size:14px;line-height:1.5;background:#ffffff;padding:12px;border-radius:6px;border:1px solid #ffe4e6;'><strong>Pesan Teknis:</strong> " . htmlspecialchars($e->getMessage()) . "</p>"
                        . "<p style='font-size:14px;line-height:1.6;color:#4b5563;'>"
                        . "Pastikan server MySQL sedang berjalan dan kredensial di <code>.env</code> sudah benar:<br>"
                        . "&bull; Host: <code>{$host}:{$port}</code><br>"
                        . "&bull; Database: <code>{$database}</code><br>"
                        . "&bull; User: <code>{$username}</code>"
                        . "</p>"
                        . "<p style='font-size:13px;color:#6b7280;'>Jika tabel belum dibuat, silakan import <code>database/schema.sql</code> atau jalankan installer <code>php database/install.php</code>.</p>";
                }

                $html .= "</div>";
                die($html);
            }
        }

        return self::$instance;
    }
}
