<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1">Admin Dashboard</h1>
        <p class="text-muted">Quick overview of the system.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="<?= base_url('admin/concerts') ?>">Manage Concerts</a>
        <a class="btn btn-outline-primary" href="<?= base_url('admin/vouchers') ?>">Manage Vouchers</a>
        <a class="btn btn-outline-primary" href="<?= base_url('admin/bookings') ?>">View Bookings</a>
        <a class="btn btn-outline-primary" href="<?= base_url('admin/users') ?>">View Users</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Concerts</p>
                <h2 class="h4 mb-0"><?= e((string)($stats['concerts'] ?? 0)) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Bookings</p>
                <h2 class="h4 mb-0"><?= e((string)($stats['bookings'] ?? 0)) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Users</p>
                <h2 class="h4 mb-0"><?= e((string)($stats['users'] ?? 0)) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Vouchers</p>
                <h2 class="h4 mb-0"><?= e((string)($stats['vouchers'] ?? 0)) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body">
        <h2 class="h5">Total Revenue</h2>
        <p class="fs-4 fw-semibold mb-0">Rp <?= e(number_format((float)($stats['revenue'] ?? 0), 0, ',', '.')) ?></p>
        <p class="text-muted mb-0">Sum of confirmed bookings.</p>
    </div>
</div>
