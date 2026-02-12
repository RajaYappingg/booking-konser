<?php

declare(strict_types=1);

class AdminController extends Controller
{
    public function index(): void
    {
        require_admin();

        $db = Database::getInstance();
        $stats = [
            'concerts' => (int)$db->query('SELECT COUNT(*) FROM concerts')->fetchColumn(),
            'bookings' => (int)$db->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
            'users' => (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'vouchers' => (int)$db->query('SELECT COUNT(*) FROM vouchers')->fetchColumn(),
            'revenue' => (float)$db->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status = 'confirmed'")->fetchColumn(),
        ];

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
        ]);
    }

    public function concerts(): void
    {
        require_admin();

        $concertModel = new Concert();
        $concerts = $concertModel->all();

        $this->view('admin/concerts/index', [
            'title' => 'Manage Concerts',
            'concerts' => $concerts,
        ]);
    }

    public function createConcertForm(): void
    {
        require_admin();

        $this->view('admin/concerts/form', [
            'title' => 'Add Concert',
            'formAction' => base_url('admin/concerts'),
            'formMethod' => 'post',
            'concert' => null,
            'seatPrices' => $this->defaultSeatPrices(null),
        ]);
    }

    public function storeConcert(): void
    {
        require_admin();

        $data = $this->sanitizeConcertData($_POST);
        if ($data === null) {
            redirect('admin/concerts/create');
        }

        $seatPrices = $this->sanitizeSeatPrices($_POST);
        if ($seatPrices === null) {
            redirect('admin/concerts/create');
        }

        $concertModel = new Concert();
        $concertId = $concertModel->create($data);

        $seatModel = new Seat();
        $seatModel->setCategoryPrices($concertId, $seatPrices);

        flash('success', 'Concert created successfully.');
        redirect('admin/concerts');
    }

    public function editConcertForm(string $id): void
    {
        require_admin();

        $concertModel = new Concert();
        $concert = $concertModel->find((int)$id);

        if (!$concert) {
            flash('warning', 'Concert not found.');
            redirect('admin/concerts');
        }

        $this->view('admin/concerts/form', [
            'title' => 'Edit Concert',
            'formAction' => base_url('admin/concerts/' . $concert['id'] . '/update'),
            'formMethod' => 'post',
            'concert' => $concert,
            'seatPrices' => $this->getSeatPricesForForm($concert),
        ]);
    }

    public function updateConcert(string $id): void
    {
        require_admin();

        $concertModel = new Concert();
        $concert = $concertModel->find((int)$id);

        if (!$concert) {
            flash('warning', 'Concert not found.');
            redirect('admin/concerts');
        }

        $data = $this->sanitizeConcertData($_POST);
        if ($data === null) {
            redirect('admin/concerts/' . $id . '/edit');
        }

        $seatPrices = $this->sanitizeSeatPrices($_POST);
        if ($seatPrices === null) {
            redirect('admin/concerts/' . $id . '/edit');
        }

        $concertModel->update((int)$id, $data);

        $seatModel = new Seat();
        $seatModel->setCategoryPrices((int)$id, $seatPrices);

        flash('success', 'Concert updated successfully.');
        redirect('admin/concerts');
    }

    public function deleteConcert(string $id): void
    {
        require_admin();

        $concertModel = new Concert();
        $concert = $concertModel->find((int)$id);

        if (!$concert) {
            flash('warning', 'Concert not found.');
            redirect('admin/concerts');
        }

        $concertModel->delete((int)$id);

        flash('success', 'Concert deleted successfully.');
        redirect('admin/concerts');
    }

    public function vouchers(): void
    {
        require_admin();

        $voucherModel = new Voucher();
        $vouchers = $voucherModel->all();

        $this->view('admin/vouchers/index', [
            'title' => 'Manage Vouchers',
            'vouchers' => $vouchers,
        ]);
    }

    public function createVoucherForm(): void
    {
        require_admin();

        $this->view('admin/vouchers/form', [
            'title' => 'Add Voucher',
            'formAction' => base_url('admin/vouchers'),
            'formMethod' => 'post',
            'voucher' => null,
        ]);
    }

    public function storeVoucher(): void
    {
        require_admin();

        $data = $this->sanitizeVoucherData($_POST);
        if ($data === null) {
            redirect('admin/vouchers/create');
        }

        $voucherModel = new Voucher();
        $voucherModel->create($data);

        flash('success', 'Voucher created successfully.');
        redirect('admin/vouchers');
    }

    public function editVoucherForm(string $id): void
    {
        require_admin();

        $voucherModel = new Voucher();
        $voucher = $voucherModel->find((int)$id);

        if (!$voucher) {
            flash('warning', 'Voucher not found.');
            redirect('admin/vouchers');
        }

        $this->view('admin/vouchers/form', [
            'title' => 'Edit Voucher',
            'formAction' => base_url('admin/vouchers/' . $voucher['id'] . '/update'),
            'formMethod' => 'post',
            'voucher' => $voucher,
        ]);
    }

    public function updateVoucher(string $id): void
    {
        require_admin();

        $voucherModel = new Voucher();
        $voucher = $voucherModel->find((int)$id);

        if (!$voucher) {
            flash('warning', 'Voucher not found.');
            redirect('admin/vouchers');
        }

        $data = $this->sanitizeVoucherData($_POST);
        if ($data === null) {
            redirect('admin/vouchers/' . $id . '/edit');
        }

        $voucherModel->update((int)$id, $data);

        flash('success', 'Voucher updated successfully.');
        redirect('admin/vouchers');
    }

    public function deleteVoucher(string $id): void
    {
        require_admin();

        $voucherModel = new Voucher();
        $voucher = $voucherModel->find((int)$id);

        if (!$voucher) {
            flash('warning', 'Voucher not found.');
            redirect('admin/vouchers');
        }

        $voucherModel->delete((int)$id);

        flash('success', 'Voucher deleted successfully.');
        redirect('admin/vouchers');
    }

    public function bookings(): void
    {
        require_admin();

        $bookingModel = new Booking();
        $bookings = $bookingModel->getAll();

        $this->view('admin/bookings/index', [
            'title' => 'All Bookings',
            'bookings' => $bookings,
        ]);
    }

    private function sanitizeConcertData(array $input): ?array
    {
        [$isValid, $errors, $clean] = Validation::validate($input, [
            'title' => 'required|max:150',
            'artist' => 'required|max:100',
            'genre' => 'required|max:50',
            'description' => 'max:1000',
            'date' => 'required',
            'location' => 'required|max:200',
            'price' => 'required',
            'available_seats' => 'required|int',
            'duration_minutes' => 'required|int',
            'setlist' => 'max:2000',
            'status' => 'required',
            'preorder_multiplier' => 'required',
            'image_url' => 'max:255',
        ]);

        if (!$isValid) {
            flash('danger', 'Please check the concert form fields.');
            return null;
        }

        if (!is_numeric($clean['price']) || (float)$clean['price'] < 0) {
            flash('danger', 'Concert price must be a positive number.');
            return null;
        }

        if (!is_numeric($clean['preorder_multiplier']) || (float)$clean['preorder_multiplier'] < 1) {
            flash('danger', 'Preorder multiplier must be 1 or greater.');
            return null;
        }

        $status = (string)$clean['status'];
        if (!in_array($status, ['upcoming', 'coming_soon'], true)) {
            flash('danger', 'Concert status is invalid.');
            return null;
        }

        return [
            'title' => (string)$clean['title'],
            'artist' => (string)$clean['artist'],
            'genre' => (string)$clean['genre'],
            'description' => $clean['description'] !== '' ? (string)$clean['description'] : null,
            'date' => (string)$clean['date'],
            'location' => (string)$clean['location'],
            'price' => (float)$clean['price'],
            'available_seats' => (int)$clean['available_seats'],
            'duration_minutes' => (int)$clean['duration_minutes'],
            'setlist' => $clean['setlist'] !== '' ? (string)$clean['setlist'] : null,
            'status' => $status,
            'preorder_multiplier' => (float)$clean['preorder_multiplier'],
            'image_url' => $clean['image_url'] !== '' ? (string)$clean['image_url'] : null,
        ];
    }

    private function sanitizeVoucherData(array $input): ?array
    {
        [$isValid, $errors, $clean] = Validation::validate($input, [
            'code' => 'required|max:50',
            'discount_type' => 'required',
            'amount' => 'required',
            'max_uses' => 'int',
            'expires_at' => 'max:30',
            'active' => 'required|int',
        ]);

        if (!$isValid) {
            flash('danger', 'Please check the voucher form fields.');
            return null;
        }

        $discountType = (string)$clean['discount_type'];
        if (!in_array($discountType, ['percent', 'fixed'], true)) {
            flash('danger', 'Voucher discount type is invalid.');
            return null;
        }

        if (!is_numeric($clean['amount']) || (float)$clean['amount'] < 0) {
            flash('danger', 'Voucher amount must be a positive number.');
            return null;
        }

        $maxUses = $clean['max_uses'] !== null && $clean['max_uses'] !== '' ? (int)$clean['max_uses'] : null;
        $expiresAt = $clean['expires_at'] !== '' ? (string)$clean['expires_at'] : null;
        $active = (int)$clean['active'] === 1 ? 1 : 0;

        return [
            'code' => strtoupper((string)$clean['code']),
            'discount_type' => $discountType,
            'amount' => (float)$clean['amount'],
            'max_uses' => $maxUses,
            'expires_at' => $expiresAt,
            'active' => $active,
        ];
    }

    private function sanitizeSeatPrices(array $input): ?array
    {
        [$isValid, $errors, $clean] = Validation::validate($input, [
            'vvip_price' => 'required',
            'vip_price' => 'required',
            'elite_price' => 'required',
            'normal_price' => 'required',
        ]);

        if (!$isValid) {
            flash('danger', 'Please fill all seat category prices.');
            return null;
        }

        $fieldMap = [
            'vvip_price' => 'VVIP',
            'vip_price' => 'VIP',
            'elite_price' => 'Elite',
            'normal_price' => 'Normal',
        ];

        $prices = [];
        foreach ($fieldMap as $field => $label) {
            if (!is_numeric($clean[$field]) || (float)$clean[$field] < 0) {
                flash('danger', $label . ' price must be a positive number.');
                return null;
            }

            $prices[str_replace('_price', '', $field)] = (float)$clean[$field];
        }

        return $prices;
    }

    private function defaultSeatPrices(?array $concert): array
    {
        $basePrice = $concert && isset($concert['price']) ? (float)$concert['price'] : 0.0;

        if ($basePrice <= 0) {
            return [
                'vvip' => '',
                'vip' => '',
                'elite' => '',
                'normal' => '',
            ];
        }

        return [
            'vvip' => $basePrice * 2.0,
            'vip' => $basePrice * 1.5,
            'elite' => $basePrice * 1.2,
            'normal' => $basePrice,
        ];
    }

    private function getSeatPricesForForm(array $concert): array
    {
        $priceMap = $this->defaultSeatPrices($concert);
        $seatModel = new Seat();
        $categories = $seatModel->getCategoriesByConcert((int)$concert['id']);

        foreach ($categories as $category) {
            $code = (string)($category['code'] ?? '');
            if ($code !== '' && array_key_exists($code, $priceMap)) {
                $priceMap[$code] = (float)$category['price'];
            }
        }

        return $priceMap;
    }
}
