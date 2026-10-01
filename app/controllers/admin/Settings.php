<?php
/**
 * Controller Admin Settings
 * Mengelola konfigurasi umum situs web
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
        $settingModel = $this->model('Setting');

        if ($this->request->isPost()) {
            if (!Request::validateCsrf()) {
                flash('error', 'Token keamanan (CSRF) tidak valid.');
                $this->redirect('admin/settings');
                return;
            }

            $allowedKeys = ['site_title', 'site_tagline', 'admin_email', 'footer_text'];

            foreach ($allowedKeys as $key) {
                $val = trim((string)$this->request->post($key));
                $settingModel->set($key, $val);
            }

            flash('success', 'Pengaturan situs web berhasil diperbarui!');
            $this->redirect('admin/settings');
            return;
        }

        $settings = $settingModel->allAsKeyVal();

        $this->view('admin/settings/index', [
            'pageTitle' => 'Pengaturan Situs',
            'settings'  => $settings
        ], 'layouts/admin');
    }
}
