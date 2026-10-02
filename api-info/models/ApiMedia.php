<?php
/**
 * Model ApiMedia
 * Mengelola tabel media pada REST API
 */
class ApiMedia extends ApiModel
{
    protected string $table = 'media';

    public function allOrdered(): array
    {
        return $this->all('id DESC');
    }
}
