<?php
/**
 * Konfigurasi Database
 * Mengambil kredensial secara aman dari berkas .env (Zero-dependency)
 * Tidak ada kredensial sensitif yang di-hardcode ke git/GitHub
 */
return [
    'driver'   => env('DB_DRIVER', 'mysql'),
    'host'     => env('DB_HOST', 'localhost'),
    'port'     => (int)env('DB_PORT', 3306),
    'database' => env('DB_DATABASE', 'cms_framework'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset'  => env('DB_CHARSET', 'utf8mb4'),
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
