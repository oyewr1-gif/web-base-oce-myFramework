<?php
/**
 * Entrypoint Mandiri REST API (api-info)
 * Terpisah dari CMS Web Utama
 */

define('API_PATH', __DIR__);
define('ROOT_PATH', dirname(__DIR__));

// Muat Autoloader Mandiri REST API
require_once API_PATH . '/core/ApiAutoloader.php';
ApiAutoloader::register([
    API_PATH . '/core',
    API_PATH . '/models',
    API_PATH . '/controllers'
]);

// Muat konfigurasi environment .env
if (file_exists(API_PATH . '/.env')) {
    ApiEnv::load(API_PATH . '/.env');
} else {
    ApiEnv::load(ROOT_PATH . '/.env');
}

// Muat konfigurasi dasar
$config = require API_PATH . '/config/app.php';
if (!empty($config['timezone'])) {
    date_default_timezone_set($config['timezone']);
}

// Global Exception Handler: Pastikan semua uncaught exception di REST API mengembalikan format JSON
set_exception_handler(function (\Throwable $e) {
    ApiResponse::error("Kesalahan server API: " . $e->getMessage(), 500);
});

// Global Shutdown Function: Tangani fatal error PHP di server API agar tetap berformat JSON
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ApiResponse::error("Fatal Error server API: " . $error['message'] . " (file: " . basename($error['file']) . ":" . $error['line'] . ")", 500);
    }
});

// Jalankan API Router dalam blok try-catch
try {
    $router = new ApiRouter();
    $router->dispatch();
} catch (\Throwable $e) {
    ApiResponse::error("Kesalahan eksekusi API: " . $e->getMessage(), 500);
}
