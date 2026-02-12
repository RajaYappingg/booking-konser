<div class="row">
    <div class="col-lg-12">
        <div class="jumbotron bg-light p-5 rounded-lg mb-5">
            <h1 class="display-5 fw-bold">Welcome to Concert Booking</h1>
            <p class="lead">Discover and book your favorite concerts</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12">
        <h2 class="mb-4">Featured Concerts</h2>
    </div>
</div>

<div class="row">
    <?php if (!empty($featured)): ?>
        <?php foreach ($featured as $concert): ?>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($concert['image_url'])): ?>
                        <img src="<?= e($concert['image_url']) ?>" class="card-img-top" alt="<?= e($concert['title']) ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <span class="text-white">🎵</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= e($concert['title']) ?></h5>
                        <p class="card-text text-muted"><?= e($concert['artist']) ?></p>
                        <p class="card-text">
                            <small class="text-muted">
                                📍 <?= e($concert['location']) ?><br>
                                📅 <?= date('M d, Y H:i', strtotime($concert['date'])) ?>
                            </small>
                        </p>
                        <p class="card-text mt-auto">
                            <strong class="text-primary">Rp. <?= number_format($concert['price'], 0, ',', '.') ?></strong>
                        </p>
                        <a href="<?= base_url('concerts/' . $concert['id']) ?>" class="btn btn-primary btn-sm">View Details</a>
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
