<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1">All Concerts</h1>
        <p class="text-muted">Browse upcoming events and book your seat.</p>
    </div>
    <form class="toolbar row g-2 align-items-center" method="get" action="<?= base_url('concerts') ?>">
        <div class="col-12 col-md">
            <input
                type="text"
                class="form-control"
                name="q"
                placeholder="Search title, artist, venue"
                value="<?= isset($query) ? e((string)$query) : '' ?>"
            >
        </div>
        <div class="col-6 col-md-auto">
            <select class="form-select" name="genre">
                <option value="">All genres</option>
                <?php foreach (($genres ?? []) as $genreOption): ?>
                    <option value="<?= e((string)$genreOption) ?>" <?= ($selectedGenre ?? '') === $genreOption ? 'selected' : '' ?>>
                        <?= e((string)$genreOption) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-auto">
            <select class="form-select" name="artist">
                <option value="">All artists</option>
                <?php foreach (($artists ?? []) as $artistOption): ?>
                    <option value="<?= e((string)$artistOption) ?>" <?= ($selectedArtist ?? '') === $artistOption ? 'selected' : '' ?>>
                        <?= e((string)$artistOption) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-auto">
            <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
        </div>
        <div class="col-6 col-md-auto">
            <a class="btn btn-outline-secondary w-100" href="<?= base_url('concerts') ?>">Reset</a>
        </div>
    </form>
</div>

<?php if (empty($concerts)): ?>
    <div class="alert alert-info">No concerts found.</div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($concerts as $concert): ?>
            <?php
            $status = $concert['status'] ?? 'upcoming';
            $statusLabel = $status === 'coming_soon' ? 'Coming Soon' : 'Upcoming';
            try {
                $concertDate = new DateTime((string)$concert['date']);
                $now = new DateTime();
                $diffDays = (int)$now->diff($concertDate)->format('%r%a');
                if ($status !== 'coming_soon' && $diffDays > 30) {
                    $statusLabel = 'Scheduled';
                }
            } catch (Exception $e) {
                $statusLabel = $status === 'coming_soon' ? 'Coming Soon' : 'Upcoming';
            }
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card concert-card h-100 fade-up">
                    <div class="card-body">
                        <h2 class="h5 mb-2"><?= e($concert['title']) ?></h2>
                        <p class="concert-meta mb-1">Artist: <?= e($concert['artist']) ?></p>
                        <p class="concert-meta mb-1">Genre: <?= e($concert['genre'] ?? '-') ?></p>
                        <p class="concert-meta mb-1">Venue: <?= e($concert['location']) ?></p>
                        <p class="concert-meta mb-2">Date: <?= e(date('d M Y, H:i', strtotime($concert['date']))) ?></p>
                        <span class="badge-soft mb-2"><?= e($statusLabel) ?></span>
                        <p class="mb-2 fw-semibold">Price: Rp <?= e(number_format((float)$concert['price'], 0, ',', '.')) ?></p>
                        <p class="mb-3">Seats left: <?= e((string)$concert['available_seats']) ?></p>
                        <a class="btn btn-brand" href="<?= base_url('concerts/' . $concert['id']) ?>">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
