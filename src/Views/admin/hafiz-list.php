<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-people me-2"></i>Data Hafiz</h4>
    <a href="<?= APP_URL ?>/admin/hafiz/create" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Tambah Data Hafiz
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-3">
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
                <select class="form-select" name="status_kelulusan">
                    <option value="">-- Semua --</option>
                    <option value="pending" <?= ($filters['status_kelulusan'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="lulus" <?= ($filters['status_kelulusan'] ?? '') === 'lulus' ? 'selected' : '' ?>>Lulus</option>
                    <option value="tidak_lulus" <?= ($filters['status_kelulusan'] ?? '') === 'tidak_lulus' ? 'selected' : '' ?>>Tidak Lulus</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" name="tahun_tes"
                    value="<?= htmlspecialchars($filters['tahun_tes'] ?? TAHUN_ANGGARAN) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Filter</button>
                <a href="<?= APP_URL ?>/admin/hafiz" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
                        <th class="text-center">Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($hafizList)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada data ditemukan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $startNo = (($pagination['page'] - 1) * $pagination['per_page']) + 1;
                        foreach ($hafizList as $i => $hafiz):
                        ?>
                            <tr>
                                <td><?= $startNo + $i ?></td>
                                <td><code><?= htmlspecialchars($hafiz['nik']) ?></code></td>
                                <td>
                                    <strong><?= htmlspecialchars($hafiz['nama']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($hafiz['telepon'] ?? '-') ?></small>
                                </td>
                                <td><?= htmlspecialchars($hafiz['kabupaten_kota_nama']) ?></td>
                                <td><?= htmlspecialchars($hafiz['sertifikat_tahfidz'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $hafiz['status_kelulusan'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $hafiz['status_kelulusan'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= APP_URL ?>/admin/hafiz/<?= $hafiz['id'] ?>/edit" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                        onclick="confirmDelete(<?= $hafiz['id'] ?>, '<?= htmlspecialchars($hafiz['nama']) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
                    <p>Apakah Anda yakin ingin menghapus data <strong id="deleteName"></strong>?</p>
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
    function confirmDelete(id, name) {
        document.getElementById('deleteForm').action = '<?= APP_URL ?>/admin/hafiz/' + id + '/delete';
        document.getElementById('deleteName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>