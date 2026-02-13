<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-person-gear me-2"></i>Manajemen User</h4>
    <a href="<?= APP_URL ?>/admin/users/create" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah User
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" class="form-control" name="search" placeholder="Nama / Username"
                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select class="form-select" name="role">
                    <option value="">-- Semua Role --</option>
                    <option value="<?= ROLE_ADMIN_PROV ?>" <?= ($filters['role'] ?? '') === ROLE_ADMIN_PROV ? 'selected' : '' ?>>Admin Provinsi</option>
                    <option value="<?= ROLE_ADMIN_KABKO ?>" <?= ($filters['role'] ?? '') === ROLE_ADMIN_KABKO ? 'selected' : '' ?>>Admin Kab/Ko</option>
                    <option value="<?= ROLE_PENGUJI ?>" <?= ($filters['role'] ?? '') === ROLE_PENGUJI ? 'selected' : '' ?>>Penguji</option>
                    <option value="<?= ROLE_HAFIZ ?>" <?= ($filters['role'] ?? '') === ROLE_HAFIZ ? 'selected' : '' ?>>Hafiz (Penerima)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kabupaten/Kota</label>
                <select class="form-select" name="kabupaten_kota_id">
                    <option value="">-- Semua Wilayah --</option>
                    <?php foreach ($kabkoList as $id => $nama): ?>
                        <option value="<?= $id ?>" <?= ($filters['kabupaten_kota_id'] ?? '') == $id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Filter</button>
                <a href="<?= APP_URL ?>/admin/users" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
                        <th>User</th>
                        <th>Role</th>
                        <th>Wilayah</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                Tidak ada user ditemukan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $startNo = (($pagination['page'] - 1) * $pagination['limit']) + 1;
                        foreach ($users as $i => $u):
                        ?>
                            <tr>
                                <td><?= $startNo + $i ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($u['nama'] ?? 'Tanpa Nama') ?></strong>
                                    <br><small class="text-muted">@<?= htmlspecialchars($u['username']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?= ucfirst(str_replace('_', ' ', $u['role'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($u['kabupaten_kota_nama'] ?? 'Pusat/Provinsi') ?></td>
                                <td>
                                    <?php if ($u['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Non-aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '-' ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= APP_URL ?>/admin/users/<?= $u['id'] ?>/edit" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($u['id'] !== getCurrentUserId()): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                            onclick="confirmDelete(<?= $u['id'] ?>, '<?= htmlspecialchars($u['nama'] ?: $u['username']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
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
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <?php
                    $totalPages = $pagination['total_pages'];
                    $currentPage = $pagination['page'];
                    $range = 1; // Reduced range to prevent overflow

                    // First & Prev
                    if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1&<?= http_build_query(array_filter($filters)) ?>">&laquo;</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>&<?= http_build_query(array_filter($filters)) ?>">&lsaquo;</a>
                        </li>
                    <?php endif; ?>

                    <?php
                    for ($p = 1; $p <= $totalPages; $p++):
                        if ($p == 1 || $p == $totalPages || ($p >= $currentPage - $range && $p <= $currentPage + $range)):
                    ?>
                        <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&<?= http_build_query(array_filter($filters)) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php elseif (($p == $currentPage - $range - 1) || ($p == $currentPage + $range + 1)): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; endfor; ?>

                    <?php
                    // Next & Last
                    if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>&<?= http_build_query(array_filter($filters)) ?>">&rsaquo;</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $totalPages ?>&<?= http_build_query(array_filter($filters)) ?>">&raquo;</a>
                        </li>
                    <?php endif; ?>
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
                    <p>Apakah Anda yakin ingin menghapus user <strong id="deleteName"></strong>?</p>
                    <p class="text-danger small">Tindakan ini tidak dapat dibatalkan.</p>
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
        document.getElementById('deleteForm').action = '<?= APP_URL ?>/admin/users/' + id + '/delete';
        document.getElementById('deleteName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>