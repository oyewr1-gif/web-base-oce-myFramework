<?php
/**
 * Controller Admin Users (Master Pengguna)
 * Mengelola akun pengguna dan penetapan level akses (Admin / Editor)
 * Khusus untuk Administrator (Role Admin)
 */
class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function index(): void
    {
        $api = new ApiClient();
        $users = $api->getUsers();

        $this->view('admin/users/index', [
            'pageTitle' => 'Master Pengguna & Level Akses',
            'users'     => $users
        ], 'layouts/admin');
    }

    public function create(): void
    {
        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('admin/users/create');
                return;
            }

            $name     = trim((string)$this->request->post('name'));
            $username = trim((string)$this->request->post('username'));
            $email    = trim((string)$this->request->post('email'));
            $password = (string)$this->request->post('password');
            $role     = in_array($this->request->post('role'), ['admin', 'editor']) ? $this->request->post('role') : 'editor';

            if (empty($name) || empty($username) || empty($email) || empty($password)) {
                flash('error', 'Nama, username, email, dan password wajib diisi.');
                $this->redirect('admin/users/create');
                return;
            }

            $api = new ApiClient();
            $result = $api->createUser([
                'name'     => $name,
                'username' => $username,
                'email'    => $email,
                'password' => $password,
                'role'     => $role
            ]);

            if ($result) {
                flash('success', "Pengguna baru '{$name}' ({$role}) berhasil ditambahkan.");
                $this->redirect('admin/users');
                return;
            } else {
                flash('error', $api->getLastError() ?: 'Gagal menambahkan pengguna baru.');
                $this->redirect('admin/users/create');
                return;
            }
        }

        $this->view('admin/users/create', [
            'pageTitle' => 'Tambah Pengguna Baru'
        ], 'layouts/admin');
    }

    public function edit(string $id = ''): void
    {
        $id = (int)$id;
        $api = new ApiClient();
        $user = $api->getUser($id);

        if (!$user) {
            flash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('admin/users');
            return;
        }

        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('admin/users/edit/' . $id);
                return;
            }

            $name     = trim((string)$this->request->post('name'));
            $username = trim((string)$this->request->post('username'));
            $email    = trim((string)$this->request->post('email'));
            $password = (string)$this->request->post('password');
            $role     = in_array($this->request->post('role'), ['admin', 'editor']) ? $this->request->post('role') : $user['role'];

            if (empty($name) || empty($username) || empty($email)) {
                flash('error', 'Nama, username, dan email tidak boleh kosong.');
                $this->redirect('admin/users/edit/' . $id);
                return;
            }

            $updateData = [
                'name'     => $name,
                'username' => $username,
                'email'    => $email,
                'role'     => $role
            ];

            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            $result = $api->updateUser($id, $updateData);

            if ($result) {
                flash('success', "Perubahan data pengguna '{$name}' berhasil disimpan.");
                $this->redirect('admin/users');
                return;
            } else {
                flash('error', $api->getLastError() ?: 'Gagal memperbarui data pengguna.');
                $this->redirect('admin/users/edit/' . $id);
                return;
            }
        }

        $this->view('admin/users/edit', [
            'pageTitle' => 'Edit Pengguna: ' . ($user['name'] ?? ''),
            'user'      => $user
        ], 'layouts/admin');
    }

    public function delete(string $id = ''): void
    {
        $id = (int)$id;
        $currentUser = current_user();

        if ($id === (int)($currentUser['id'] ?? 0)) {
            flash('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login.');
            $this->redirect('admin/users');
            return;
        }

        $api = new ApiClient();
        if ($id > 0 && $api->deleteUser($id)) {
            flash('success', 'Pengguna berhasil dihapus.');
        } else {
            flash('error', $api->getLastError() ?: 'Gagal menghapus pengguna.');
        }

        $this->redirect('admin/users');
    }
}
