<?php
/**
 * Database Connection Manager Mandiri untuk REST API (api-info)
 */
class ApiDatabase
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require API_PATH . '/config/database.php';

            $host     = $config['host'] ?? 'localhost';
            $port     = $config['port'] ?? 3306;
            $database = $config['database'] ?? 'cms_framework';
            $username = $config['username'] ?? 'info';
            $password = $config['password'] ?? 'Info123*';
            $charset  = $config['charset'] ?? 'utf8mb4';

            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                ApiResponse::error("Koneksi Database API Gagal: " . $e->getMessage(), 500);
            }
        }

        return self::$instance;
    }
}
