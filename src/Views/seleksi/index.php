<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Seleksi Hafiz - Tahun <?= $tahun ?></h4>
    <div>
        <a href="<?= APP_URL ?>/seleksi/export?tahun=<?= $tahun ?>&format=excel" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-people text-primary fs-1"></i>
                <h3 class="mb-0"><?= number_format($stats['total_peserta'] ?? 0) ?></h3>
                <small class="text-muted">Total Peserta</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center h-100 border-info">
            <div class="card-body">
                <i class="bi bi-pencil-square text-info fs-1"></i>
                <h3 class="mb-0 text-info"><?= number_format($stats['sudah_dinilai'] ?? 0) ?></h3>
                <small class="text-muted">Sudah Dinilai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center h-100 border-success">
            <div class="card-body">
                <i class="bi bi-check-circle text-success fs-1"></i>
                <h3 class="mb-0 text-success"><?= number_format($stats['lulus'] ?? 0) ?></h3>
                <small class="text-muted">Lulus</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center h-100 border-danger">
            <div class="card-body">
                <i class="bi bi-x-circle text-danger fs-1"></i>
                <h3 class="mb-0 text-danger"><?= number_format($stats['tidak_lulus'] ?? 0) ?></h3>
                <small class="text-muted">Tidak Lulus</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" name="tahun" value="<?= $tahun ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" class="form-control" name="search" placeholder="Nama / NIK"
                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>
            <?php if (hasRole(ROLE_ADMIN_PROV)): ?>
                <div class="col-md-3">
                    <label class="form-label">Kabupaten/Kota</label>
                    <select class="form-select" name="kabupaten_kota_id">
                        <option value="">-- Semua --</option>
                        <?php foreach ($kabkoList as $id => $nama): ?>
                            <option value="<?= $id ?>" <?= ($filters['kabupaten_kota_id'] ?? '') == $id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nama) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">-- Semua --</option>
                    <option value="belum" <?= ($filters['status_lulus'] ?? '') === 'belum' ? 'selected' : '' ?>>Belum Dinilai</option>
                    <option value="lulus" <?= ($filters['status_lulus'] ?? '') === 'lulus' ? 'selected' : '' ?>>Lulus</option>
                    <option value="tidak_lulus" <?= ($filters['status_lulus'] ?? '') === 'tidak_lulus' ? 'selected' : '' ?>>Tidak Lulus</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Filter</button>
                <a href="<?= APP_URL ?>/seleksi" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Kab/Kota</th>
                        <th>Hafalan</th>
                        <th class="text-center">Nilai Wawasan</th>
                        <th class="text-center">Nilai Hafalan</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Status</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pesertaList)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada data ditemukan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $startNo = (($pagination['page'] - 1) * $pagination['per_page']) + 1;
                        foreach ($pesertaList as $i => $peserta):
                        ?>
                            <tr>
                                <td><?= $startNo + $i ?></td>
                                <td><code><?= htmlspecialchars($peserta['nik']) ?></code></td>
                                <td><strong><?= htmlspecialchars($peserta['nama']) ?></strong></td>
                                <td><small><?= htmlspecialchars($peserta['kabupaten_kota_nama']) ?></small></td>
                                <td><?= htmlspecialchars($peserta['sertifikat_tahfidz'] ?? '-') ?></td>
                                <td class="text-center">
                                    <?= $peserta['nilai_wawasan'] !== null ? number_format($peserta['nilai_wawasan'], 1) : '-' ?>
                                </td>
                                <td class="text-center">
                                    <?= $peserta['nilai_hafalan'] !== null ? number_format($peserta['nilai_hafalan'], 1) : '-' ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($peserta['nilai_total'] !== null): ?>
                                        <strong class="<?= $peserta['nilai_total'] >= 70 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($peserta['nilai_total'], 1) ?>
                                        </strong>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($peserta['nilai_total'] !== null): ?>
                                        <span class="badge bg-<?= $peserta['status_lulus'] ? 'success' : 'danger' ?>">
                                            <?= $peserta['status_lulus'] ? 'LULUS' : 'TIDAK LULUS' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Belum Dinilai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= APP_URL ?>/seleksi/<?= $peserta['hafiz_id'] ?>/nilai?tahun=<?= $tahun ?>"
                                        class="btn btn-sm btn-<?= $peserta['nilai_total'] !== null ? 'outline-primary' : 'primary' ?>">
                                        <i class="bi bi-pencil-square"></i>
                                        <?= $peserta['nilai_total'] !== null ? 'Edit' : 'Input' ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-white">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                        <li class="page-item <?= $p == $pagination['page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&tahun=<?= $tahun ?>&<?= http_build_query(array_filter($filters, fn($v) => $v !== null && $v !== '')) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>