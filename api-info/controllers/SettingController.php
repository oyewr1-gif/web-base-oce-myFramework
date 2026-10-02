<?php
/**
 * Controller Setting REST API
 */
class SettingController extends ApiController
{
    public function index(): void
    {
        $settingModel = new ApiSetting();
        $settings = $settingModel->allAsKeyVal();

        // Hanya tampilkan setting publik
        $publicSettings = [
            'site_title'   => $settings['site_title'] ?? '',
            'site_tagline' => $settings['site_tagline'] ?? '',
            'site_logo'    => $settings['site_logo'] ?? '',
            'footer_text'  => $settings['footer_text'] ?? ''
        ];

        ApiResponse::success($publicSettings, "Pengaturan situs berhasil diambil.");
    }

    public function all(): void
    {
        $this->requireAdmin();
        $settingModel = new ApiSetting();
        $settings = $settingModel->allAsKeyVal();

        ApiResponse::success($settings, "Semua pengaturan situs berhasil diambil.");
    }

    public function update(): void
    {
        $this->requireAdmin();

        $settingModel = new ApiSetting();
        $allowedKeys = ['site_title', 'site_tagline', 'admin_email', 'site_logo', 'footer_text'];
        
        $updated = [];
        foreach ($allowedKeys as $key) {
            $val = $this->input($key);
            if ($val !== null) {
                $settingModel->set($key, trim((string)$val));
                $updated[$key] = trim((string)$val);
            }
        }

        ApiResponse::success($updated, "Pengaturan situs berhasil diperbarui.");
    }
}
