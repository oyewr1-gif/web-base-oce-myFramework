<?php
/**
 * Core Base Controller
 * Menyediakan method view rendering, master layout, model loader, dan auth guard
 */
class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Render view dengan master layout via output buffering
     *
     * @param string $view Nama file view relatif terhadap app/views/ (tanpa .php)
     * @param array $data Data variabel yang akan di-extract ke dalam view
     * @param string|null $layout Nama file master layout (default: layouts/frontend). Beri null jika tanpa layout.
     */
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/frontend'): void
    {
        // Extract data ke scope view
        extract($data);

        $viewFile = APP_PATH . '/views/' . ltrim($view, '/') . '.php';

        if (!file_exists($viewFile)) {
            die("View file tidak ditemukan: <code>{$viewFile}</code>");
        }

        // Mulai buffering konten spesifik halaman
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Jika layout ditentukan, bungkus konten ke dalam layout
        if ($layout !== null) {
            $layoutFile = APP_PATH . '/views/' . ltrim($layout, '/') . '.php';
            if (!file_exists($layoutFile)) {
                die("Master layout file tidak ditemukan: <code>{$layoutFile}</code>");
            }
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Instansiasi Model
     */
    protected function model(string $modelName)
    {
        $className = basename(str_replace('\\', '/', $modelName));

        // 1. Selalu prioritaskan memuat file model dari app/models/
        $file = APP_PATH . '/models/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
        }

        // 2. Pastikan class turunan dari Model
        if (class_exists($className) && is_subclass_of($className, 'Model')) {
            return new $className();
        }

        if (class_exists($className)) {
            return new $className();
        }

        throw new Exception("Model [{$className}] tidak ditemukan.");
    }

    /**
     * Redirect ke path tertentu
     */
    protected function redirect(string $path): void
    {
        Response::redirect(base_url($path));
    }

    /**
     * Output JSON
     */
    protected function json($data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }

    /**
     * Proteksi autentikasi: wajib login
     */
    protected function requireAuth(): void
    {
        if (!is_logged_in()) {
            flash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
            $this->redirect('auth/login');
        }
    }

    /**
     * Proteksi otorisasi: wajib role admin
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!is_admin()) {
            flash('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            $this->redirect('admin/dashboard');
        }
    }
}
