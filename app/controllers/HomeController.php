<?php

declare(strict_types=1);

class HomeController extends Controller
{
    public function index(): void
    {
        $concertModel = new Concert();
        $featured = $concertModel->featured();

        $this->view('home/index', [
            'title' => 'Concert Booking',
            'featured' => $featured,
        ]);
    }
}
