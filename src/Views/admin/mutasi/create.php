<?php
$title = "Ajukan Mutasi Hafiz";
?>
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Pengajuan Mutasi Hafiz</h4>
</div>

<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-body">
        <form action="<?= APP_URL ?>/admin/mutasi/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Pilih Hafiz (Asal Kab/Kota Anda)</label>
                <select name="hafiz_id" class="form-select" required>
                    <option value="">-- Pilih Hafiz --</option>
                    <?php foreach ($hafizList as $h): ?>
                        <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['nama']) ?> - <?= $h['nik'] ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Hafiz yang akan dipindahkan harus aktif dan terdaftar di wilayah Anda.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Kabupaten/Kota Tujuan</label>
                <select name="tujuan_kabko_id" class="form-select" required>
                    <option value="">-- Pilih Tujuan --</option>
                    <?php foreach ($kabkoList as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Alasan Kepindahan</label>
                <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Pindah domisili mengikuti suami/istri..."></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4 border-top pt-3">
                <a href="<?= APP_URL ?>/admin/mutasi" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-send me-2"></i> Ajukan Mutasi
                </button>
            </div>
        </form>
    </div>
</div>