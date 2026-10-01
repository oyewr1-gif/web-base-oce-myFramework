<?php
/**
 * Model Setting
 * Menyimpan konfigurasi dinamis situs web
 */
class Setting extends Model
{
    protected string $table = 'settings';
    protected string $primaryKey = 'id';

    public function get(string $key, $default = null): ?string
    {
        $setting = $this->firstWhere("setting_key = :key", ['key' => $key]);
        return $setting ? $setting['setting_value'] : $default;
    }

    public function set(string $key, string $value): bool
    {
        $existing = $this->firstWhere("setting_key = :key", ['key' => $key]);
        if ($existing) {
            return $this->update($existing['id'], ['setting_value' => $value]);
        } else {
            return (bool)$this->insert([
                'setting_key'   => $key,
                'setting_value' => $value
            ]);
        }
    }

    public function allAsKeyVal(): array
    {
        $rows = $this->all();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
}
