<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-person me-2"></i>Profil Saya</h4>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card text-center">
            <div class="card-body py-5">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                    <i class="bi bi-person-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="mb-1"><?= htmlspecialchars($hafiz['nama']) ?></h5>
                <p class="text-muted mb-3"><?= htmlspecialchars($hafiz['kabupaten_kota_nama']) ?></p>
                <span class="badge bg-<?= $hafiz['status_kelulusan'] === 'lulus' ? 'success' : ($hafiz['status_kelulusan'] === 'pending' ? 'warning' : 'danger') ?> fs-6">
                    <?= ucfirst(str_replace('_', ' ', $hafiz['status_kelulusan'])) ?>
                </span>
            </div>
        </div>
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
                        <td width="200" class="text-muted">Sertifikat Tahfidz</td>
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
                            <td class="text-muted">Tempat Mengajar</td>
                            <td><?= htmlspecialchars($hafiz['tempat_mengajar'] ?? '-') ?></td>
                        </tr>
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

        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle me-2"></i>
            Untuk mengubah data profil, silakan hubungi Admin Kabupaten/Kota Anda.
        </div>
    </div>
</div>