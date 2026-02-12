<?php

declare(strict_types=1);

class Voucher extends Model
{
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

    public function incrementUsage(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE vouchers SET used_count = used_count + 1 WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }
}
