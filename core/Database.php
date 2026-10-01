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
                // Tampilkan pesan error yang informatif
                die("<div style='font-family:sans-serif;padding:20px;background:#ffebee;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;margin:20px;'>"
                    . "<h3 style='margin-top:0;'>Database Connection Error</h3>"
                    . "<p>" . htmlspecialchars($e->getMessage()) . "</p>"
                    . "<small>Pastikan MySQL aktif dan database <strong>{$database}</strong> sudah dibuat dengan user <strong>{$username}</strong>.<br>"
                    . "Jalankan skrip <code>php database/install.php</code> atau import <code>database/schema.sql</code>.</small>"
                    . "</div>");
            }
        }

        return self::$instance;
    }
}
