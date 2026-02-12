<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1">All Users</h1>
        <p class="text-muted">List of registered users.</p>
    </div>
    <a class="btn btn-outline-primary" href="<?= base_url('admin') ?>">Back to Dashboard</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e((string)$user['id']) ?></td>
                            <td><?= e((string)$user['name']) ?></td>
                            <td><?= e((string)$user['email']) ?></td>
                            <td><?= e((string)$user['role']) ?></td>
                            <td><?= e($user['phone'] ? (string)$user['phone'] : '-') ?></td>
                            <td><?= e($user['city'] ? (string)$user['city'] : '-') ?></td>
                            <td><?= e(date('d M Y', strtotime((string)$user['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
