<?php
/**
 * Controller Admin Settings (Headless REST API Client)
 * Mengelola konfigurasi umum situs web via REST API (api-info)
 */
class SettingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function index(): void
    {
        $api = new ApiClient();

        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('admin/settings');
                return;
            }

            $allowedKeys = ['site_title', 'site_tagline', 'admin_email', 'site_logo', 'footer_text'];
            $postData = [];
            foreach ($allowedKeys as $key) {
                if ($this->request->post($key) !== null) {
                    $postData[$key] = trim((string)$this->request->post($key));
                }
            }

            // Opsi 1: Reset / hapus logo kustom jika checkbox remove_logo dicentang
            if ($this->request->post('remove_logo') === '1') {
                $postData['site_logo'] = '';
            }

            // Opsi 2: Unggah berkas logo baru jika pengguna memilih file
            $logoFile = $this->request->file('logo_file');
            if ($logoFile && !empty($logoFile['name']) && ($logoFile['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_OK) {
                $upload = save_uploaded_media($logoFile, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif', 'ico']);
                if (!$upload['success']) {
                    flash('error', 'Gagal mengunggah logo: ' . $upload['error']);
                    $this->redirect('admin/settings');
                    return;
                }

                $postData['site_logo'] = $upload['file_path'];

                // Daftarkan juga metadata logo ke tabel media via REST API
                $api->registerMedia([
                    'filename'      => $upload['filename'],
                    'original_name' => $upload['original_name'],
                    'file_path'     => $upload['file_path'],
                    'mime_type'     => $upload['mime_type'],
                    'file_size'     => $upload['file_size']
                ]);
            }

            $result = $api->updateSettings($postData);
            if ($result) {
                flash('success', 'Pengaturan situs dan logo berhasil diperbarui!');
            } else {
                flash('error', $api->getLastError() ?: 'Gagal memperbarui pengaturan via API.');
            }

            $this->redirect('admin/settings');
            return;
        }

        $settings = $api->getSettings();

        $this->view('admin/settings/index', [
            'pageTitle' => 'Pengaturan Situs',
            'settings'  => $settings
        ], 'layouts/admin');
    }
}
