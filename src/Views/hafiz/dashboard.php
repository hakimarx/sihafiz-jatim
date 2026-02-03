<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
</div>

<!-- Profil Singkat -->
<div class="card mb-4 border-start border-success border-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                    <i class="bi bi-person-circle text-success fs-1"></i>
                </div>
            </div>
            <div class="col">
                <h5 class="mb-1"><?= htmlspecialchars($hafiz['nama']) ?></h5>
                <p class="text-muted mb-0">
                    <i class="bi bi-card-text me-1"></i>NIK: <?= htmlspecialchars($hafiz['nik']) ?> &bull;
                    <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($hafiz['kabupaten_kota_nama']) ?>
                </p>
            </div>
            <div class="col-auto">
                <span class="badge bg-<?= $hafiz['status_kelulusan'] === 'lulus' ? 'success' : ($hafiz['status_kelulusan'] === 'pending' ? 'warning' : 'danger') ?> fs-6">
                    <?= ucfirst(str_replace('_', ' ', $hafiz['status_kelulusan'])) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan Bulan Ini -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-journal-text text-primary fs-1 mb-2"></i>
                <h3 class="mb-0"><?= $summary['total_laporan'] ?? 0 ?></h3>
                <small class="text-muted">Total Laporan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center h-100 border-success">
            <div class="card-body">
                <i class="bi bi-check-circle text-success fs-1 mb-2"></i>
                <h3 class="mb-0 text-success"><?= $summary['disetujui'] ?? 0 ?></h3>
                <small class="text-muted">Disetujui</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center h-100 border-warning">
            <div class="card-body">
                <i class="bi bi-hourglass-split text-warning fs-1 mb-2"></i>
                <h3 class="mb-0 text-warning"><?= $summary['pending'] ?? 0 ?></h3>
                <small class="text-muted">Menunggu</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center h-100 border-danger">
            <div class="card-body">
                <i class="bi bi-x-circle text-danger fs-1 mb-2"></i>
                <h3 class="mb-0 text-danger"><?= $summary['ditolak'] ?? 0 ?></h3>
                <small class="text-muted">Ditolak</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-4">
                <a href="<?= APP_URL ?>/hafiz/laporan/create" class="text-decoration-none">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="bi bi-plus-lg text-primary fs-4"></i>
                    </div>
                    <p class="mb-0 text-dark">Input Laporan</p>
                </a>
            </div>
            <div class="col-4">
                <a href="<?= APP_URL ?>/hafiz/laporan" class="text-decoration-none">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="bi bi-list-ul text-success fs-4"></i>
                    </div>
                    <p class="mb-0 text-dark">Riwayat Laporan</p>
                </a>
            </div>
            <div class="col-4">
                <a href="<?= APP_URL ?>/hafiz/profil" class="text-decoration-none">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="bi bi-person text-info fs-4"></i>
                    </div>
                    <p class="mb-0 text-dark">Profil Saya</p>
                </a>
            </div>
        </div>
    </div>
</div>

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