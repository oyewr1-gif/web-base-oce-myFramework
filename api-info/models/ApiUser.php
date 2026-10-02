<?php
/**
 * Model ApiUser
 */
class ApiUser extends ApiModel
{
    protected string $table = 'users';

    public function authenticate(string $identifier, string $password): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :id1 OR username = :id2 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id1' => $identifier, 'id2' => $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    public function createToken(int $userId, string $name = 'API Token'): string
    {
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO api_tokens (user_id, token, name) VALUES (:uid, :token, :name)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'uid'   => $userId,
            'token' => $token,
            'name'  => $name
        ]);
        return $token;
    }

    public function allSafe(): array
    {
        $sql = "SELECT id, username, name, email, role, created_at, updated_at FROM {$this->table} ORDER BY id ASC";
        return $this->query($sql);
    }

    public function findSafe($id): ?array
    {
        $sql = "SELECT id, username, name, email, role, created_at, updated_at FROM {$this->table} WHERE id = :id LIMIT 1";
        $results = $this->query($sql, ['id' => $id]);
        return $results ? $results[0] : null;
    }

    public function findByUsername(string $username, ?int $excludeId = null): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :u";
        $params = ['u' => $username];
        if ($excludeId !== null) {
            $sql .= " AND id != :eid";
            $params['eid'] = $excludeId;
        }
        $results = $this->query($sql . " LIMIT 1", $params);
        return $results ? $results[0] : null;
    }

    public function findByEmail(string $email, ?int $excludeId = null): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :e";
        $params = ['e' => $email];
        if ($excludeId !== null) {
            $sql .= " AND id != :eid";
            $params['eid'] = $excludeId;
        }
        $results = $this->query($sql . " LIMIT 1", $params);
        return $results ? $results[0] : null;
    }
}
