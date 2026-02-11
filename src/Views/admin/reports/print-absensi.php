<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Absensi Kegiatan') ?></title>
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
            width: 210mm;
            margin: 0 auto;
            padding: 15mm 20mm;
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
            font-size: 10pt;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
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

        table.data-table td.sign-col {
            width: 80px;
            height: 40px;
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

        .no-print {
            text-align: center;
            margin: 20px 0;
        }

        .btn-print {
            padding: 10px 30px;
            font-size: 14pt;
            cursor: pointer;
            background: #343a40;
            color: #fff;
            border: none;
            border-radius: 5px;
            margin: 0 5px;
        }

        .btn-print:hover {
            background: #23272b;
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
                padding: 10mm 15mm;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Absensi</button>
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
            <h2>Daftar Hadir Kegiatan</h2>
            <p><strong><?= htmlspecialchars($namaKegiatan ?? 'Kegiatan Pembinaan') ?></strong></p>
        </div>

        <?php
        $bulanNames = [
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
        $tanggalFormatted = '-';
        if (!empty($tanggalKegiatan)) {
            $dt = strtotime($tanggalKegiatan);
            $tanggalFormatted = date('d', $dt) . ' ' . ($bulanNames[(int)date('n', $dt)] ?? '') . ' ' . date('Y', $dt);
        }
        ?>

        <!-- Info -->
        <div class="info-box">
            <table>
                <tr>
                    <td><strong>Nama Kegiatan</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($namaKegiatan ?? '-') ?></td>
                </tr>
                <tr>
                    <td><strong>Tanggal</strong></td>
                    <td>:</td>
                    <td><?= $tanggalFormatted ?></td>
                </tr>
                <tr>
                    <td><strong>Kabupaten/Kota</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($kabko['nama'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td><strong>Tempat</strong></td>
                    <td>:</td>
                    <td>..................................................................</td>
                </tr>
            </table>
        </div>

        <!-- Tabel Absensi -->
        <?php if (!empty($hafiz)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>Alamat</th>
                        <th>Desa/Kelurahan</th>
                        <th>Kecamatan</th>
                        <th style="width: 80px;">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($hafiz as $h): ?>
                        <tr>
                            <td class="center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($h['nik'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['nama'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['alamat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['desa_kelurahan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['kecamatan'] ?? '-') ?></td>
                            <td class="sign-col"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Total Peserta:</strong> <?= count($hafiz) ?> orang</p>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; border: 1px solid #ccc;">
                <p><em>Tidak ada data hafiz yang lulus untuk kabupaten/kota ini.</em></p>
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
                <p><?= htmlspecialchars($kabko['nama'] ?? 'Surabaya') ?>, <?= $tanggalFormatted ?></p>
                <p><strong>Penanggung Jawab</strong></p>
                <div class="line"></div>
                <p>NIP. .....................</p>
            </div>
        </div>
    </div>
</body>

</html>