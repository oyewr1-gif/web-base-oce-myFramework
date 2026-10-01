<?php
/**
 * Core Router
 * Menjalankan Convention over Configuration URL Dispatcher:
 * /{controller}/{method}/{param1}/{param2}
 * Mendukung subfolder admin: /admin/{controller}/{method}/{param1}
 */
class Router
{
    private string $defaultController = 'Home';
    private string $defaultMethod = 'index';

    public function __construct()
    {
        $config = require APP_PATH . '/config/app.php';
        if (!empty($config['default_controller'])) {
            $this->defaultController = ucfirst($config['default_controller']);
        }
    }

    public function dispatch(): void
    {
        $urlSegments = $this->parseUrl();

        $subfolder = '';
        $controllerName = $this->defaultController;
        $methodName = $this->defaultMethod;
        $params = [];

        // 1. Cek apakah segmen pertama adalah 'admin'
        if (!empty($urlSegments[0]) && strtolower($urlSegments[0]) === 'admin') {
            $subfolder = 'admin/';
            array_shift($urlSegments); // hapus 'admin'

            // Default controller untuk admin jika URL hanya /admin atau /admin/
            $controllerName = !empty($urlSegments[0]) ? ucfirst(array_shift($urlSegments)) : 'Dashboard';
        } elseif (!empty($urlSegments[0])) {
            $controllerName = ucfirst(array_shift($urlSegments));
        }

        // 2. Ambil nama method (action)
        if (!empty($urlSegments[0])) {
            $methodName = array_shift($urlSegments);
        }

        // 3. Sisanya adalah parameter
        $params = $urlSegments;

        // 4. Cari file controller (dukung nama file Post.php maupun PostController.php)
        $controllerFile = APP_PATH . '/controllers/' . $subfolder . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            $controllerFileWithSuffix = APP_PATH . '/controllers/' . $subfolder . $controllerName . 'Controller.php';
            if (file_exists($controllerFileWithSuffix)) {
                $controllerFile = $controllerFileWithSuffix;
            }
        }

        if (!file_exists($controllerFile)) {
            Response::notFound("Controller [{$subfolder}{$controllerName}] tidak ditemukan.");
            return;
        }

        require_once $controllerFile;

        // Cek nama class: prioritaskan PostController lalu fallback ke Post
        $targetClass = null;
        if (class_exists($controllerName . 'Controller')) {
            $targetClass = $controllerName . 'Controller';
        } elseif (class_exists($controllerName)) {
            $targetClass = $controllerName;
        }

        if (!$targetClass) {
            Response::notFound("Class Controller [{$controllerName}Controller] atau [{$controllerName}] tidak ditemukan di dalam file.");
            return;
        }

        $controllerInstance = new $targetClass();

        // 5. Cek method dalam controller
        if (!method_exists($controllerInstance, $methodName) || !is_callable([$controllerInstance, $methodName])) {
            Response::notFound("Method [{$methodName}] tidak ditemukan pada controller [{$targetClass}].");
            return;
        }

        // 6. Panggil method dengan parameter
        call_user_func_array([$controllerInstance, $methodName], $params);
    }

    /**
     * Parse path URL menjadi array segmen
     */
    private function parseUrl(): array
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Hapus query string (?foo=bar)
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        // Hapus subdirektori script name jika framework tidak di-root domain
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && !empty($scriptDir)) {
            if (strpos($uri, $scriptDir) === 0) {
                $uri = substr($uri, strlen($scriptDir));
            } elseif (substr($scriptDir, -7) === '/public') {
                $parentDir = substr($scriptDir, 0, -7);
                if (!empty($parentDir) && strpos($uri, $parentDir) === 0) {
                    $uri = substr($uri, strlen($parentDir));
                }
            }
        }

        // Hapus /index.php jika ada di URL
        $uri = preg_replace('#^/index\.php#i', '', $uri);

        $trimmed = trim($uri, '/');
        if ($trimmed === '') {
            return [];
        }

        return explode('/', $trimmed);
    }
}
