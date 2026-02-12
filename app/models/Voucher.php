<?php

declare(strict_types=1);

class Voucher extends Model
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM vouchers ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM vouchers WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $voucher = $stmt->fetch();
        return $voucher ?: null;
    }

    public function findAvailableForUpdate(string $code): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, code, discount_type, amount, max_uses, used_count, expires_at, active '
            . 'FROM vouchers '
            . 'WHERE code = :code AND active = 1 '
            . 'AND (expires_at IS NULL OR expires_at > NOW()) '
            . 'AND (max_uses IS NULL OR used_count < max_uses) '
            . 'FOR UPDATE'
        );
        $stmt->execute([':code' => $code]);
        $voucher = $stmt->fetch();

        return $voucher ?: null;
    }

    public function getActiveForClient(): array
    {
        $stmt = $this->db->prepare(
            'SELECT code, discount_type, amount '
            . 'FROM vouchers '
            . 'WHERE active = 1 '
            . 'AND (expires_at IS NULL OR expires_at > NOW()) '
            . 'AND (max_uses IS NULL OR used_count < max_uses) '
            . 'ORDER BY code ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function incrementUsage(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE vouchers SET used_count = used_count + 1 WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO vouchers (code, discount_type, amount, max_uses, expires_at, active) '
            . 'VALUES (:code, :discount_type, :amount, :max_uses, :expires_at, :active)'
        );
        $stmt->execute([
            ':code' => $data['code'],
            ':discount_type' => $data['discount_type'],
            ':amount' => $data['amount'],
            ':max_uses' => $data['max_uses'],
            ':expires_at' => $data['expires_at'],
            ':active' => $data['active'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE vouchers SET code = :code, discount_type = :discount_type, amount = :amount, '
            . 'max_uses = :max_uses, expires_at = :expires_at, active = :active '
            . 'WHERE id = :id'
        );
        $stmt->execute([
            ':code' => $data['code'],
            ':discount_type' => $data['discount_type'],
            ':amount' => $data['amount'],
            ':max_uses' => $data['max_uses'],
            ':expires_at' => $data['expires_at'],
            ':active' => $data['active'],
            ':id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM vouchers WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
