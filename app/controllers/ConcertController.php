<?php

declare(strict_types=1);

class ConcertController extends Controller
{
    public function index(): void
    {
        $concertModel = new Concert();
        $query = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $genre = isset($_GET['genre']) ? trim((string)$_GET['genre']) : '';
        $artist = isset($_GET['artist']) ? trim((string)$_GET['artist']) : '';
        $concerts = $concertModel->filter($query, $genre, $artist);
        $genres = $concertModel->getGenres();
        $artists = $concertModel->getArtists();

        $this->view('concerts/index', [
            'title' => 'All Concerts',
            'concerts' => $concerts,
            'query' => $query,
            'genres' => $genres,
            'artists' => $artists,
            'selectedGenre' => $genre,
            'selectedArtist' => $artist,
        ]);
    }

    public function show(string $id): void
    {
        $concertModel = new Concert();
        $concert = $concertModel->find((int)$id);

        if (!$concert) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Concert Not Found']);
            return;
        }

        $alreadyBooked = false;
        $seatModel = new Seat();
        $seatModel->ensureSeatMap((int)$id, (float)$concert['price']);
        $seatCategories = $seatModel->getCategoriesByConcert((int)$id);
        $seats = $seatModel->getSeatsByConcert((int)$id);

        $preorderMultiplier = isset($concert['preorder_multiplier']) ? (float)$concert['preorder_multiplier'] : 1.0;
        if ($preorderMultiplier < 1) {
            $preorderMultiplier = 1.0;
        }
        $preorderActive = false;
        try {
            $concertDate = new DateTime((string)$concert['date']);
            $now = new DateTime();
            $diffDays = (int)$now->diff($concertDate)->format('%r%a');
            $preorderActive = $diffDays >= 30 && (($concert['status'] ?? 'upcoming') === 'coming_soon');
        } catch (Exception $e) {
            $preorderActive = false;
        }
        if (is_logged_in()) {
            $bookingModel = new Booking();
            $alreadyBooked = $bookingModel->hasBooking((int)$_SESSION['user']['id'], (int)$id);
        }

        $this->view('concerts/show', [
            'title' => $concert['title'],
            'concert' => $concert,
            'alreadyBooked' => $alreadyBooked,
            'seatCategories' => $seatCategories,
            'seats' => $seats,
            'preorderActive' => $preorderActive,
            'preorderMultiplier' => $preorderMultiplier,
        ]);
    }
}
