<?php
/**
 * Router Mandiri untuk REST API (api-info)
 * Memetakan request HTTP ke ApiController yang sesuai
 */
class ApiRouter
{
    public function dispatch(): void
    {
        // Tangani preflight CORS jika ada
        ApiResponse::setCorsHeaders();

        $uriSegments = $this->parseUrl();

        // Jika URL kosong (hanya /api-info atau /api-info/), tampilkan dokumentasi ringkas API
        if (empty($uriSegments)) {
            $this->showWelcome();
            return;
        }

        // Segment 1: Resource / Controller
        $resource = strtolower(array_shift($uriSegments));
        // Normalisasi nama (contoh: 'posts' -> 'Post', 'categories' -> 'Category')
        $controllerMap = [
            'post'       => 'PostController',
            'posts'      => 'PostController',
            'category'   => 'CategoryController',
            'categories' => 'CategoryController',
            'auth'       => 'AuthController',
            'setting'    => 'SettingController',
            'settings'   => 'SettingController',
        ];

        $controllerName = $controllerMap[$resource] ?? (ucfirst($resource) . 'Controller');
        $controllerFile = API_PATH . '/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            ApiResponse::error("Endpoint resource '{$resource}' tidak ditemukan.", 404);
            return;
        }

        require_once $controllerFile;
        if (!class_exists($controllerName)) {
            ApiResponse::error("Class controller '{$controllerName}' tidak valid.", 500);
            return;
        }

        $controllerInstance = new $controllerName();
        $httpMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Segment 2: Action / Method atau ID
        $action = !empty($uriSegments) ? array_shift($uriSegments) : '';
        $params = $uriSegments;

        // Jika segment ke-2 adalah angka (misal /posts/12), jadikan param dan tentukan action dari HTTP method
        if (is_numeric($action)) {
            array_unshift($params, $action);
            $action = match ($httpMethod) {
                'GET'    => 'read',
                'PUT'    => 'update',
                'DELETE' => 'delete',
                default  => 'read'
            };
        } elseif (empty($action)) {
            // Default action sesuai HTTP method
            $action = match ($httpMethod) {
                'GET'    => 'index',
                'POST'   => 'create',
                default  => 'index'
            };
        }

        if (!method_exists($controllerInstance, $action) || !is_callable([$controllerInstance, $action])) {
            ApiResponse::error("Action '{$action}' tidak didukung pada resource '{$resource}'.", 405);
            return;
        }

        call_user_func_array([$controllerInstance, $action], $params);
    }

    private function parseUrl(): array
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        // Hapus base path script jika dieksekusi di subfolder
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // Hapus kata 'api-info' jika request diarahkan melalui root rewrite
        $uri = preg_replace('#^/api-info#i', '', $uri);
        $uri = preg_replace('#^/index\.php#i', '', $uri);

        $trimmed = trim($uri, '/');
        return $trimmed === '' ? [] : explode('/', $trimmed);
    }

    private function showWelcome(): void
    {
        $config = require API_PATH . '/config/app.php';
        ApiResponse::success([
            'api_name'    => $config['api_name'] ?? 'CMS Info REST API',
            'version'     => $config['version'] ?? '1.0.0',
            'status'      => 'online',
            'endpoints'   => [
                'GET /posts'                  => 'Ambil daftar artikel publik',
                'GET /posts/read/{id_or_slug}' => 'Ambil detail artikel',
                'POST /posts/create'          => 'Tambah artikel baru (Wajib Header Authorization: Bearer <token>)',
                'PUT /posts/update/{id}'      => 'Update artikel (Wajib Header Authorization: Bearer <token>)',
                'DELETE /posts/delete/{id}'   => 'Hapus artikel (Wajib Header Authorization: Bearer <token>)',
                'GET /categories'             => 'Ambil daftar kategori',
                'POST /auth/login'            => 'Login dan peroleh Bearer Token (Kirim username/email & password)',
                'GET /auth/me'                => 'Cek data user pemilik token (Wajib Bearer Token)',
                'GET /settings'               => 'Ambil pengaturan umum situs'
            ],
            'authentication' => 'Header: Authorization: Bearer <token> ATAU X-API-KEY: <token>'
        ], 'CMS REST API Service is running!');
    }
}
