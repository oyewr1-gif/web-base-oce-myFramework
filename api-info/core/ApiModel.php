<?php
/**
 * Base Model Mandiri untuk REST API (api-info)
 */
class ApiModel
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = ApiDatabase::getConnection();
        if (empty($this->table)) {
            $className = strtolower((new ReflectionClass($this))->getShortName());
            $this->table = str_replace('api', '', $className) . 's';
        }
    }

    public function find($id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit !== null) $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function where(string $condition, array $params = [], string $orderBy = 'id DESC', ?int $limit = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$condition}";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit !== null) $sql .= " LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function firstWhere(string $condition, array $params = []): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$condition} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(array $data)
    {
        $fields = array_keys($data);
        $columns = implode(', ', array_map(fn($f) => "`{$f}`", $fields));
        $placeholders = ':' . implode(', :', $fields);

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $this->db->lastInsertId() ?: true;
    }

    public function update($id, array $data): bool
    {
        $setClauses = [];
        $params = ['__pk_id' => $id];

        foreach ($data as $column => $value) {
            $setClauses[] = "`{$column}` = :{$column}";
            $params[$column] = $value;
        }

        $setSql = implode(', ', $setClauses);
        $sql = "UPDATE {$this->table} SET {$setSql} WHERE `{$this->primaryKey}` = :__pk_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE `{$this->primaryKey}` = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
