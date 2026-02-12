<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Registrasi - ' . APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cdf0d6;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #198754;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-green-900 via-green-700 to-emerald-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 rounded-full bg-white/10 backdrop-blur-md mb-4 shadow-2xl">
                <i class="bi bi-person-plus-fill text-5xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Pendaftaran Hafiz</h1>
            <p class="text-green-100 opacity-80">Silakan pilih metode pendaftaran atau aktivasi akun Anda.</p>
        </div>

        <!-- Main Card -->
        <div class="glass rounded-3xl shadow-2xl p-6 md:p-8 border border-white/20">
            <!-- Flash Message -->
            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-xl <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-blue-50 text-blue-700 border border-blue-100' ?> flex items-start gap-3">
                    <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-circle-fill' : 'bi-info-circle-fill' ?> mt-0.5"></i>
                    <div class="text-sm font-medium"><?= $flash['message'] ?></div>
                </div>
            <?php endif; ?>

            <?php
            $activeTab = !empty($ssoData) ? 'tab-fresh' : 'tab-nik';
            ?>

            <!-- Tabs Navigation -->
            <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
                <button type="button" class="whitespace-nowrap px-4 pb-4 text-center font-semibold transition-all tab-btn <?= $activeTab === 'tab-nik' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-400 hover:text-green-600' ?>" data-target="tab-nik" onclick="switchTab('tab-nik', this)">
                    <i class="bi bi-card-heading me-2"></i>Aktivasi NIK
                </button>
                <button type="button" class="whitespace-nowrap px-4 pb-4 text-center font-semibold transition-all tab-btn <?= $activeTab === 'tab-nama' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-400 hover:text-green-600' ?>" data-target="tab-nama" onclick="switchTab('tab-nama', this)">
                    <i class="bi bi-search me-2"></i>Cari Nama
                </button>
                <button type="button" class="whitespace-nowrap px-4 pb-4 text-center font-semibold transition-all tab-btn <?= $activeTab === 'tab-fresh' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-400 hover:text-green-600' ?>" data-target="tab-fresh" onclick="switchTab('tab-fresh', this)">
                    <i class="bi bi-person-plus me-2"></i>Daftar Baru
                </button>
            </div>

            <!-- Tab NIK (Default: Activation) -->
            <div id="tab-nik" class="tab-content <?= $activeTab === 'tab-nik' ? '' : 'hidden' ?>">
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-4 text-sm text-blue-800">
                    <i class="bi bi-info-circle-fill me-2"></i>Gunakan tab ini jika data Anda <strong>sudah ada</strong> (dari hasil import/seleksi).
                </div>

                <form action="<?= APP_URL ?>/register/check-nik" method="POST" class="space-y-6">
                    <?= csrfField() ?>
                    <div>
                        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Nomor Induk Kependudukan (NIK)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="bi bi-card-heading"></i>
                            </span>
                            <input type="number" name="nik" id="nik" value="<?= htmlspecialchars($nik ?? '') ?>"
                                class="block w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all text-lg font-medium tracking-widest"
                                placeholder="16 digit angka..." required>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 italic">Pastikan NIK sesuai dengan yang terdaftar.</p>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-green-900/20 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2">
                        LANJUTKAN AKTIVASI <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- Tab Cari Nama (Alternative Activation) -->
            <div id="tab-nama" class="tab-content <?= $activeTab === 'tab-nama' ? '' : 'hidden' ?>">
                <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 mb-4 text-sm text-yellow-800">
                    <i class="bi bi-lightbulb-fill me-2"></i>Gunakan tab ini jika Anda lupa NIK atau NIK tidak ditemukan.
                </div>

                <form action="<?= APP_URL ?>/register/check-nama" method="POST" class="space-y-4">
                    <?= csrfField() ?>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="nama_cari" class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium" placeholder="Nama sesuai sertifikat..." required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Kabupaten/Kota</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <select name="kabko_id" class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium appearance-none" required>
                                <option value="">-- Pilih Kota Asal --</option>
                                <?php if (!empty($kabkoList)): ?>
                                    <?php foreach ($kabkoList as $kab): ?>
                                        <option value="<?= $kab['id'] ?>"><?= htmlspecialchars($kab['nama']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Kode Keamanan</label>
                        <div class="flex gap-3">
                            <div class="w-1/3 bg-gray-200 rounded-xl flex items-center justify-center font-mono text-xl tracking-widest text-gray-600 select-none border border-gray-300">
                                <?= $captcha['code'] ?>
                                <input type="hidden" name="captcha_hash" value="<?= $captcha['hash'] ?>">
                            </div>
                            <input type="text" name="captcha" class="block w-2/3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium text-center tracking-widest" placeholder="Ketik kode..." required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-900/20 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 mt-2">
                        CARI DATA SAYA <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>

            <!-- Tab Daftar Baru (Fresh Register) -->
            <div id="tab-fresh" class="tab-content <?= $activeTab === 'tab-fresh' ? '' : 'hidden' ?>">

                <?php if (!empty($ssoData)): ?>
                    <div class="bg-green-50 p-4 rounded-xl border border-green-100 mb-4 flex items-start gap-3">
                        <img src="<?= htmlspecialchars($ssoData['foto'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($ssoData['nama'])) ?>" class="w-10 h-10 rounded-full">
                        <div>
                            <h4 class="font-bold text-green-800">Mendaftar sebagai <?= htmlspecialchars($ssoData['nama']) ?></h4>
                            <p class="text-xs text-green-600"><?= htmlspecialchars($ssoData['email']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mb-6">
                        <a href="<?= APP_URL ?>/login/google" class="w-full bg-white border border-gray-200 text-gray-700 font-bold py-3 rounded-xl shadow-sm hover:bg-gray-50 transition-all flex items-center justify-center gap-2 text-sm">
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                            Daftar Cepat dengan Google
                        </a>
                        <div class="relative flex py-3 items-center">
                            <div class="flex-grow border-t border-gray-200"></div>
                            <span class="flex-shrink mx-4 text-gray-400 text-xs uppercase">Atau Manual</span>
                            <div class="flex-grow border-t border-gray-200"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/register/fresh" method="POST" class="space-y-4">
                    <?= csrfField() ?>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Nama Lengkap (Sesuai KTP)</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($ssoData['nama'] ?? '') ?>" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium" placeholder="Nama Lengkap..." required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">NIK (16 Digit)</label>
                        <input type="number" name="nik" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium" placeholder="NIK..." required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Kabupaten/Kota</label>
                            <select name="kabko_id" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium appearance-none" required>
                                <option value="">-- Pilih --</option>
                                <?php if (!empty($kabkoList)): ?>
                                    <?php foreach ($kabkoList as $kab): ?>
                                        <option value="<?= $kab['id'] ?>"><?= htmlspecialchars($kab['nama']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">No. HP / WA</label>
                            <input type="number" name="telepon" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium" placeholder="08..." required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Kode Keamanan</label>
                        <div class="flex gap-3">
                            <div class="w-1/3 bg-gray-200 rounded-xl flex items-center justify-center font-mono text-xl tracking-widest text-gray-600 select-none border border-gray-300">
                                <?= $captcha['code'] ?>
                                <!-- Hash is already in the other form, but we need unique name or shared helper? 
                                     Actually, RegistrationController generates ONE captcha per request. 
                                     So the hash is same for all forms. We can reuse the value or output hidden input again. -->
                                <input type="hidden" name="captcha_hash" value="<?= $captcha['hash'] ?>">
                            </div>
                            <input type="text" name="captcha" class="block w-2/3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium text-center tracking-widest" placeholder="Ketik kode..." required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-purple-900/20 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 mt-2">
                        DAFTAR SEKARANG <i class="bi bi-person-plus"></i>
                    </button>
                </form>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-gray-500 text-sm">Sudah punya akun aktif?
                    <a href="<?= APP_URL ?>/login" class="text-green-600 font-bold hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>

        <p class="text-center mt-8 text-green-100/50 text-sm">
            &copy; <?= date('Y') ?> LPTQ Provinsi Jawa Timur
        </p>
    </div>

    <script>
        function switchTab(tabId, btn) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Show selected tab
            document.getElementById(tabId).classList.remove('hidden');

            // Reset buttons
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('text-green-600', 'border-b-2', 'border-green-600');
                el.classList.add('text-gray-400');
            });

            // Activate button
            btn.classList.remove('text-gray-400');
            btn.classList.add('text-green-600', 'border-b-2', 'border-green-600');
        }
    </script>
</body>

</html>