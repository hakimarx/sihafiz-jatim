<?php
// Layout Admin is handled by the Controller.
?>

<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-printer me-2"></i>Cetak Laporan</h4>
</div>

<div class="row">
    <!-- Card 1: Laporan Harian -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-success"><i class="bi bi-journal-text me-2"></i>Laporan Harian</h5>
            </div>
            <div class="card-body">
                <form action="<?= APP_URL ?>/admin/reports/print/harian" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label">Kabupaten/Kota</label>
                        <select name="kabupaten_kota_id" class="form-select" required>
                            <?php foreach ($kabupatenKota as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                <?php
                                $months = [
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember'
                                ];
                                $currentMonth = date('n');
                                foreach ($months as $num => $name): ?>
                                    <option value="<?= $num ?>" <?= $num == $currentMonth ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select" required>
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $currentYear - 2; $y--): ?>
                                    <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-success">
                            <i class="bi bi-printer me-2"></i>Cetak Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Card 2: Data Hafiz -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="bi bi-people me-2"></i>Data Hafiz</h5>
            </div>
            <div class="card-body">
                <form action="<?= APP_URL ?>/admin/reports/print/hafiz" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label">Kabupaten/Kota</label>
                        <select name="kabupaten_kota_id" class="form-select" required>
                            <?php foreach ($kabupatenKota as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Kelulusan</label>
                        <select name="status_kelulusan" class="form-select">
                            <option value="all">Semua Status</option>
                            <option value="lulus" selected>Lulus</option>
                            <option value="pending">Pending</option>
                            <option value="tidak_lulus">Tidak Lulus</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-printer me-2"></i>Cetak Data Hafiz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Card 3: Absensi Kegiatan -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-dark"><i class="bi bi-calendar-check me-2"></i>Absensi Kegiatan</h5>
            </div>
            <div class="card-body">
                <form action="<?= APP_URL ?>/admin/reports/print/absensi" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label">Kabupaten/Kota</label>
                        <select name="kabupaten_kota_id" class="form-select" required>
                            <?php foreach ($kabupatenKota as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control" placeholder="Contoh: Pembinaan Tahap 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal_kegiatan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-dark">
                            <i class="bi bi-printer me-2"></i>Cetak Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>