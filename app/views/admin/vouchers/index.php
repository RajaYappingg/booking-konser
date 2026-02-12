<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1">Manage Vouchers</h1>
        <p class="text-muted">Create and update discount vouchers.</p>
    </div>
    <a class="btn btn-primary" href="<?= base_url('admin/vouchers/create') ?>">Add Voucher</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Max Uses</th>
                    <th class="text-end">Used</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vouchers)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No vouchers found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vouchers as $voucher): ?>
                        <tr>
                            <td class="fw-semibold"><?= e((string)$voucher['code']) ?></td>
                            <td><?= e((string)$voucher['discount_type']) ?></td>
                            <td class="text-end"><?= e(number_format((float)$voucher['amount'], 2, '.', '')) ?></td>
                            <td class="text-end"><?= e($voucher['max_uses'] !== null ? (string)$voucher['max_uses'] : '-') ?></td>
                            <td class="text-end"><?= e((string)$voucher['used_count']) ?></td>
                            <td><?= e($voucher['expires_at'] ? date('d M Y H:i', strtotime((string)$voucher['expires_at'])) : '-') ?></td>
                            <td>
                                <span class="badge bg-<?= (int)$voucher['active'] === 1 ? 'success' : 'secondary' ?>">
                                    <?= (int)$voucher['active'] === 1 ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/vouchers/' . $voucher['id'] . '/edit') ?>">Edit</a>
                                <form action="<?= base_url('admin/vouchers/' . $voucher['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this voucher?');">
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
