<div class="row g-4">
    <div class="col-lg-7">
        <?php if (!empty($concert['image_url'])): ?>
            <img
                src="<?= e($concert['image_url']) ?>"
                alt="<?= e($concert['title']) ?>"
                class="img-fluid rounded mb-3"
                style="max-height: 360px; width: 100%; object-fit: cover;"
            >
        <?php else: ?>
            <div class="rounded mb-3 d-flex align-items-center justify-content-center" style="height: 240px; background: #edf2ff;">
                <span class="fs-1">🎵</span>
            </div>
        <?php endif; ?>
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

        <div class="d-flex flex-wrap gap-3 detail-badges">
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
        <div class="card shadow-sm border-0 booking-card">
            <div class="card-body">
                <h2 class="h5 mb-3">Book Tickets</h2>

                <?php if (!is_logged_in()): ?>
                    <div class="alert alert-warning">Please log in to book tickets.</div>
                    <a class="btn btn-primary w-100" href="<?= base_url('login') ?>">Login</a>
                <?php elseif (is_admin()): ?>
                    <div class="alert alert-info">Admin account cannot place bookings.</div>
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
                                    <?php $legendClass = 'legend-' . ($category['code'] ?? ''); ?>
                                    <span class="badge legend-badge <?= e($legendClass) ?> me-1">
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
                            <div class="form-text" id="voucher_note"></div>
                            <div class="form-text" id="subtotal_note"></div>
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
                            <label for="account_number" class="form-label" id="account_number_label">Account Number</label>
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
                            <div class="fw-semibold mb-2">Scan this QRIS code to pay.</div>
                            <img
                                src="<?= base_url('images/qris.png') ?>"
                                alt="QRIS payment"
                                style="max-width: 180px; width: 100%; height: auto;"
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="confirm_button" disabled>Confirm Booking</button>
                    </form>
                    <style>
                        .booking-card {
                            border-radius: 18px;
                            background: #ffffff;
                            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
                        }

                        .detail-badges .badge {
                            border: 1px solid #e2e8f0;
                            background: #f8fafc;
                            font-weight: 600;
                        }

                        .booking-card .form-control,
                        .booking-card .form-select {
                            border-radius: 12px;
                            border-color: #e2e8f0;
                        }

                        .seat-map {
                            display: grid;
                            gap: 10px;
                            padding: 10px;
                            border: 1px solid #e2e8f0;
                            border-radius: 14px;
                            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
                        }

                        .legend-badge {
                            border: 1px solid transparent;
                            color: #1a1a1a;
                            font-weight: 600;
                        }

                        .legend-vvip { background: #ffe8d2; border-color: #f3c89f; }
                        .legend-vip { background: #fff3cd; border-color: #f1d087; }
                        .legend-elite { background: #e7f1ff; border-color: #a9c7f5; }
                        .legend-normal { background: #e9f7ef; border-color: #a7d9bd; }

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
                            border-radius: 8px;
                            padding: 6px 0;
                            font-size: 11px;
                            cursor: pointer;
                            transition: transform 0.15s ease, box-shadow 0.15s ease;
                        }

                        .seat:not(.seat-booked):hover {
                            transform: translateY(-1px);
                            box-shadow: 0 6px 12px rgba(15, 23, 42, 0.12);
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
                            const voucherInput = document.getElementById('voucher_code');
                            const voucherNote = document.getElementById('voucher_note');
                            const subtotalNote = document.getElementById('subtotal_note');
                            const voucherList = <?= json_encode($availableVouchers ?? []) ?>;
                            const voucherMap = Array.isArray(voucherList)
                                ? voucherList.reduce((map, voucher) => {
                                    const code = String(voucher.code || '').toUpperCase();
                                    if (code !== '') {
                                        map[code] = voucher;
                                    }
                                    return map;
                                }, {})
                                : {};

                            const formatCurrency = (value) => {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            };

                            const applyVoucher = (baseTotal) => {
                                if (!voucherInput) {
                                    return { total: baseTotal, discount: 0, valid: false };
                                }

                                const code = voucherInput.value.trim().toUpperCase();
                                if (code === '' || !voucherMap[code]) {
                                    return { total: baseTotal, discount: 0, valid: false };
                                }

                                const voucher = voucherMap[code];
                                let discount = 0;
                                if (voucher.discount_type === 'percent') {
                                    discount = baseTotal * (Number(voucher.amount || 0) / 100);
                                } else {
                                    discount = Number(voucher.amount || 0);
                                }

                                if (discount > baseTotal) {
                                    discount = baseTotal;
                                }

                                return { total: baseTotal - discount, discount, valid: true, code };
                            };

                            const updateSummary = () => {
                                const selected = Array.from(document.querySelectorAll('.seat-selected'));
                                const sorted = selected.sort((a, b) => a.dataset.code.localeCompare(b.dataset.code));
                                const seatIds = sorted.map((seat) => seat.dataset.seatId);
                                const seatCodes = sorted.map((seat) => seat.dataset.code);
                                const isPreorder = Boolean(<?= !empty($preorderActive) ? 'true' : 'false' ?>);
                                const stepMultiplier = Number(<?= !empty($preorderActive) ? json_encode($stepMultiplier) : '1' ?>);
                                const baseTotal = sorted.reduce((sum, seat, index) => {
                                    let price = Number(seat.dataset.price || 0);
                                    if (isPreorder) {
                                        price *= Math.pow(stepMultiplier, index);
                                    }
                                    return sum + price;
                                }, 0);
                                const voucherResult = applyVoucher(baseTotal);

                                seatIdsInput.value = seatIds.join(',');
                                seatList.textContent = seatCodes.length ? seatCodes.join(', ') : '-';
                                seatCount.textContent = String(seatCodes.length);
                                seatTotal.textContent = formatCurrency(voucherResult.total);
                                if (voucherNote) {
                                    voucherNote.textContent = voucherResult.valid
                                        ? 'Voucher applied: -' + formatCurrency(voucherResult.discount)
                                        : '';
                                }
                                if (subtotalNote) {
                                    subtotalNote.textContent = voucherResult.valid
                                        ? 'Base subtotal: ' + formatCurrency(baseTotal)
                                        : '';
                                }
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
                            const accountLabel = document.getElementById('account_number_label');
                            const providerSelect = document.getElementById('payment_provider');

                            if (!typeSelect || !accountGroup || !walletGroup) {
                                return;
                            }

                            const updateFields = () => {
                                const type = typeSelect.value;
                                const isBank = type === 'bank_transfer';
                                const isWallet = type === 'ewallet';
                                const isQris = type === 'qris';

                                accountGroup.style.display = (isBank || isWallet) ? '' : 'none';
                                walletGroup.style.display = isWallet ? '' : 'none';
                                if (qrisNote) {
                                    qrisNote.classList.toggle('d-none', !isQris);
                                }

                                accountInput.required = isBank || isWallet;
                                providerSelect.required = isWallet;

                                if (accountLabel) {
                                    accountLabel.textContent = isWallet ? 'Phone Number' : 'Account Number';
                                }
                                if (accountInput) {
                                    accountInput.placeholder = isWallet ? 'Phone number (e-wallet)' : 'Bank account number';
                                }

                                if (!isBank && !isWallet) {
                                    accountInput.value = '';
                                }

                                if (!isWallet) {
                                    providerSelect.value = '';
                                }
                            };

                            typeSelect.addEventListener('change', updateFields);
                            if (voucherInput) {
                                voucherInput.addEventListener('input', updateSummary);
                            }
                            updateFields();
                            updateSummary();
                        })();
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
