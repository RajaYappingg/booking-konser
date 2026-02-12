<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1">All Bookings</h1>
        <p class="text-muted">Review every booking made by users.</p>
    </div>
    <a class="btn btn-outline-primary" href="<?= base_url('admin') ?>">Back to Dashboard</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Concert</th>
                    <th>Date</th>
                    <th>Qty</th>
                    <th>Seats</th>
                    <th class="text-end">Discount</th>
                    <th class="text-end">Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">No bookings found.</td>
                    </tr>
                <?php else: ?>
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
                            <td><?= e((string)$booking['id']) ?></td>
                            <td><?= e((string)$booking['user_name']) ?></td>
                            <td><?= e((string)$booking['user_email']) ?></td>
                            <td class="fw-semibold"><?= e((string)$booking['title']) ?></td>
                            <td><?= e(date('d M Y, H:i', strtotime((string)$booking['date']))) ?></td>
                            <td><?= e((string)$booking['quantity']) ?></td>
                            <td><?= e($seatCodes !== '' ? $seatCodes : '-') ?></td>
                            <td class="text-end">Rp <?= e(number_format((float)$booking['discount_amount'], 0, ',', '.')) ?></td>
                            <td class="text-end">Rp <?= e(number_format((float)$booking['total_price'], 0, ',', '.')) ?></td>
                            <td>
                                <?= e($paymentLabel) ?>
                                <span class="text-muted"><?= e($paymentDetail !== '' ? '· ' . $paymentDetail : '') ?></span>
                            </td>
                            <td><span class="badge <?= e($statusClass) ?>"><?= e($statusLabel) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
