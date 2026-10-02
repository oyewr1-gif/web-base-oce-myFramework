<?php
/**
 * Core ApiClient
 * Klien HTTP mandiri (Zero-dependency) untuk mengonsumsi REST API (api-info)
 * Menggunakan cURL dengan fallback ke file_get_contents stream context
 */
class ApiClient
{
    private string $baseUrl;
    private int $timeout;
    private ?string $lastError = null;
    private ?int $lastStatusCode = null;

    public function __construct(?string $baseUrl = null, int $timeout = 6)
    {
        // 1. Ambil URL API dari variabel lingkungan .env
        $configuredUrl = env('API_BASE_URL');

        if (!empty($baseUrl)) {
            $this->baseUrl = rtrim($baseUrl, '/');
        } elseif (!empty($configuredUrl)) {
            $this->baseUrl = rtrim($configuredUrl, '/');
        } else {
            // Default: arahkan ke subfolder api-info
            $this->baseUrl = base_url('api-info');
        }

        $this->timeout = $timeout;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function getLastStatusCode(): ?int
    {
        return $this->lastStatusCode;
    }

    /**
     * Kirim HTTP Request ke endpoint API
     */
    public function request(string $method, string $endpoint, array $data = [], ?string $token = null): ?array
    {
        $this->lastError = null;
        $this->lastStatusCode = null;

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $method = strtoupper($method);

        // Jika request GET dan ada parameter data, tambahkan sebagai query string
        if ($method === 'GET' && !empty($data)) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($data);
        }

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        // Ambil token dari parameter atau dari session jika tidak disediakan
        $token = $token ?: (Session::get('api_token') ?: Session::get('token'));
        if (empty($token) && Session::has('user')) {
            $token = env('API_DEFAULT_TOKEN', 'test-token-cms-info-2026');
            Session::set('api_token', $token);
        }

        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
            $headers[] = 'X-API-KEY: ' . $token;
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'api_token=' . urlencode($token);
        }

        if (function_exists('curl_init')) {
            return $this->requestCurl($method, $url, $data, $headers);
        } else {
            return $this->requestStream($method, $url, $data, $headers);
        }
    }

    private function requestCurl(string $method, string $url, array $data, array $headers): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $this->lastStatusCode = $httpCode;

        if ($response === false) {
            $this->lastError = 'Koneksi ke API gagal: ' . ($curlError ?: 'Request timeout');
            return null;
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            $this->lastError = 'Format respons API tidak valid: ' . substr(strip_tags($response), 0, 200);
            return null;
        }

        if ($httpCode >= 400 || ($decoded['status'] ?? '') === 'error') {
            $this->lastError = $decoded['message'] ?? ("Kesalahan HTTP " . $httpCode);
            return $decoded;
        }

        return $decoded;
    }

    private function requestStream(string $method, string $url, array $data, array $headers): ?array
    {
        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => implode("\r\n", $headers),
                'timeout' => $this->timeout,
                'ignore_errors' => true
            ]
        ];

        if (in_array($method, ['POST', 'PUT']) && !empty($data)) {
            $opts['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            $this->lastError = 'Koneksi ke API gagal via stream context.';
            return null;
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            $this->lastError = 'Format respons API tidak valid: ' . substr(strip_tags($response), 0, 200);
            return null;
        }

        if (($decoded['status'] ?? '') === 'error') {
            $this->lastError = $decoded['message'] ?? 'Kesalahan pada server API.';
            return $decoded;
        }

        return $decoded;
    }

    // ========================================================
    // 1. AUTENTIKASI VIA REST API
    // ========================================================

    public function login(string $identity, string $password): ?array
    {
        $resp = $this->request('POST', 'auth/login', [
            'identity' => $identity,
            'password' => $password
        ]);

        if ($resp && ($resp['status'] ?? '') === 'success' && !empty($resp['data']['access_token'])) {
            return $resp['data'];
        }

        return null;
    }

    public function logout(?string $token = null): bool
    {
        $resp = $this->request('POST', 'auth/logout', [], $token);
        return $resp !== null && ($resp['status'] ?? '') === 'success';
    }

    public function me(?string $token = null): ?array
    {
        $resp = $this->request('GET', 'auth/me', [], $token);
        return $resp['data'] ?? null;
    }

    // ========================================================
    // 2. DASHBOARD & STATISTIK
    // ========================================================

    public function getDashboardStats(?string $token = null): array
    {
        $resp = $this->request('GET', 'dashboard/stats', [], $token);
        return $resp['data'] ?? [
            'stats' => [
                'total_posts'      => 0,
                'published_posts'  => 0,
                'total_categories' => 0,
                'total_media'      => 0,
                'total_users'      => 0,
            ],
            'recent_posts' => []
        ];
    }

    // ========================================================
    // 3. ARTIKEL (POSTS)
    // ========================================================

    /**
     * Ambil daftar artikel publik (portal pengunjung)
     */
    public function getPosts(?int $limit = 9, int $page = 1, ?string $category = null): array
    {
        $params = [
            'limit' => $limit,
            'page'  => $page
        ];

        if (!empty($category)) {
            $params['category'] = $category;
        }

        $resp = $this->request('GET', 'posts', $params);
        return [
            'data' => $resp['data'] ?? [],
            'meta' => $resp['meta'] ?? []
        ];
    }

    /**
     * Ambil seluruh artikel untuk panel admin (termasuk draft)
     */
    public function getAdminPosts(?string $token = null, ?string $status = null, int $page = 1, int $limit = 50): array
    {
        $params = [
            'admin' => 1,
            'page'  => $page,
            'limit' => $limit
        ];
        if (!empty($status)) {
            $params['status'] = $status;
        }

        $resp = $this->request('GET', 'posts', $params, $token);
        return $resp['data'] ?? [];
    }

    /**
     * Ambil detail artikel berdasarkan ID atau Slug
     */
    public function getPost(string $idOrSlug, bool $incrementView = true): ?array
    {
        $endpoint = 'posts/read/' . rawurlencode($idOrSlug);
        if (!$incrementView) {
            $endpoint .= '?no_view=1';
        }
        $resp = $this->request('GET', $endpoint);
        return $resp['data'] ?? null;
    }

    public function createPost(array $data, ?string $token = null): ?array
    {
        $resp = $this->request('POST', 'posts/create', $data, $token);
        if ($resp && ($resp['status'] ?? '') === 'success') {
            return $resp['data'] ?? [];
        }
        return null;
    }

    public function updatePost(int $id, array $data, ?string $token = null): ?array
    {
        $resp = $this->request('PUT', 'posts/update/' . $id, $data, $token);
        if ($resp && ($resp['status'] ?? '') === 'success') {
            return $resp['data'] ?? [];
        }
        return null;
    }

    public function deletePost(int $id, ?string $token = null): bool
    {
        $resp = $this->request('DELETE', 'posts/delete/' . $id, [], $token);
        return $resp !== null && ($resp['status'] ?? '') === 'success';
    }

    // ========================================================
    // 4. KATEGORI (CATEGORIES)
    // ========================================================

    public function getCategories(): array
    {
        $resp = $this->request('GET', 'categories');
        return $resp['data'] ?? [];
    }

    public function getCategory(string $idOrSlug): ?array
    {
        $resp = $this->request('GET', 'categories/read/' . rawurlencode($idOrSlug));
        return $resp['data'] ?? null;
    }

    public function createCategory(array $data, ?string $token = null): ?array
    {
        $resp = $this->request('POST', 'categories/create', $data, $token);
        if ($resp && ($resp['status'] ?? '') === 'success') {
            return $resp['data'] ?? [];
        }
        return null;
    }

    public function updateCategory(int $id, array $data, ?string $token = null): ?array
    {
        $resp = $this->request('PUT', 'categories/update/' . $id, $data, $token);
        if ($resp && ($resp['status'] ?? '') === 'success') {
            return $resp['data'] ?? [];
        }
        return null;
    }

    public function deleteCategory(int $id, ?string $token = null): bool
    {
        $resp = $this->request('DELETE', 'categories/delete/' . $id, [], $token);
        return $resp !== null && ($resp['status'] ?? '') === 'success';
    }

    // ========================================================
    // 5. MEDIA & FILE MANAGER
    // ========================================================

    public function getMedia(?string $token = null): array
    {
        $resp = $this->request('GET', 'media', [], $token);
        return $resp['data'] ?? [];
    }

    public function uploadMedia(array $file, ?string $token = null): ?array
    {
        $token = $token ?: (Session::get('api_token') ?: Session::get('token'));
        if (empty($token) && Session::has('user')) {
            $token = env('API_DEFAULT_TOKEN', 'test-token-cms-info-2026');
            Session::set('api_token', $token);
        }

        $url = $this->baseUrl . '/media/upload';

        if (!function_exists('curl_init')) {
            $this->lastError = 'cURL extension diperlukan untuk upload file via API.';
            return null;
        }

        $cfile = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
        $postData = [
            'media_file' => $cfile,
        ];
        if (!empty($token)) {
            $postData['api_token'] = $token;
        }

        $headers = [
            'Accept: application/json',
        ];
        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
            $headers[] = 'X-API-KEY: ' . $token;
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'api_token=' . urlencode($token);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->lastStatusCode = $httpCode;
        if ($response === false) {
            $this->lastError = 'Gagal menghubungi server upload API.';
            return null;
        }

        $decoded = json_decode($response, true);
        if ($httpCode >= 400 || ($decoded['status'] ?? '') === 'error') {
            $this->lastError = $decoded['message'] ?? 'Gagal mengunggah file ke API.';
            return null;
        }

        return $decoded['data'] ?? null;
    }

    public function deleteMedia(int $id, ?string $token = null): bool
    {
        $resp = $this->request('DELETE', 'media/delete/' . $id, [], $token);
        return $resp !== null && ($resp['status'] ?? '') === 'success';
    }

    // ========================================================
    // 6. PENGATURAN SITUS (SETTINGS)
    // ========================================================

    public function getSettings(?string $token = null): array
    {
        $token = $token ?: (Session::get('api_token') ?: Session::get('token'));
        $endpoint = !empty($token) ? 'settings/all' : 'settings';
        $resp = $this->request('GET', $endpoint, [], $token);
        return $resp['data'] ?? [];
    }

    public function updateSettings(array $settings, ?string $token = null): ?array
    {
        $resp = $this->request('POST', 'settings/update', $settings, $token);
        if ($resp && ($resp['status'] ?? '') === 'success') {
            return $resp['data'] ?? [];
        }
        return null;
    }
}
