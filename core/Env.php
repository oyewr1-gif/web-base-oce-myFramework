<?php
/**
 * Core Env Loader
 * Membaca variabel lingkungan dari file .env secara mandiri (Zero-dependency)
 */
class Env
{
    private static bool $loaded = false;

    public static function load(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Abaikan baris komentar atau baris kosong
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Ambil pasangan key dan value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Bersihkan kutip ganda atau tunggal di ujung nilai
                $len = strlen($value);
                if ($len >= 2) {
                    if (($value[0] === '"' && $value[$len - 1] === '"') ||
                        ($value[0] === "'" && $value[$len - 1] === "'")) {
                        $value = substr($value, 1, -1);
                    }
                }

                // Parsing tipe data umum
                $lowerVal = strtolower($value);
                if ($lowerVal === 'true' || $lowerVal === '(true)') {
                    $value = true;
                } elseif ($lowerVal === 'false' || $lowerVal === '(false)') {
                    $value = false;
                } elseif ($lowerVal === 'null' || $lowerVal === '(null)') {
                    $value = null;
                } elseif ($lowerVal === 'empty' || $lowerVal === '(empty)') {
                    $value = '';
                }

                $_ENV[$key] = $value;
                if (is_scalar($value)) {
                    putenv("{$key}={$value}");
                }
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        $val = getenv($key);
        if ($val !== false) {
            return $val;
        }

        return $default;
    }

    public static function isLoaded(): bool
    {
        return self::$loaded;
    }
}
