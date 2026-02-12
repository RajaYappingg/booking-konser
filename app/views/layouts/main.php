<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Concert Booking') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #111111;
            --brand-dark: #000000;
            --ink: #0b0b0b;
            --muted: #5c5c5c;
            --surface: #ffffff;
            --surface-alt: #f5f5f5;
            --stroke: #e1e1e1;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Space Grotesk', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(70% 120% at 15% 0%, rgba(0, 0, 0, 0.06) 0%, rgba(0, 0, 0, 0) 55%),
                radial-gradient(60% 80% at 90% 10%, rgba(0, 0, 0, 0.04) 0%, rgba(0, 0, 0, 0) 60%),
                var(--surface-alt);
        }
        main {
            flex: 1;
        }
        footer {
            background-color: var(--surface-alt);
            border-top: 1px solid var(--stroke);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 0.2px;
        }
        .navbar {
            background: linear-gradient(90deg, #111111 0%, #1d1d1d 60%, #2a2a2a 100%);
        }
        .nav-link {
            font-weight: 500;
        }
        .page-shell {
            margin-top: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .hero {
            background: linear-gradient(120deg, #ffffff 0%, #f2f2f2 55%, #ffffff 100%);
            border: 1px solid var(--stroke);
            border-radius: 22px;
            padding: 2.5rem;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            position: relative;
            overflow: hidden;
        }
        .hero::after {
            content: "";
            position: absolute;
            inset: auto -20% -60% auto;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0));
            filter: blur(2px);
        }
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 3.5vw, 3rem);
            margin-bottom: 0.5rem;
        }
        .hero-subtitle {
            color: var(--muted);
            max-width: 520px;
        }
        .section-title {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .toolbar {
            padding: 1rem 1.25rem;
            background: var(--surface);
            border: 1px solid var(--stroke);
            border-radius: 16px;
            box-shadow: 0 12px 25px rgba(15, 23, 42, 0.05);
        }
        .concert-card {
            border-radius: 18px;
            border: 1px solid var(--stroke);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            background: var(--surface);
        }
        .concert-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 35px rgba(15, 23, 42, 0.12);
        }
        .concert-card .card-title {
            font-weight: 600;
        }
        .concert-meta {
            font-size: 0.9rem;
            color: var(--muted);
        }
        .badge-soft {
            background: #f1f1f1;
            color: #1a1a1a;
            border-radius: 999px;
            padding: 0.35rem 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .btn-brand {
            background: var(--brand);
            border-color: var(--brand);
            color: #ffffff;
        }
        .btn-brand:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
        }
        .fade-up {
            animation: fadeUp 0.6s ease both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">🎵 Concert Booking</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('concerts') ?>">Concerts</a>
                    </li>
                    <?php if (is_logged_in()): ?>
                        <?php if (!is_admin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('bookings') ?>">My Bookings</a>
                            </li>
                        <?php endif; ?>
                        <?php if (is_admin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin') ?>">Admin</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <form action="<?= base_url('logout') ?>" method="post" class="d-inline">
                                <button type="submit" class="nav-link btn btn-link p-0">Logout</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('register') ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (!empty($flash)): ?>
        <div class="container mt-3">
            <?php foreach ($flash as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= e($type === 'error' ? 'danger' : $type) ?> alert-dismissible fade show" role="alert">
                        <?= e($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <div class="container page-shell">
            <?php require $templatePath; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-5 py-4">
        <div class="container text-center text-muted">
            <p>&copy; 2026 Concert Booking App. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
