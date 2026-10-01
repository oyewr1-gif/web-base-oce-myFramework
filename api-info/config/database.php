<?php
/**
 * Konfigurasi Database REST API (api-info)
 * Mengambil kredensial secara aman dari berkas .env
 * Tidak ada kredensial sensitif yang di-hardcode ke git/GitHub
 */
return [
    'driver'   => ApiEnv::get('API_DB_DRIVER', ApiEnv::get('DB_DRIVER', 'mysql')),
    'host'     => ApiEnv::get('API_DB_HOST', ApiEnv::get('DB_HOST', 'localhost')),
    'port'     => (int)ApiEnv::get('API_DB_PORT', ApiEnv::get('DB_PORT', 3306)),
    'database' => ApiEnv::get('API_DB_DATABASE', ApiEnv::get('DB_DATABASE', 'cms_framework')),
    'username' => ApiEnv::get('API_DB_USERNAME', ApiEnv::get('DB_USERNAME', 'root')),
    'password' => ApiEnv::get('API_DB_PASSWORD', ApiEnv::get('DB_PASSWORD', '')),
    'charset'  => ApiEnv::get('API_DB_CHARSET', ApiEnv::get('DB_CHARSET', 'utf8mb4')),
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
