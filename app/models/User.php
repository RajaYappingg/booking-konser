<?php

declare(strict_types=1);

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $name, string $email, string $password, string $role = 'user'): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hash,
            ':role' => $role,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, email, phone, city, role, created_at '
            . 'FROM users '
            . 'ORDER BY created_at DESC'
        );
        return $stmt->fetchAll();
    }
}
