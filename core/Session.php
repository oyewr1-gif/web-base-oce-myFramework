<?php
/**
 * Core Session Manager
 * Mengelola sesi pengguna dan flash notification message
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Pengaturan keamanan cookie session
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Set pesan flash (hanya tampil 1 kali pada request berikutnya)
     */
    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['_flashes'][$type][] = $message;
    }

    /**
     * Ambil pesan flash dan langsung hapus dari session
     */
    public static function getFlashes(): array
    {
        self::start();
        $flashes = $_SESSION['_flashes'] ?? [];
        unset($_SESSION['_flashes']);
        return $flashes;
    }

    public static function hasFlash(string $type): bool
    {
        self::start();
        return !empty($_SESSION['_flashes'][$type]);
    }
}
