<?php

declare(strict_types=1);

class Concert extends Model
{
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT c.*, prices.min_price AS min_seat_price, prices.max_price AS max_seat_price '
            . 'FROM concerts c '
            . 'LEFT JOIN (SELECT concert_id, MIN(price) AS min_price, MAX(price) AS max_price '
            . 'FROM seat_categories GROUP BY concert_id) prices '
            . 'ON prices.concert_id = c.id '
            . 'ORDER BY c.date ASC'
        );
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO concerts (title, artist, genre, description, date, location, price, available_seats, duration_minutes, setlist, status, preorder_multiplier, image_url) '
            . 'VALUES (:title, :artist, :genre, :description, :date, :location, :price, :available_seats, :duration_minutes, :setlist, :status, :preorder_multiplier, :image_url)'
        );
        $stmt->execute([
            ':title' => $data['title'],
            ':artist' => $data['artist'],
            ':genre' => $data['genre'],
            ':description' => $data['description'],
            ':date' => $data['date'],
            ':location' => $data['location'],
            ':price' => $data['price'],
            ':available_seats' => $data['available_seats'],
            ':duration_minutes' => $data['duration_minutes'],
            ':setlist' => $data['setlist'],
            ':status' => $data['status'],
            ':preorder_multiplier' => $data['preorder_multiplier'],
            ':image_url' => $data['image_url'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE concerts SET title = :title, artist = :artist, genre = :genre, description = :description, '
            . 'date = :date, location = :location, price = :price, available_seats = :available_seats, '
            . 'duration_minutes = :duration_minutes, setlist = :setlist, status = :status, '
            . 'preorder_multiplier = :preorder_multiplier, image_url = :image_url '
            . 'WHERE id = :id'
        );
        $stmt->execute([
            ':title' => $data['title'],
            ':artist' => $data['artist'],
            ':genre' => $data['genre'],
            ':description' => $data['description'],
            ':date' => $data['date'],
            ':location' => $data['location'],
            ':price' => $data['price'],
            ':available_seats' => $data['available_seats'],
            ':duration_minutes' => $data['duration_minutes'],
            ':setlist' => $data['setlist'],
            ':status' => $data['status'],
            ':preorder_multiplier' => $data['preorder_multiplier'],
            ':image_url' => $data['image_url'],
            ':id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM concerts WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function search(string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return $this->all();
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM concerts '
            . 'WHERE title LIKE :q OR artist LIKE :q OR location LIKE :q OR genre LIKE :q '
            . 'ORDER BY date ASC'
        );
        $stmt->execute([':q' => '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    public function filter(string $query = '', string $genre = '', string $artist = ''): array
    {
        $query = trim($query);
        $genre = trim($genre);
        $artist = trim($artist);

        $sql = 'SELECT * FROM concerts';
        $conditions = [];
        $params = [];

        if ($query !== '') {
            $conditions[] = '(title LIKE ? OR artist LIKE ? OR location LIKE ? OR genre LIKE ?)';
            $likeValue = '%' . $query . '%';
            $params[] = $likeValue;
            $params[] = $likeValue;
            $params[] = $likeValue;
            $params[] = $likeValue;
        }

        if ($genre !== '') {
            $conditions[] = 'genre = ?';
            $params[] = $genre;
        }

        if ($artist !== '') {
            $conditions[] = 'artist = ?';
            $params[] = $artist;
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY date ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getGenres(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT genre FROM concerts ORDER BY genre ASC');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getArtists(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT artist FROM concerts ORDER BY artist ASC');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
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
