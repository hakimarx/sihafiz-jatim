<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Laporan Harian</h4>
    <a href="<?= APP_URL ?>/hafiz/laporan/create" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Input Laporan Baru
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($laporanList)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <p class="mb-3">Belum ada laporan</p>
                <a href="<?= APP_URL ?>/hafiz/laporan/create" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Buat Laporan Pertama
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kegiatan</th>
                            <th>Deskripsi</th>
                            <th>Lokasi</th>
                            <th class="text-center">Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laporanList as $laporan): ?>
                            <tr>
                                <td>
                                    <strong><?= date('d M Y', strtotime($laporan['tanggal'])) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= ucfirst($laporan['jenis_kegiatan']) ?></span>
                                </td>
                                <td>
                                    <?= htmlspecialchars(substr($laporan['deskripsi'], 0, 50)) ?>
                                    <?= strlen($laporan['deskripsi']) > 50 ? '...' : '' ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= !empty($laporan['lokasi']) ? htmlspecialchars($laporan['lokasi']) : '-' ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $laporan['status_verifikasi'] ?>">
                                        <?= ucfirst($laporan['status_verifikasi']) ?>
                                    </span>
                                    <?php if ($laporan['status_verifikasi'] === 'ditolak' && $laporan['catatan_verifikasi']): ?>
                                        <br><small class="text-danger"><?= htmlspecialchars($laporan['catatan_verifikasi']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($laporan['status_verifikasi'] === 'pending'): ?>
                                        <a href="<?= APP_URL ?>/hafiz/laporan/<?= $laporan['id'] ?>/edit" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                            onclick="confirmDelete(<?= $laporan['id'] ?>)">
                                            <i class="bi bi-trash"></i>
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
                            <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                <?= csrfField() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus laporan ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '<?= APP_URL ?>/hafiz/laporan/' + id + '/delete';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>