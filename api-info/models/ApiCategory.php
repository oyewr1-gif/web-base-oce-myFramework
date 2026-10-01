<?php
/**
 * Model ApiCategory
 */
class ApiCategory extends ApiModel
{
    protected string $table = 'categories';

    public function allWithCount(): array
    {
        $sql = "SELECT c.id, c.name, c.slug, c.description, COUNT(p.id) as total_posts
                FROM {$this->table} c
                LEFT JOIN posts p ON p.category_id = c.id AND p.status = 'published'
                GROUP BY c.id
                ORDER BY c.name ASC";
        return $this->query($sql);
    }
}
