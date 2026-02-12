<div class="row g-4">
    <div class="col-lg-7">
        <h1 class="h3 mb-2"><?= e($concert['title']) ?></h1>
        <p class="text-muted mb-1">Artist: <?= e($concert['artist']) ?></p>
        <p class="text-muted mb-1">Genre: <?= e($concert['genre'] ?? '-') ?></p>
        <p class="text-muted mb-1">Venue: <?= e($concert['location']) ?></p>
        <p class="text-muted mb-1">Date: <?= e(date('d M Y', strtotime($concert['date']))) ?></p>
        <p class="text-muted mb-1">Showtime: <?= e(date('H:i', strtotime($concert['date']))) ?></p>
        <p class="text-muted mb-3">Duration: <?= e((string)($concert['duration_minutes'] ?? 0)) ?> minutes</p>

        <?php if (!empty($concert['description'])): ?>
            <p class="mb-4"><?= e($concert['description']) ?></p>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-3">
            <div class="badge bg-light text-dark">Price: Rp <?= e(number_format((float)$concert['price'], 0, ',', '.')) ?></div>
            <div class="badge bg-light text-dark">Seats left: <?= e((string)$concert['available_seats']) ?></div>
            <div class="badge bg-light text-dark">Status: <?= e(($concert['status'] ?? 'upcoming') === 'coming_soon' ? 'Coming Soon' : 'Upcoming') ?></div>
        </div>

        <?php if (!empty($concert['setlist'])): ?>
            <div class="mt-4">
                <h2 class="h6">Setlist</h2>
                <p class="mb-0 text-muted"><?= e($concert['setlist']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h5 mb-3">Book Tickets</h2>

                <?php if (!is_logged_in()): ?>
                    <div class="alert alert-warning">Please log in to book tickets.</div>
                    <a class="btn btn-primary w-100" href="<?= base_url('login') ?>">Login</a>
                <?php elseif ($alreadyBooked): ?>
                    <div class="alert alert-info">You already placed an order for this concert.</div>
                    <a class="btn btn-outline-primary w-100" href="<?= base_url('bookings') ?>">View My Booking</a>
                <?php elseif ((int)$concert['available_seats'] <= 0): ?>
                    <div class="alert alert-danger">Sorry, this concert is sold out.</div>
                <?php else: ?>
                    <?php
                    $categoryMap = [];
                    foreach ($seatCategories as $category) {
                        $categoryMap[$category['code']] = $category;
                    }

                    $stepMultiplier = !empty($preorderActive) ? (float)$preorderMultiplier : 1.0;

                    $seatRows = [];
                    foreach ($seats as $seat) {
                        if (preg_match('/^([A-Za-z]+)/', (string)$seat['seat_code'], $matches)) {
                            $rowKey = strtoupper($matches[1]);
                        } else {
                            $rowKey = 'ROW';
                        }
                        $seatRows[$rowKey][] = $seat;
                    }
                    ksort($seatRows);
                    ?>
                    <form action="<?= base_url('bookings') ?>" method="post">
                        <input type="hidden" name="concert_id" value="<?= e((string)$concert['id']) ?>">

                        <div class="mb-3">
                            <label class="form-label">Choose Seats</label>
                            <div class="seat-legend mb-2">
                                <?php foreach ($seatCategories as $category): ?>
                                    <span class="badge bg-light text-dark me-1">
                                        <?= e($category['name']) ?>: Rp <?= e(number_format((float)$category['price'], 0, ',', '.')) ?>
                                    </span>
                                <?php endforeach; ?>
                                <span class="badge bg-secondary">Booked</span>
                                <span class="badge bg-primary">Selected</span>
                            </div>

                            <?php if (!empty($preorderActive)): ?>
                                <div class="alert alert-warning py-2">Pre-order active: each additional seat is multiplied by x<?= e(number_format($stepMultiplier, 1)) ?>.</div>
                            <?php endif; ?>

                            <div class="seat-map">
                                <?php foreach ($seatRows as $row => $rowSeats): ?>
                                    <div class="seat-row">
                                        <div class="seat-row-label"><?= e($row) ?></div>
                                        <div class="seat-row-items">
                                            <?php foreach ($rowSeats as $seat): ?>
                                                <?php
                                                $category = $categoryMap[$seat['category_code']] ?? null;
                                                $price = $category ? (float)$category['price'] : 0.0;
                                                $isBooked = $seat['status'] !== 'available';
                                                ?>
                                                <button
                                                    type="button"
                                                    class="seat seat-<?= e($seat['category_code']) ?><?= $isBooked ? ' seat-booked' : '' ?>"
                                                    data-seat-id="<?= e((string)$seat['id']) ?>"
                                                    data-code="<?= e((string)$seat['seat_code']) ?>"
                                                    data-price="<?= e((string)$price) ?>"
                                                    <?= $isBooked ? 'disabled' : '' ?>
                                                >
                                                    <?= e((string)$seat['seat_code']) ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-2 small text-muted">Max 10 seats per booking.</div>
                            <div class="mt-2" id="seat_error" style="display: none;">
                                <div class="alert alert-danger py-2 mb-0">Maximum 10 seats selected.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Selected Seats</label>
                            <div id="seat_list" class="form-control" style="min-height: 42px;"></div>
                            <div class="form-text">Selected: <span id="seat_count">0</span> seat(s).</div>
                            <input type="hidden" id="seat_ids" name="seat_ids" value="" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtotal</label>
                            <div class="form-control" id="seat_total">Rp 0</div>
                        </div>

                        <div class="mb-3">
                            <label for="voucher_code" class="form-label">Voucher Code (optional)</label>
                            <input
                                type="text"
                                class="form-control"
                                id="voucher_code"
                                name="voucher_code"
                                maxlength="30"
                                placeholder="EX: FESTIVAL10"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="payment_type" class="form-label">Payment Type</label>
                            <select class="form-select" id="payment_type" name="payment_type" required>
                                <option value="" selected disabled>Choose payment type</option>
                                <option value="bank_transfer">Bank transfer</option>
                                <option value="qris">QRIS</option>
                                <option value="ewallet">E-wallet</option>
                            </select>
                        </div>

                        <div class="mb-3" id="account_number_group" style="display: none;">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input
                                type="text"
                                class="form-control"
                                id="account_number"
                                name="account_number"
                                maxlength="50"
                                placeholder="Bank account number"
                            >
                        </div>

                        <div class="mb-3" id="wallet_provider_group" style="display: none;">
                            <label for="payment_provider" class="form-label">E-wallet Provider</label>
                            <select class="form-select" id="payment_provider" name="payment_provider">
                                <option value="" selected disabled>Choose provider</option>
                                <option value="gopay">GoPay</option>
                                <option value="shopeepay">ShopeePay</option>
                                <option value="dana">DANA</option>
                                <option value="ovo">OVO</option>
                            </select>
                            <div class="form-text">We will redirect to your selected wallet after booking.</div>
                        </div>

                        <div class="alert alert-info d-none" id="qris_note">
                            QRIS will be shown after booking is confirmed.
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="confirm_button" disabled>Confirm Booking</button>
                    </form>
                    <style>
                        .seat-map {
                            display: grid;
                            gap: 10px;
                            padding: 10px;
                            border: 1px solid #e5e5e5;
                            border-radius: 8px;
                            background: #fafafa;
                        }

                        .seat-row {
                            display: grid;
                            grid-template-columns: 24px 1fr;
                            gap: 8px;
                            align-items: center;
                        }

                        .seat-row-label {
                            font-weight: 600;
                            color: #6c757d;
                            text-align: center;
                        }

                        .seat-row-items {
                            display: grid;
                            grid-template-columns: repeat(10, minmax(28px, 1fr));
                            gap: 6px;
                        }

                        .seat {
                            border: 1px solid #ced4da;
                            background: #ffffff;
                            border-radius: 6px;
                            padding: 4px 0;
                            font-size: 11px;
                            cursor: pointer;
                        }

                        .seat-vvip { background: #ffe8d2; border-color: #f3c89f; }
                        .seat-vip { background: #fff3cd; border-color: #f1d087; }
                        .seat-elite { background: #e7f1ff; border-color: #a9c7f5; }
                        .seat-normal { background: #e9f7ef; border-color: #a7d9bd; }
                        .seat-selected { background: #0d6efd; border-color: #0d6efd; color: #fff; }
                        .seat-booked { background: #dee2e6; border-color: #ced4da; color: #6c757d; cursor: not-allowed; }
                    </style>
                    <script>
                        (function () {
                            const maxSeats = 10;
                            const seatButtons = document.querySelectorAll('.seat[data-seat-id]');
                            const seatIdsInput = document.getElementById('seat_ids');
                            const seatList = document.getElementById('seat_list');
                            const seatCount = document.getElementById('seat_count');
                            const seatTotal = document.getElementById('seat_total');
                            const seatError = document.getElementById('seat_error');
                            const confirmButton = document.getElementById('confirm_button');

                            const formatCurrency = (value) => {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            };

                            const updateSummary = () => {
                                const selected = Array.from(document.querySelectorAll('.seat-selected'));
                                const sorted = selected.sort((a, b) => a.dataset.code.localeCompare(b.dataset.code));
                                const seatIds = sorted.map((seat) => seat.dataset.seatId);
                                const seatCodes = sorted.map((seat) => seat.dataset.code);
                                const isPreorder = Boolean(<?= !empty($preorderActive) ? 'true' : 'false' ?>);
                                const stepMultiplier = Number(<?= !empty($preorderActive) ? json_encode($stepMultiplier) : '1' ?>);
                                const total = sorted.reduce((sum, seat, index) => {
                                    let price = Number(seat.dataset.price || 0);
                                    if (isPreorder) {
                                        price *= Math.pow(stepMultiplier, index);
                                    }
                                    return sum + price;
                                }, 0);

                                seatIdsInput.value = seatIds.join(',');
                                seatList.textContent = seatCodes.length ? seatCodes.join(', ') : '-';
                                seatCount.textContent = String(seatCodes.length);
                                seatTotal.textContent = formatCurrency(total);
                                if (confirmButton) {
                                    confirmButton.disabled = seatCodes.length === 0;
                                }
                            };

                            seatButtons.forEach((seat) => {
                                seat.addEventListener('click', () => {
                                    if (seat.classList.contains('seat-booked')) {
                                        return;
                                    }

                                    const selected = document.querySelectorAll('.seat-selected');
                                    if (!seat.classList.contains('seat-selected') && selected.length >= maxSeats) {
                                        if (seatError) {
                                            seatError.style.display = 'block';
                                        }
                                        return;
                                    }

                                    if (seatError) {
                                        seatError.style.display = 'none';
                                    }

                                    seat.classList.toggle('seat-selected');
                                    updateSummary();
                                });
                            });

                            const typeSelect = document.getElementById('payment_type');
                            const accountGroup = document.getElementById('account_number_group');
                            const walletGroup = document.getElementById('wallet_provider_group');
                            const qrisNote = document.getElementById('qris_note');
                            const accountInput = document.getElementById('account_number');
                            const providerSelect = document.getElementById('payment_provider');

                            if (!typeSelect || !accountGroup || !walletGroup) {
                                return;
                            }

                            const updateFields = () => {
                                const type = typeSelect.value;
                                const isBank = type === 'bank_transfer';
                                const isWallet = type === 'ewallet';
                                const isQris = type === 'qris';

                                accountGroup.style.display = isBank ? '' : 'none';
                                walletGroup.style.display = isWallet ? '' : 'none';
                                if (qrisNote) {
                                    qrisNote.classList.toggle('d-none', !isQris);
                                }

                                accountInput.required = isBank;
                                providerSelect.required = isWallet;

                                if (!isBank) {
                                    accountInput.value = '';
                                }

                                if (!isWallet) {
                                    providerSelect.value = '';
                                }
                            };

                            typeSelect.addEventListener('change', updateFields);
                            updateFields();
                            updateSummary();
                        })();
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
