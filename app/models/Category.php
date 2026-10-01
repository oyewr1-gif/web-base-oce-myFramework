<?php
/**
 * Model Category
 */
class Category extends Model
{
    protected string $table = 'categories';

    public function findBySlug(string $slug): ?array
    {
        return $this->firstWhere("slug = :slug", ['slug' => $slug]);
    }

    public function allWithCount(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as total_posts 
                FROM {$this->table} c
                LEFT JOIN posts p ON p.category_id = c.id AND p.status = 'published'
                GROUP BY c.id
                ORDER BY c.name ASC";
        return $this->query($sql);
    }
}
