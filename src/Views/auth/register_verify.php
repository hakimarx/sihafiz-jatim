<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Verifikasi - ' . APP_NAME ?></title>
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
    </style>
</head>

<body class="bg-gradient-to-br from-green-900 via-green-700 to-emerald-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 rounded-full bg-white/10 backdrop-blur-md mb-4 shadow-2xl">
                <i class="bi bi-person-check-fill text-5xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2"><?= ($step ?? '') === 'choose' ? 'Pilih Data Anda' : 'Verifikasi Identitas' ?></h1>
            <p class="text-green-100 opacity-80">
                <?= ($step ?? '') === 'choose' ? 'Berikut hasil pencarian berdasarkan nama dan kota Anda.' : 'Pastikan ini data Anda dengan memasukkan tanggal lahir.' ?>
            </p>
        </div>

        <!-- Main Card -->
        <div class="glass rounded-3xl shadow-2xl p-8 border border-white/20">
            <!-- Flash Message -->
            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-xl <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-blue-50 text-blue-700 border border-blue-100' ?> flex items-start gap-3">
                    <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-circle-fill' : 'bi-info-circle-fill' ?> mt-0.5"></i>
                    <div class="text-sm font-medium"><?= $flash['message'] ?></div>
                </div>
            <?php endif; ?>

            <?php if (($step ?? '') === 'choose'): ?>
                <!-- STEP: CHOOSE -->
                <form action="<?= APP_URL ?>/register/choose" method="POST" class="space-y-4">
                    <?= csrfField() ?>

                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                        <?php foreach (($choices ?? []) as $idx => $choice): ?>
                            <label class="cursor-pointer block relative group">
                                <input type="radio" name="hafiz_id" value="<?= $choice['id'] ?>" class="peer sr-only" required <?= $idx === 0 ? 'checked' : '' ?>>
                                <div class="p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition-all peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:shadow-md">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 peer-checked:bg-green-600 peer-checked:text-white transition-colors">
                                            <i class="bi bi-person-fill text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($choice['nama_samaran']) ?></h3>
                                            <div class="text-sm text-gray-500 mt-1 space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <i class="bi bi-card-heading"></i> NIK: <?= htmlspecialchars($choice['nik_samaran']) ?>
                                                </div>
                                                <?php if (!empty($choice['kabupaten'])): ?>
                                                    <div class="flex items-center gap-2">
                                                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($choice['kabupaten']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="absolute top-4 right-4 text-green-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="bi bi-check-circle-fill text-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-green-900/20 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2">
                        PILIH DATA INI <i class="bi bi-arrow-right"></i>
                    </button>

                    <div class="text-center mt-4">
                        <a href="<?= APP_URL ?>/register" class="text-sm text-gray-500 hover:text-green-600 underline">Bukan data Anda? Cari ulang</a>
                    </div>
                </form>

            <?php else: ?>
                <!-- STEP: VERIFY -->
                <div class="bg-green-50 border border-green-100 rounded-xl p-4 mb-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                        <i class="bi bi-person-check text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800"><?= htmlspecialchars($nama_samaran ?? '') ?></h3>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($kabupaten ?? '') ?></p>
                    </div>
                </div>

                <form action="<?= APP_URL ?>/register/verify" method="POST" class="space-y-5">
                    <?= csrfField() ?>
                    <input type="hidden" name="hafiz_id" value="<?= $id ?? '' ?>">
                    <input type="hidden" name="kabupaten_kota_id" value="<?= $kabupaten_kota_id ?? '' ?>">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Tanggal Lahir (Sesuai KTP)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" name="tanggal_lahir" class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 italic">Tanggal lahir digunakan untuk memverifikasi kepemilikan data.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Nomor HP / WhatsApp</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="bi bi-whatsapp"></i>
                            </span>
                            <input type="number" name="telepon" class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-600 focus:border-green-600 transition-all font-medium" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <p class="mt-1 text-xs text-blue-600 font-medium"><i class="bi bi-info-circle me-1"></i>Nomor HP ini akan menjadi Password Login Anda.</p>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-green-900/20 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2">
                        AKTIVASI AKUN <i class="bi bi-check-lg"></i>
                    </button>

                    <div class="text-center mt-4">
                        <a href="<?= APP_URL ?>/register" class="text-sm text-gray-500 hover:text-green-600 underline">Batal & Kembali</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <p class="text-center mt-8 text-green-100/50 text-sm">
            &copy; <?= date('Y') ?> LPTQ Provinsi Jawa Timur
        </p>
    </div>
</body>

</html>