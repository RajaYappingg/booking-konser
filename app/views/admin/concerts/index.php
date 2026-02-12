<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1">Manage Concerts</h1>
        <p class="text-muted">Add, edit, or remove concerts.</p>
    </div>
    <a class="btn btn-primary" href="<?= base_url('admin/concerts/create') ?>">Add Concert</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Seats</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($concerts)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No concerts found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($concerts as $concert): ?>
                        <tr>
                            <td class="fw-semibold"><?= e((string)$concert['title']) ?></td>
                            <td><?= e((string)$concert['artist']) ?></td>
                            <td><?= e(date('d M Y H:i', strtotime((string)$concert['date']))) ?></td>
                            <td>
                                <span class="badge bg-<?= ($concert['status'] ?? 'upcoming') === 'coming_soon' ? 'warning' : 'success' ?>">
                                    <?= e(($concert['status'] ?? 'upcoming') === 'coming_soon' ? 'Coming Soon' : 'Upcoming') ?>
                                </span>
                            </td>
                            <td class="text-end">Rp <?= e(number_format((float)$concert['price'], 0, ',', '.')) ?></td>
                            <td class="text-end"><?= e((string)$concert['available_seats']) ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/concerts/' . $concert['id'] . '/edit') ?>">Edit</a>
                                <form action="<?= base_url('admin/concerts/' . $concert['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this concert?');">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
