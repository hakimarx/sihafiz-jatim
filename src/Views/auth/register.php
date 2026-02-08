<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background: linear-gradient(135deg, #198754 0%, #0d6efd 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-person-plus text-success fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-success">Pendaftaran Hafiz Baru</h4>
                            <p class="text-muted small">Silakan lengkapi data diri Anda untuk mendaftar</p>
                        </div>

                        <!-- Flash Message -->
                        <?php $flash = getFlash(); ?>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> py-2">
                                <small><?= $flash['message'] ?></small>
                            </div>
                        <?php endif; ?>

                        <!-- Registration Form -->
                        <form action="<?= APP_URL ?>/register" method="POST">
                            <?= csrfField() ?>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap (Sesuai KTP)</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="CONTOH: AHMAD FUADI" required autofocus>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nik" class="form-label">NIK (16 Digit)</label>
                                    <input type="text" class="form-control" id="nik" name="nik" maxlength="16" placeholder="35xxxxxxxxxxxxxx" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telepon" class="form-label">Nomor WhatsApp Aktif</label>
                                    <input type="text" class="form-control" id="telepon" name="telepon" placeholder="08xxxxxxxxxx" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email (Opsional)</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com">
                            </div>

                            <div class="mb-4">
                                <label for="kabupaten_kota_id" class="form-label">Domisili (Kabupaten/Kota)</label>
                                <select class="form-select" id="kabupaten_kota_id" name="kabupaten_kota_id" required>
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                    <?php foreach ($kabkoList as $kabko): ?>
                                        <option value="<?= $kabko['id'] ?>"><?= $kabko['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="p-3 bg-light rounded mb-4 border border-success border-opacity-25">
                                <label for="captcha" class="form-label mb-2"><strong>Verifikasi Keamanan</strong></label>
                                <p class="small text-muted mb-2">Berapa hasil dari <strong><?= $captcha['question'] ?></strong> ?</p>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-shield-check text-success"></i>
                                    </span>
                                    <input type="number" class="form-control border-start-0 ps-0" id="captcha" name="captcha"
                                        placeholder="Jawaban Angka" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2 mb-3">
                                <i class="bi bi-check-circle me-2"></i>Daftar Sekarang
                            </button>

                            <div class="text-center">
                                <p class="small text-muted mb-0">Sudah punya akun? <a href="<?= APP_URL ?>/login" class="text-success fw-bold text-decoration-none">Masuk di sini</a></p>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4 text-white-50 small">
                    <p>&copy; <?= date('Y') ?> LPTQ Jawa Timur. Semua Hak Dilindungi.</p>
                </div>
            </div>
        </div>
    </div>
</div>