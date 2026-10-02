<?php
/**
 * Controller Auth (Headless REST API Client)
 * Mengelola proses masuk (login) dan keluar (logout) 100% via REST API (api-info)
 * Tanpa akses langsung ke model database
 */
class AuthController extends Controller
{
    public function index(): void
    {
        $this->login();
    }

    public function login(): void
    {
        if (is_logged_in()) {
            $this->redirect('admin/dashboard');
            return;
        }

        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('auth/login');
                return;
            }

            $identity = trim((string)$this->request->post('identity'));
            $password = (string)$this->request->post('password');

            if (empty($identity) || empty($password)) {
                flash('error', 'Silakan masukkan username/email dan password.');
                $this->redirect('auth/login');
                return;
            }

            // Otentikasi murni melalui REST API (api-info)
            $api = new ApiClient();
            $authData = $api->login($identity, $password);

            if ($authData && !empty($authData['access_token'])) {
                // Simpan profil user dan token sesi API
                Session::set('user', $authData['user']);
                Session::set('api_token', $authData['access_token']);
                
                flash('success', 'Selamat datang kembali, ' . ($authData['user']['name'] ?? 'Admin') . '!');
                $this->redirect('admin/dashboard');
                return;
            } else {
                $errorMsg = $api->getLastError() ?: 'Email/Username atau password salah.';
                flash('error', $errorMsg);
                $this->redirect('auth/login');
                return;
            }
        }

        $this->view('auth/login', [
            'pageTitle' => 'Masuk ke Admin Panel'
        ], 'layouts/auth');
    }

    public function logout(): void
    {
        $token = Session::get('api_token');
        if (!empty($token)) {
            $api = new ApiClient();
            $api->logout($token);
        }

        Session::remove('user');
        Session::remove('api_token');
        Session::destroy();
        flash('success', 'Anda telah berhasil logout.');
        $this->redirect('auth/login');
    }
}
