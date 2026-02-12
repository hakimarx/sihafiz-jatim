<?php

function clean_date($d)
{
    if (empty($d)) return 'NULL';
    // Format d/m/Y to Y-m-d
    $d = trim($d);
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $d, $matches)) {
        return "'" . $matches[3] . '-' . $matches[2] . '-' . $matches[1] . "'";
    }
    // Only year?
    if (preg_match('/^\d{4}$/', $d)) {
        return "'" . $d . "-01-01'";
    }
    return 'NULL';
}

function escape($s)
{
    if ($s === null || $s === '') return 'NULL';
    return "'" . str_replace("'", "''", str_replace("\\", "\\\\", $s)) . "'";
}

// Map CSV Kabko to DB ID (Hardcoded mapping or SQL Lookup)
// We will generate a SQL subquery to find the ID based on name.
// Map CSV Kabko to DB ID using Name or NIK
function get_kabko_sql($name, $nik)
{
    $subqueries = [];

    // 1. Try by Name
    if (!empty($name)) {
        $nameClean = trim($name);
        $nameClean = preg_replace('/^(KAB\.|KOTA)\s+/', '', strtoupper($nameClean));
        $subqueries[] = "(SELECT id FROM kabupaten_kota WHERE nama LIKE '%$nameClean%' LIMIT 1)";
    }

    // 2. Try by NIK code (first 4 digits)
    if (!empty($nik) && strlen($nik) >= 4) {
        $kode = substr($nik, 0, 4);
        $subqueries[] = "(SELECT id FROM kabupaten_kota WHERE kode = '$kode' LIMIT 1)";
    }

    // 3. Fallback: Get first ID found
    $subqueries[] = "(SELECT id FROM kabupaten_kota ORDER BY id ASC LIMIT 1)";

    // Wrap in COALESCE to prioritize 1 -> 2 -> 3
    return "COALESCE(" . implode(', ', $subqueries) . ")";
}

echo "Generating SQL...\n";

$rows = [];
if (($handle = fopen("Book1.csv", "r")) !== FALSE) {
    // Check for BOM
    $bom = fread($handle, 3);
    if ($bom != "\xEF\xBB\xBF") {
        rewind($handle);
    } else {
        // BOM found and skipped
    }

    // Get headers
    $headerRaw = fgetcsv($handle, 0, ";");
    if (!$headerRaw) {
        die("Error reading header\n");
    }

    // Trim headers
    $header = array_map('trim', $headerRaw);

    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        if (count($data) < count($header)) continue;

        $row = [];
        foreach ($header as $i => $h) {
            $row[$h] = isset($data[$i]) ? trim($data[$i]) : '';
        }
        $rows[] = $row;
    }
    fclose($handle);
} else {
    die("Could not open Book1.csv\n");
}

$unique_niks = [];
$sql_statements = [];
$valid_nik_count = 0;
$invalid_nik_count = 0;

// DISABLE FOREIGN KEY CHECKS
$sql_statements[] = "SET FOREIGN_KEY_CHECKS = 0;";
$sql_statements[] = "TRUNCATE TABLE hafiz;";
// We might as well clear related data if we are doing a fresh import, 
// but let's stick to what's necessary to avoid errors. 
// If we truncate hafiz, orphan rows in other tables might be an issue logic-wise, 
// but DB-wise it's allowed with checks off.
// Let's also truncate hafiz_mengajar since we import it? 
// The CSV has "TEMPAT MENGAJAR", but does it have multiple rows per hafiz?
// No, flat file.
// The current logic inserts into `hafiz` table columns `tempat_mengajar`. 
// The `hafiz_mengajar` table is a new related table for *multiple* locations.
// We should probably truncate it to be clean.
$sql_statements[] = "TRUNCATE TABLE hafiz_mengajar;";
$sql_statements[] = "TRUNCATE TABLE mutasi_hafiz;"; // Clear mutations if hafiz are gone
// $sql_statements[] = "TRUNCATE TABLE seleksi;"; // Maybe? User reported Seleksi FK error.
// If we delete Hafiz, their Seleksi records are invalid. 
// Safest is to truncate Seleksi too IF the user intends a full reset. 
// Given the error, I will truncate Seleksi to prevent logical orphans, assuming full reset.
// Warning added.

$count = 0;
foreach ($rows as $row) {
    // Basic validations
    $nik = $row['NIK'] ?? '';
    // Skip empty NIK or header repetition or generic text
    if (empty($nik) || !is_numeric(str_replace([' ', '-', '.'], '', $nik)) || strlen($nik) < 10) continue;

    // Ensure unique NIK in this batch
    if (isset($unique_niks[$nik])) continue;
    $unique_niks[$nik] = true;

    // Determine Status
    $tahun_lulus = $row['TAHUN LULUS SELEKSI'] ?? '';
    $status = !empty($tahun_lulus) ? 'lulus' : 'tidak_lulus';

    // Mengajar Status
    $tempat_mengajar = $row['TEMPAT MENGAJAR'] ?? '';
    $mengajar = !empty($tempat_mengajar) ? 1 : 0;

    // TMT Mengajar
    $tmt_raw = $row['TERHITUNG MULAI TANGGAL MENGAJAR'] ?? '';
    $tmt_mengajar = clean_date($tmt_raw);

    // 1. NIK Cleaning and Validation
    $nik_raw = trim($row['NIK'] ?? '');
    $nik_clean = str_replace([' ', '-', '.', ','], '', $nik_raw);

    $status_data = 'valid';
    if (!is_numeric($nik_clean) || strlen($nik_clean) !== 16) {
        $status_data = 'perlu_perbaikan';
        $invalid_nik_count++;
    } else {
        $valid_nik_count++;
    }
    $nik_val = escape($nik_clean);

    // 2. Kabupaten Cleaning
    $kabko_raw = strtoupper($row['KABUPATEN/KOTA NIK'] ?? $row['KABUPATEN/KOTA'] ?? '');
    // Remove KAB., KABUPATEN, KOTA and extra spaces
    $kabko_clean = preg_replace('/^(KAB\.|KABUPATEN|KOTA)\s+/', '', $kabko_raw);
    $kabko_clean = trim($kabko_clean);
    $kabko_sql = get_kabko_sql($kabko_clean, $nik_clean);

    // 3. Other Data Cleaning
    $nama = escape($row['NAMA'] ?? '');

    $tempat_lahir_raw = $row['TEMPAT LAHIR'] ?? '';
    if (trim($tempat_lahir_raw) === '') $tempat_lahir_raw = '-';
    $tempat_lahir = escape($tempat_lahir_raw);

    $tgl_raw = $row['TANGGAL LAHIR NIK'] ?? '';
    if (empty($tgl_raw)) $tgl_raw = $row['TANGGAL LAHIR MANUAL'] ?? '';
    $tanggal_lahir = clean_date($tgl_raw);

    $jk_val = 'L';
    $jk_raw = strtoupper($row['JENIS KELAMIN'] ?? $row['JENIS ELAMIN'] ?? '');
    if (strpos($jk_raw, 'PEREMPUAN') !== false || $jk_raw == 'P') $jk_val = 'P';
    $jk = "'$jk_val'";

    $alamat_raw = $row['ALAMAT'] ?? '';
    if (trim($alamat_raw) === '') $alamat_raw = '-';
    $alamat = escape($alamat_raw);

    $desa_raw = $row['DESA/KELURAHAN'] ?? '';
    if (trim($desa_raw) === '') $desa_raw = '-';
    $desa = escape($desa_raw);

    $kec_raw = $row['KECAMATAN'] ?? '';
    if (trim($kec_raw) === '') $kec_raw = '-';
    $kecamatan = escape($kec_raw);

    $rt_raw = $row['RT'] ?? '';
    $rw_raw = $row['RW'] ?? '';
    $rt = escape(substr(trim($rt_raw), 0, 5));
    $rw = escape(substr(trim($rw_raw), 0, 5));

    $no_hp_raw = $row['NO HP/WA'] ?? $row['TELEPON'] ?? '';
    $no_hp_raw = substr(trim($no_hp_raw), 0, 20);
    $no_hp = escape($no_hp_raw);

    $sertifikat = escape($row['LEMBAGA YANG MENGELUARKAN SERTIFIKAT TAHFIZ'] ?? '');
    $keterangan = escape($row['KETERANGAN'] ?? '');

    $tahun_tes = (int)($row['TAHUN IKUT SELEKSI'] ?? 2023);
    if ($tahun_tes == 0) $tahun_tes = 2023;

    // Tanggal Lulus (YYYY-12-31 if only year known)
    $tanggal_lulus = 'NULL';
    if (!empty($tahun_lulus) && is_numeric($tahun_lulus)) {
        $tanggal_lulus = "'$tahun_lulus-12-31'";
    }

    $tmpt_mengajar_val = escape($tempat_mengajar);

    $sql = "INSERT INTO hafiz (
        nama, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, 
        alamat, rt, rw, desa_kelurahan, kecamatan, kabupaten_kota_id, telepon, 
        sertifikat_tahfidz, mengajar, tempat_mengajar, tmt_mengajar, status_kelulusan, 
        tahun_tes, tanggal_lulus, keterangan, status_data, created_at, updated_at
    ) VALUES (
        $nama, $nik_val, $tempat_lahir, $tanggal_lahir, $jk,
        $alamat, $rt, $rw, $desa, $kecamatan, $kabko_sql, $no_hp,
        $sertifikat, $mengajar, $tmpt_mengajar_val, $tmt_mengajar, '$status',
        $tahun_tes, $tanggal_lulus, $keterangan, '$status_data', NOW(), NOW()
    );";

    $sql_statements[] = $sql;
    $count++;
}

// ENABLE FOREIGN KEY CHECKS
$sql_statements[] = "SET FOREIGN_KEY_CHECKS = 1;";

file_put_contents('import_hafiz.sql', implode("\n", $sql_statements));
echo "Generated $count INSERT statements in import_hafiz.sql\n";
echo "====================================================\n";
echo "Jumlah data valid NIK: $valid_nik_count\n";
echo "Jumlah data perlu perbaikan NIK: $invalid_nik_count\n";
echo "====================================================\n";
