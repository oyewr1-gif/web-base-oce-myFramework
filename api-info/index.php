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

// Jalankan API Router
$router = new ApiRouter();
$router->dispatch();
