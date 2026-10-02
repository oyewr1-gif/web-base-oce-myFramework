<?php
/**
 * Model ApiPost
 */
class ApiPost extends ApiModel
{
    protected string $table = 'posts';

    public function allPublished(?int $limit = null, int $offset = 0, $categorySlugOrId = null): array
    {
        $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.thumbnail, p.status, p.views, p.created_at,
                       u.name as author_name, c.name as category_name, c.slug as category_slug
                FROM {$this->table} p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'published'";

        $params = [];
        if ($categorySlugOrId !== null && $categorySlugOrId !== '') {
            if (is_numeric($categorySlugOrId)) {
                $sql .= " AND p.category_id = :cat";
                $params['cat'] = (int)$categorySlugOrId;
            } else {
                $sql .= " AND c.slug = :cat";
                $params['cat'] = $categorySlugOrId;
            }
        }

        $sql .= " ORDER BY p.id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        return $this->query($sql, $params);
    }

    public function countPublished($categorySlugOrId = null): int
    {
        $sql = "SELECT COUNT(p.id) as total
                FROM {$this->table} p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'published'";

        $params = [];
        if ($categorySlugOrId !== null && $categorySlugOrId !== '') {
            if (is_numeric($categorySlugOrId)) {
                $sql .= " AND p.category_id = :cat";
                $params['cat'] = (int)$categorySlugOrId;
            } else {
                $sql .= " AND c.slug = :cat";
                $params['cat'] = $categorySlugOrId;
            }
        }

        $results = $this->query($sql, $params);
        return (int)($results[0]['total'] ?? 0);
    }

    public function findDetail($idOrSlug, bool $incrementView = true): ?array
    {
        $field = is_numeric($idOrSlug) ? 'p.id' : 'p.slug';
        $sql = "SELECT p.*, u.name as author_name, c.name as category_name, c.slug as category_slug
                FROM {$this->table} p
                LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE {$field} = :val LIMIT 1";

        $results = $this->query($sql, ['val' => $idOrSlug]);
        if (!empty($results) && $incrementView) {
            $postId = (int)$results[0]['id'];
            $this->execute("UPDATE {$this->table} SET views = views + 1 WHERE id = :id", ['id' => $postId]);
            $results[0]['views'] = (int)$results[0]['views'] + 1;
        }

        return $results ? $results[0] : null;
    }
}
