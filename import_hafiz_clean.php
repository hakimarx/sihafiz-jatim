<?php
/**
 * ============================================
 * IMPORT DATA HAFIZ BERSIH KE DATABASE
 * ============================================
 * Script ini mengimport data dari Book1_clean.csv
 * ke database SiHafiz Jatim.
 * 
 * Cara pakai:
 * 1. Upload file ini ke server
 * 2. Akses via browser (hanya admin)
 * 3. Atau jalankan via CLI: php import_hafiz_clean.php
 * ============================================
 */

// Load configuration
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/src/Core/Database.php';

// === KONFIGURASI ===
$csvFile = __DIR__ . '/Book1_clean.csv';
$batchSize = 100; // Insert per batch
$dryRun = false;  // Set true untuk test tanpa insert

// === MAPPING KABUPATEN ===
// Mapping nama di CSV ke nama di database
function getKabupatenMapping(): array
{
    $kabkoList = Database::query("SELECT id, nama FROM kabupaten_kota ORDER BY nama");
    $map = [];
    foreach ($kabkoList as $k) {
        $map[strtolower(trim($k['nama']))] = $k['id'];
    }
    return $map;
}

function findKabkoId(string $asalCsv, array $mapping): ?int
{
    $asal = strtolower(trim($asalCsv));
    
    // Direct match
    if (isset($mapping[$asal])) {
        return $mapping[$asal];
    }
    
    // Partial match - cari yang mengandung
    foreach ($mapping as $name => $id) {
        // Ambil kata kunci: misalnya "banyuwangi" dari "Kabupaten Banyuwangi" 
        $parts = explode(' ', $asal);
        $lastPart = end($parts);
        if (stripos($name, $lastPart) !== false) {
            return $id;
        }
    }
    
    // Trim "Kab." atau "Kabupaten" prefix dan coba lagi
    $cleaned = preg_replace('/^(kab\.|kabupaten|kota)\s*/i', '', $asal);
    foreach ($mapping as $name => $id) {
        if (stripos($name, $cleaned) !== false) {
            return $id;
        }
    }
    
    return null;
}

function parseDate(string $dateStr): ?string
{
    if (empty($dateStr)) return null;
    
    // Format: DD/MM/YYYY
    $parts = explode('/', $dateStr);
    if (count($parts) === 3) {
        $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
        $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
        $year = $parts[2];
        
        // Validasi
        if (checkdate((int)$month, (int)$day, (int)$year)) {
            return "$year-$month-$day";
        }
    }
    
    return null;
}

// === MAIN ===
echo "============================================\n";
echo " IMPORT DATA HAFIZ KE DATABASE\n";
echo "============================================\n\n";

if (!file_exists($csvFile)) {
    die("ERROR: File $csvFile tidak ditemukan!\nJalankan clean_and_export.ps1 terlebih dahulu.\n");
}

// Load mapping
$kabkoMapping = getKabupatenMapping();
echo "Kabupaten/Kota terdaftar: " . count($kabkoMapping) . "\n";

// Read CSV
$handle = fopen($csvFile, 'r');
if (!$handle) {
    die("ERROR: Tidak bisa membuka file CSV!\n");
}

// Read header
$header = fgetcsv($handle, 0, ';');
// Remove BOM if present
if ($header) {
    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
    // Also remove quotes PowerShell might add
    $header = array_map(function($h) { return trim($h, '"'); }, $header);
}

echo "Kolom CSV: " . implode(', ', $header) . "\n\n";

$imported = 0;
$skipped = 0;
$errors = 0;
$notFoundKabko = [];
$lineNum = 1;

while (($row = fgetcsv($handle, 0, ';')) !== false) {
    $lineNum++;
    
    // Map ke associative array
    $data = [];
    foreach ($header as $i => $col) {
        $data[trim($col, '"')] = isset($row[$i]) ? trim($row[$i], '"') : '';
    }
    
    $nik = trim($data['NIK'] ?? '');
    $nama = strtoupper(trim($data['NAMA'] ?? ''));
    $asal = trim($data['ASAL'] ?? '');
    $tahun = (int)($data['TAHUN'] ?? 0);
    
    // Skip jika NIK bukan 16 digit atau nama kosong
    if (!preg_match('/^\d{16}$/', $nik) || empty($nama)) {
        $skipped++;
        continue;
    }
    
    // Cari kabupaten
    $kabkoId = findKabkoId($asal, $kabkoMapping);
    if (!$kabkoId) {
        if (!isset($notFoundKabko[$asal])) {
            $notFoundKabko[$asal] = 0;
        }
        $notFoundKabko[$asal]++;
        $skipped++;
        continue;
    }
    
    // Parse tanggal lahir (prioritas: TANGGAL LAHIR NIK, lalu TANGGAL LAHIR)
    $tglLahirStr = !empty($data['TANGGAL LAHIR NIK']) ? $data['TANGGAL LAHIR NIK'] : ($data['TANGGAL LAHIR'] ?? '');
    $tglLahir = parseDate($tglLahirStr);
    if (!$tglLahir) {
        $tglLahir = '1970-01-01'; // Default jika tidak valid
    }
    
    // Jenis Kelamin
    $jk = strtoupper(trim($data['JK'] ?? 'L'));
    if ($jk !== 'L' && $jk !== 'P') $jk = 'L';
    
    // TMT Mengajar
    $tmtMengajar = parseDate($data['TMT MENGAJAR'] ?? '');
    
    // Data hafiz
    $hafizData = [
        'nik' => $nik,
        'nama' => $nama,
        'tempat_lahir' => strtoupper(trim($data['TEMPAT LAHIR'] ?? '')),
        'tanggal_lahir' => $tglLahir,
        'jenis_kelamin' => $jk,
        'alamat' => trim($data['ALAMAT'] ?? ''),
        'rt' => trim($data['RT'] ?? ''),
        'rw' => trim($data['RW'] ?? ''),
        'desa_kelurahan' => strtoupper(trim($data['DESA/KELURAHAN'] ?? '')),
        'kecamatan' => strtoupper(trim($data['KECAMATAN'] ?? '')),
        'kabupaten_kota_id' => $kabkoId,
        'telepon' => trim($data['TELEPON'] ?? ''),
        'sertifikat_tahfidz' => trim($data['SERTIFIKAT TAHFIDZ'] ?? ''),
        'mengajar' => !empty($data['MENGAJAR']) ? 1 : 0,
        'tmt_mengajar' => $tmtMengajar,
        'tempat_mengajar' => trim($data['MENGAJAR'] ?? ''),
        'tahun_tes' => $tahun > 0 ? $tahun : 2015,
        'status_kelulusan' => 'lulus', // Semua data ini sudah LULUS
        'keterangan' => trim($data['KETERANGAN'] ?? ''),
        'is_aktif' => 1
    ];
    
    if ($dryRun) {
        $imported++;
        continue;
    }
    
    try {
        // Check if already exists (NIK + tahun_tes)
        $existing = Database::queryOne(
            "SELECT id FROM hafiz WHERE nik = :nik AND tahun_tes = :tahun",
            ['nik' => $nik, 'tahun' => $hafizData['tahun_tes']]
        );
        
        if ($existing) {
            $skipped++;
            continue;
        }
        
        // Insert hafiz (tanpa user account - nanti dibuat saat registrasi)
        Database::execute(
            "INSERT INTO hafiz (nik, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, 
             alamat, rt, rw, desa_kelurahan, kecamatan, kabupaten_kota_id, telepon,
             sertifikat_tahfidz, mengajar, tmt_mengajar, tempat_mengajar, 
             tahun_tes, status_kelulusan, keterangan, is_aktif)
             VALUES (:nik, :nama, :tempat_lahir, :tanggal_lahir, :jenis_kelamin,
             :alamat, :rt, :rw, :desa_kelurahan, :kecamatan, :kabupaten_kota_id, :telepon,
             :sertifikat_tahfidz, :mengajar, :tmt_mengajar, :tempat_mengajar,
             :tahun_tes, :status_kelulusan, :keterangan, :is_aktif)",
            $hafizData
        );
        
        $imported++;
        
        if ($imported % 500 === 0) {
            echo "  Progress: $imported records imported...\n";
        }
        
    } catch (Exception $e) {
        $errors++;
        if ($errors <= 10) {
            echo "  ERROR Line $lineNum (NIK: $nik): " . $e->getMessage() . "\n";
        }
    }
}

fclose($handle);

echo "\n============================================\n";
echo " HASIL IMPORT\n";
echo "============================================\n";
echo "Berhasil  : $imported\n";
echo "Dilewati  : $skipped\n";
echo "Error     : $errors\n";

if (!empty($notFoundKabko)) {
    echo "\nKabupaten yang tidak ditemukan di database:\n";
    foreach ($notFoundKabko as $name => $count) {
        echo "  - $name ($count data)\n";
    }
}

echo "\n============================================\n";
echo " CATATAN PENTING\n";
echo "============================================\n";
echo "- Data hafiz BELUM memiliki akun user (user_id = NULL)\n";
echo "- Hafiz akan mendapat akun saat KLAIM/REGISTRASI\n";
echo "- Skenario: Hafiz input NIK → sistem cocokkan dengan data → buat akun\n";
echo "============================================\n";
