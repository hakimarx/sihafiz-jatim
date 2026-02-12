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
            <div class="col-md-2">
                <label class="form-label">Cari</label>
                <input type="text" class="form-control" name="search" placeholder="Nama / NIK"
                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>
            <?php if (hasRole(ROLE_ADMIN_PROV)): ?>
                <div class="col-md-2">
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
                <label class="form-label">Kualitas Data</label>
                <select class="form-select" name="status_data">
                    <option value="">-- Semua --</option>
                    <option value="valid" <?= ($filters['status_data'] ?? '') === 'valid' ? 'selected' : '' ?>>Valid</option>
                    <option value="perlu_perbaikan" <?= ($filters['status_data'] ?? '') === 'perlu_perbaikan' ? 'selected' : '' ?>>Perlu Perbaikan</option>
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
                        <th>NIK / Nama</th>
                        <th>Lokasi Seleksi</th>
                        <th>Thn Lulus</th>
                        <th>Tempat Mengajar</th>
                        <th>Mulai Mengajar</th>
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
                            $isDeceased = ($hafiz['is_meninggal'] ?? 0) == 1;
                        ?>
                            <tr class="<?= $isDeceased ? 'table-secondary' : '' ?>">
                                <td><?= $startNo + $i ?></td>
                                <td>
                                    <code><?= htmlspecialchars($hafiz['nik']) ?></code><br>
                                    <strong><?= htmlspecialchars($hafiz['nama']) ?></strong>
                                    <?php if ($isDeceased): ?><span class="badge bg-dark ms-1">Wafat</span><?php endif; ?>
                                    <div class="text-muted small"><?= htmlspecialchars($hafiz['telepon'] ?? '') ?></div>
                                </td>
                                <td>
                                    <?= htmlspecialchars($hafiz['lokasi_seleksi'] ?? '-') ?>
                                    <div class="text-muted small"><?= htmlspecialchars($hafiz['kabupaten_kota_nama']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($hafiz['tahun_lulus'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($hafiz['tempat_mengajar'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    if (!empty($hafiz['tmt_mengajar'])) {
                                        echo date('d/m/Y', strtotime($hafiz['tmt_mengajar']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $hafiz['status_kelulusan'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $hafiz['status_kelulusan'])) ?>
                                    </span>
                                    <?php if (($hafiz['status_data'] ?? 'valid') !== 'valid'): ?>
                                        <div class="mt-1"><span class="badge bg-danger">Perlu Perbaikan</span></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= APP_URL ?>/admin/hafiz/<?= $hafiz['id'] ?>/edit" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if (!$isDeceased): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Tandai Meninggal"
                                                onclick="confirmDeceased(<?= $hafiz['id'] ?>, '<?= htmlspecialchars($hafiz['nama']) ?>')">
                                                <i class="bi bi-flag-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                            onclick="confirmDelete(<?= $hafiz['id'] ?>, '<?= htmlspecialchars($hafiz['nama']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
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

<!-- Deceased Modal -->
<div class="modal fade" id="deceasedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deceasedForm" method="POST">
                <?= csrfField() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Status Meninggal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menandai <strong id="deceasedName"></strong> sebagai meninggal dunia?</p>
                    <div class="alert alert-warning">
                        <small><i class="bi bi-exclamation-triangle me-1"></i> Hafiz ini tidak akan menerima insentif lagi.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">Ya, Tandai Wafat</button>
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

    function confirmDeceased(id, name) {
        document.getElementById('deceasedForm').action = '<?= APP_URL ?>/admin/hafiz/' + id + '/mark-deceased';
        document.getElementById('deceasedName').textContent = name;
        new bootstrap.Modal(document.getElementById('deceasedModal')).show();
    }
</script>