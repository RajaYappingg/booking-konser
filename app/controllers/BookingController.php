<?php

declare(strict_types=1);

class BookingController extends Controller
{
    public function index(): void
    {
        require_auth();

        if (is_admin()) {
            flash('warning', 'Admin account cannot access bookings.');
            redirect('admin');
        }

        $bookingModel = new Booking();
        $bookings = $bookingModel->getByUser((int)$_SESSION['user']['id']);

        $this->view('bookings/index', [
            'title' => 'My Bookings',
            'bookings' => $bookings,
        ]);
    }

    public function store(): void
    {
        require_auth();

        if (is_admin()) {
            flash('warning', 'Admin account cannot place bookings.');
            redirect('admin');
        }

        [$isValid, $errors, $clean] = Validation::validate($_POST, [
            'concert_id' => 'required|int',
            'seat_ids' => 'required|max:500',
            'payment_type' => 'required|max:20',
            'payment_provider' => 'max:30',
            'account_number' => 'max:50',
            'voucher_code' => 'max:30',
        ]);

        if (!$isValid) {
            flash('danger', 'Invalid booking request.');
            redirect('concerts');
        }

        $seatIds = array_filter(
            array_map('intval', explode(',', (string)$clean['seat_ids'])),
            static fn (int $id) => $id > 0
        );
        $seatIds = array_values(array_unique($seatIds));

        $bookingModel = new Booking();
        [$success, $message] = $bookingModel->createBooking(
            (int)$_SESSION['user']['id'],
            (int)$clean['concert_id'],
            $seatIds,
            (string)$clean['payment_type'],
            isset($clean['payment_provider']) ? (string)$clean['payment_provider'] : '',
            isset($clean['account_number']) ? (string)$clean['account_number'] : '',
            isset($clean['voucher_code']) ? (string)$clean['voucher_code'] : ''
        );

        if ($success) {
            flash('success', $message);
            redirect('bookings');
        }

        flash('danger', $message);
        redirect('concerts/' . (int)$clean['concert_id']);
    }

    public function cancel(string $id): void
    {
        require_auth();

        if (is_admin()) {
            flash('warning', 'Admin account cannot cancel bookings.');
            redirect('admin');
        }

        $bookingId = (int)$id;
        if ($bookingId < 1) {
            flash('danger', 'Invalid booking request.');
            redirect('bookings');
        }

        $bookingModel = new Booking();
        [$success, $message] = $bookingModel->cancelBooking(
            (int)$_SESSION['user']['id'],
            $bookingId
        );

        if ($success) {
            flash('success', $message);
            redirect('bookings');
        }

        flash('danger', $message);
        redirect('bookings');
    }
}
