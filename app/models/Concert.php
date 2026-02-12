<?php

declare(strict_types=1);

class Concert extends Model
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM concerts ORDER BY date ASC');
        return $stmt->fetchAll();
    }

    public function search(string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return $this->all();
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM concerts '
            . 'WHERE title LIKE :q OR artist LIKE :q OR location LIKE :q '
            . 'ORDER BY date ASC'
        );
        $stmt->execute([':q' => '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    public function featured(int $limit = 4): array
    {
        $stmt = $this->db->prepare('SELECT * FROM concerts ORDER BY date ASC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM concerts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $concert = $stmt->fetch();
        return $concert ?: null;
    }
}
