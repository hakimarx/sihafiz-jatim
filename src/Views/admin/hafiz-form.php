<div class="page-header">
    <h4 class="mb-0">
        <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?> me-2"></i>
        <?= $isEdit ? 'Edit' : 'Tambah' ?> Data Hafiz
    </h4>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= APP_URL ?>/admin/hafiz<?= $isEdit ? '/' . $hafiz['id'] . '/update' : '' ?>" method="POST">
            <?= csrfField() ?>

            <!-- Data Pribadi -->
            <h6 class="text-muted mb-3 border-bottom pb-2"><i class="bi bi-person me-2"></i>Data Pribadi</h6>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nik" maxlength="16" pattern="[0-9]{16}"
                        value="<?= htmlspecialchars($hafiz['nik'] ?? '') ?>"
                        <?= $isEdit ? 'readonly' : 'required' ?>>
                    <div class="form-text">16 digit angka</div>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" required
                        value="<?= htmlspecialchars($hafiz['nama'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="tempat_lahir" required
                        value="<?= htmlspecialchars($hafiz['tempat_lahir'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="tanggal_lahir" required
                        value="<?= htmlspecialchars($hafiz['tanggal_lahir'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-select" name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" <?= ($hafiz['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($hafiz['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Alamat -->
            <h6 class="text-muted mb-3 border-bottom pb-2"><i class="bi bi-geo-alt me-2"></i>Alamat</h6>
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="alamat" rows="2" required><?= htmlspecialchars($hafiz['alamat'] ?? '') ?></textarea>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">RT</label>
                    <input type="text" class="form-control" name="rt" maxlength="5"
                        value="<?= htmlspecialchars($hafiz['rt'] ?? '') ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">RW</label>
                    <input type="text" class="form-control" name="rw" maxlength="5"
                        value="<?= htmlspecialchars($hafiz['rw'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="desa_kelurahan" required
                        value="<?= htmlspecialchars($hafiz['desa_kelurahan'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="kecamatan" required
                        value="<?= htmlspecialchars($hafiz['kecamatan'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                    <select class="form-select" name="kabupaten_kota_id" required <?= hasRole(ROLE_ADMIN_KABKO) ? 'disabled' : '' ?>>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($kabkoList as $id => $nama): ?>
                            <option value="<?= $id ?>" <?= ($defaultKabko ?? '') == $id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nama) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (hasRole(ROLE_ADMIN_KABKO)): ?>
                        <input type="hidden" name="kabupaten_kota_id" value="<?= $defaultKabko ?>">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kontak -->
            <h6 class="text-muted mb-3 border-bottom pb-2"><i class="bi bi-telephone me-2"></i>Kontak</h6>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon/HP</label>
                    <input type="tel" class="form-control" name="telepon"
                        value="<?= htmlspecialchars($hafiz['telepon'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email"
                        value="<?= htmlspecialchars($hafiz['email'] ?? '') ?>">
                </div>
            </div>

            <!-- Data Hafalan -->
            <h6 class="text-muted mb-3 border-bottom pb-2"><i class="bi bi-book me-2"></i>Data Hafalan & Mengajar</h6>
            <div class="row mb-3">
                <div class="col-md-5 mb-3">
                    <label class="form-label">Lembaga yang Mengeluarkan Sertifikat Tahfiz</label>
                    <input type="text" class="form-control" name="sertifikat_tahfidz" placeholder="Contoh: PP. Al-Amin"
                        value="<?= htmlspecialchars($hafiz['sertifikat_tahfidz'] ?? '') ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Mengajar?</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="mengajar" id="mengajar" value="1"
                            <?= ($hafiz['mengajar'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="mengajar">Ya</label>
                    </div>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Tempat Mengajar Utama</label>
                    <div class="input-group">
                        <input type="text" class="form-control mb-2" name="tempat_mengajar" placeholder="Tempat"
                            value="<?= htmlspecialchars($hafiz['tempat_mengajar'] ?? '') ?>">
                        <input type="date" class="form-control mb-2" name="tmt_mengajar"
                            value="<?= htmlspecialchars($hafiz['tmt_mengajar'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Tempat Mengajar Lainnya -->
            <div id="mengajar-container" class="mb-4">
                <label class="form-label d-flex justify-content-between">
                    Tempat Mengajar Lainnya
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-mengajar">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Tempat
                    </button>
                </label>

                <?php if (!empty($mengajarList)): ?>
                    <?php foreach ($mengajarList as $i => $item): ?>
                        <div class="row mengajar-row mb-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="mengajar_tempat[]" placeholder="Nama Tempat Mengajar" value="<?= htmlspecialchars($item['tempat_mengajar']) ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="date" class="form-control" name="mengajar_tmt[]" value="<?= htmlspecialchars($item['tmt_mengajar']) ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger w-100 remove-mengajar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <script>
                document.getElementById('add-mengajar').addEventListener('click', function() {
                    const container = document.getElementById('mengajar-container');
                    const row = document.createElement('div');
                    row.className = 'row mengajar-row mb-2';
                    row.innerHTML = `
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="mengajar_tempat[]" placeholder="Nama Tempat Mengajar">
                    </div>
                    <div class="col-md-4">
                        <input type="date" class="form-control" name="mengajar_tmt[]">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger w-100 remove-mengajar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                    container.appendChild(row);
                });

                document.getElementById('mengajar-container').addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-mengajar') || e.target.parentElement.classList.contains('remove-mengajar')) {
                        const row = e.target.closest('.mengajar-row');
                        row.remove();
                    }
                });
            </script>

            <?php if ($isEdit): ?>
                <!-- Data Bank (hanya saat edit) -->
                <h6 class="text-muted mb-3 border-bottom pb-2"><i class="bi bi-bank me-2"></i>Data Bank</h6>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" class="form-control" name="nama_bank"
                            value="<?= htmlspecialchars($hafiz['nama_bank'] ?? 'Bank Jatim') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" name="nomor_rekening"
                            value="<?= htmlspecialchars($hafiz['nomor_rekening'] ?? '') ?>">
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="2"><?= htmlspecialchars($hafiz['keterangan'] ?? '') ?></textarea>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Buttons -->
            <div class="d-flex justify-content-between pt-3 border-top">
                <a href="<?= APP_URL ?>/admin/hafiz" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Data' ?>
                </button>
            </div>
        </form>
    </div>
</div>