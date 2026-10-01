<?php
/**
 * Core Base Model
 * Menyediakan CRUD helper bawaan berbasis PDO
 */
class Model
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getConnection();
        if (empty($this->table)) {
            // Default nama tabel: pluralized lowercase dari nama class
            $className = strtolower((new ReflectionClass($this))->getShortName());
            $this->table = $className . 's';
        }
    }

    /**
     * Cari satu record berdasarkan primary key
     */
    public function find($id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Ambil semua data dengan opsi sorting & limit
     */
    public function all(string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Ambil data berdasarkan kondisi WHERE
     * Contoh: $model->where("status = :status AND user_id = :uid", ['status' => 'published', 'uid' => 1])
     */
    public function where(string $condition, array $params = [], string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$condition}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ambil 1 record pertama berdasarkan kondisi
     */
    public function firstWhere(string $condition, array $params = []): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$condition} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Insert data baru
     * Mengembalikan lastInsertId atau true
     */
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

    /**
     * Update data berdasarkan primary key
     */
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

    /**
     * Hapus record berdasarkan primary key
     */
    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE `{$this->primaryKey}` = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Hitung total baris
     */
    public function count(string $condition = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($condition)) {
            $sql .= " WHERE {$condition}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Eksekusi query kustom dengan prepared statements
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Eksekusi non-query (INSERT, UPDATE, DELETE kustom)
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}
