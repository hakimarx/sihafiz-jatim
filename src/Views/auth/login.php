<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .login-bg {
            background-image: url('https://images.unsplash.com/photo-1584281723351-9dec8275f297?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="login-bg min-h-screen flex items-center justify-center p-4 relative">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-green-900/60 backdrop-blur-[2px]"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- Brand -->
        <div class="text-center mb-10">
            <div class="inline-block p-4 rounded-3xl bg-white shadow-2xl mb-4 transform hover:scale-105 transition-all">
                <?php
                $logoHome = Setting::get('app_logo_home');
                $logoUrl = $logoHome ? APP_URL . $logoHome : APP_URL . '/assets/img/logo-lptq.png';
                ?>
                <img src="<?= $logoUrl ?>" alt="Logo" class="h-20 w-auto">
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight"><?= htmlspecialchars(Setting::get('app_name', APP_NAME)) ?></h1>
            <p class="text-green-100 opacity-80 mt-2 font-light">Sistem Pelaporan Tahfidz Jawa Timur</p>
        </div>

        <!-- Login Card -->
        <div class="glass rounded-[2rem] shadow-2xl overflow-hidden border border-white/30">
            <div class="p-8 md:p-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                    <span class="w-1.5 h-8 bg-green-600 rounded-full"></span>
                    Selamat Datang
                </h2>

                <!-- Flash Message -->
                <?php $flash = getFlash(); ?>
                <?php if ($flash): ?>
                    <div class="mb-8 p-4 rounded-2xl <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-green-50 text-green-700 border border-green-100' ?> flex items-start gap-3 animate-pulse">
                        <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> mt-0.5"></i>
                        <div class="text-sm font-medium"><?= $flash['message'] ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/login" method="POST" class="space-y-6">
                    <?= csrfField() ?>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest pl-1">ID Pengguna</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-green-600 transition-colors">
                                <i class="bi bi-person-badge"></i>
                            </span>
                            <input type="text" name="username" required
                                class="w-full pl-11 pr-4 py-4 bg-white/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all outline-none font-medium"
                                placeholder="NIK atau No. HP">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest pl-1">Kata Sandi</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-green-600 transition-colors">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-11 pr-12 py-4 bg-white/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all outline-none font-medium"
                                placeholder="Masukkan password...">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        <div class="flex justify-end">
                            <a href="<?= APP_URL ?>/forgot-password" class="text-xs font-bold text-green-700 hover:text-green-600 transition-colors">Lupa Password?</a>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 rounded-2xl border border-gray-100">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Verifikasi Keamanan</label>
                        <div class="flex items-center gap-4">
                            <span class="text-xl font-bold text-gray-700 tracking-widest"><?= $captcha['question'] ?> =</span>
                            <input type="number" name="captcha" required
                                class="w-20 px-3 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-center font-bold text-lg"
                                placeholder="?">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-green-900/30 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-3">
                        MASUK SEKARANG <i class="bi bi-box-arrow-in-right text-xl"></i>
                    </button>
                </form>

                <div class="mt-6">
                    <a href="<?= APP_URL ?>/login/google" class="w-full bg-white border border-gray-200 text-gray-700 font-bold py-4 rounded-2xl shadow-sm hover:bg-gray-50 hover:shadow-md transition-all flex items-center justify-center gap-3">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-6 h-6" alt="Google">
                        Masuk dengan Google
                    </a>
                </div>

                <div class="mt-8 text-center space-y-4">
                    <div class="flex items-center gap-4">
                        <span class="h-px bg-gray-100 flex-grow"></span>
                        <span class="text-xs font-bold text-gray-400 uppercase">Hafiz Baru?</span>
                        <span class="h-px bg-gray-100 flex-grow"></span>
                    </div>

                    <a href="<?= APP_URL ?>/register" class="inline-block w-full py-4 rounded-2xl border-2 border-green-600 text-green-600 font-bold hover:bg-green-50 transition-colors">
                        AKTIVASI AKUN ANDA
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center mt-10 text-white/40 text-sm font-medium uppercase tracking-widest">
            &copy; <?= date('Y') ?> LPTQ PROVINSI JAWA TIMUR
        </p>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                pwd.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>

</html>