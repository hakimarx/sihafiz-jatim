<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-gear me-2"></i>Pengaturan Aplikasi</h4>
</div>

<div class="row">
    <div class="col-md-8">
        <form action="<?= APP_URL ?>/admin/settings" method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>

            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-primary"><i class="bi bi-display me-2"></i>Identitas Visual</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Logo Dashboard</label>
                            <div class="mb-3">
                                <?php if (!empty($settings['app_logo'])): ?>
                                    <img src="<?= APP_URL . $settings['app_logo'] ?>" alt="Logo" class="img-thumbnail mb-2" style="max-height: 100px;">
                                <?php else: ?>
                                    <div class="bg-light p-4 text-center border rounded mb-2">
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                        <p class="small text-muted mb-0">Belum ada logo</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <small class="text-muted">Rekomendasi ukuran: 200x200px (PNG Transparan).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Favicon</label>
                            <div class="mb-3">
                                <?php if (!empty($settings['app_favicon'])): ?>
                                    <img src="<?= APP_URL . $settings['app_favicon'] ?>" alt="Favicon" class="img-thumbnail mb-2" style="max-height: 50px;">
                                <?php else: ?>
                                    <div class="bg-light p-3 text-center border rounded mb-2">
                                        <i class="bi bi-app-indicator fs-2 text-muted"></i>
                                        <p class="small text-muted mb-0">Belum ada favicon</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" class="form-control" name="favicon" accept="image/x-icon,image/png">
                            <small class="text-muted">Ukuran: 32x32px atau 64x64px.</small>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-house-door me-1"></i>Logo Halaman Utama (Login)</label>
                            <div class="mb-3">
                                <?php if (!empty($settings['app_logo_home'])): ?>
                                    <img src="<?= APP_URL . $settings['app_logo_home'] ?>" alt="Logo Home" class="img-thumbnail mb-2" style="max-height: 100px;">
                                <?php else: ?>
                                    <div class="bg-light p-4 text-center border rounded mb-2">
                                        <i class="bi bi-house-door fs-1 text-muted"></i>
                                        <p class="small text-muted mb-0">Belum ada logo halaman utama</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" class="form-control" name="logo_home" accept="image/*">
                            <small class="text-muted">Logo yang tampil di halaman login & registrasi. Rekomendasi: 300x300px (PNG Transparan).</small>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info border-0 bg-info bg-opacity-10 mt-4">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Logo Halaman Utama</strong> ditampilkan di halaman login dan registrasi sebagai identitas utama aplikasi yang dilihat publik. 
                                    Jika tidak diatur, akan menggunakan logo default bawaan sistem.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Umum</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Aplikasi</label>
                        <input type="text" class="form-control" name="app_name" value="<?= htmlspecialchars($settings['app_name'] ?? APP_NAME) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat / Instansi</label>
                        <textarea class="form-control" name="app_address" rows="3"><?= htmlspecialchars($settings['app_address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Anggaran Aktif</label>
                        <input type="number" class="form-control" name="tahun_aktif" value="<?= htmlspecialchars($settings['tahun_aktif'] ?? TAHUN_ANGGARAN) ?>">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-4">
        <div class="card mb-4 bg-success text-white">
            <div class="card-body">
                <h6><i class="bi bi-lightning-fill me-2"></i>Tips</h6>
                <p class="small mb-0">
                    Pengaturan ini berlaku untuk seluruh sistem. Pastikan logo yang diunggah memiliki kontras yang baik dengan warna background sidebar yang gelap.
                </p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-primary"><i class="bi bi-database-fill-up me-2"></i>Migrasi Data (Excel)</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">Gunakan fitur ini untuk mengimport data lama dari file Excel yang sudah dikonversi ke format <strong>CSV (semicolon separated)</strong>.</p>

                <form action="<?= APP_URL ?>/admin/import" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih File CSV</label>
                        <input type="file" class="form-control form-control-sm" name="csv_file" accept=".csv" required>
                        <div class="form-text mt-1" style="font-size: 0.75rem;">
                            Pastikan format kolom sesuai dengan template standar (Tahun; Kabko; NIK; Nama; dst).
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-success btn-sm w-100" onclick="return confirm('Yakin ingin mengimport data? Proses ini mungkin memakan waktu beberapa saat.')">
                        <i class="bi bi-upload me-1"></i> Mulai Import Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>