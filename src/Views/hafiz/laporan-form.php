<div class="page-header">
    <h4 class="mb-0">
        <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?> me-2"></i>
        <?= $isEdit ? 'Edit' : 'Input' ?> Laporan Harian
    </h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="<?= APP_URL ?>/hafiz/laporan<?= $isEdit ? '/' . $laporan['id'] . '/update' : '' ?>" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" required
                                value="<?= htmlspecialchars($laporan['tanggal'] ?? date('Y-m-d')) ?>"
                                max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_kegiatan" required>
                                <option value="">-- Pilih Kegiatan --</option>
                                <?php foreach ($jenisKegiatan as $jenis): ?>
                                    <option value="<?= $jenis ?>" <?= ($laporan['jenis_kegiatan'] ?? '') === $jenis ? 'selected' : '' ?>>
                                        <?= ucfirst($jenis) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="4" required
                            placeholder="Jelaskan kegiatan yang dilakukan..."><?= htmlspecialchars($laporan['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi</label>
                            <input type="text" class="form-control" name="lokasi"
                                placeholder="Tempat kegiatan dilaksanakan"
                                value="<?= htmlspecialchars($laporan['lokasi'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Durasi (menit)</label>
                            <input type="number" class="form-control" name="durasi_menit" min="0"
                                placeholder="Contoh: 60"
                                value="<?= htmlspecialchars($laporan['durasi_menit'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Foto Bukti Kegiatan</label>
                        <?php if ($isEdit && !empty($laporan['foto'])): ?>
                            <div class="mb-2">
                                <img src="<?= APP_URL . htmlspecialchars($laporan['foto']) ?>" alt="Foto" class="img-thumbnail" style="max-width: 200px;">
                                <p class="small text-muted">Foto saat ini. Upload baru untuk mengganti.</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="foto" accept="image/jpeg,image/png" id="fotoInput">
                        <div class="form-text">Format: JPG/PNG, Maksimal 2MB</div>

                        <!-- Preview -->
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="<?= APP_URL ?>/hafiz/laporan" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Kirim Laporan' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bi bi-info-circle me-2"></i>Panduan
            </div>
            <div class="card-body">
                <h6>Jenis Kegiatan:</h6>
                <ul class="small">
                    <li><strong>Mengajar</strong> - Kegiatan mengajar Al-Quran</li>
                    <li><strong>Murojah</strong> - Mengulang hafalan</li>
                    <li><strong>Khataman</strong> - Menyelesaikan bacaan Al-Quran</li>
                    <li><strong>Lainnya</strong> - Kegiatan keagamaan lainnya</li>
                </ul>

                <h6 class="mt-3">Tips:</h6>
                <ul class="small">
                    <li>Upload foto kegiatan untuk bukti yang lebih kuat</li>
                    <li>Deskripsi yang jelas memudahkan proses verifikasi</li>
                    <li>Laporan dapat diedit selama belum diverifikasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Image preview
    document.getElementById('fotoInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];

        if (file) {
            // Validate size (2MB)
            if (file.size > 2097152) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                e.target.value = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
</script>