<?php

declare(strict_types=1);

class HomeController extends Controller
{
    public function index(): void
    {
        $concertModel = new Concert();
        $concerts = $concertModel->all();

        $this->view('home/index', [
            'title' => 'Concert Booking',
            'concerts' => $concerts,
        ]);
    }
}
