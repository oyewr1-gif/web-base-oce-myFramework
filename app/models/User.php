<?php
/**
 * Model User
 */
class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->firstWhere("email = :email", ['email' => $email]);
    }

    public function findByUsername(string $username): ?array
    {
        return $this->firstWhere("username = :username", ['username' => $username]);
    }

    public function authenticate(string $identifier, string $password): ?array
    {
        // Cari via email atau username
        $user = $this->firstWhere("email = :id1 OR username = :id2", [
            'id1' => $identifier,
            'id2' => $identifier
        ]);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }
}
