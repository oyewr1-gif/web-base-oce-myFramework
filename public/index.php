<?php
/**
 * Modern PHP MVC CMS - Front Controller
 * Zero-dependency / 100% Native PHP
 */

// 1. Definisikan konstanta path
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CORE_PATH', ROOT_PATH . '/core');

// 2. Load Core Autoloader
require_once CORE_PATH . '/Autoloader.php';
Autoloader::register([
    CORE_PATH,
    APP_PATH . '/models',
    APP_PATH . '/controllers',
    APP_PATH . '/controllers/admin'
]);

// 3. Muat berkas .env jika ada
Env::load(ROOT_PATH . '/.env');

// 4. Load Helper Global
require_once CORE_PATH . '/Helper.php';

// 5. Inisialisasi Session
Session::start();

// 5. Muat konfigurasi dasar
$config = require APP_PATH . '/config/app.php';
if (!empty($config['timezone'])) {
    date_default_timezone_set($config['timezone']);
}

// 6. Jalankan Router (Convention over Configuration)
$router = new Router();
$router->dispatch();
