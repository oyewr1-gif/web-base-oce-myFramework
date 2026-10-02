<?php
/**
 * Base Controller Mandiri untuk REST API (api-info)
 * Mengelola parsing JSON request dan autentikasi Bearer Token
 */
class ApiController
{
    protected array $input = [];
    protected ?array $currentUser = null;

    public function __construct()
    {
        // Parse JSON raw payload dari request body
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->input = $decoded;
            }
        }

        // Gabungkan juga data dari $_POST dan $_GET
        $this->input = array_merge($_GET, $_POST, $this->input);
    }

    protected function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    protected function input(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->input;
        }
        return $this->input[$key] ?? $default;
    }

    /**
     * Middleware Proteksi Endpoint REST API via Token
     */
    protected function requireAuth(): array
    {
        $token = $this->extractToken();

        if (empty($token)) {
            ApiResponse::error("Akses ditolak. Header 'Authorization: Bearer <token>' atau 'X-API-KEY' tidak ditemukan.", 401);
        }

        $db = ApiDatabase::getConnection();
        $stmt = $db->prepare("SELECT t.*, u.id as user_id, u.username, u.name, u.email, u.role
                              FROM api_tokens t
                              JOIN users u ON u.id = t.user_id
                              WHERE t.token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();

        if (!$user) {
            ApiResponse::error("Token otentikasi tidak valid atau telah kedaluwarsa.", 401);
        }

        $this->currentUser = $user;
        return $user;
    }

    private function extractToken(): ?string
    {
        // 1. Cek dari getallheaders() (Apache / FPM)
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $auth = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
            if (!empty($auth) && preg_match('/Bearer\s+(\S+)/i', $auth, $matches)) {
                return $matches[1];
            }
            if (!empty($headers['X-API-KEY'] ?? ($headers['x-api-key'] ?? ''))) {
                return $headers['X-API-KEY'] ?? $headers['x-api-key'];
            }
        }

        // 2. Cek dari $_SERVER environment
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
        if (!empty($authHeader) && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            return $matches[1];
        }

        if (!empty($_SERVER['HTTP_X_API_KEY'])) {
            return $_SERVER['HTTP_X_API_KEY'];
        }

        // 3. Fallback dari query parameter atau post parameter
        if (!empty($_GET['api_token'])) {
            return $_GET['api_token'];
        }
        if (!empty($_GET['token'])) {
            return $_GET['token'];
        }
        if (!empty($_POST['api_token'])) {
            return $_POST['api_token'];
        }
        if (!empty($_POST['token'])) {
            return $_POST['token'];
        }
        if (!empty($this->input['api_token'])) {
            return $this->input['api_token'];
        }
        if (!empty($this->input['token'])) {
            return $this->input['token'];
        }

        return null;
    }
}
