<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Pendaftar</h6>
                        <h3 class="mb-0 text-success"><?= number_format($totalPendaftar) ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-people text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card" style="border-left-color: #0d6efd;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Lolos Seleksi</h6>
                        <h3 class="mb-0 text-primary"><?= number_format($totalLulus) ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-check-circle text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card pending">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Menunggu Seleksi</h6>
                        <h3 class="mb-0 text-warning"><?= number_format($totalPending) ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats by Kabupaten/Kota -->
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-bar-chart me-2"></i>
            <?php if (hasRole(ROLE_ADMIN_KABKO) && !empty($stats) && count($stats) === 1): ?>
                Rekap <?= htmlspecialchars($stats[0]['nama'] ?? '') ?> - Tahun <?= TAHUN_ANGGARAN ?>
            <?php else: ?>
                Rekap per Kabupaten/Kota - Tahun <?= TAHUN_ANGGARAN ?>
            <?php endif; ?>
        </h5>
        <?php if (hasRole(ROLE_ADMIN_KABKO) && isset($pendingApproval) && $pendingApproval > 0): ?>
            <span class="badge bg-danger">
                <i class="bi bi-bell-fill me-1"></i> <?= $pendingApproval ?> Hafiz Baru Menunggu Approval
            </span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kabupaten/Kota</th>
                        <th class="text-center">Total Pendaftar</th>
                        <th class="text-center">Lulus</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $i => $stat): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($stat['nama']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($stat['kode']) ?></small>
                            </td>
                            <td class="text-center"><?= number_format($stat['total_pendaftar'] ?? 0) ?></td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= number_format($stat['total_lulus'] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark"><?= number_format($stat['total_pending'] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <a href="<?= APP_URL ?>/admin/hafiz?kabupaten_kota_id=<?= $stat['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="2">TOTAL</td>
                        <td class="text-center"><?= number_format($totalPendaftar) ?></td>
                        <td class="text-center"><?= number_format($totalLulus) ?></td>
                        <td class="text-center"><?= number_format($totalPending) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>