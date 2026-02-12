<?php

declare(strict_types=1);

class Seat extends Model
{
    public function ensureSeatMap(int $concertId, float $basePrice): void
    {
        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM seats WHERE concert_id = :id');
        $countStmt->execute([':id' => $concertId]);
        if ((int)$countStmt->fetchColumn() > 0) {
            return;
        }

        $this->db->beginTransaction();

        try {
            $categoryCountStmt = $this->db->prepare(
                'SELECT COUNT(*) FROM seat_categories WHERE concert_id = :id'
            );
            $categoryCountStmt->execute([':id' => $concertId]);
            $categoryCount = (int)$categoryCountStmt->fetchColumn();

            if ($categoryCount === 0) {
                $categories = [
                    ['code' => 'vvip', 'name' => 'VVIP', 'price' => $basePrice * 2.0],
                    ['code' => 'vip', 'name' => 'VIP', 'price' => $basePrice * 1.5],
                    ['code' => 'elite', 'name' => 'Elite', 'price' => $basePrice * 1.2],
                    ['code' => 'normal', 'name' => 'Normal', 'price' => $basePrice],
                ];

                $insertCategory = $this->db->prepare(
                    'INSERT INTO seat_categories (concert_id, code, name, price) '
                    . 'VALUES (:concert_id, :code, :name, :price)'
                );

                foreach ($categories as $category) {
                    $insertCategory->execute([
                        ':concert_id' => $concertId,
                        ':code' => $category['code'],
                        ':name' => $category['name'],
                        ':price' => $category['price'],
                    ]);
                }
            }

            $rowCategories = [
                'A' => 'vvip',
                'B' => 'vip',
                'C' => 'vip',
                'D' => 'elite',
                'E' => 'elite',
                'F' => 'elite',
                'G' => 'normal',
                'H' => 'normal',
                'I' => 'normal',
                'J' => 'normal',
            ];

            $insertSeat = $this->db->prepare(
                'INSERT INTO seats (concert_id, seat_code, category_code) '
                . 'VALUES (:concert_id, :seat_code, :category_code)'
            );

            foreach ($rowCategories as $row => $categoryCode) {
                for ($i = 1; $i <= 10; $i++) {
                    $insertSeat->execute([
                        ':concert_id' => $concertId,
                        ':seat_code' => $row . $i,
                        ':category_code' => $categoryCode,
                    ]);
                }
            }

            $updateSeats = $this->db->prepare(
                'UPDATE concerts SET available_seats = :qty WHERE id = :id'
            );
            $updateSeats->execute([
                ':qty' => 100,
                ':id' => $concertId,
            ]);

            $this->db->commit();
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
        }
    }

    public function getCategoriesByConcert(int $concertId): array
    {
        $stmt = $this->db->prepare(
            'SELECT code, name, price '
            . 'FROM seat_categories '
            . 'WHERE concert_id = :id '
            . 'ORDER BY price DESC'
        );
        $stmt->execute([':id' => $concertId]);
        return $stmt->fetchAll();
    }

    public function getSeatsByConcert(int $concertId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, seat_code, category_code, status '
            . 'FROM seats '
            . 'WHERE concert_id = :id '
            . 'ORDER BY seat_code ASC'
        );
        $stmt->execute([':id' => $concertId]);
        return $stmt->fetchAll();
    }
}
