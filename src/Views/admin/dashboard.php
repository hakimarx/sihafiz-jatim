<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Dashboard Statistik</h4>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Huffadz</h6>
                        <h3 class="mb-0 text-success"><?= number_format($totalPendaftar) ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-people text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card" style="border-left-color: #0d6efd;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Lolos Seleksi</h6>
                        <h3 class="mb-0 text-primary"><?= number_format($totalLulus) ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-check-circle text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card" style="border-left-color: #6f42c1;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Laki-laki</h6>
                        <h3 class="mb-0" style="color: #6f42c1;"><?= number_format($totalLakiLaki) ?></h3>
                    </div>
                    <div class="rounded-circle p-3" style="background: rgba(111,66,193,0.1);">
                        <i class="bi bi-gender-male fs-4" style="color: #6f42c1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card" style="border-left-color: #e91e8c;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Perempuan</h6>
                        <h3 class="mb-0" style="color: #e91e8c;"><?= number_format($totalPerempuan) ?></h3>
                    </div>
                    <div class="rounded-circle p-3" style="background: rgba(233,30,140,0.1);">
                        <i class="bi bi-gender-female fs-4" style="color: #e91e8c;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Laporan Harian Summary -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card" style="border-left-color: #20c997;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Sudah Laporan</h6>
                        <h3 class="mb-0" style="color: #20c997;"><?= number_format($statsLaporan['hafiz_sudah_laporan'] ?? 0) ?></h3>
                        <small class="text-muted">dari <?= number_format($statsLaporan['total_hafiz'] ?? 0) ?> hafiz</small>
                    </div>
                    <div class="rounded-circle p-3" style="background: rgba(32,201,151,0.1);">
                        <i class="bi bi-journal-check fs-4" style="color: #20c997;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card" style="border-left-color: #fd7e14;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Belum Laporan</h6>
                        <h3 class="mb-0" style="color: #fd7e14;"><?= number_format($statsLaporan['hafiz_belum_laporan'] ?? 0) ?></h3>
                    </div>
                    <div class="rounded-circle p-3" style="background: rgba(253,126,20,0.1);">
                        <i class="bi bi-journal-x fs-4" style="color: #fd7e14;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card" style="border-left-color: #ffc107;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Laporan Pending</h6>
                        <h3 class="mb-0 text-warning"><?= number_format($statsLaporan['laporan_pending'] ?? 0) ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card" style="border-left-color: #198754;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Laporan Disetujui</h6>
                        <h3 class="mb-0 text-success"><?= number_format($statsLaporan['laporan_disetujui'] ?? 0) ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-check2-all text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($pendingApproval) && $pendingApproval > 0): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-bell-fill me-2 fs-5"></i>
        <div>
            <strong><?= $pendingApproval ?> Pendaftaran Baru</strong> menunggu persetujuan.
            <a href="<?= APP_URL ?>/admin/pending" class="alert-link ms-2">Lihat &raquo;</a>
        </div>
    </div>
<?php endif; ?>

<!-- Map Section -->
<div class="card mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-map me-2"></i>Peta Lokasi Kegiatan</h5>
        <small class="text-muted">Menampilkan 200 lokasi kegiatan terbaru</small>
    </div>
    <div class="card-body p-0">
        <div id="map" style="height: 400px; width: 100%;"></div>
    </div>
</div>

<!-- Leaflet Infrastructure -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Center of East Java
        var map = L.map('map').setView([-7.5360, 112.2384], 8);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var markers = <?= json_encode($mapMarkers) ?>;
        var markerGroup = L.featureGroup();

        markers.forEach(function(m) {
            if (m.lokasi) {
                // Parse "lat, lng" or "-lat, lng"
                var parts = m.lokasi.split(',');
                if (parts.length === 2) {
                    var lat = parseFloat(parts[0].trim());
                    var lng = parseFloat(parts[1].trim());

                    if (!isNaN(lat) && !isNaN(lng)) {
                        var popupContent = `
                            <div style="width: 200px;">
                                <strong>${m.hafiz_nama}</strong><br>
                                <small class="text-muted">${m.kabko_nama}</small><hr class="my-1">
                                <div><i class="bi bi-calendar-event me-1"></i> ${m.tanggal}</div>
                                <div><i class="bi bi-tag me-1"></i> ${m.jenis_kegiatan}</div>
                                ${m.foto ? `<img src="<?= APP_URL ?>${m.foto}" class="img-fluid mt-2 rounded" style="max-height: 100px; width: 100%; object-fit: cover;">` : ''}
                                <a href="<?= APP_URL ?>/admin/laporan?id=${m.id}" class="btn btn-xs btn-primary w-100 mt-2 py-1" style="font-size: 10px;">Detail Laporan</a>
                            </div>
                        `;

                        var marker = L.marker([lat, lng]).bindPopup(popupContent);
                        markerGroup.addLayer(marker);
                    }
                }
            }
        });

        markerGroup.addTo(map);

        if (markers.length > 0 && markerGroup.getLayers().length > 0) {
            map.fitBounds(markerGroup.getBounds().pad(0.1));
        }
    });
</script>

<!-- Rekap per Kabupaten/Kota -->
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            <i class="bi bi-geo-alt me-2"></i>
            <?php if (hasRole(ROLE_ADMIN_KABKO) && !empty($stats) && count($stats) === 1): ?>
                Rekap <?= htmlspecialchars($stats[0]['nama'] ?? '') ?>
            <?php else: ?>
                Rekap Huffadz per Kabupaten/Kota
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kabupaten/Kota</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Laki-laki</th>
                        <th class="text-center">Perempuan</th>
                        <th class="text-center">Lulus</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $i => $stat): ?>
                        <?php
                        // Find gender stats for this kabko
                        $genderRow = null;
                        foreach ($statsByGender as $gs) {
                            if ($gs['id'] == $stat['id']) {
                                $genderRow = $gs;
                                break;
                            }
                        }
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($stat['nama']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($stat['kode']) ?></small>
                            </td>
                            <td class="text-center"><?= number_format($stat['total_pendaftar'] ?? 0) ?></td>
                            <td class="text-center">
                                <span class="text-primary"><?= number_format($genderRow['laki_laki'] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <span style="color: #e91e8c;"><?= number_format($genderRow['perempuan'] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= number_format($stat['total_lulus'] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark"><?= number_format($stat['total_pending'] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <a href="<?= APP_URL ?>/admin/hafiz?kabupaten_kota_id=<?= $stat['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="2">TOTAL</td>
                        <td class="text-center"><?= number_format($totalPendaftar) ?></td>
                        <td class="text-center text-primary"><?= number_format($totalLakiLaki) ?></td>
                        <td class="text-center" style="color: #e91e8c;"><?= number_format($totalPerempuan) ?></td>
                        <td class="text-center"><?= number_format($totalLulus) ?></td>
                        <td class="text-center"><?= number_format($totalPending) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Statistik per Tahun Kelulusan -->
<?php if (!empty($statsByTahun)): ?>
    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-calendar-check me-2"></i>Statistik per Tahun Kelulusan
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th class="text-center">Total Lulus</th>
                            <th class="text-center">Laki-laki</th>
                            <th class="text-center">Perempuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statsByTahun as $row): ?>
                            <tr>
                                <td><strong><?= $row['tahun'] ?></strong></td>
                                <td class="text-center">
                                    <span class="badge bg-success fs-6"><?= number_format($row['total_lulus']) ?></span>
                                </td>
                                <td class="text-center text-primary"><?= number_format($row['laki_laki']) ?></td>
                                <td class="text-center" style="color: #e91e8c;"><?= number_format($row['perempuan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td>TOTAL</td>
                            <td class="text-center"><?= number_format(array_sum(array_column($statsByTahun, 'total_lulus'))) ?></td>
                            <td class="text-center text-primary"><?= number_format(array_sum(array_column($statsByTahun, 'laki_laki'))) ?></td>
                            <td class="text-center" style="color: #e91e8c;"><?= number_format(array_sum(array_column($statsByTahun, 'perempuan'))) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>