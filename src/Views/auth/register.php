<div class="min-vh-100 d-flex align-items-center justify-content-center py-5"
    style="background: linear-gradient(135deg, #0f5132 0%, #198754 100%); position: relative; overflow: hidden;">

    <!-- Background Decoration -->
    <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

    <div class="container position-relative" style="z-index: 1;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">

                <!-- Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <!-- Card Header -->
                    <div class="card-header bg-white text-center pt-4 pb-0 border-0">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle p-3 mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-check text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Klaim Akun Hafiz</h3>
                        <p class="text-muted small">Masukkan NIK Anda untuk mengklaim akun</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <!-- Info Box -->
                        <div class="alert alert-info border-0 bg-info bg-opacity-10 rounded-3 mb-4" role="alert">
                            <i class="bi bi-info-circle-fill text-info me-2"></i>
                            <small>
                                <strong>Langkah 1 dari 3:</strong> Masukkan NIK KTP Anda. 
                                Hanya hafiz yang <u>sudah terdaftar dan lulus seleksi</u> yang dapat mengklaim akun.
                            </small>
                        </div>

                        <!-- Flash Message -->
                        <?php $flash = getFlash(); ?>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> d-flex align-items-start mb-4 rounded-3 shadow-sm border-0" role="alert">
                                <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> me-2 fs-5 mt-1"></i>
                                <div><?= $flash['message'] ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= APP_URL ?>/register/check-nik" method="POST" class="needs-validation" novalidate>
                            <?= csrfField() ?>

                            <!-- NIK -->
                            <div class="mb-4">
                                <label for="nik" class="form-label fw-semibold text-secondary small text-uppercase">Nomor Induk Kependudukan (NIK)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 fs-5" id="nik" name="nik" 
                                           maxlength="16" placeholder="16 digit sesuai KTP" required pattern="[0-9]{16}"
                                           inputmode="numeric" autocomplete="off"
                                           style="letter-spacing: 2px; font-family: 'Courier New', monospace;">
                                </div>
                                <div class="form-text small mt-2">
                                    <i class="bi bi-lock-fill text-muted me-1"></i>
                                    NIK Anda aman dan terenkripsi.
                                </div>
                            </div>

                            <!-- Captcha Security -->
                            <div class="mb-4">
                                <div class="p-3 bg-opacity-10 bg-success rounded-3 border border-success border-opacity-25">
                                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-shield-lock me-1"></i>VERIFIKASI KEAMANAN</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white px-3 py-2 rounded border fw-bold text-dark user-select-none fs-5">
                                            <?= $captcha['question'] ?> = ?
                                        </div>
                                        <input type="number" class="form-control" name="captcha" placeholder="Jawaban..." required style="max-width: 150px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-3 shadow-sm transition-hover fs-5">
                                CARI DATA SAYA <i class="bi bi-search ms-2"></i>
                            </button>
                        </form>
                    </div>

                    <div class="card-footer bg-light p-4 text-center border-top-0">
                        <p class="text-muted mb-0 small">
                            Sudah memiliki akun? <a href="<?= APP_URL ?>/login" class="text-success fw-bold text-decoration-none">Masuk disini</a>
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

    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px #f8f9fa inset !important;
    }
</style>