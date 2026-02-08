<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Hafiz.php';
require_once __DIR__ . '/../src/Models/KabupatenKota.php';

set_time_limit(0);
ini_set('memory_limit', '1G');

$csvFile = 'd:\Seleksi Huffadz aplikasi data hafidz 2023\Seleksi Huffazh 2023.csv';
if (!file_exists($csvFile)) {
    die("CSV file not found.");
}

$handle = fopen($csvFile, "r");
$header = fgetcsv($handle, 1000, ";");

$count = 0;
$imported = 0;
$skipped = 0;

$kabkoList = Database::query("SELECT id, nama FROM kabupaten_kota");
$kabkoMap = [];
foreach ($kabkoList as $k) {
    $normalized = trim(str_replace(['kabupaten ', 'kota ', 'kab. ', 'k. '], '', strtolower($k['nama'])));
    $kabkoMap[$normalized] = $k['id'];
}

echo "Starting import (Full)...\n";
$logFile = __DIR__ . '/../import_progress.log';
file_put_contents($logFile, "Full Import started at " . date('H:i:s') . "\n");

while (($line = fgets($handle)) !== FALSE) {
    if (trim($line) === '') continue;
    $data = explode(';', $line);
    if (count($data) < 17) continue;

    $count++;
    if ($count % 500 === 0) {
        $msg = "Progress: $count rows... (Imported: $imported, Skipped: $skipped)\n";
        echo $msg;
        file_put_contents($logFile, $msg, FILE_APPEND);
    }

    $csvData = [
        'tahun' => preg_replace('/[^0-9]/', '', $data[1] ?? '2023'),
        'kabko_name' => $data[16] ?? '',
        'nik' => preg_replace('/[^0-9]/', '', $data[4] ?? ''),
        'nama' => trim($data[5] ?? ''),
        'tempat_lahir' => trim($data[6] ?? ''),
        'tanggal_lahir' => trim($data[8] ?? ''),
        'jk' => trim($data[10] ?? 'L'),
        'alamat' => trim($data[11] ?? ''),
        'rt' => trim($data[12] ?? ''),
        'rw' => trim($data[13] ?? ''),
        'desa' => trim($data[14] ?? ''),
        'kecamatan' => trim($data[15] ?? ''),
        'sertifikat' => trim($data[17] ?? ''),
        'mengajar' => trim($data[18] ?? ''),
        'telepon' => preg_replace('/[^0-9]/', '', $data[20] ?? ''),
        'lulus' => trim($data[22] ?? '')
    ];

    if (empty($csvData['nik']) || strlen($csvData['nik']) < 10) {
        $skipped++;
        continue;
    }

    $kabkoNormalized = trim(str_replace(['kabupaten ', 'kota ', 'kab. ', 'k. '], '', strtolower($csvData['kabko_name'])));
    $kabkoId = $kabkoMap[$kabkoNormalized] ?? null;

    if (!$kabkoId) {
        $kabkoNormalized2 = trim(str_replace(['kabupaten ', 'kota ', 'kab. ', 'k. '], '', strtolower($data[3] ?? '')));
        $kabkoId = $kabkoMap[$kabkoNormalized2] ?? null;
    }

    if (!$kabkoId) {
        $skipped++;
        continue;
    }

    if (Hafiz::nikExists($csvData['nik'], (int)$csvData['tahun'])) {
        $skipped++;
        continue;
    }

    try {
        // Convert date
        $birthDate = null;
        if (!empty($csvData['tanggal_lahir'])) {
            $p = explode('/', $csvData['tanggal_lahir']);
            if (count($p) === 3) {
                $birthDate = "{$p[2]}-{$p[1]}-{$p[0]}";
            }
        }

        Hafiz::create([
            'nik' => $csvData['nik'],
            'nama' => strtoupper($csvData['nama']),
            'tempat_lahir' => $csvData['tempat_lahir'],
            'tanggal_lahir' => $birthDate,
            'jenis_kelamin' => $csvData['jk'] === 'L' ? 'L' : 'P',
            'alamat' => $csvData['alamat'],
            'rt' => $csvData['rt'],
            'rw' => $csvData['rw'],
            'desa_kelurahan' => $csvData['desa'],
            'kecamatan' => $csvData['kecamatan'],
            'kabupaten_kota_id' => $kabkoId,
            'telepon' => $csvData['telepon'],
            'email' => null,
            'sertifikat_tahfidz' => $csvData['sertifikat'],
            'mengajar' => $csvData['mengajar'] ? 1 : 0,
            'tmt_mengajar' => null,
            'tahun_tes' => (int)$csvData['tahun']
        ]);

        $isLulus = strtolower($csvData['lulus']) === 'lulus' ? 'lulus' : 'pending';
        if ($isLulus === 'lulus') {
            $lastId = Database::lastInsertId();
            Database::execute("UPDATE hafiz SET status_kelulusan = 'lulus' WHERE id = :id", ['id' => $lastId]);
        }

        $imported++;
    } catch (Exception $e) {
        $skipped++;
    }
}

fclose($handle);

$finalMsg = "Full Import Finished at " . date('H:i:s') . "!\nTotal: $count, Imported: $imported, Skipped: $skipped\n";
file_put_contents($logFile, $finalMsg, FILE_APPEND);
echo $finalMsg;
