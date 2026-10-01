<?php
/**
 * Core Autoloader
 * Mengelola autoloading class tanpa Composer
 */
class Autoloader
{
    private static array $dirs = [];

    public static function register(array $directories = []): void
    {
        self::$dirs = $directories;
        spl_autoload_register([__CLASS__, 'load']);
    }

    public static function addDirectory(string $dir): void
    {
        if (is_dir($dir) && !in_array($dir, self::$dirs)) {
            self::$dirs[] = rtrim($dir, '/\\');
        }
    }

    public static function load(string $className): void
    {
        // Ganti namespace backslash dengan directory separator
        $cleanedClass = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $basename = basename($cleanedClass);

        // 1. Cek langsung di direktori yang terdaftar
        foreach (self::$dirs as $dir) {
            $file = $dir . DIRECTORY_SEPARATOR . $basename . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }

            // Jika class berakhiran Controller, coba cari berkas tanpa kata Controller (contoh: PostController -> Post.php)
            if (substr($basename, -10) === 'Controller') {
                $shortName = substr($basename, 0, -10);
                $fileShort = $dir . DIRECTORY_SEPARATOR . $shortName . '.php';
                if (file_exists($fileShort)) {
                    require_once $fileShort;
                    return;
                }
            }

            // Cek path bersarang jika class memiliki namespace (misal Admin\Dashboard)
            $nestedFile = $dir . DIRECTORY_SEPARATOR . $cleanedClass . '.php';
            if (file_exists($nestedFile)) {
                require_once $nestedFile;
                return;
            }
        }
    }
}
