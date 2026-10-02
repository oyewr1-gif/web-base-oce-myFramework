<?php
/**
 * Controller User REST API (Master Users)
 * Mengelola data pengguna dan peran hak akses (Admin & Editor)
 * Khusus untuk Administrator
 */
class UserController extends ApiController
{
    public function index(): void
    {
        $this->requireAdmin();
        $userModel = new ApiUser();
        $users = $userModel->allSafe();

        ApiResponse::success($users, "Daftar pengguna berhasil diambil.");
    }

    public function read($id = ''): void
    {
        $this->requireAdmin();
        $id = (int)$id;

        $userModel = new ApiUser();
        $user = $userModel->findSafe($id);

        if (!$user) {
            ApiResponse::error("Pengguna tidak ditemukan.", 404);
            return;
        }

        ApiResponse::success($user, "Detail pengguna berhasil diambil.");
    }

    public function create(): void
    {
        $this->requireAdmin();

        $name     = trim((string)$this->input('name'));
        $username = trim((string)$this->input('username'));
        $email    = trim((string)$this->input('email'));
        $password = (string)$this->input('password');
        $role     = in_array($this->input('role'), ['admin', 'editor']) ? $this->input('role') : 'editor';

        if (empty($name) || empty($username) || empty($email) || empty($password)) {
            ApiResponse::error("Field nama, username, email, dan password wajib diisi.", 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ApiResponse::error("Format alamat email tidak valid.", 422);
            return;
        }

        $userModel = new ApiUser();

        // Cek keunikan username
        if ($userModel->findByUsername($username)) {
            ApiResponse::error("Username '{$username}' sudah digunakan oleh akun lain.", 422);
            return;
        }

        // Cek keunikan email
        if ($userModel->findByEmail($email)) {
            ApiResponse::error("Alamat email '{$email}' sudah terdaftar.", 422);
            return;
        }

        $insertData = [
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role'     => $role
        ];

        $userId = $userModel->insert($insertData);
        $createdUser = $userModel->findSafe((int)$userId);

        ApiResponse::success($createdUser, "Pengguna baru berhasil ditambahkan.", 201);
    }

    public function update($id = ''): void
    {
        $this->requireAdmin();
        $id = (int)$id;

        $userModel = new ApiUser();
        $user = $userModel->find($id);

        if (!$user) {
            ApiResponse::error("Pengguna tidak ditemukan.", 404);
            return;
        }

        $name     = trim((string)$this->input('name', $user['name']));
        $username = trim((string)$this->input('username', $user['username']));
        $email    = trim((string)$this->input('email', $user['email']));
        $password = (string)$this->input('password', '');
        $role     = in_array($this->input('role'), ['admin', 'editor']) ? $this->input('role') : $user['role'];

        if (empty($name) || empty($username) || empty($email)) {
            ApiResponse::error("Field nama, username, dan email tidak boleh kosong.", 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ApiResponse::error("Format alamat email tidak valid.", 422);
            return;
        }

        // Cek keunikan username untuk akun lain
        if ($userModel->findByUsername($username, $id)) {
            ApiResponse::error("Username '{$username}' sudah digunakan oleh pengguna lain.", 422);
            return;
        }

        // Cek keunikan email untuk akun lain
        if ($userModel->findByEmail($email, $id)) {
            ApiResponse::error("Alamat email '{$email}' sudah digunakan oleh pengguna lain.", 422);
            return;
        }

        $updateData = [
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
            'role'     => $role
        ];

        // Jika password diisi, perbarui password hash
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $userModel->update($id, $updateData);
        $updatedUser = $userModel->findSafe($id);

        ApiResponse::success($updatedUser, "Data pengguna berhasil diperbarui.");
    }

    public function delete($id = ''): void
    {
        $admin = $this->requireAdmin();
        $id = (int)$id;

        if ($id <= 0) {
            ApiResponse::error("ID pengguna tidak valid.", 400);
            return;
        }

        // Cegah menghapus akun sendiri
        if ($id === (int)($admin['user_id'] ?? 0)) {
            ApiResponse::error("Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login.", 403);
            return;
        }

        $userModel = new ApiUser();
        $user = $userModel->find($id);

        if (!$user) {
            ApiResponse::error("Pengguna tidak ditemukan.", 404);
            return;
        }

        $userModel->delete($id);
        ApiResponse::success(null, "Pengguna '{$user['name']}' ({$user['username']}) berhasil dihapus.");
    }
}
