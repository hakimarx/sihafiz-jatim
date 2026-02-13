<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46', // Primary Religious Color
                            900: '#064e3b',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#d4af37', // Gold Accent
                            600: '#b4942b',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        arabic: ['Amiri', 'serif'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons (Keep for consistency) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #064e3b;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4af37' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .font-arabic {
            font-family: 'Amiri', serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translatey(0px); }
            50% { transform: translatey(-10px); }
            100% { transform: translatey(0px); }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-emerald-800 to-transparent opacity-50"></div>
    <div class="absolute -top-20 -right-20 w-96 h-96 bg-gold-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>
    <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>

    <div class="max-w-md w-full relative z-10 floating">
        <!-- Brand -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 rounded-full bg-white/10 backdrop-blur-md mb-4 shadow-xl border border-white/10">
                <?php
                $logoHome = Setting::get('app_logo_home');
                $logoUrl = $logoHome ? APP_URL . $logoHome : APP_URL . '/assets/img/logo-lptq.png';
                ?>
                <!-- Placeholder Icon if Image fails/missing, handled by onerror or just logic -->
                <img src="<?= $logoUrl ?>" alt="Logo" class="h-16 w-auto drop-shadow-lg" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                <i class="bi bi-book-half text-4xl text-white" style="display:none"></i>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight font-arabic"><?= htmlspecialchars(Setting::get('app_name', APP_NAME)) ?></h1>
            <p class="text-emerald-100/80 mt-2 text-lg font-light">Sistem Informasi & Pelaporan Tahfidz</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-3xl overflow-hidden relative">
            <!-- Decorative Top Border -->
            <div class="h-2 w-full bg-gradient-to-r from-emerald-600 via-gold-500 to-emerald-600"></div>

            <div class="p-8 md:p-10">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 font-arabic">Assalamu'alaikum</h2>
                    <p class="text-gray-500 text-sm mt-1">Silakan masuk untuk melanjutkan aktivitas Anda.</p>
                </div>

                <!-- Flash Message -->
                <?php $flash = getFlash(); ?>
                <?php if ($flash): ?>
                    <div class="mb-6 p-4 rounded-xl <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-800 border-l-4 border-red-500' : 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-500' ?> flex items-start gap-3 shadow-sm">
                        <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-octagon-fill' : 'bi-check-circle-fill' ?> mt-0.5 text-lg"></i>
                        <div class="text-sm font-medium leading-relaxed"><?= $flash['message'] ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/login" method="POST" class="space-y-6">
                    <?= csrfField() ?>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 ml-1">ID Pengguna / NIK</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-emerald-600 transition-colors">
                                <i class="bi bi-person-vcard text-xl"></i>
                            </span>
                            <input type="text" name="username" required
                                class="w-full pl-12 pr-4 py-4 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none font-medium text-gray-800 placeholder-gray-400"
                                placeholder="Masukkan NIK atau No. HP">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label class="block text-sm font-semibold text-gray-700">Kata Sandi</label>
                            <a href="<?= APP_URL ?>/forgot-password" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Lupa Sandi?</a>
                        </div>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-emerald-600 transition-colors">
                                <i class="bi bi-lock text-xl"></i>
                            </span>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-12 pr-12 py-4 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none font-medium text-gray-800 placeholder-gray-400"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-emerald-600 transition-colors cursor-pointer">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha -->
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 flex items-center justify-between gap-3">
                         <div class="flex items-center gap-2 px-3">
                             <i class="bi bi-shield-check text-emerald-600 text-lg"></i>
                             <span class="font-bold text-gray-600 tracking-widest text-lg"><?= $captcha['question'] ?> = </span>
                         </div>
                         <input type="number" name="captcha" required
                                class="w-20 px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-center font-bold text-lg"
                                placeholder="?">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-900/20 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 group">
                        <span class="tracking-wide text-lg">MASUK</span>
                        <i class="bi bi-arrow-right-circle-fill text-xl group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <div class="mt-8 relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-medium">Metode Lain</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3">
                    <a href="<?= APP_URL ?>/login/google" class="w-full bg-white border border-gray-200 text-gray-700 font-bold py-3.5 rounded-xl shadow-sm hover:shadow-md hover:bg-gray-50 transition-all flex items-center justify-center gap-3">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-6 h-6" alt="Google">
                        <span>Masuk dengan Google</span>
                    </a>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Belum punya akun? 
                        <a href="<?= APP_URL ?>/register" class="text-emerald-700 font-bold hover:underline decoration-2 underline-offset-4 decoration-gold-500">
                            Daftar Sekarang
                        </a>
                    </p>
                </div>
            </div>
            
            <!-- Bottom Pattern -->
            <div class="h-3 w-full bg-repeating-linear-gradient(45deg, #065f46, #065f46 10px, #047857 10px, #047857 20px)"></div>
        </div>

        <!-- Footer -->
        <p class="text-center mt-8 text-white/50 text-xs font-medium tracking-widest uppercase">
            &copy; <?= date('Y') ?> LPTQ Provinsi Jawa Timur
        </p>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'bi bi-eye-slash-fill';
            } else {
                pwd.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>