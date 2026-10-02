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

    public function __construct(?string $baseUrl = null, int $timeout = 5)
    {
        // 1. Ambil URL API dari variabel lingkungan .env
        $configuredUrl = env('API_BASE_URL');

        if (!empty($baseUrl)) {
            $this->baseUrl = rtrim($baseUrl, '/');
        } elseif (!empty($configuredUrl)) {
            $this->baseUrl = rtrim($configuredUrl, '/');
        } else {
            // Default lokal: arahkan ke subfolder api-info
            $this->baseUrl = base_url('api-info');
        }

        $this->timeout = $timeout;
    }

    /**
     * Kirim HTTP Request ke endpoint API
     */
    public function request(string $method, string $endpoint, array $data = [], ?string $token = null): ?array
    {
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

        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        // Cek ketersediaan cURL
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Toleran untuk dev lokal

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
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
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
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    // ========================================================
    // HELPER KHUSUS KONTEN PORTAL FRONT-END
    // ========================================================

    /**
     * Ambil daftar artikel publik (dengan opsi kategori & pagination)
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
     * Ambil detail artikel berdasarkan ID atau Slug
     */
    public function getPost(string $idOrSlug): ?array
    {
        $resp = $this->request('GET', 'posts/read/' . rawurlencode($idOrSlug));
        return $resp['data'] ?? null;
    }

    /**
     * Ambil seluruh kategori artikel
     */
    public function getCategories(): array
    {
        $resp = $this->request('GET', 'categories');
        return $resp['data'] ?? [];
    }

    /**
     * Ambil detail kategori berdasarkan ID atau Slug
     */
    public function getCategory(string $idOrSlug): ?array
    {
        $resp = $this->request('GET', 'categories/read/' . rawurlencode($idOrSlug));
        return $resp['data'] ?? null;
    }

    /**
     * Ambil pengaturan publik situs web
     */
    public function getSettings(): array
    {
        $resp = $this->request('GET', 'settings');
        return $resp['data'] ?? [];
    }
}
