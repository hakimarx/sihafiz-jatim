<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-person me-2"></i>Profil Saya</h4>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="mb-3">
            <?php if (!empty($hafiz['foto_profil'])): ?>
                <img src="<?= APP_URL . htmlspecialchars($hafiz['foto_profil']) ?>" alt="Foto Profil" class="img-thumbnail rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
            <?php else: ?>
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4">
                    <i class="bi bi-person-circle text-success" style="font-size: 4rem;"></i>
                </div>
            <?php endif; ?>
        </div>
        <h5 class="mb-1"><?= htmlspecialchars($hafiz['nama']) ?></h5>
        <p class="text-muted mb-3"><?= htmlspecialchars($hafiz['kabupaten_kota_nama']) ?></p>
        <div class="mb-3">
            <span class="badge bg-<?= $hafiz['status_kelulusan'] === 'lulus' ? 'success' : ($hafiz['status_kelulusan'] === 'pending' ? 'warning' : 'danger') ?> mb-2 d-inline-block">
                Status Seleksi: <?= ucfirst(str_replace('_', ' ', $hafiz['status_kelulusan'])) ?>
            </span>
            <br>
            <span class="badge bg-<?= !empty($hafiz['foto_ktp']) ? 'info' : 'secondary' ?>">
                <i class="bi bi-card-image me-1"></i> KTP: <?= !empty($hafiz['foto_ktp']) ? 'Terunggah' : 'Belum Ada' ?>
            </span>
        </div>

        <div class="grid d-grid gap-2">
            <a href="<?= APP_URL ?>/hafiz/profil/edit" class="btn btn-primary">
                <i class="bi bi-pencil-square me-2"></i>Edit Profil
            </a>
            <a href="<?= APP_URL ?>/hafiz/password" class="btn btn-outline-secondary">
                <i class="bi bi-key me-2"></i>Ubah Password
            </a>
        </div>
    </div>
</div>

<?php if (!empty($hafiz['foto_ktp'])): ?>
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 small fw-bold">Scan KTP</h6>
        </div>
        <div class="card-body p-2">
            <img src="<?= APP_URL . htmlspecialchars($hafiz['foto_ktp']) ?>" alt="KTP" class="img-fluid rounded border shadow-sm">
        </div>
    </div>
<?php endif; ?>
</div>

<div class="col-lg-8">
    <div class="card">
        <div class="card-header bg-white">
            <h6 class="mb-0"><i class="bi bi-card-list me-2"></i>Data Pribadi</h6>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <td width="200" class="text-muted">NIK</td>
                    <td><code><?= htmlspecialchars($hafiz['nik']) ?></code></td>
                </tr>
                <tr>
                    <td class="text-muted">Nama Lengkap</td>
                    <td><strong><?= htmlspecialchars($hafiz['nama']) ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Tempat, Tanggal Lahir</td>
                    <td><?= htmlspecialchars($hafiz['tempat_lahir']) ?>, <?= date('d F Y', strtotime($hafiz['tanggal_lahir'])) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Jenis Kelamin</td>
                    <td><?= $hafiz['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Alamat</td>
                    <td>
                        <?= htmlspecialchars($hafiz['alamat']) ?>
                        <?php if ($hafiz['rt'] || $hafiz['rw']): ?>
                            RT <?= htmlspecialchars($hafiz['rt'] ?? '-') ?> / RW <?= htmlspecialchars($hafiz['rw'] ?? '-') ?>
                        <?php endif; ?>
                        <br>
                        <?= htmlspecialchars($hafiz['desa_kelurahan']) ?>, <?= htmlspecialchars($hafiz['kecamatan']) ?>
                        <br>
                        <?= htmlspecialchars($hafiz['kabupaten_kota_nama']) ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Telepon</td>
                    <td><?= htmlspecialchars($hafiz['telepon'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Email</td>
                    <td><?= htmlspecialchars($hafiz['email'] ?? '-') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-white">
            <h6 class="mb-0"><i class="bi bi-book me-2"></i>Data Hafalan</h6>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <td width="200" class="text-muted">Lembaga yang Mengeluarkan Sertifikat Tahfiz</td>
                    <td><?= htmlspecialchars($hafiz['sertifikat_tahfidz'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Mengajar</td>
                    <td><?= $hafiz['mengajar'] ? 'Ya' : 'Tidak' ?></td>
                </tr>
                <?php if ($hafiz['mengajar']): ?>
                    <tr>
                        <td class="text-muted">TMT Mengajar</td>
                        <td><?= $hafiz['tmt_mengajar'] ? date('d F Y', strtotime($hafiz['tmt_mengajar'])) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tempat Mengajar Utama</td>
                        <td><?= htmlspecialchars($hafiz['tempat_mengajar'] ?? '-') ?></td>
                    </tr>
                <?php endif; ?>

                <?php if (!empty($mengajarList)): ?>
                    <?php foreach ($mengajarList as $item): ?>
                        <tr>
                            <td class="text-muted">Tempat Mengajar Lainnya</td>
                            <td>
                                <?= htmlspecialchars($item['tempat_mengajar']) ?>
                                <small class="text-muted">(TMT: <?= date('d F Y', strtotime($item['tmt_mengajar'])) ?>)</small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr>
                    <td class="text-muted">Tahun Tes</td>
                    <td><?= htmlspecialchars($hafiz['tahun_tes']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Status Insentif</td>
                    <td>
                        <span class="badge bg-<?= $hafiz['status_insentif'] === 'aktif' ? 'success' : 'secondary' ?>">
                            <?= ucfirst(str_replace('_', ' ', $hafiz['status_insentif'])) ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <?php if ($hafiz['nama_bank'] || $hafiz['nomor_rekening']): ?>
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-bank me-2"></i>Data Bank</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="200" class="text-muted">Nama Bank</td>
                        <td><?= htmlspecialchars($hafiz['nama_bank'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nomor Rekening</td>
                        <td><code><?= htmlspecialchars($hafiz['nomor_rekening'] ?? '-') ?></code></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>
</div>