<?php
/**
 * Standarisasi Response JSON & CORS untuk REST API (api-info)
 */
class ApiResponse
{
    public static function setCorsHeaders(): void
    {
        $config = require API_PATH . '/config/app.php';
        $origin = $config['cors_origin'] ?? '*';

        header("Access-Control-Allow-Origin: {$origin}");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY, X-Requested-With");
        header("Access-Control-Max-Age: 86400");

        // Handle browser preflight OPTIONS request
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    public static function success($data = null, string $message = 'Success', int $statusCode = 200, array $meta = []): void
    {
        self::setCorsHeaders();
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');

        $response = [
            'status'  => 'success',
            'code'    => $statusCode,
            'message' => $message,
            'data'    => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(string $message = 'Terjadi kesalahan', int $statusCode = 400, array $errors = []): void
    {
        self::setCorsHeaders();
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');

        $response = [
            'status'  => 'error',
            'code'    => $statusCode,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
