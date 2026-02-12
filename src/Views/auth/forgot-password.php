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
                    <p class="text-white-50 opacity-75">Pemulihan Akses Akun</p>
                </div>

                <!-- Glassmorphism Card -->
                <div class="card login-card border-0 shadow-lg overflow-hidden">
                    <div class="card-body p-4 p-md-5">

                        <h4 class="fw-bold text-dark mb-3 text-center">Lupa Password?</h4>
                        <p class="text-muted text-center small mb-4">Masukkan email Anda untuk menerima instruksi pengaturan ulang password.</p>

                        <!-- Flash Message -->
                        <?php $flash = getFlash(); ?>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'info' ? 'primary' : $flash['type']) ?> border-0 shadow-sm mb-4 d-flex align-items-center gap-2 py-3 rounded-3" role="alert">
                                <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill' ?> fs-5"></i>
                                <div class="small fw-medium"><?= $flash['message'] ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Form -->
                        <form action="<?= APP_URL ?>/forgot-password" method="POST" class="login-form">
                            <?= csrfField() ?>

                            <div class="mb-4">
                                <label for="email" class="form-label small fw-bold text-secondary text-uppercase">Email Terdaftar</label>
                                <div class="input-group login-input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-envelope text-success"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email"
                                        placeholder="nama@email.com" required autofocus>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold login-btn mb-4">
                                KIRIM INSTRUKSI <i class="bi bi-send ms-2"></i>
                            </button>

                            <div class="text-center">
                                <a href="<?= APP_URL ?>/login" class="text-success fw-bold text-decoration-none small">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-5 text-white-50 small">
                    <p class="mb-0 opacity-75">&copy; <?= date('Y') ?> LPTQ Provinsi Jawa Timur</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reuse login styles */
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
</style>