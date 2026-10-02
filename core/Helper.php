<?php
/**
 * Global Helper Functions
 * Fungsi bantu esensial untuk view, controller, dan URL
 */

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $config = require APP_PATH . '/config/app.php';
        $baseUrl = rtrim($config['base_url'] ?? '', '/');

        // Deteksi otomatis jika base_url di config kosong
        if (empty($baseUrl)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
            $subDir = dirname($scriptName);
            $subDir = ($subDir === '/' || $subDir === '\\') ? '' : $subDir;

            // Jika URL saat ini diakses tanpa '/public' di path-nya, sesuaikan subDir agar rapi
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            if (strpos($requestUri, '/public') === false && substr($subDir, -7) === '/public') {
                $subDir = substr($subDir, 0, -7);
            }

            $baseUrl = $protocol . $host . $subDir;
        }

        return $baseUrl . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    function upload_url(string $path): string
    {
        return base_url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e(?string $string): string
    {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Request::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        // Ganti karakter non-alfanumerik dengan strip
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Hapus karakter yang tidak diinginkan
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim strip
        $text = trim($text, '-');
        // Hapus duplikasi strip
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);

        return empty($text) ? 'n-a-' . time() : $text;
    }
}

if (!function_exists('flash')) {
    function flash(string $type, string $message): void
    {
        Session::setFlash($type, $message);
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        return Session::get('user');
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return Session::has('user') && !empty(Session::get('user')['id']);
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $user = current_user();
        return $user && ($user['role'] ?? '') === 'admin';
    }
}

if (!function_exists('get_site_setting')) {
    function get_site_setting(string $key, $default = '', bool $fresh = false)
    {
        static $settings = null;
        if ($fresh || $settings === null) {
            try {
                if (class_exists('ApiClient')) {
                    $api = new ApiClient();
                    $settings = $api->getSettings();
                } else {
                    $settings = [];
                }
            } catch (Throwable $e) {
                $settings = [];
            }
        }
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('site_logo_url')) {
    function site_logo_url(?string $customLogo = null, bool $forDark = false): string
    {
        $logo = $customLogo;
        if (empty($logo)) {
            $logo = get_site_setting('site_logo', '');
        }

        if (!empty($logo)) {
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                return $logo;
            }
            if (str_starts_with($logo, 'assets/')) {
                return base_url($logo);
            }
            if (str_starts_with($logo, 'uploads/')) {
                return base_url($logo);
            }
            return upload_url($logo);
        }

        return asset($forDark ? 'images/logo-light.svg' : 'images/logo.svg');
    }
}

