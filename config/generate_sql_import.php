<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('memory_limit', '512M');

echo "Memulai proses generate SQL (v2.0 - dengan pembersihan NIK & daerah)...\n";

$csvFile = realpath(__DIR__ . '/../Book1.csv');
$outputFile = __DIR__ . '/import_data_huffaz.sql';

if (!$csvFile || !file_exists($csvFile)) {
    die("ERROR: File CSV tidak ditemukan di: " . __DIR__ . '/../Book1.csv' . "\n");
}

$handle = fopen($csvFile, "r");
if (!$handle) {
    die("ERROR: Gagal membuka file CSV.\n");
}

$output = fopen($outputFile, "w");
if (!$output) {
    die("ERROR: Gagal membuat file output SQL.\n");
}

// ============================================
// MAPPING NAMA DAERAH → TABEL kabupaten_kota
// ============================================
// Normalisasi nama daerah dari CSV ke nama di database
$daerahMapping = [
    'Kabupaten Bangkalan'   => 'Kabupaten Bangkalan',
    'Kabupaten Banyuwangi'  => 'Kabupaten Banyuwangi',
    'Kabupaten Blitar'      => 'Kabupaten Blitar',
    'Kabupaten Bojonegoro'  => 'Kabupaten Bojonegoro',
    'Kabupaten Bondowoso'   => 'Kabupaten Bondowoso',
    'Kabupaten Gresik'      => 'Kabupaten Gresik',
    'Kabupaten Jember'      => 'Kabupaten Jember',
    'Kabupaten Jombang'     => 'Kabupaten Jombang',
    'Kabupaten Kediri'      => 'Kabupaten Kediri',
    'Kabupaten Lamongan'    => 'Kabupaten Lamongan',
    'Kabupaten Lumajang'    => 'Kabupaten Lumajang',
    'Kabupaten Madiun'      => 'Kabupaten Madiun',
    'Kabupaten Magetan'     => 'Kabupaten Magetan',
    'Kabupaten Malang'      => 'Kabupaten Malang',
    'Kabupaten Mojokerto'   => 'Kabupaten Mojokerto',
    'Kabupaten Nganjuk'     => 'Kabupaten Nganjuk',
    'Kabupaten Ngawi'       => 'Kabupaten Ngawi',
    'Kabupaten Pacitan'     => 'Kabupaten Pacitan',
    'Kabupaten Pamekasan'   => 'Kabupaten Pamekasan',
    'Kabupaten Pasuruan'    => 'Kabupaten Pasuruan',
    'Kabupaten Ponorogo'    => 'Kabupaten Ponorogo',
    'Kabupaten Probolinggo' => 'Kabupaten Probolinggo',
    'Kabupaten Sampang'     => 'Kabupaten Sampang',
    'Kabupaten Sidoarjo'    => 'Kabupaten Sidoarjo',
    'Kabupaten Situbondo'   => 'Kabupaten Situbondo',
    'Kabupaten Sumenep'     => 'Kabupaten Sumenep',
    'Kabupaten Trenggalek'  => 'Kabupaten Trenggalek',
    'Kabupaten Tuban'       => 'Kabupaten Tuban',
    'Kabupaten Tulungagung' => 'Kabupaten Tulungagung',
    'Kota Batu'             => 'Kota Batu',
    'Kota Blitar'           => 'Kota Blitar',
    'Kota Kediri'           => 'Kota Kediri',
    'Kota Madiun'           => 'Kota Madiun',
    'Kota Malang'           => 'Kota Malang',
    'Kota Mojokerto'        => 'Kota Mojokerto',
    'Kota Pasuruan'         => 'Kota Pasuruan',
    'Kota Probolinggo'      => 'Kota Probolinggo',
    'Kota Surabaya'         => 'Kota Surabaya',
];

// ============================================
// FUNGSI PEMBERSIHAN NIK
// ============================================
function cleanNik($nik)
{
    // Hapus spasi leading/trailing
    $nik = trim($nik);

    // Hapus karakter non-digit (X, ?, huruf, dsb)
    $cleaned = preg_replace('/[^0-9]/', '', $nik);

    // Jika setelah dibersihkan hasilnya 16 digit, gunakan
    if (strlen($cleaned) === 16) {
        return ['nik' => $cleaned, 'status' => 'cleaned', 'original' => $nik];
    }

    // Jika 15 digit dan dimulai dengan angka valid provinsi (35 = Jatim), tambah 0 di depan
    if (strlen($cleaned) === 15 && substr($cleaned, 0, 1) === '5') {
        $cleaned = '3' . $cleaned;
        if (strlen($cleaned) === 16) {
            return ['nik' => $cleaned, 'status' => 'fixed_prefix', 'original' => $nik];
        }
    }

    // Jika kosong, return null
    if (empty($cleaned)) {
        return ['nik' => null, 'status' => 'empty', 'original' => $nik];
    }

    // Untuk NIK yang tetap tidak valid, return original cleaned (dipakai sebagai identifier)
    return ['nik' => $cleaned, 'status' => 'invalid', 'original' => $nik];
}

function normalizeDaerah($asal, $mapping)
{
    $asal = trim($asal);

    // Cek exact match setelah trim
    if (isset($mapping[$asal])) {
        return $mapping[$asal];
    }

    // Cek case-insensitive match
    foreach ($mapping as $key => $value) {
        if (strcasecmp($asal, $key) === 0) {
            return $value;
        }
    }

    // Cek partial match (LIKE)
    foreach ($mapping as $key => $value) {
        if (stripos($asal, $key) !== false || stripos($key, $asal) !== false) {
            return $value;
        }
    }

    return $asal; // Return as-is if no match
}

// ============================================
// HEADER SQL
// ============================================
fwrite($output, "-- ============================================\n");
fwrite($output, "-- Script Import Data Huffaz dari Book1.csv\n");
fwrite($output, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
fwrite($output, "-- Versi 2.0 - dengan pembersihan NIK & daerah\n");
fwrite($output, "-- ============================================\n\n");
fwrite($output, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
fwrite($output, "SET FOREIGN_KEY_CHECKS = 0;\n");
fwrite($output, "SET time_zone = \"+07:00\";\n");
fwrite($output, "START TRANSACTION;\n\n");

// Baca header
$header = fgetcsv($handle, 2000, ";", '"', '');
if (!$header) {
    die("ERROR: Gagal membaca header CSV.\n");
}
echo "Header: " . implode(";", $header) . "\n";

$count = 0;
$countValid = 0;
$countCleaned = 0;
$countInvalid = 0;
$countSkipped = 0;
$tempCounter = 0;
$nikLog = []; // Log NIK yang bermasalah

while (($data = fgetcsv($handle, 2000, ";", '"', '')) !== FALSE) {
    if (count($data) < 6) continue;

    $asal_nama = isset($data[3]) ? trim($data[3]) : '';
    $nik_raw = isset($data[4]) ? trim($data[4]) : '';
    $nama = isset($data[5]) ? trim($data[5]) : '';
    $tempat_lahir = isset($data[6]) ? trim($data[6]) : '';
    $tgl_lahir_str = isset($data[8]) ? trim($data[8]) : '';
    $jk_str = isset($data[10]) ? trim($data[10]) : '';
    $alamat = isset($data[11]) ? trim($data[11]) : '';
    $rt = isset($data[12]) ? trim($data[12]) : '';
    $rw = isset($data[13]) ? trim($data[13]) : '';
    $desa = isset($data[14]) ? trim($data[14]) : '';
    $kecamatan = isset($data[15]) ? trim($data[15]) : '';
    // Tambahan kolom baru
    $sertifikat = isset($data[17]) ? trim($data[17]) : '';
    $tempat_mengajar = isset($data[18]) ? trim($data[18]) : '';
    $tmt_mengajar_str = isset($data[19]) ? trim($data[19]) : '';
    $telepon = isset($data[20]) ? trim($data[20]) : ''; // Kolom 20 TELEPON
    $keterangan = isset($data[21]) ? trim($data[21]) : '';
    $tahun_lulus = isset($data[22]) ? trim($data[22]) : '';

    $tahun_masuk = isset($data[1]) ? intval($data[1]) : 2023;

    // Skip baris tanpa nama
    if (empty($nama)) {
        $countSkipped++;
        continue;
    }

    // ============================================
    // PEMBERSIHAN NIK
    // ============================================
    $nikResult = cleanNik($nik_raw);

    if ($nikResult['status'] === 'empty' || $nikResult['nik'] === null) {
        $tempCounter++;
        $nikResult['nik'] = 'TEMP' . str_pad($tempCounter, 12, '0', STR_PAD_LEFT);
        $nikResult['status'] = 'temp_generated';
        $nikLog[] = "TEMP: {$nikResult['nik']} ← Nama: $nama (NIK kosong)";
    } elseif ($nikResult['status'] === 'invalid') {
        $nikLog[] = "INVALID: {$nikResult['nik']} ← Original: {$nikResult['original']} - Nama: $nama";
        $countInvalid++;
    } elseif ($nikResult['status'] === 'cleaned' || $nikResult['status'] === 'fixed_prefix') {
        $countCleaned++;
        if ($nikResult['original'] !== $nikResult['nik']) {
            $nikLog[] = "CLEANED: {$nikResult['nik']} ← Original: {$nikResult['original']} - Nama: $nama";
        }
    }

    $nik = $nikResult['nik'];

    // ============================================
    // NORMALISASI DAERAH
    // ============================================
    $asal_clean = normalizeDaerah($asal_nama, $daerahMapping);

    // Sanitasi untuk SQL - Hapus newline dan extra spaces
    $escaped_nama = addslashes(trim(preg_replace('/\s+/', ' ', $nama)));
    $escaped_nik = addslashes(trim(preg_replace('/\s+/', ' ', $nik)));
    $escaped_alamat = addslashes(trim(preg_replace('/\s+/', ' ', $alamat)));
    $escaped_tempat = addslashes(trim(preg_replace('/\s+/', ' ', $tempat_lahir)));
    $asal_escaped = addslashes(trim(preg_replace('/\s+/', ' ', $asal_clean)));
    $escaped_desa = addslashes(trim(preg_replace('/\s+/', ' ', $desa)));
    $escaped_kecamatan = addslashes(trim(preg_replace('/\s+/', ' ', $kecamatan)));
    $escaped_rt = addslashes(trim(preg_replace('/\s+/', ' ', $rt)));
    $escaped_rw = addslashes(trim(preg_replace('/\s+/', ' ', $rw)));
    
    // Sanitasi kolom baru
    $escaped_sertifikat = addslashes(trim(preg_replace('/\s+/', ' ', $sertifikat)));
    $escaped_tempat_mengajar = addslashes(trim(preg_replace('/\s+/', ' ', $tempat_mengajar)));
    $escaped_telepon = addslashes(trim(preg_replace('/\s+/', ' ', $telepon)));
    $escaped_keterangan = addslashes(trim(preg_replace('/\s+/', ' ', $keterangan)));
    
    // Logic Mengajar (Boolean)
    $is_mengajar = !empty($tempat_mengajar) ? 1 : 0;

    // Kabupaten ID
    $asal_sql = "(SELECT id FROM kabupaten_kota WHERE nama = '$asal_escaped' LIMIT 1)";
    $asal_sql_safe = "COALESCE($asal_sql, 1)";

    // Tanggal Lahir (YYYY-MM-DD)
    // Default 2000-01-01 agar tidak error di database yang setting-nya NOT NULL
    $tgl_lahir_sql = "'2000-01-01'"; 
    if (!empty($tgl_lahir_str)) {
        $dt = DateTime::createFromFormat('d/m/Y', $tgl_lahir_str);
        if (!$dt) $dt = DateTime::createFromFormat('Y-m-d', $tgl_lahir_str);
        // Fallback untuk tanggal 2 digit tahun yg mungkin terdeteksi masa depan
        if ($dt && $dt->format('Y') > date('Y')) {
             $dt = DateTime::createFromFormat('d/m/y', $tgl_lahir_str); 
        }
        if ($dt) $tgl_lahir_sql = "'" . $dt->format('Y-m-d') . "'";
    }

    // TMT Mengajar
    $tmt_sql = "NULL";
    if (!empty($tmt_mengajar_str)) {
        // Coba format DD/MM/YYYY
        $dt = DateTime::createFromFormat('d/m/Y', $tmt_mengajar_str);
        if (!$dt) $dt = DateTime::createFromFormat('Y-m-d', $tmt_mengajar_str); // Coba YYYY-MM-DD
        if (!$dt) $dt = DateTime::createFromFormat('d-m-Y', $tmt_mengajar_str); // Coba DD-MM-YYYY
        
        if ($dt) $tmt_sql = "'" . $dt->format('Y-m-d') . "'";
    }

    // Tanggal Lulus (Ambil dari Tahun Lulus -> YYYY-01-01)
    $tgl_lulus_sql = "NULL";
    if (!empty($tahun_lulus) && is_numeric($tahun_lulus) && strlen($tahun_lulus) == 4) {
        $tgl_lulus_sql = "'{$tahun_lulus}-01-01'";
    }

    // Jenis Kelamin
    $jk = (stripos($jk_str, 'P') !== false || stripos($jk_str, 'W') !== false) ? 'P' : 'L';

    // Password Hash (Cost 4 agar lebih cepat untuk proses import massal awal)
    $password_hash = password_hash($nik, PASSWORD_DEFAULT, ['cost' => 4]);

    // Komentar data
    fwrite($output, "-- Data: $escaped_nama ($escaped_nik)\n");

    // INSERT users
    $sql = "INSERT INTO `users` (`username`, `password`, `role`, `nama`, `is_active`, `kabupaten_kota_id`, `telepon`) VALUES ";
    $sql .= "('$escaped_nik', '$password_hash', 'hafiz', '$escaped_nama', 1, $asal_sql_safe, '$escaped_telepon') ";
    $sql .= "ON DUPLICATE KEY UPDATE `nama`='$escaped_nama', `kabupaten_kota_id`=$asal_sql_safe, `telepon`='$escaped_telepon';\n";

    // SET user_id variable
    $sql .= "SET @last_user_id = (SELECT id FROM `users` WHERE `username`='$escaped_nik' LIMIT 1);\n";

    // INSERT hafiz
    $sql .= "INSERT INTO `hafiz` (`user_id`, `nik`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, ";
    $sql .= "`alamat`, `kabupaten_kota_id`, `tahun_tes`, `desa_kelurahan`, `kecamatan`, `rt`, `rw`, ";
    $sql .= "`telepon`, `sertifikat_tahfidz`, `mengajar`, `tempat_mengajar`, `tmt_mengajar`, `tanggal_lulus`, `keterangan`) VALUES ";
    
    $sql .= "(@last_user_id, '$escaped_nik', '$escaped_nama', '$escaped_tempat', $tgl_lahir_sql, '$jk', ";
    $sql .= "'$escaped_alamat', $asal_sql_safe, $tahun_masuk, '$escaped_desa', '$escaped_kecamatan', '$escaped_rt', '$escaped_rw', ";
    $sql .= "'$escaped_telepon', '$escaped_sertifikat', $is_mengajar, '$escaped_tempat_mengajar', $tmt_sql, $tgl_lulus_sql, '$escaped_keterangan') ";
    
    $sql .= "ON DUPLICATE KEY UPDATE `nama`='$escaped_nama', `alamat`='$escaped_alamat', ";
    $sql .= "`desa_kelurahan`='$escaped_desa', `kecamatan`='$escaped_kecamatan', `kabupaten_kota_id`=$asal_sql_safe, ";
    $sql .= "`telepon`='$escaped_telepon', `sertifikat_tahfidz`='$escaped_sertifikat', `mengajar`=$is_mengajar, ";
    $sql .= "`tempat_mengajar`='$escaped_tempat_mengajar', `tmt_mengajar`=$tmt_sql, `tanggal_lulus`=$tgl_lulus_sql, `keterangan`='$escaped_keterangan';\n\n";

    fwrite($output, $sql);

    $count++;
    $countValid++;
    if ($count % 500 == 0) {
        echo "Processed $count rows...\n";
    }
}

fwrite($output, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
fwrite($output, "COMMIT;\n");

fclose($handle);
fclose($output);

// Tulis log NIK bermasalah
$logFile = __DIR__ . '/nik_issues_log.txt';
file_put_contents(
    $logFile,
    "=== LOG NIK BERMASALAH ===\n" .
        "Generated: " . date('Y-m-d H:i:s') . "\n" .
        "Total records: $count\n" .
        "NIK valid: $countValid\n" .
        "NIK dibersihkan: $countCleaned\n" .
        "NIK invalid (tetap): $countInvalid\n" .
        "Baris dilewati: $countSkipped\n" .
        "NIK temp generated: $tempCounter\n" .
        "================================\n\n" .
        implode("\n", $nikLog) . "\n"
);

echo "\n=== SELESAI ===\n";
echo "Total records diproses: $count\n";
echo "NIK dibersihkan: $countCleaned\n";
echo "NIK invalid: $countInvalid\n";
echo "NIK temp generated: $tempCounter\n";
echo "Baris dilewati (tanpa nama): $countSkipped\n";
echo "File output: $outputFile\n";
echo "Log NIK: $logFile\n";
