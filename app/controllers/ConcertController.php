<?php

declare(strict_types=1);

class ConcertController extends Controller
{
    public function index(): void
    {
        $concertModel = new Concert();
        $query = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $concerts = $query !== '' ? $concertModel->search($query) : $concertModel->all();

        $this->view('concerts/index', [
            'title' => 'All Concerts',
            'concerts' => $concerts,
            'query' => $query,
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
        ]);
    }
}
