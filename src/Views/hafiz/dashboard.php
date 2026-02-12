<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12 text-center py-4">
        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
            <i class="bi bi-person-check text-success display-4"></i>
        </div>
        <h2 class="fw-bold">Selamat Datang, <?= htmlspecialchars(explode(' ', $hafiz['nama'])[0]) ?>!</h2>
        <p class="lead text-muted">Aplikasi Laporan Harian Huffadz Jatim</p>
    </div>
</div>

<!-- Main Action Buttons (LARGE) -->
<div class="row g-4 mb-5">
    <!-- Input Laporan Button -->
    <div class="col-md-6">
        <a href="<?= APP_URL ?>/hafiz/laporan/create" class="card h-100 text-decoration-none border-0 shadow-lg hover-scale" style="background: linear-gradient(135deg, #198754 0%, #157347 100%); transition: transform 0.2s;">
            <div class="card-body text-center py-5 text-white">
                <i class="bi bi-plus-circle display-1 mb-3"></i>
                <h2 class="fw-bold">ISI LAPORAN</h2>
                <p class="opacity-75 fs-5">Klik di sini untuk menambah setoran/kegiatan</p>
            </div>
        </a>
    </div>

    <!-- Riwayat Laporan Button -->
    <div class="col-md-6">
        <a href="<?= APP_URL ?>/hafiz/laporan" class="card h-100 text-decoration-none border-0 shadow-lg hover-scale" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); transition: transform 0.2s;">
            <div class="card-body text-center py-5 text-white">
                <i class="bi bi-journal-text display-1 mb-3"></i>
                <h2 class="fw-bold">LIHAT RIWAYAT</h2>
                <p class="opacity-75 fs-5">Klik di sini untuk melihat laporan yang sudah dikirim</p>
            </div>
        </a>
    </div>
</div>

<!-- Status & Stats (Simple) -->
<div class="row mb-4 text-center">
    <div class="col-6 col-md-3 mb-3">
        <div class="p-3 bg-white rounded shadow-sm border-start border-primary border-4">
            <h1 class="fw-bold mb-0"><?= $summary['total_laporan'] ?? 0 ?></h1>
            <small class="text-muted text-uppercase fw-bold">Total Dikirim</small>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="p-3 bg-white rounded shadow-sm border-start border-success border-4">
            <h1 class="fw-bold mb-0 text-success"><?= $summary['disetujui'] ?? 0 ?></h1>
            <small class="text-muted text-uppercase fw-bold">Disetujui</small>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="p-3 bg-white rounded shadow-sm border-start border-warning border-4">
            <h1 class="fw-bold mb-0 text-warning"><?= $summary['pending'] ?? 0 ?></h1>
            <small class="text-muted text-uppercase fw-bold">Menunggu</small>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="p-3 bg-white rounded shadow-sm border-start border-info border-4">
            <?php
            // Karena hanya hafiz LULUS yang bisa login, maka statusnya pasti Lulus/Penerima Insentif
            // Kecuali jika ada anomali data (misal admin mengubah status setelah user login)
            $statusLabel = 'Penerima Insentif';
            $statusClass = 'text-success';

            if ($hafiz['status_kelulusan'] !== 'lulus') {
                $statusLabel = 'Non-Aktif';
                $statusClass = 'text-danger';
            }
            ?>
            <h4 class="fw-bold mb-0 <?= $statusClass ?>" style="font-size: 1.2rem; margin-top: 0.5rem; margin-bottom: 0.5rem;"><?= $statusLabel ?></h4>
            <small class="text-muted text-uppercase fw-bold">Status Peserta</small>
        </div>
    </div>
</div>

<style>
    .hover-scale:hover {
        transform: scale(1.02);
    }
</style>

<!-- Laporan Terbaru -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Laporan Terbaru</h6>
        <a href="<?= APP_URL ?>/hafiz/laporan" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentLaporan)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <p class="mb-0">Belum ada laporan</p>
                <a href="<?= APP_URL ?>/hafiz/laporan/create" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-lg me-1"></i> Buat Laporan Pertama
                </a>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($recentLaporan as $laporan): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    <span class="badge bg-secondary me-2"><?= ucfirst($laporan['jenis_kegiatan']) ?></span>
                                    <?= date('d M Y', strtotime($laporan['tanggal'])) ?>
                                </h6>
                                <p class="mb-0 text-muted small">
                                    <?= htmlspecialchars(substr($laporan['deskripsi'], 0, 100)) ?>...
                                </p>
                            </div>
                            <span class="badge badge-<?= $laporan['status_verifikasi'] ?>">
                                <?= ucfirst($laporan['status_verifikasi']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>