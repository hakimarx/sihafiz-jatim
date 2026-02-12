<?php
$title = "Transfer Hafiz";
$role = getCurrentUserRole();
?>
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>History Mutasi / Transfer Hafiz</h4>
</div>

<?php if ($role === ROLE_ADMIN_KABKO): ?>
    <div class="mb-4">
        <a href="<?= APP_URL ?>/admin/mutasi/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Ajukan Mutasi Baru
        </a>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hafiz</th>
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <?php if ($role === 'admin_prov'): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mutasi)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data mutasi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($mutasi as $m): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($m['created_at'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($m['nama_hafiz']) ?></strong><br>
                                    <span class="text-muted small"><?= htmlspecialchars($m['nik']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($m['asal_kabko']) ?></td>
                                <td><?= htmlspecialchars($m['tujuan_kabko']) ?><br>
                                    <span class="badge bg-light text-dark border">To: Kab/Kota Baru</span>
                                </td>
                                <td><?= htmlspecialchars($m['alasan']) ?></td>
                                <td>
                                    <?php if ($m['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif ($m['status'] === 'approved'): ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($role === 'admin_prov'): ?>
                                    <td>
                                        <?php if ($m['status'] === 'pending'): ?>
                                            <a href="<?= APP_URL ?>/admin/mutasi/<?= $m['id'] ?>/approve"
                                                class="btn btn-sm btn-success"
                                                onclick="return confirm('Setujui mutasi ini? Hafiz akan dipindahkan ke Kab/Kota Tujuan.')">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="<?= APP_URL ?>/admin/mutasi/<?= $m['id'] ?>/reject"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Tolak mutasi ini?')">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                        <?php else: ?>
                                            <i class="bi bi-check-circle-fill text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>