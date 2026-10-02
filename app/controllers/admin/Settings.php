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

            $allowedKeys = ['site_title', 'site_tagline', 'admin_email', 'footer_text'];
            $postData = [];
            foreach ($allowedKeys as $key) {
                $postData[$key] = trim((string)$this->request->post($key));
            }

            $result = $api->updateSettings($postData);
            if ($result) {
                flash('success', 'Pengaturan situs web berhasil diperbarui via API!');
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
