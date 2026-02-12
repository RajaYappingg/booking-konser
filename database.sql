-- Concert Booking Database Schema

CREATE DATABASE IF NOT EXISTS konser_db;
USE konser_db;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Concerts table
CREATE TABLE concerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    artist VARCHAR(100) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    description TEXT,
    date DATETIME NOT NULL,
    location VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    available_seats INT NOT NULL,
    duration_minutes INT NOT NULL,
    setlist TEXT,
    status ENUM('upcoming', 'coming_soon') DEFAULT 'upcoming',
    preorder_multiplier DECIMAL(5, 2) DEFAULT 2.00,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    concert_id INT NOT NULL,
    quantity INT NOT NULL,
    payment_type ENUM('bank_transfer', 'qris', 'ewallet') NOT NULL,
    payment_provider VARCHAR(30),
    account_number VARCHAR(50),
    total_price DECIMAL(10, 2) NOT NULL,
    voucher_code VARCHAR(50),
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (concert_id) REFERENCES concerts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_concert (user_id, concert_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seat categories table
CREATE TABLE seat_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    concert_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_concert_code (concert_id, code),
    FOREIGN KEY (concert_id) REFERENCES concerts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seats table
CREATE TABLE seats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    concert_id INT NOT NULL,
    seat_code VARCHAR(10) NOT NULL,
    category_code VARCHAR(10) NOT NULL,
    status ENUM('available', 'booked') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_concert_seat (concert_id, seat_code),
    FOREIGN KEY (concert_id) REFERENCES concerts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Booking seats table
CREATE TABLE booking_seats (
    booking_id INT NOT NULL,
    seat_id INT NOT NULL,
    PRIMARY KEY (booking_id, seat_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vouchers table
CREATE TABLE vouchers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_type ENUM('percent', 'fixed') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    max_uses INT,
    used_count INT DEFAULT 0,
    expires_at DATETIME,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Concert Data
INSERT INTO concerts (title, artist, genre, description, date, location, price, available_seats, duration_minutes, setlist, status, preorder_multiplier, image_url) VALUES
('Rock Festival 2026', 'The Rolling Stones', 'Rock', 'An amazing rock concert experience', '2026-03-15 19:00:00', 'Jakarta International Stadium', 500000, 100, 120, 'Start Me Up, Tumbling Dice, Angie, Satisfaction', 'upcoming', 2.00, '/images/rock-festival.jpg'),
('Jazz Night', 'Miles Davis Tribute', 'Jazz', 'Smooth jazz performance', '2026-03-20 20:00:00', 'Convention Center', 250000, 100, 90, 'So What, Freddie Freeloader, Blue in Green, All Blues', 'upcoming', 2.00, '/images/jazz-night.jpg'),
('Pop Extravaganza', 'Taylor Swift', 'Pop', 'Pop music at its finest', '2026-04-10 18:00:00', 'Grand Arena', 750000, 100, 140, 'Blank Space, Style, Shake It Off, Love Story', 'coming_soon', 2.00, '/images/pop-extravaganza.jpg'),
('Classical Symphony', 'Indonesia Philharmonic', 'Classical', 'Classical music masterpiece', '2026-04-25 19:30:00', 'Concert Hall', 300000, 100, 110, 'Symphony No.5, Canon in D, Four Seasons', 'coming_soon', 2.00, '/images/classical-symphony.jpg');

-- Sample Vouchers
INSERT INTO vouchers (code, discount_type, amount, max_uses, expires_at, active) VALUES
('FESTIVAL10', 'percent', 10, 100, '2026-12-31 23:59:59', 1),
('SAVE50000', 'fixed', 50000, 50, '2026-12-31 23:59:59', 1);

-- Create indexes for better query performance
CREATE INDEX idx_concert_date ON concerts(date);
CREATE INDEX idx_booking_user ON bookings(user_id);
CREATE INDEX idx_booking_concert ON bookings(concert_id);
CREATE INDEX idx_booking_status ON bookings(status);
CREATE INDEX idx_voucher_code ON vouchers(code);
CREATE INDEX idx_seat_category_concert ON seat_categories(concert_id);
CREATE INDEX idx_seats_concert ON seats(concert_id);
CREATE INDEX idx_seats_status ON seats(status);
