<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1">All Concerts</h1>
        <p class="text-muted">Browse upcoming events and book your seat.</p>
    </div>
    <form class="d-flex" method="get" action="<?= base_url('concerts') ?>">
        <input
            type="text"
            class="form-control me-2"
            name="q"
            placeholder="Search by title, artist, venue"
            value="<?= isset($query) ? e((string)$query) : '' ?>"
        >
        <button class="btn btn-outline-primary" type="submit">Search</button>
    </form>
</div>

<?php if (empty($concerts)): ?>
    <div class="alert alert-info">No concerts found.</div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($concerts as $concert): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h5 mb-2"><?= e($concert['title']) ?></h2>
                        <p class="mb-1 text-muted">Artist: <?= e($concert['artist']) ?></p>
                        <p class="mb-1 text-muted">Venue: <?= e($concert['location']) ?></p>
                        <p class="mb-2 text-muted">Date: <?= e(date('d M Y, H:i', strtotime($concert['date']))) ?></p>
                        <p class="mb-2 fw-semibold">Price: Rp <?= e(number_format((float)$concert['price'], 0, ',', '.')) ?></p>
                        <p class="mb-3">Seats left: <?= e((string)$concert['available_seats']) ?></p>
                        <a class="btn btn-primary" href="<?= base_url('concerts/' . $concert['id']) ?>">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
