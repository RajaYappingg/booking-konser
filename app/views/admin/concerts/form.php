<?php
$isEdit = $concert !== null;
$dateValue = '';
if ($isEdit && !empty($concert['date'])) {
    $dateValue = date('Y-m-d\TH:i', strtotime((string)$concert['date']));
}
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1"><?= $isEdit ? 'Edit Concert' : 'Add Concert' ?></h1>
        <p class="text-muted">Fill in the concert details.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= base_url('admin/concerts') ?>">Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?= e($formAction) ?>" method="post" autocomplete="off">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" id="title" name="title" required maxlength="150" value="<?= e((string)($concert['title'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="artist">Artist</label>
                    <input class="form-control" id="artist" name="artist" required maxlength="100" value="<?= e((string)($concert['artist'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="genre">Genre</label>
                    <input class="form-control" id="genre" name="genre" required maxlength="50" value="<?= e((string)($concert['genre'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php $statusValue = (string)($concert['status'] ?? 'upcoming'); ?>
                        <option value="upcoming" <?= $statusValue === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                        <option value="coming_soon" <?= $statusValue === 'coming_soon' ? 'selected' : '' ?>>Coming Soon</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="date">Date</label>
                    <input class="form-control" id="date" name="date" type="datetime-local" required value="<?= e($dateValue) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="location">Location</label>
                    <input class="form-control" id="location" name="location" required maxlength="200" value="<?= e((string)($concert['location'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="price">Base Price</label>
                    <input class="form-control" id="price" name="price" type="number" step="0.01" min="0" required value="<?= e((string)($concert['price'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="vvip_price">VVIP Price</label>
                    <input class="form-control" id="vvip_price" name="vvip_price" type="number" step="0.01" min="0" required value="<?= e((string)($seatPrices['vvip'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="available_seats">Available Seats</label>
                    <input class="form-control" id="available_seats" name="available_seats" type="number" min="0" required value="<?= e((string)($concert['available_seats'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="vip_price">VIP Price</label>
                    <input class="form-control" id="vip_price" name="vip_price" type="number" step="0.01" min="0" required value="<?= e((string)($seatPrices['vip'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="duration_minutes">Duration (minutes)</label>
                    <input class="form-control" id="duration_minutes" name="duration_minutes" type="number" min="1" required value="<?= e((string)($concert['duration_minutes'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="elite_price">Elite Price</label>
                    <input class="form-control" id="elite_price" name="elite_price" type="number" step="0.01" min="0" required value="<?= e((string)($seatPrices['elite'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="preorder_multiplier">Preorder Multiplier</label>
                    <input class="form-control" id="preorder_multiplier" name="preorder_multiplier" type="number" step="0.01" min="1" required value="<?= e((string)($concert['preorder_multiplier'] ?? '1')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="normal_price">Normal Price</label>
                    <input class="form-control" id="normal_price" name="normal_price" type="number" step="0.01" min="0" required value="<?= e((string)($seatPrices['normal'] ?? '')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" for="image_url">Image URL</label>
                    <input class="form-control" id="image_url" name="image_url" maxlength="255" value="<?= e((string)($concert['image_url'] ?? '')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" maxlength="1000"><?= e((string)($concert['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" for="setlist">Setlist</label>
                    <textarea class="form-control" id="setlist" name="setlist" rows="4" maxlength="2000"><?= e((string)($concert['setlist'] ?? '')) ?></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save</button>
                <a class="btn btn-outline-secondary" href="<?= base_url('admin/concerts') ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>
