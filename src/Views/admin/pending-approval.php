<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-person-check me-2"></i>Persetujuan Pendaftaran</h4>
    <span class="badge bg-info fs-6">
        <i class="bi bi-clock-history me-1"></i><?= count($pendingUsers) ?> menunggu persetujuan
    </span>
</div>

<?php if (empty($pendingUsers)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle p-4 mb-3" style="width: 100px; height: 100px;">
                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-muted mt-3">Tidak ada pendaftaran yang menunggu persetujuan</h5>
            <p class="text-muted mb-0">Semua pendaftaran baru sudah diproses.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nama Hafiz</th>
                            <th>Username (No HP)</th>
                            <th>Kabupaten/Kota</th>
                            <th>Tanggal Daftar</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingUsers as $i => $pu): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($pu['hafiz_nama'] ?? $pu['nama'] ?? '-') ?></div>
                                    <?php if (!empty($pu['hafiz_nik'])): ?>
                                        <small class="text-muted font-monospace">NIK: <?= substr($pu['hafiz_nik'], 0, 6) ?>****<?= substr($pu['hafiz_nik'], -4) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="font-monospace"><?= htmlspecialchars($pu['username']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark">
                                        <?= htmlspecialchars($pu['kabupaten_kota_nama'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= date('d M Y H:i', strtotime($pu['created_at'])) ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal<?= $pu['id'] ?>"
                                                title="Setujui">
                                            <i class="bi bi-check-lg me-1"></i>Setujui
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal<?= $pu['id'] ?>"
                                                title="Tolak">
                                            <i class="bi bi-x-lg me-1"></i>Tolak
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Approve Modal -->
                            <div class="modal fade" id="approveModal<?= $pu['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Konfirmasi Persetujuan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center mb-3">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle p-3" style="width: 70px; height: 70px;">
                                                    <i class="bi bi-person-check text-success" style="font-size: 2rem;"></i>
                                                </div>
                                            </div>
                                            <p class="text-center">Anda yakin ingin <strong class="text-success">menyetujui</strong> pendaftaran:</p>
                                            <div class="bg-light rounded p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-5 text-muted">Nama</div>
                                                    <div class="col-7 fw-bold"><?= htmlspecialchars($pu['hafiz_nama'] ?? $pu['nama'] ?? '-') ?></div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <div class="col-5 text-muted">Username</div>
                                                    <div class="col-7 font-monospace"><?= htmlspecialchars($pu['username']) ?></div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <div class="col-5 text-muted">Wilayah</div>
                                                    <div class="col-7"><?= htmlspecialchars($pu['kabupaten_kota_nama'] ?? '-') ?></div>
                                                </div>
                                            </div>
                                            <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-0">
                                                <small><i class="bi bi-info-circle me-1"></i>Setelah disetujui, hafiz ini dapat <strong>login</strong> ke sistem menggunakan No HP dan password yang dibuat saat pendaftaran.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <form action="<?= APP_URL ?>/admin/pending/<?= $pu['id'] ?>/approve" method="POST" class="d-inline">
                                                <?= csrfField() ?>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-lg me-1"></i>Ya, Setujui
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal<?= $pu['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Konfirmasi Penolakan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center mb-3">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle p-3" style="width: 70px; height: 70px;">
                                                    <i class="bi bi-person-x text-danger" style="font-size: 2rem;"></i>
                                                </div>
                                            </div>
                                            <p class="text-center">Anda yakin ingin <strong class="text-danger">menolak</strong> pendaftaran:</p>
                                            <div class="bg-light rounded p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-5 text-muted">Nama</div>
                                                    <div class="col-7 fw-bold"><?= htmlspecialchars($pu['hafiz_nama'] ?? $pu['nama'] ?? '-') ?></div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <div class="col-5 text-muted">Username</div>
                                                    <div class="col-7 font-monospace"><?= htmlspecialchars($pu['username']) ?></div>
                                                </div>
                                            </div>
                                            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 mb-0">
                                                <small><i class="bi bi-exclamation-triangle me-1"></i><strong>Perhatian:</strong> Akun user akan dihapus dan hafiz harus mendaftar ulang jika ingin mengakses sistem.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <form action="<?= APP_URL ?>/admin/pending/<?= $pu['id'] ?>/reject" method="POST" class="d-inline">
                                                <?= csrfField() ?>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-x-lg me-1"></i>Ya, Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
