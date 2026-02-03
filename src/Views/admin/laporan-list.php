<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-file-earmark-check me-2"></i>Verifikasi Laporan Harian</h4>
    <a href="<?= APP_URL ?>/admin/laporan/export?status=disetujui&format=excel" class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="pending" <?= ($filters['status_verifikasi'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="disetujui" <?= ($filters['status_verifikasi'] ?? '') === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="ditolak" <?= ($filters['status_verifikasi'] ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    <option value="" <?= ($filters['status_verifikasi'] ?? '') === '' ? 'selected' : '' ?>>Semua</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" class="form-control" name="tanggal_dari"
                    value="<?= htmlspecialchars($filters['tanggal_dari'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" class="form-control" name="tanggal_sampai"
                    value="<?= htmlspecialchars($filters['tanggal_sampai'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Filter</button>
                <a href="<?= APP_URL ?>/admin/laporan" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($laporanList)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                Tidak ada laporan ditemukan
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hafiz</th>
                            <th>Kegiatan</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Status</th>
                            <th width="180" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laporanList as $laporan): ?>
                            <tr>
                                <td>
                                    <strong><?= date('d M Y', strtotime($laporan['tanggal'])) ?></strong>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($laporan['hafiz_nama']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($laporan['kabupaten_kota_nama'] ?? '') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= ucfirst($laporan['jenis_kegiatan']) ?></span>
                                </td>
                                <td>
                                    <?= htmlspecialchars(substr($laporan['deskripsi'], 0, 80)) ?>
                                    <?= strlen($laporan['deskripsi']) > 80 ? '...' : '' ?>
                                    <?php if ($laporan['foto']): ?>
                                        <br><a href="<?= APP_URL . htmlspecialchars($laporan['foto']) ?>" target="_blank" class="small">
                                            <i class="bi bi-image"></i> Lihat Foto
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $laporan['status_verifikasi'] ?>">
                                        <?= ucfirst($laporan['status_verifikasi']) ?>
                                    </span>
                                    <?php if ($laporan['verified_at']): ?>
                                        <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($laporan['verified_at'])) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($laporan['status_verifikasi'] === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-success" title="Setujui"
                                            onclick="verifyLaporan(<?= $laporan['id'] ?>, 'disetujui')">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" title="Tolak"
                                            onclick="showRejectModal(<?= $laporan['id'] ?>)">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-white">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                        <li class="page-item <?= $p == $pagination['page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&<?= http_build_query(array_filter($filters)) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Approve Form (hidden) -->
<form id="approveForm" method="POST" style="display: none;">
    <?= csrfField() ?>
    <input type="hidden" name="status" value="disetujui">
</form>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="status" value="ditolak">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan/Alasan Penolakan</label>
                        <textarea class="form-control" name="catatan" rows="3" required
                            placeholder="Berikan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function verifyLaporan(id, status) {
        if (!confirm('Yakin ingin menyetujui laporan ini?')) return;

        const form = document.getElementById('approveForm');
        form.action = '<?= APP_URL ?>/admin/laporan/' + id + '/verify';
        form.submit();
    }

    function showRejectModal(id) {
        document.getElementById('rejectForm').action = '<?= APP_URL ?>/admin/laporan/' + id + '/verify';
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }
</script>