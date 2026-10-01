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
            'footer_text'  => $settings['footer_text'] ?? ''
        ];

        ApiResponse::success($publicSettings, "Pengaturan situs berhasil diambil.");
    }
}
