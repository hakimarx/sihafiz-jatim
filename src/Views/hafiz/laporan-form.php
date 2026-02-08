<div class="page-header">
    <h4 class="mb-0">
        <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?> me-2"></i>
        <?= $isEdit ? 'Edit' : 'Input' ?> Laporan Harian
    </h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= APP_URL ?>/hafiz/laporan<?= $isEdit ? '/' . $laporan['id'] . '/update' : '' ?>" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <!-- 1. Foto Bukti di Atas -->
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label class="form-label fw-bold"><i class="bi bi-camera me-1"></i> Foto Bukti Kegiatan (Upload Terlebih Dahulu)</label>
                        <?php if ($isEdit && !empty($laporan['foto'])): ?>
                            <div class="mb-2">
                                <img src="<?= APP_URL . htmlspecialchars($laporan['foto']) ?>" alt="Foto" class="img-thumbnail" style="max-width: 200px;">
                                <p class="small text-muted">Foto saat ini. Upload baru untuk mengganti.</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="foto" accept="image/jpeg,image/png" id="fotoInput">
                        <div class="form-text">Format: JPG/PNG. <strong>Sistem akan otomatis mengisi tanggal & lokasi jika tersedia di foto.</strong></div>

                        <!-- Preview & Detection Status -->
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            <div id="detectionStatus" class="small mt-1"></div>
                        </div>
                    </div>

                    <!-- 2. Tanggal & Jenis -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" id="tanggalInput" required
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

                    <!-- 3. Deskripsi -->
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="4" required
                            placeholder="Jelaskan kegiatan yang dilakukan..."><?= htmlspecialchars($laporan['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <!-- 4. Lokasi -->
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Lokasi</label>
                            <input type="text" class="form-control" name="lokasi" id="lokasiInput"
                                placeholder="Pilih foto untuk mengisi otomatis, atau ketik manual..."
                                value="<?= htmlspecialchars($laporan['lokasi'] ?? '') ?>">
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
        <div class="card shadow-sm">
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

<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script>
    // Image preview and EXIF detection
    document.getElementById('fotoInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        const status = document.getElementById('detectionStatus');

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

                // Status detection
                status.className = 'small mt-1 text-primary';
                status.textContent = 'Mendeteksi data foto...';

                EXIF.getData(file, function() {
                    let detectedDate = EXIF.getTag(this, "DateTimeOriginal");
                    let lat = EXIF.getTag(this, "GPSLatitude");
                    let lon = EXIF.getTag(this, "GPSLongitude");
                    let latRef = EXIF.getTag(this, "GPSLatitudeRef") || "N";
                    let lonRef = EXIF.getTag(this, "GPSLongitudeRef") || "E";

                    let results = [];

                    if (detectedDate) {
                        // Format: YYYY:MM:DD HH:MM:SS -> YYYY-MM-DD
                        let datePart = detectedDate.split(' ')[0].replace(/:/g, '-');
                        document.getElementById('tanggalInput').value = datePart;
                        results.push('Tanggal');
                    }

                    if (lat && lon) {
                        let latitude = lat[0] + lat[1] / 60 + lat[2] / 3600;
                        let longitude = lon[0] + lon[1] / 60 + lon[2] / 3600;
                        if (latRef === 'S') latitude = -latitude;
                        if (lonRef === 'W') longitude = -longitude;

                        document.getElementById('lokasiInput').value = latitude.toFixed(6) + ', ' + longitude.toFixed(6);
                        results.push('Lokasi (GPS)');
                    }

                    if (results.length > 0) {
                        status.className = 'small mt-1 text-success fw-bold';
                        status.textContent = '✓ Berhasil mendeteksi: ' + results.join(' & ');
                    } else {
                        status.className = 'small mt-1 text-muted';
                        status.textContent = 'Data lokasi/tanggal tidak ditemukan di foto.';
                    }
                });
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
</script>