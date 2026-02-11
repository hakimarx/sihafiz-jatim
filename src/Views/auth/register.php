<div class="min-vh-100 d-flex align-items-center justify-content-center py-5"
    style="background: linear-gradient(135deg, #0f5132 0%, #198754 100%); position: relative; overflow: hidden;">

    <!-- Background Decoration -->
    <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

    <div class="container position-relative" style="z-index: 1;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <?php
                $sso = $_SESSION['sso_register'] ?? null;
                $activeTab = $sso ? 'baru' : 'nik';
                ?>

                <!-- Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <!-- Card Header -->
                    <div class="card-header bg-white text-center pt-4 pb-0 border-0">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle p-3 mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-check text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Registrasi Hafiz</h3>
                        <p class="text-muted small mb-3">Pilih cara pendaftaran</p>
                    </div>

                    <div class="card-body p-4 p-md-5 pt-2">
                        <!-- Flash Message -->
                        <?php $flash = getFlash(); ?>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'info' ? 'primary' : $flash['type']) ?> d-flex align-items-start mb-4 rounded-3 shadow-sm border-0" role="alert">
                                <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill' ?> me-2 fs-5 mt-1"></i>
                                <div><?= $flash['message'] ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Tab Navigation -->
                        <ul class="nav nav-pills nav-fill mb-4 gap-2" id="registerTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill d-flex align-items-center justify-content-center gap-2 <?= $activeTab === 'nik' ? 'active' : '' ?>"
                                    id="nik-tab" data-bs-toggle="pill" data-bs-target="#nik-panel"
                                    type="button" role="tab" aria-controls="nik-panel" aria-selected="<?= $activeTab === 'nik' ? 'true' : 'false' ?>">
                                    <i class="bi bi-card-heading"></i>
                                    <span>Cari NIK</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill d-flex align-items-center justify-content-center gap-2"
                                    id="nama-tab" data-bs-toggle="pill" data-bs-target="#nama-panel"
                                    type="button" role="tab" aria-controls="nama-panel" aria-selected="false">
                                    <i class="bi bi-person-badge"></i>
                                    <span>Cari Nama</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill d-flex align-items-center justify-content-center gap-2 <?= $activeTab === 'baru' ? 'active' : '' ?>"
                                    id="baru-tab" data-bs-toggle="pill" data-bs-target="#baru-panel"
                                    type="button" role="tab" aria-controls="baru-panel" aria-selected="<?= $activeTab === 'baru' ? 'true' : 'false' ?>">
                                    <i class="bi bi-person-plus"></i>
                                    <span>Daftar Baru</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="registerTabContent">
                            <!-- TAB 1: CARI NIK -->
                            <div class="tab-pane fade <?= $activeTab === 'nik' ? 'show active' : '' ?>" id="nik-panel" role="tabpanel" aria-labelledby="nik-tab">
                                <!-- Info Box -->
                                <div class="alert alert-info border-0 bg-info bg-opacity-10 rounded-3 mb-4" role="alert">
                                    <i class="bi bi-info-circle-fill text-info me-2"></i>
                                    <small>
                                        <strong>Langkah 1 dari 3:</strong> Masukkan NIK KTP Anda (16 digit).
                                        Hanya hafiz yang <u>sudah terdaftar</u> yang dapat mengklaim akun.
                                    </small>
                                </div>

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

                            <!-- TAB 2: CARI NAMA -->
                            <div class="tab-pane fade" id="nama-panel" role="tabpanel" aria-labelledby="nama-tab">
                                <!-- Info Box -->
                                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 rounded-3 mb-4" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                    <small>
                                        <strong>Pendaftaran Alternatif:</strong> Gunakan pencarian ini jika NIK Anda
                                        <u>tidak terdaftar</u> atau <u>tidak sesuai format 16 digit</u>.
                                    </small>
                                </div>

                                <form action="<?= APP_URL ?>/register/check-nama" method="POST" class="needs-validation" novalidate>
                                    <?= csrfField() ?>

                                    <!-- Nama -->
                                    <div class="mb-4">
                                        <label for="nama_cari" class="form-label fw-semibold text-secondary small text-uppercase">Nama Lengkap</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control bg-light border-start-0" id="nama_cari" name="nama_cari"
                                                placeholder="Nama sesuai data terdaftar" required minlength="3"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <!-- Kabupaten/Kota -->
                                    <div class="mb-4">
                                        <label for="kabko_id" class="form-label fw-semibold text-secondary small text-uppercase">Kabupaten/Kota</label>
                                        <select class="form-select form-select-lg bg-light" id="kabko_id" name="kabko_id" required>
                                            <option value="">-- Pilih Kabupaten/Kota --</option>
                                            <?php if (!empty($kabko_list)): ?>
                                                <?php foreach ($kabko_list as $kk): ?>
                                                    <option value="<?= $kk['id'] ?>"><?= htmlspecialchars($kk['nama']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
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
                                    <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-3 shadow-sm transition-hover fs-5 text-dark">
                                        CARI DATA SAYA <i class="bi bi-search ms-2"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- TAB 3: DAFTAR BARU -->
                            <div class="tab-pane fade <?= $activeTab === 'baru' ? 'show active' : '' ?>" id="baru-panel" role="tabpanel" aria-labelledby="baru-tab">
                                <div class="text-center mb-4">
                                    <?php if ($sso): ?>
                                        <div class="alert alert-primary border-0 bg-primary bg-opacity-10 d-flex align-items-center gap-2 mb-3">
                                            <?php if ($sso['foto']): ?>
                                                <img src="<?= $sso['foto'] ?>" class="rounded-circle" width="32" height="32">
                                            <?php else: ?>
                                                <i class="bi bi-person-circle fs-4"></i>
                                            <?php endif; ?>
                                            <div class="text-start">
                                                <div class="fw-bold small">Mendaftar via SSO <?= ucfirst($sso['type']) ?></div>
                                                <div class="small opacity-75"><?= $sso['email'] ?></div>
                                            </div>
                                            <a href="<?= APP_URL ?>/register" class="ms-auto btn btn-link btn-sm text-decoration-none p-0">Batal</a>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Data Anda belum terdaftar? Daftar baru disini atau gunakan akun sosial media Anda.</p>

                                        <div class="d-grid gap-2 mb-4">
                                            <a href="<?= APP_URL ?>/login/google" class="btn btn-outline-danger py-2 rounded-3 d-flex align-items-center justify-content-center gap-2">
                                                <i class="bi bi-google"></i> Daftar dengan Google
                                            </a>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <a href="#" class="btn btn-outline-primary py-2 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2 disabled">
                                                        <i class="bi bi-facebook"></i> Facebook
                                                    </a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="#" class="btn btn-outline-dark py-2 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2 disabled">
                                                        <i class="bi bi-instagram"></i> Instagram
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-4">
                                            <hr class="flex-grow-1">
                                            <span class="px-3 text-muted small fw-bold">ATAU</span>
                                            <hr class="flex-grow-1">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <form action="<?= APP_URL ?>/register/fresh" method="POST" class="needs-validation" novalidate>
                                    <?= csrfField() ?>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary text-uppercase">Nama Lengkap</label>
                                        <input type="text" class="form-control" name="nama" required placeholder="Nama sesuai KTP" value="<?= htmlspecialchars($sso['nama'] ?? '') ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary text-uppercase">NIK (16 Digit)</label>
                                        <input type="text" class="form-control" name="nik" required maxlength="16" pattern="[0-9]{16}" placeholder="16 digit angka">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary text-uppercase">Kabupaten/Kota</label>
                                        <select class="form-select" name="kabko_id" required>
                                            <option value="">-- Pilih Wilayah --</option>
                                            <?php if (!empty($kabko_list)): ?>
                                                <?php foreach ($kabko_list as $kk): ?>
                                                    <option value="<?= $kk['id'] ?>"><?= htmlspecialchars($kk['nama']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-secondary text-uppercase">Nomor HP / WhatsApp</label>
                                        <input type="tel" class="form-control" name="telepon" required placeholder="Contoh: 08123456789">
                                    </div>

                                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-3 shadow-sm transition-hover fs-5">
                                        <?= $sso ? 'SELESAIKAN PENDAFTARAN' : 'DAFTAR SEKARANG' ?> <i class="bi <?= $sso ? 'bi-check-circle' : 'bi-person-plus' ?> ms-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
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

    .nav-pills .nav-link {
        color: #6c757d;
        font-weight: 600;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
        background-color: #198754;
        color: white;
        border-color: #198754;
    }

    .nav-pills .nav-link:not(.active):hover {
        background-color: #f8f9fa !important;
        border-color: #198754;
        color: #198754;
    }
</style>