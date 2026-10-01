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
}
