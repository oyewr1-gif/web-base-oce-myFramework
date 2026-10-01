<?php
/**
 * Controller Auth REST API
 */
class AuthController extends ApiController
{
    public function login(): void
    {
        $identity = trim((string)$this->input('identity', $this->input('username', $this->input('email', ''))));
        $password = (string)$this->input('password', '');

        if (empty($identity) || empty($password)) {
            ApiResponse::error("Kredensial tidak lengkap. Kirim 'identity' (email/username) dan 'password'.", 422);
            return;
        }

        $userModel = new ApiUser();
        $user = $userModel->authenticate($identity, $password);

        if (!$user) {
            ApiResponse::error("Email/username atau password salah.", 401);
            return;
        }

        // Buat token baru
        $token = $userModel->createToken((int)$user['id'], 'Mobile/Web Client - ' . date('Y-m-d H:i'));

        ApiResponse::success([
            'token_type'   => 'Bearer',
            'access_token' => $token,
            'user'         => [
                'id'       => (int)$user['id'],
                'username' => $user['username'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'role'     => $user['role']
            ]
        ], 'Login berhasil. Gunakan access_token pada header Authorization.');
    }

    public function me(): void
    {
        $user = $this->requireAuth();
        ApiResponse::success([
            'id'         => (int)$user['user_id'],
            'username'   => $user['username'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'token_name' => $user['name']
        ], 'Data profil pengguna terautentikasi.');
    }
}
