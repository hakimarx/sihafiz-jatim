<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<div class="login-wrapper min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="login-bg-pattern"></div>
    <div class="login-glow-1"></div>
    <div class="login-glow-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">

                <!-- Logo & Brand -->
                <div class="text-center mb-4 login-header">
                    <?php
                    $logoHome = Setting::get('app_logo_home');
                    $logoUrl = $logoHome ? APP_URL . $logoHome : APP_URL . '/assets/img/logo-lptq.png';
                    ?>
                    <img src="<?= $logoUrl ?>" alt="Logo LPTQ" class="img-fluid mb-3 login-logo" style="max-height: 100px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));">
                    <h2 class="fw-bold text-white mb-1"><?= htmlspecialchars(Setting::get('app_name', APP_NAME)) ?></h2>
                    <p class="text-white-50 opacity-75">Sistem Pelaporan & Data Huffadz Jawa Timur</p>
                </div>

                <!-- Glassmorphism Card -->
                <div class="card login-card border-0 shadow-lg overflow-hidden">
                    <div class="card-body p-4 p-md-5">

                        <h4 class="fw-bold text-dark mb-4 text-center">Silakan Masuk</h4>

                        <!-- Flash Message -->
                        <?php $flash = getFlash(); ?>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'info' ? 'primary' : $flash['type']) ?> border-0 shadow-sm mb-4 d-flex align-items-center gap-2 py-3 rounded-3" role="alert">
                                <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill' ?> fs-5"></i>
                                <div class="small fw-medium"><?= $flash['message'] ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Form -->
                        <form action="<?= APP_URL ?>/login" method="POST" class="login-form">
                            <?= csrfField() ?>

                            <div class="mb-4">
                                <label for="username" class="form-label small fw-bold text-secondary text-uppercase">Username</label>
                                <div class="input-group login-input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-person text-success"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="username" name="username"
                                        placeholder="NIK / No. HP" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold text-secondary text-uppercase">Password</label>
                                <div class="input-group login-input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-lock text-success"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password" name="password"
                                        placeholder="••••••••" required>
                                    <span class="input-group-text bg-transparent border-start-0" style="cursor: pointer;" onclick="togglePassword()">
                                        <i class="bi bi-eye text-muted" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="captcha-box p-3 rounded-3 border bg-light mb-4">
                                    <label class="form-label small fw-bold text-success mb-2 text-uppercase d-flex align-items-center gap-2">
                                        <i class="bi bi-shield-lock"></i> Verifikasi Keamanan
                                    </label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="captcha-question fw-bold text-dark fs-5">
                                            <?= $captcha['question'] ?> = ?
                                        </div>
                                        <input type="number" class="form-control" name="captcha" placeholder="..." required style="max-width: 100px;">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold login-btn mb-4">
                                MASUK KE AKUN <i class="bi bi-arrow-right ms-2"></i>
                            </button>

                            <div class="d-flex align-items-center mb-4">
                                <hr class="flex-grow-1 opacity-25">
                                <span class="px-3 text-muted small fw-bold">ATAU</span>
                                <hr class="flex-grow-1 opacity-25">
                            </div>

                            <div class="d-grid gap-2">
                                <a href="<?= APP_URL ?>/login/google" class="btn btn-outline-danger py-2 d-flex align-items-center justify-content-center gap-2 rounded-3 transition-hover shadow-sm">
                                    <i class="bi bi-google"></i> Lanjutkan dengan Google
                                </a>
                            </div>
                        </form>

                        <div class="text-center mt-4 pt-2">
                            <p class="small text-muted mb-0">Hafiz baru? <a href="<?= APP_URL ?>/register" class="text-success fw-bold text-decoration-none border-bottom border-success border-2">Daftar Akun Baru</a></p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5 text-white-50 small">
                    <p class="mb-0 opacity-75">&copy; <?= date('Y') ?> LPTQ Provinsi Jawa Timur</p>
                </div>
            </div>
        </div>
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

<style>
    :root {
        --primary-green: #198754;
        --secondary-gold: #c5a059;
        --dark-green: #0b4b32;
    }

    body {
        font-family: 'Inter', sans-serif;
    }

    .login-wrapper {
        background: linear-gradient(135deg, var(--dark-green) 0%, var(--primary-green) 100%);
        position: relative;
        overflow: hidden;
    }

    .login-bg-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.1;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0l30 30-30 30-30-30z' fill='%23ffffff' fill-opacity='1' fill-rule='evenodd'/%3E%3C/svg%3E");
        background-size: 60px 60px;
        z-index: 1;
    }

    .login-glow-1 {
        position: absolute;
        top: -100px;
        right: -100px;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(197, 160, 89, 0.2) 0%, transparent 70%);
        z-index: 1;
    }

    .login-glow-2 {
        position: absolute;
        bottom: -150px;
        left: -150px;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        z-index: 1;
    }

    .login-logo {
        transition: transform 0.5s ease;
    }

    .login-logo:hover {
        transform: scale(1.05) rotate(5deg);
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-input-group {
        border-bottom: 2px solid #eee;
        transition: all 0.3s ease;
    }

    .login-input-group:focus-within {
        border-color: var(--primary-green);
    }

    .login-input-group .form-control {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        padding-top: 12px;
        padding-bottom: 12px;
        font-family: 'Outfit', sans-serif;
        font-size: 1.1rem;
    }

    .login-btn {
        background: linear-gradient(to right, var(--primary-green), var(--dark-green));
        border: none;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
        background: linear-gradient(to right, var(--dark-green), var(--primary-green));
    }

    .captcha-box {
        border-left: 4px solid var(--primary-green) !important;
    }

    .transition-hover:hover {
        transform: translateY(-2px);
        background-color: #f8f9fa !important;
    }

    h2,
    h4 {
        font-family: 'Outfit', sans-serif;
    }
</style>