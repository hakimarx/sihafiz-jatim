<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Data - <?= APP_NAME ?></title>
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
    </style>
</head>

<body class="bg-gradient-to-br from-green-900 via-green-700 to-emerald-800 min-h-screen py-12 px-4 flex items-center justify-center">
    <div class="max-w-2xl w-full">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Konfirmasi Data Anda</h1>
            <p class="text-green-100 opacity-80">Kami menemukan data yang cocok. Silakan lengkapi informasi berikut.</p>
        </div>

        <div class="glass rounded-3xl shadow-2xl overflow-hidden border border-white/20">
            <!-- Header Info -->
            <div class="bg-green-600 px-8 py-6 text-white flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-3xl">
                    <i class="bi bi-person-check text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold"><?= htmlspecialchars($hafiz['nama']) ?></h2>
                    <p class="opacity-80 text-sm">Status: <?= $hafiz['status_kelulusan'] === 'lulus' ? 'Lulus Ujian' : 'Pending' ?></p>
                </div>
            </div>

            <div class="p-8">
                <!-- Flash Message -->
                <?php $flash = getFlash(); ?>
                <?php if ($flash): ?>
                    <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 border border-red-100 flex items-start gap-3">
                        <i class="bi bi-exclamation-circle-fill mt-0.5"></i>
                        <div class="text-sm font-medium"><?= $flash['message'] ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/register/verify" method="POST" class="space-y-6">
                    <?= csrfField() ?>
                    <input type="hidden" name="hafiz_id" value="<?= $hafiz['id'] ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- NIK Input -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">NIK KTP (16 Digit)</label>
                            <input type="number" name="nik" id="nikInput" value="<?= htmlspecialchars($hafiz['nik']) ?>"
                                class="w-full px-4 py-3 bg-gray-50 border <?= strlen($hafiz['nik']) !== 16 ? 'border-red-500 bg-red-50' : 'border-gray-200' ?> rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-medium"
                                required>
                            <?php if (strlen($hafiz['nik']) !== 16): ?>
                                <p class="mt-1 text-xs text-red-600 font-bold italic"><i class="bi bi-exclamation-triangle"></i> NIK di data kami hanya <?= strlen($hafiz['nik']) ?> digit. Mohon perbaiki.</p>
                            <?php endif; ?>
                        </div>

                        <!-- HP Input -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Nomor HP/WA Aktif</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="bi bi-whatsapp"></i>
                                </span>
                                <input type="number" name="telepon" value="<?= htmlspecialchars($hafiz['telepon'] ?? '') ?>"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-medium"
                                    placeholder="0812..." required>
                            </div>
                            <p class="mt-1 text-xs text-blue-600 italic">Ini akan digunakan sebagai password login Anda.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kabupaten Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Kabupaten/Kota</label>
                            <select name="kabupaten_kota_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-medium" required>
                                <option value="">-- Pilih Kota --</option>
                                <?php foreach ($kabkoList as $kabko): ?>
                                    <option value="<?= $kabko['id'] ?>" <?= $kabko['id'] == $hafiz['kabupaten_kota_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kabko['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tempat Lahir (Read-only for validation) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-400 mb-2 uppercase tracking-wider">Tempat Lahir</label>
                            <div class="px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 font-medium">
                                <?= htmlspecialchars($hafiz['tempat_lahir'] ?? '-') ?>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mt-6">
                        <div class="flex gap-3">
                            <i class="bi bi-info-circle-fill text-blue-600 mt-0.5"></i>
                            <div class="text-sm text-blue-800 leading-relaxed">
                                <strong>Penting:</strong> Setelah menekan tombol Aktivasi, akun Anda akan diverifikasi oleh Admin Kabupaten/Kota. Silakan cek berkala atau hubungi admin jika akun belum aktif dalam 1x24 jam.
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 rounded-2xl shadow-xl transform transition hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        AKTIVASI SEKARANG <i class="bi bi-shield-check"></i>
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center mt-8 text-green-100/50 text-sm">
            Kesalahan data? <a href="<?= APP_URL ?>/register" class="underline hover:text-white">Kembali</a>
        </p>
    </div>

    <script>
        const nikInput = document.getElementById('nikInput');
        nikInput.addEventListener('input', function() {
            if (this.value.length === 16) {
                this.classList.remove('border-red-500', 'bg-red-50');
                this.classList.add('border-green-500', 'bg-green-50');
            } else {
                this.classList.remove('border-green-500', 'bg-green-50');
                this.classList.add('border-red-500', 'bg-red-50');
            }
        });
    </script>
</body>

</html>