<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">My Bookings</h1>
        <p class="text-muted">Manage and review your concert orders.</p>
    </div>
</div>

<?php if (empty($bookings)): ?>
    <div class="alert alert-info">You have no bookings yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Concert</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Qty</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Detail</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <?php
                    $paymentType = $booking['payment_type'] ?? '';
                    $paymentProvider = $booking['payment_provider'] ?? '';
                    $accountNumber = $booking['account_number'] ?? '';
                    $providerLabels = [
                        'gopay' => 'GoPay',
                        'shopeepay' => 'ShopeePay',
                        'dana' => 'DANA',
                        'ovo' => 'OVO',
                    ];

                    $paymentLabel = 'N/A';
                    $paymentDetail = '';

                    $status = $booking['status'] ?? '';
                    $statusLabel = $status !== '' ? $status : 'unknown';
                    $statusClass = 'bg-success';
                    $seatCodes = $booking['seat_codes'] ?? '';
                    if ($status === 'pending') {
                        $statusClass = 'bg-warning text-dark';
                    } elseif ($status === 'cancelled') {
                        $statusClass = 'bg-secondary';
                    }

                    if ($paymentType === 'bank_transfer') {
                        $paymentLabel = 'Bank Transfer';
                        $paymentDetail = $accountNumber !== '' ? $accountNumber : '-';
                    } elseif ($paymentType === 'qris') {
                        $paymentLabel = 'QRIS';
                    } elseif ($paymentType === 'ewallet') {
                        $paymentLabel = 'E-wallet';
                        if ($paymentProvider !== '' && isset($providerLabels[$paymentProvider])) {
                            $paymentDetail = $providerLabels[$paymentProvider];
                        } elseif ($paymentProvider !== '') {
                            $paymentDetail = strtoupper($paymentProvider);
                        }
                    }
                    ?>
                    <tr>
                        <td><?= e($booking['title']) ?></td>
                        <td><?= e(date('d M Y, H:i', strtotime($booking['date']))) ?></td>
                        <td><?= e($booking['location']) ?></td>
                        <td><?= e((string)$booking['quantity']) ?></td>
                        <td><?= e($seatCodes !== '' ? $seatCodes : '-') ?></td>
                        <td>Rp <?= e(number_format((float)$booking['price'], 0, ',', '.')) ?></td>
                        <td>Rp <?= e(number_format((float)$booking['discount_amount'], 0, ',', '.')) ?></td>
                        <td>Rp <?= e(number_format((float)$booking['total_price'], 0, ',', '.')) ?></td>
                        <td><?= e($paymentLabel) ?></td>
                        <td><?= e($paymentDetail !== '' ? $paymentDetail : '-') ?></td>
                        <td><span class="badge <?= e($statusClass) ?>"><?= e($statusLabel) ?></span></td>
                        <td>
                            <?php if (in_array($status, ['pending', 'confirmed'], true)): ?>
                                <form action="<?= base_url('bookings/' . (int)$booking['id'] . '/cancel') ?>" method="post" class="d-inline">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this booking?');">
                                        Cancel
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
