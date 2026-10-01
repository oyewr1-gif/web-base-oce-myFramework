<?php
/**
 * Model ApiPost
 */
class ApiPost extends ApiModel
{
    protected string $table = 'posts';

    public function allPublished(?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.thumbnail, p.status, p.views, p.created_at,
                       u.name as author_name, c.name as category_name, c.slug as category_slug
                FROM {$this->table} p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'published'
                ORDER BY p.id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        return $this->query($sql);
    }

    public function findDetail($idOrSlug): ?array
    {
        $field = is_numeric($idOrSlug) ? 'p.id' : 'p.slug';
        $sql = "SELECT p.*, u.name as author_name, c.name as category_name, c.slug as category_slug
                FROM {$this->table} p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE {$field} = :val LIMIT 1";

        $results = $this->query($sql, ['val' => $idOrSlug]);
        return $results ? $results[0] : null;
    }
}
