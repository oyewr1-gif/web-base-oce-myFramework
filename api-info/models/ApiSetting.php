<?php
/**
 * Model ApiSetting
 */
class ApiSetting extends ApiModel
{
    protected string $table = 'settings';

    public function allAsKeyVal(): array
    {
        $rows = $this->all();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function set(string $key, ?string $value): bool
    {
        $existing = $this->firstWhere("setting_key = :k", ['k' => $key]);
        if ($existing) {
            $sql = "UPDATE {$this->table} SET setting_value = :v WHERE setting_key = :k";
            return $this->execute($sql, ['v' => $value, 'k' => $key]) >= 0;
        } else {
            $sql = "INSERT INTO {$this->table} (setting_key, setting_value) VALUES (:k, :v)";
            return $this->execute($sql, ['k' => $key, 'v' => $value]) > 0;
        }
    }
}
