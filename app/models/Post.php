<?php
/**
 * Model Post
 */
class Post extends Model
{
    protected string $table = 'posts';

    public function allWithRelations(?string $status = null, ?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT p.*, u.name as author_name, c.name as category_name, c.slug as category_slug
                FROM {$this->table} p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id";
        
        $params = [];
        if ($status !== null) {
            $sql .= " WHERE p.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY p.id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        return $this->query($sql, $params);
    }

    public function findBySlugWithRelations(string $slug): ?array
    {
        $sql = "SELECT p.*, u.name as author_name, c.name as category_name, c.slug as category_slug
                FROM {$this->table} p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.slug = :slug LIMIT 1";
        
        $results = $this->query($sql, ['slug' => $slug]);
        return $results ? $results[0] : null;
    }

    public function findWithRelations(int $id): ?array
    {
        $sql = "SELECT p.*, u.name as author_name, c.name as category_name
                FROM {$this->table} p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.id = :id LIMIT 1";
        
        $results = $this->query($sql, ['id' => $id]);
        return $results ? $results[0] : null;
    }

    public function getByCategory(int $categoryId, ?int $limit = null): array
    {
        $sql = "SELECT p.*, u.name as author_name, c.name as category_name
                FROM {$this->table} p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.category_id = :cat_id AND p.status = 'published'
                ORDER BY p.id DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->query($sql, ['cat_id' => $categoryId]);
    }

    public function incrementViews(int $id): void
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = :id";
        $this->execute($sql, ['id' => $id]);
    }
}
