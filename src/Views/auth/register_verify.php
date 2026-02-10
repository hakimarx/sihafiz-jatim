<div class="min-vh-100 d-flex align-items-center justify-content-center py-5"
    style="background: linear-gradient(135deg, #0f5132 0%, #198754 100%); position: relative; overflow: hidden;">

    <!-- Background Decoration -->
    <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

    <div class="container position-relative" style="z-index: 1;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <!-- Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <!-- Card Header -->
                    <div class="card-header bg-white text-center pt-4 pb-0 border-0">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle p-3 mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-person-check-fill text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Verifikasi Identitas</h3>
                        <p class="text-muted small">Langkah 2 dari 3</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <!-- Data Found -->
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 rounded-3 mb-4" role="alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                <strong>Data ditemukan!</strong>
                            </div>
                            <div class="ms-4">
                                <p class="mb-1"><strong>Nama:</strong> <?= htmlspecialchars($nama_samaran) ?></p>
                                <?php if (!empty($kabupaten)): ?>
                                <p class="mb-0"><strong>Wilayah:</strong> <?= htmlspecialchars($kabupaten) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Flash Message -->
                        <?php $flash = getFlash(); ?>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> d-flex align-items-start mb-4 rounded-3 shadow-sm border-0" role="alert">
                                <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> me-2 fs-5 mt-1"></i>
                                <div><?= $flash['message'] ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 rounded-3 mb-4" role="alert">
                            <i class="bi bi-info-circle text-warning me-1"></i>
                            <small>Untuk membuktikan bahwa Anda adalah pemilik NIK tersebut, silakan masukkan <strong>tanggal lahir</strong> sesuai KTP, lalu buat password baru.</small>
                        </div>

                        <form action="<?= APP_URL ?>/register/verify" method="POST" class="needs-validation" novalidate>
                            <?= csrfField() ?>

                            <div class="row g-3">
                                <!-- Tanggal Lahir -->
                                <div class="col-12">
                                    <label for="tanggal_lahir" class="form-label fw-semibold text-secondary small text-uppercase">
                                        <i class="bi bi-calendar-date me-1"></i>Tanggal Lahir (sesuai KTP)
                                    </label>
                                    <input type="date" class="form-control form-control-lg bg-light" id="tanggal_lahir" name="tanggal_lahir" required>
                                    <div class="form-text small">Harus sama persis dengan data di KTP Anda.</div>
                                </div>

                                <hr class="my-2">

                                <!-- No HP -->
                                <div class="col-12">
                                    <label for="telepon" class="form-label fw-semibold text-secondary small text-uppercase">
                                        <i class="bi bi-whatsapp me-1"></i>Nomor WhatsApp Aktif
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0 text-muted">+62</span>
                                        <input type="tel" class="form-control bg-light border-start-0" id="telepon" name="telepon" 
                                               placeholder="8xxxxxxxxxx" required pattern="[0-9]+" minlength="10"
                                               value="<?= htmlspecialchars($old_telepon ?? '') ?>"
                                               inputmode="numeric">
                                    </div>
                                    <div class="form-text small">Akan digunakan sebagai <strong>username</strong> untuk login.</div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold text-secondary small text-uppercase">
                                        <i class="bi bi-lock me-1"></i>Password Baru
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control bg-light" id="password" name="password" 
                                               placeholder="Min. 6 karakter" required minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirm" class="form-label fw-semibold text-secondary small text-uppercase">
                                        <i class="bi bi-lock-fill me-1"></i>Konfirmasi Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control bg-light" id="password_confirm" name="password_confirm" 
                                               placeholder="Ulangi password" required minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirm', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-3 shadow-sm transition-hover fs-5">
                                        KLAIM AKUN SAYA <i class="bi bi-check-circle ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-light p-4 text-center border-top-0">
                        <p class="text-muted mb-0 small">
                            <a href="<?= APP_URL ?>/register" class="text-secondary text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali ke langkah awal</a>
                            &nbsp;|&nbsp;
                            <a href="<?= APP_URL ?>/login" class="text-success fw-bold text-decoration-none">Sudah punya akun? Login</a>
                        </p>
                    </div>
                </div>

                <div class="text-center mt-4 text-white-50 small">
                    <p>&copy; <?= date('Y') ?> LPTQ Provinsi Jawa Timur. <br>Sistem Informasi Hafiz (SiHafiz).</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover {
        transition: all 0.3s ease;
    }

    .transition-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        background-color: #fff !important;
    }
</style>

<script>
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
