<?php
/**
 * Core Response Class
 * Mengelola HTTP response status, redirect, dan JSON output
 */
class Response
{
    public static function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public static function header(string $name, string $value): void
    {
        header("{$name}: {$value}");
    }

    public static function redirect(string $url, int $statusCode = 302): void
    {
        self::setStatusCode($statusCode);
        header("Location: {$url}");
        exit;
    }

    public static function json($data, int $statusCode = 200): void
    {
        self::setStatusCode($statusCode);
        self::header('Content-Type', 'application/json; charset=UTF-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function notFound(string $message = '404 - Halaman Tidak Ditemukan'): void
    {
        self::setStatusCode(404);
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><title>404 Not Found</title>"
            . "<style>body{font-family:system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8fafc;color:#1e293b;}"
            . ".box{text-align:center;padding:40px;background:#fff;border-radius:12px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);max-width:480px;width:90%;}"
            . "h1{font-size:72px;margin:0;color:#6366f1;line-height:1;}p{font-size:18px;color:#64748b;margin:16px 0 24px;}"
            . "a{display:inline-block;background:#4f46e5;color:#fff;text-decoration:none;padding:10px 24px;border-radius:6px;font-weight:500;}"
            . "a:hover{background:#4338ca;}</style></head><body>"
            . "<div class='box'><h1>404</h1><p>" . htmlspecialchars($message) . "</p>"
            . "<a href='" . base_url() . "'>&larr; Kembali ke Beranda</a></div></body></html>";
        exit;
    }
}
