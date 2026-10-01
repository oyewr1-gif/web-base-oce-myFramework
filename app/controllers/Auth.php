<?php
/**
 * Controller Auth
 * Mengelola proses masuk (login) dan keluar (logout)
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

            $userModel = $this->model('User');
            $user = $userModel->authenticate($identity, $password);

            if ($user) {
                // Simpan user info ke session (kecuali password hash)
                unset($user['password']);
                Session::set('user', $user);
                flash('success', 'Selamat datang kembali, ' . $user['name'] . '!');
                $this->redirect('admin/dashboard');
                return;
            } else {
                flash('error', 'Email/Username atau password salah.');
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
        Session::remove('user');
        Session::destroy();
        flash('success', 'Anda telah berhasil logout.');
        $this->redirect('auth/login');
    }
}
