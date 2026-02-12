<?php
$isEdit = $voucher !== null;
$expiresValue = '';
if ($isEdit && !empty($voucher['expires_at'])) {
    $expiresValue = date('Y-m-d\TH:i', strtotime((string)$voucher['expires_at']));
}
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1"><?= $isEdit ? 'Edit Voucher' : 'Add Voucher' ?></h1>
        <p class="text-muted">Set voucher rules and availability.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= base_url('admin/vouchers') ?>">Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?= e($formAction) ?>" method="post" autocomplete="off">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="code">Code</label>
                    <input class="form-control" id="code" name="code" required maxlength="50" value="<?= e((string)($voucher['code'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="discount_type">Discount Type</label>
                    <?php $typeValue = (string)($voucher['discount_type'] ?? 'percent'); ?>
                    <select class="form-select" id="discount_type" name="discount_type" required>
                        <option value="percent" <?= $typeValue === 'percent' ? 'selected' : '' ?>>Percent (%)</option>
                        <option value="fixed" <?= $typeValue === 'fixed' ? 'selected' : '' ?>>Fixed (Rp)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="amount">Amount</label>
                    <input class="form-control" id="amount" name="amount" type="number" step="0.01" min="0" required value="<?= e((string)($voucher['amount'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="max_uses">Max Uses</label>
                    <input class="form-control" id="max_uses" name="max_uses" type="number" min="0" value="<?= e((string)($voucher['max_uses'] ?? '')) ?>">
                    <div class="form-text">Leave blank for unlimited uses.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="expires_at">Expires At</label>
                    <input class="form-control" id="expires_at" name="expires_at" type="datetime-local" value="<?= e($expiresValue) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="active">Status</label>
                    <?php $activeValue = (int)($voucher['active'] ?? 1); ?>
                    <select class="form-select" id="active" name="active" required>
                        <option value="1" <?= $activeValue === 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $activeValue === 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save</button>
                <a class="btn btn-outline-secondary" href="<?= base_url('admin/vouchers') ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>
