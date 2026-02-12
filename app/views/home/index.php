<section class="hero mb-5 fade-up">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <h1 class="hero-title">Welcome to Concert Booking</h1>
            <p class="hero-subtitle">Discover and book your favorite concerts with curated seat maps and smooth checkout.</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
            <span class="badge-soft">Curated Events</span>
            <span class="badge-soft ms-2">Seat Map Ready</span>
        </div>
    </div>
</section>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="section-title mb-0">All Concerts</h2>
</div>

<div class="row">
    <?php if (!empty($concerts)): ?>
        <?php foreach ($concerts as $concert): ?>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card concert-card h-100 fade-up">
                    <?php if (!empty($concert['image_url'])): ?>
                        <img src="<?= e($concert['image_url']) ?>" class="card-img-top" alt="<?= e($concert['title']) ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px; background: #edf2ff;">
                            <span class="fs-3">🎵</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div>
                                <h5 class="card-title mb-1"><?= e($concert['title']) ?></h5>
                                <p class="concert-meta mb-0"><?= e($concert['artist']) ?></p>
                            </div>
                            <span class="badge-soft"><?= e($concert['genre'] ?? 'Live') ?></span>
                        </div>
                        <p class="concert-meta mb-3">
                            📍 <?= e($concert['location']) ?><br>
                            📅 <?= date('M d, Y H:i', strtotime($concert['date'])) ?>
                        </p>
                        <div class="mt-auto d-flex align-items-center justify-content-between">
                            <?php
                            $minPrice = $concert['min_seat_price'] ?? null;
                            $maxPrice = $concert['max_seat_price'] ?? null;
                            $hasRange = $minPrice !== null && $maxPrice !== null;
                            $priceText = 'Rp. ' . number_format((float)$concert['price'], 0, ',', '.');
                            if ($hasRange) {
                                $minText = 'Rp. ' . number_format((float)$minPrice, 0, ',', '.');
                                $maxText = 'Rp. ' . number_format((float)$maxPrice, 0, ',', '.');
                                $priceText = $minPrice === $maxPrice ? $minText : $minText . ' - ' . $maxText;
                            }
                            ?>
                            <strong class="text-primary"><?= e($priceText) ?></strong>
                            <a href="<?= base_url('concerts/' . $concert['id']) ?>" class="btn btn-brand btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <p class="text-center text-muted py-5">No concerts available at the moment.</p>
        </div>
    <?php endif; ?>
</div>
