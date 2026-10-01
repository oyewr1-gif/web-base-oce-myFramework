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
}
