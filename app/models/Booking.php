<?php

declare(strict_types=1);

class Booking extends Model
{
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT b.id, b.booking_date, b.quantity, b.total_price, b.status, '
            . 'b.voucher_code, b.discount_amount, b.payment_type, b.payment_provider, b.account_number, '
            . 'GROUP_CONCAT(s.seat_code ORDER BY s.seat_code SEPARATOR ", ") AS seat_codes, '
            . 'c.title, c.date, c.location, c.price '
            . 'FROM bookings b '
            . 'JOIN concerts c ON b.concert_id = c.id '
            . 'LEFT JOIN booking_seats bs ON bs.booking_id = b.id '
            . 'LEFT JOIN seats s ON s.id = bs.seat_id '
            . 'WHERE b.user_id = :user_id '
            . 'GROUP BY b.id, b.booking_date, b.quantity, b.total_price, b.status, b.voucher_code, '
            . 'b.discount_amount, b.payment_type, b.payment_provider, b.account_number, '
            . 'c.title, c.date, c.location, c.price '
            . 'ORDER BY b.booking_date DESC'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getAll(string $statusFilter = 'all'): array
    {
        $statusFilter = strtolower(trim($statusFilter));
        $where = '';
        $params = [];

        if ($statusFilter === 'cancelled') {
            $where = 'WHERE b.status = :status';
            $params[':status'] = 'cancelled';
        } elseif ($statusFilter === 'active') {
            $where = 'WHERE b.status != :status';
            $params[':status'] = 'cancelled';
        }

        $stmt = $this->db->prepare(
            'SELECT b.id, b.booking_date, b.quantity, b.total_price, b.status, '
            . 'b.voucher_code, b.discount_amount, b.payment_type, b.payment_provider, b.account_number, '
            . 'GROUP_CONCAT(s.seat_code ORDER BY s.seat_code SEPARATOR ", ") AS seat_codes, '
            . 'c.title, c.date, c.location, c.price, '
            . 'u.name AS user_name, u.email AS user_email '
            . 'FROM bookings b '
            . 'JOIN concerts c ON b.concert_id = c.id '
            . 'JOIN users u ON b.user_id = u.id '
            . 'LEFT JOIN booking_seats bs ON bs.booking_id = b.id '
            . 'LEFT JOIN seats s ON s.id = bs.seat_id '
            . $where . ' '
            . 'GROUP BY b.id, b.booking_date, b.quantity, b.total_price, b.status, b.voucher_code, '
            . 'b.discount_amount, b.payment_type, b.payment_provider, b.account_number, '
            . 'c.title, c.date, c.location, c.price, u.name, u.email '
            . 'ORDER BY b.booking_date DESC'
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByConcert(int $concertId): array
    {
        $stmt = $this->db->prepare(
            'SELECT b.id, b.booking_date, b.quantity, b.total_price, b.status, '
            . 'b.voucher_code, b.discount_amount, b.payment_type, b.payment_provider, b.account_number, '
            . 'GROUP_CONCAT(s.seat_code ORDER BY s.seat_code SEPARATOR ", ") AS seat_codes, '
            . 'c.title, c.date, c.location, c.price, '
            . 'u.name AS user_name, u.email AS user_email '
            . 'FROM bookings b '
            . 'JOIN concerts c ON b.concert_id = c.id '
            . 'JOIN users u ON b.user_id = u.id '
            . 'LEFT JOIN booking_seats bs ON bs.booking_id = b.id '
            . 'LEFT JOIN seats s ON s.id = bs.seat_id '
            . 'WHERE b.concert_id = :concert_id '
            . 'GROUP BY b.id, b.booking_date, b.quantity, b.total_price, b.status, b.voucher_code, '
            . 'b.discount_amount, b.payment_type, b.payment_provider, b.account_number, '
            . 'c.title, c.date, c.location, c.price, u.name, u.email '
            . 'ORDER BY b.booking_date DESC'
        );
        $stmt->execute([':concert_id' => $concertId]);
        return $stmt->fetchAll();
    }

    public function hasBooking(int $userId, int $concertId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM bookings WHERE user_id = :user_id AND concert_id = :concert_id AND status != :status'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':concert_id' => $concertId,
            ':status' => 'cancelled',
        ]);

        return (bool)$stmt->fetch();
    }

    public function createBooking(
        int $userId,
        int $concertId,
        array $seatIds,
        string $paymentType,
        string $paymentProvider = '',
        string $accountNumber = '',
        string $voucherCode = ''
    ): array
    {
        $seatIds = array_values(array_unique(array_filter($seatIds, static fn (int $id) => $id > 0)));
        $quantity = count($seatIds);

        if ($quantity < 1) {
            return [false, 'Please select at least one seat.'];
        }

        if ($quantity > 10) {
            return [false, 'Maximum 10 tickets per booking.'];
        }

        $paymentType = strtolower(trim($paymentType));
        $paymentProvider = strtolower(trim($paymentProvider));
        $accountNumber = trim($accountNumber);

        $allowedTypes = ['bank_transfer', 'qris', 'ewallet'];
        if (!in_array($paymentType, $allowedTypes, true)) {
            return [false, 'Payment type is invalid.'];
        }

        if ($paymentType === 'bank_transfer' && $accountNumber === '') {
            return [false, 'Account number is required for bank transfer.'];
        }

        if ($paymentType === 'ewallet' && $accountNumber === '') {
            return [false, 'Phone number is required for e-wallet payments.'];
        }

        if ($paymentType === 'ewallet') {
            $allowedProviders = ['gopay', 'shopeepay', 'dana', 'ovo'];
            if ($paymentProvider === '' || !in_array($paymentProvider, $allowedProviders, true)) {
                return [false, 'E-wallet provider is invalid.'];
            }
        } else {
            $paymentProvider = '';
        }

        if ($paymentType === 'qris') {
            $accountNumber = '';
        }

        $voucherCode = strtoupper(trim($voucherCode));

        try {
            $this->db->beginTransaction();

            $concertStmt = $this->db->prepare(
                'SELECT id, title, price, available_seats, date, preorder_multiplier '
                . 'FROM concerts '
                . 'WHERE id = :id FOR UPDATE'
            );
            $concertStmt->execute([':id' => $concertId]);
            $concert = $concertStmt->fetch();

            if (!$concert) {
                $this->db->rollBack();
                return [false, 'Concert not found.'];
            }

            if ($this->hasBooking($userId, $concertId)) {
                $this->db->rollBack();
                return [false, 'You already booked this concert.'];
            }

            if ((int)$concert['available_seats'] <= 0) {
                $this->db->rollBack();
                return [false, 'Sorry, this concert is sold out.'];
            }

            if ($quantity > (int)$concert['available_seats']) {
                $this->db->rollBack();
                return [false, 'Not enough seats available for that quantity.'];
            }

            $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
            $seatStmt = $this->db->prepare(
                'SELECT s.id, s.seat_code, s.status, s.category_code, sc.price '
                . 'FROM seats s '
                . 'JOIN seat_categories sc ON sc.concert_id = s.concert_id AND sc.code = s.category_code '
                . 'WHERE s.concert_id = ? AND s.id IN (' . $placeholders . ') '
                . 'ORDER BY s.seat_code ASC '
                . 'FOR UPDATE'
            );
            $seatStmt->execute(array_merge([$concertId], $seatIds));
            $seats = $seatStmt->fetchAll();

            if (count($seats) !== count($seatIds)) {
                $this->db->rollBack();
                return [false, 'One or more seats are invalid.'];
            }

            foreach ($seats as $seat) {
                if ($seat['status'] !== 'available') {
                    $this->db->rollBack();
                    return [false, 'One or more seats are already booked.'];
                }
            }

            $existingStmt = $this->db->prepare(
                'SELECT id, status FROM bookings WHERE user_id = :user_id AND concert_id = :concert_id FOR UPDATE'
            );
            $existingStmt->execute([
                ':user_id' => $userId,
                ':concert_id' => $concertId,
            ]);
            $existingBooking = $existingStmt->fetch();

            if ($existingBooking && $existingBooking['status'] !== 'cancelled') {
                $this->db->rollBack();
                return [false, 'You already booked this concert.'];
            }

            $stepMultiplier = isset($concert['preorder_multiplier']) ? (float)$concert['preorder_multiplier'] : 1.0;
            if ($stepMultiplier < 1) {
                $stepMultiplier = 1.0;
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

            $subtotal = 0.0;
            foreach ($seats as $index => $seat) {
                $seatPrice = (float)$seat['price'];
                if ($preorderActive) {
                    $seatPrice *= pow($stepMultiplier, $index);
                }
                $subtotal += $seatPrice;
            }
            $discountAmount = 0.0;
            $voucherId = null;

            if ($voucherCode !== '') {
                $voucherModel = new Voucher();
                $voucher = $voucherModel->findAvailableForUpdate($voucherCode);

                if (!$voucher) {
                    $this->db->rollBack();
                    return [false, 'Voucher code is invalid or expired.'];
                }

                if ($voucher['discount_type'] === 'percent') {
                    $discountAmount = $subtotal * ((float)$voucher['amount'] / 100);
                } else {
                    $discountAmount = (float)$voucher['amount'];
                }

                if ($discountAmount > $subtotal) {
                    $discountAmount = $subtotal;
                }

                $voucherId = (int)$voucher['id'];
            }

            $total = $subtotal - $discountAmount;

            if ($existingBooking) {
                $updateStmt = $this->db->prepare(
                    'UPDATE bookings '
                    . 'SET quantity = :quantity, payment_type = :payment_type, payment_provider = :payment_provider, '
                    . 'account_number = :account_number, total_price = :total_price, status = :status, '
                    . 'booking_date = NOW(), voucher_code = :voucher_code, discount_amount = :discount_amount '
                    . 'WHERE id = :id'
                );
                $updateStmt->execute([
                    ':quantity' => $quantity,
                    ':payment_type' => $paymentType,
                    ':payment_provider' => $paymentProvider !== '' ? $paymentProvider : null,
                    ':account_number' => $accountNumber !== '' ? $accountNumber : null,
                    ':total_price' => $total,
                    ':status' => 'confirmed',
                    ':voucher_code' => $voucherCode !== '' ? $voucherCode : null,
                    ':discount_amount' => $discountAmount,
                    ':id' => (int)$existingBooking['id'],
                ]);
                $bookingId = (int)$existingBooking['id'];

                $deleteSeats = $this->db->prepare(
                    'DELETE FROM booking_seats WHERE booking_id = :booking_id'
                );
                $deleteSeats->execute([':booking_id' => $bookingId]);
            } else {
                $insertStmt = $this->db->prepare(
                    'INSERT INTO bookings (user_id, concert_id, quantity, payment_type, payment_provider, account_number, total_price, status, booking_date, voucher_code, discount_amount) '
                    . 'VALUES (:user_id, :concert_id, :quantity, :payment_type, :payment_provider, :account_number, :total_price, :status, NOW(), :voucher_code, :discount_amount)'
                );
                $insertStmt->execute([
                    ':user_id' => $userId,
                    ':concert_id' => $concertId,
                    ':quantity' => $quantity,
                    ':payment_type' => $paymentType,
                    ':payment_provider' => $paymentProvider !== '' ? $paymentProvider : null,
                    ':account_number' => $accountNumber !== '' ? $accountNumber : null,
                    ':total_price' => $total,
                    ':status' => 'confirmed',
                    ':voucher_code' => $voucherCode !== '' ? $voucherCode : null,
                    ':discount_amount' => $discountAmount,
                ]);
                $bookingId = (int)$this->db->lastInsertId();
            }

            $insertSeat = $this->db->prepare(
                'INSERT INTO booking_seats (booking_id, seat_id) VALUES (:booking_id, :seat_id)'
            );
            foreach ($seatIds as $seatId) {
                $insertSeat->execute([
                    ':booking_id' => $bookingId,
                    ':seat_id' => $seatId,
                ]);
            }

            $seatUpdate = $this->db->prepare(
                'UPDATE seats SET status = ? WHERE id IN (' . $placeholders . ')'
            );
            $seatUpdate->execute(array_merge(['booked'], $seatIds));

            $updateStmt = $this->db->prepare(
                'UPDATE concerts SET available_seats = available_seats - :qty WHERE id = :id'
            );
            $updateStmt->execute([':id' => $concertId, ':qty' => $quantity]);

            if ($voucherId !== null) {
                $voucherModel->incrementUsage($voucherId);
            }

            $this->db->commit();

            return [true, 'Booking confirmed. See you at the show!'];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            if ((int)$e->getCode() === 23000) {
                return [false, 'This booking already exists.'];
            }

            return [false, 'Unable to complete booking. Please try again.'];
        }
    }

    public function cancelBooking(int $userId, int $bookingId): array
    {
        try {
            $this->db->beginTransaction();

            $bookingStmt = $this->db->prepare(
                'SELECT id, concert_id, quantity, status '
                . 'FROM bookings '
                . 'WHERE id = :id AND user_id = :user_id '
                . 'FOR UPDATE'
            );
            $bookingStmt->execute([
                ':id' => $bookingId,
                ':user_id' => $userId,
            ]);
            $booking = $bookingStmt->fetch();

            if (!$booking) {
                $this->db->rollBack();
                return [false, 'Booking not found.'];
            }

            if ($booking['status'] === 'cancelled') {
                $this->db->rollBack();
                return [false, 'Booking already cancelled.'];
            }

            if (!in_array($booking['status'], ['pending', 'confirmed'], true)) {
                $this->db->rollBack();
                return [false, 'Booking cannot be cancelled.'];
            }

            $updateBooking = $this->db->prepare(
                'UPDATE bookings SET status = :status WHERE id = :id'
            );
            $updateBooking->execute([
                ':status' => 'cancelled',
                ':id' => $bookingId,
            ]);

            $seatStmt = $this->db->prepare(
                'SELECT seat_id FROM booking_seats WHERE booking_id = :booking_id'
            );
            $seatStmt->execute([':booking_id' => $bookingId]);
            $seatIds = array_map('intval', $seatStmt->fetchAll(PDO::FETCH_COLUMN));

            if (!empty($seatIds)) {
                $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
                $releaseSeats = $this->db->prepare(
                    'UPDATE seats SET status = ? WHERE id IN (' . $placeholders . ')'
                );
                $releaseSeats->execute(array_merge(['available'], $seatIds));

                $restoreSeats = $this->db->prepare(
                    'UPDATE concerts SET available_seats = available_seats + :qty WHERE id = :id'
                );
                $restoreSeats->execute([
                    ':qty' => count($seatIds),
                    ':id' => (int)$booking['concert_id'],
                ]);
            }

            $this->db->commit();

            return [true, 'Booking cancelled successfully.'];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return [false, 'Unable to cancel booking. Please try again.'];
        }
    }
}
