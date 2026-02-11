<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Data Hafiz') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
        }

        .page {
            width: 297mm;
            /* Landscape A4 */
            margin: 0 auto;
            padding: 10mm 15mm;
        }

        /* Header Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 80px;
            height: 80px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .kop-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
            flex: 1;
        }

        .kop-text h3 {
            font-size: 14pt;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop-text h2 {
            font-size: 16pt;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .kop-text p {
            font-size: 10pt;
            margin-bottom: 0;
        }

        .report-title {
            text-align: center;
            margin: 20px 0;
        }

        .report-title h2 {
            font-size: 14pt;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .report-title p {
            font-size: 11pt;
        }

        .info-box {
            margin-bottom: 15px;
        }

        .info-box table td {
            padding: 2px 10px 2px 0;
            vertical-align: top;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
        }

        table.data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        table.data-table td.center {
            text-align: center;
        }

        .badge-lulus {
            background: #198754;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8pt;
        }

        .badge-pending {
            background: #ffc107;
            color: #000;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8pt;
        }

        .badge-tidak-lulus {
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8pt;
        }

        .report-footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-block {
            text-align: center;
            width: 200px;
        }

        .signature-block .line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }

        .summary {
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }

        .summary span {
            margin-right: 20px;
        }

        .no-print {
            text-align: center;
            margin: 20px 0;
        }

        .btn-print {
            padding: 10px 30px;
            font-size: 14pt;
            cursor: pointer;
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 5px;
            margin: 0 5px;
        }

        .btn-print:hover {
            background: #0b5ed7;
        }

        .btn-back {
            padding: 10px 30px;
            font-size: 14pt;
            cursor: pointer;
            background: #6c757d;
            color: #fff;
            border: none;
            border-radius: 5px;
            margin: 0 5px;
            text-decoration: none;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .page {
                padding: 5mm 10mm;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: landscape;
                margin: 5mm;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Data Hafiz</button>
        <a class="btn-back" href="<?= APP_URL ?>/admin/reports">← Kembali</a>
    </div>

    <div class="page">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <div class="kop-logo">
                <?php
                $logo = Setting::get('app_logo');
                if ($logo): ?>
                    <img src="<?= APP_URL . $logo ?>" alt="Logo LPTQ">
                <?php else: ?>
                    <img src="<?= APP_URL ?>/assets/images/logo-lptq.png" alt="Logo LPTQ" onerror="this.style.display='none'">
                <?php endif; ?>
            </div>
            <div class="kop-text">
                <h3>Lembaga Pengembangan Tilawatil Quran</h3>
                <h2>Provinsi Jawa Timur</h2>
                <p>Jl. Raya Juanda No. 1, Surabaya | Telp: (031) 8686868</p>
            </div>
            <div class="kop-logo"></div>
        </div>

        <!-- Judul Laporan -->
        <div class="report-title">
            <h2>Data Hafiz per Kabupaten/Kota</h2>
            <p>Kabupaten/Kota: <strong><?= htmlspecialchars($kabko['nama'] ?? '-') ?></strong></p>
        </div>

        <!-- Info -->
        <div class="info-box">
            <table>
                <tr>
                    <td><strong>Kabupaten/Kota</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($kabko['nama'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td><strong>Filter Status</strong></td>
                    <td>:</td>
                    <td>
                        <?php
                        $statusLabels = [
                            'all' => 'Semua Status',
                            'lulus' => 'Lulus',
                            'pending' => 'Pending',
                            'tidak_lulus' => 'Tidak Lulus'
                        ];
                        echo $statusLabels[$status ?? 'all'] ?? 'Semua';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Tanggal Cetak</strong></td>
                    <td>:</td>
                    <td><?= date('d F Y') ?></td>
                </tr>
            </table>
        </div>

        <!-- Tabel data -->
        <?php if (!empty($hafiz)): ?>

            <!-- Summary -->
            <div class="summary">
                <strong>Ringkasan:</strong>
                <span>Total: <?= count($hafiz) ?> Hafiz</span>
                <?php
                $lulus = 0;
                $pending = 0;
                $tidakLulus = 0;
                $laki = 0;
                $perempuan = 0;
                foreach ($hafiz as $h) {
                    if (($h['status_kelulusan'] ?? '') === 'lulus') $lulus++;
                    elseif (($h['status_kelulusan'] ?? '') === 'tidak_lulus') $tidakLulus++;
                    else $pending++;
                    if (($h['jenis_kelamin'] ?? '') === 'L') $laki++;
                    else $perempuan++;
                }
                ?>
                <span>Lulus: <?= $lulus ?></span>
                <span>Tidak Lulus: <?= $tidakLulus ?></span>
                <span>Pending: <?= $pending ?></span>
                <span>|</span>
                <span>Laki-laki: <?= $laki ?></span>
                <span>Perempuan: <?= $perempuan ?></span>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Tempat, Tgl Lahir</th>
                        <th>Alamat</th>
                        <th>Desa/Kelurahan</th>
                        <th>Kecamatan</th>
                        <th>No. Telepon</th>
                        <th>Sertifikat Tahfidz</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($hafiz as $h): ?>
                        <tr>
                            <td class="center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($h['nik'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['nama'] ?? '-') ?></td>
                            <td class="center"><?= htmlspecialchars($h['jenis_kelamin'] ?? '-') ?></td>
                            <td><?= htmlspecialchars(($h['tempat_lahir'] ?? '') . ', ' . (isset($h['tanggal_lahir']) ? date('d/m/Y', strtotime($h['tanggal_lahir'])) : '-')) ?></td>
                            <td><?= htmlspecialchars($h['alamat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['desa_kelurahan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['kecamatan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['telepon'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['sertifikat_tahfidz'] ?? '-') ?></td>
                            <td class="center">
                                <?php
                                $st = $h['status_kelulusan'] ?? 'pending';
                                if ($st === 'lulus') echo '<span class="badge-lulus">LULUS</span>';
                                elseif ($st === 'tidak_lulus') echo '<span class="badge-tidak-lulus">TIDAK LULUS</span>';
                                else echo '<span class="badge-pending">PENDING</span>';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; border: 1px solid #ccc;">
                <p><em>Tidak ada data hafiz untuk filter ini.</em></p>
            </div>
        <?php endif; ?>

        <!-- Footer TTD -->
        <div class="report-footer">
            <div class="signature-block">
                <p>Mengetahui,</p>
                <p><strong>Ketua LPTQ</strong></p>
                <div class="line"></div>
                <p>NIP. .....................</p>
            </div>
            <div class="signature-block">
                <p>Surabaya, <?= date('d F Y') ?></p>
                <p><strong>Admin <?= htmlspecialchars($kabko['nama'] ?? '') ?></strong></p>
                <div class="line"></div>
                <p>NIP. .....................</p>
            </div>
        </div>
    </div>
</body>

</html>