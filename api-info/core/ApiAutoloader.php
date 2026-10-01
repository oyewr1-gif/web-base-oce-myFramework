<?php
/**
 * Autoloader Mandiri untuk aplikasi REST API (api-info)
 */
class ApiAutoloader
{
    private static array $directories = [];

    public static function register(array $dirs = []): void
    {
        self::$directories = $dirs;
        spl_autoload_register([__CLASS__, 'load']);
    }

    public static function load(string $className): void
    {
        $classFile = basename(str_replace('\\', '/', $className)) . '.php';

        foreach (self::$directories as $dir) {
            $path = $dir . DIRECTORY_SEPARATOR . $classFile;
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
    }
}
