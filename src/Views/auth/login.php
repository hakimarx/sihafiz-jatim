<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #198754 0%, #0d6efd 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-book-half text-success fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-success">SiHafiz Jatim</h4>
                            <p class="text-muted small">Sistem Informasi & Pelaporan Huffadz</p>
                        </div>

                        <!-- Flash Message -->
                        <?php $flash = getFlash(); ?>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> py-2">
                                <small><?= $flash['message'] ?></small>
                            </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form action="<?= APP_URL ?>/login" method="POST">
                            <?= csrfField() ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="username" name="username"
                                        placeholder="NIK / No. HP" required autofocus>
                                </div>
                                <div class="form-text">Masukkan NIK atau No. HP yang terdaftar</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password" name="password"
                                        placeholder="••••••••" required>
                                    <span class="input-group-text bg-light border-start-0" style="cursor: pointer;" onclick="togglePassword()">
                                        <i class="bi bi-eye text-muted" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                            </div>
                            <script>
                                function togglePassword() {
                                    const passwordInput = document.getElementById('password');
                                    const toggleIcon = document.getElementById('togglePasswordIcon');
                                    if (passwordInput.type === 'password') {
                                        passwordInput.type = 'text';
                                        toggleIcon.classList.remove('bi-eye');
                                        toggleIcon.classList.add('bi-eye-slash');
                                    } else {
                                        passwordInput.type = 'password';
                                        toggleIcon.classList.remove('bi-eye-slash');
                                        toggleIcon.classList.add('bi-eye');
                                    }
                                }
                            </script>

                            <div class="mb-4">
                                <label for="captcha" class="form-label">Keamanan: Berapa hasil dari <strong><?= $captcha['question'] ?></strong> ?</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-shield-check text-muted"></i>
                                    </span>
                                    <input type="number" class="form-control border-start-0 ps-0" id="captcha" name="captcha"
                                        placeholder="Jawaban Angka" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                            </button>

                            <div class="text-center my-3">
                                <span class="text-muted small">atau</span>
                            </div>

                            <a href="<?= APP_URL ?>/login/google" class="btn btn-outline-danger w-100 py-2">
                                <i class="bi bi-google me-2"></i>Masuk dengan Google
                            </a>
                        </form>

                        <div class="text-center mt-3">
                            <p class="small text-muted mb-0">Hafiz baru? <a href="<?= APP_URL ?>/register" class="text-success fw-bold text-decoration-none">Daftar Akun Baru</a></p>
                        </div>

                        <hr class="my-4">

                        <div class="text-center text-muted small">
                            <p class="mb-0">&copy; <?= date('Y') ?> LPTQ Jawa Timur</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>