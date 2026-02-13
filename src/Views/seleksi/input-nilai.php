<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Input Nilai Seleksi</h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-person text-success fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0"><?= htmlspecialchars($hafiz['nama']) ?></h5>
                        <small class="text-muted">
                            NIK: <?= htmlspecialchars($hafiz['nik']) ?> &bull;
                            <?= htmlspecialchars($hafiz['kabupaten_kota_nama']) ?>
                        </small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= APP_URL ?>/seleksi/<?= $hafiz['id'] ?>/nilai" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="tahun" value="<?= $tahun ?>">

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai Wawasan Kebangsaan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-lg text-center" name="nilai_wawasan"
                                    min="0" max="100" step="0.1" required
                                    value="<?= htmlspecialchars($seleksi['nilai_wawasan'] ?? '') ?>"
                                    id="nilaiWawasan" oninput="calculateTotal()">
                                <span class="input-group-text">/100</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai Hafalan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-lg text-center" name="nilai_hafalan"
                                    min="0" max="100" step="0.1" required
                                    value="<?= htmlspecialchars($seleksi['nilai_hafalan'] ?? '') ?>"
                                    id="nilaiHafalan" oninput="calculateTotal()">
                                <span class="input-group-text">/100</span>
                            </div>
                        </div>
                    </div>

                    <!-- Live Score Preview -->
                    <div class="card bg-light mb-4">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Nilai Total (Rata-rata)</h6>
                            <h1 class="display-4 mb-1" id="nilaiTotal">-</h1>
                            <span class="badge fs-6" id="statusBadge">-</span>
                            <p class="text-muted small mt-2 mb-0">Nilai minimum lulus: 70</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Tes</label>
                            <input type="datetime-local" class="form-control" name="tanggal_tes"
                                value="<?= ($seleksi['tanggal_tes'] ?? null) ? date('Y-m-d\TH:i', strtotime($seleksi['tanggal_tes'])) : date('Y-m-d\TH:i') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="3"
                            placeholder="Catatan tambahan (opsional)"><?= htmlspecialchars($seleksi['catatan'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="<?= APP_URL ?>/seleksi?tahun=<?= $tahun ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-lg me-1"></i> Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Data Hafiz -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Data Peserta</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Tempat/Tgl Lahir</td>
                        <td><?= htmlspecialchars($hafiz['tempat_lahir']) ?>, <?= date('d/m/Y', strtotime($hafiz['tanggal_lahir'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Kelamin</td>
                        <td><?= $hafiz['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat</td>
                        <td><?= htmlspecialchars($hafiz['kecamatan']) ?>, <?= htmlspecialchars($hafiz['kabupaten_kota_nama']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Hafalan</td>
                        <td><strong><?= htmlspecialchars($hafiz['sertifikat_tahfidz'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Mengajar</td>
                        <td><?= $hafiz['mengajar'] ? 'Ya - ' . htmlspecialchars($hafiz['tempat_mengajar'] ?? '') : 'Tidak' ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Panduan -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bi bi-lightbulb me-2"></i>Panduan Penilaian
            </div>
            <div class="card-body">
                <h6>Komponen Nilai:</h6>
                <ul class="small mb-3">
                    <li><strong>Wawasan Kebangsaan</strong> (50%)</li>
                    <li><strong>Hafalan Al-Quran</strong> (50%)</li>
                </ul>

                <h6>Kriteria Kelulusan:</h6>
                <ul class="small mb-0">
                    <li>Nilai Total ≥ 70: <span class="text-success fw-bold">LULUS</span></li>
                    <li>Nilai Total < 70: <span class="text-danger fw-bold">TIDAK LULUS</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateTotal() {
        const wawasan = parseFloat(document.getElementById('nilaiWawasan').value) || 0;
        const hafalan = parseFloat(document.getElementById('nilaiHafalan').value) || 0;
        const totalEl = document.getElementById('nilaiTotal');
        const badgeEl = document.getElementById('statusBadge');

        if (wawasan > 0 || hafalan > 0) {
            const total = (wawasan + hafalan) / 2;
            totalEl.textContent = total.toFixed(1);

            if (total >= 70) {
                totalEl.className = 'display-4 mb-1 text-success';
                badgeEl.textContent = 'LULUS';
                badgeEl.className = 'badge fs-6 bg-success';
            } else {
                totalEl.className = 'display-4 mb-1 text-danger';
                badgeEl.textContent = 'TIDAK LULUS';
                badgeEl.className = 'badge fs-6 bg-danger';
            }
        } else {
            totalEl.textContent = '-';
            totalEl.className = 'display-4 mb-1';
            badgeEl.textContent = '-';
            badgeEl.className = 'badge fs-6 bg-secondary';
        }
    }

    // Calculate on page load if values exist
    document.addEventListener('DOMContentLoaded', calculateTotal);
</script>