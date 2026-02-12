<?php
require_once __DIR__ . '/config/database.php';

$csvFile = __DIR__ . '/Book1.csv';
if (!file_exists($csvFile)) {
    die("File not found: $csvFile\n");
}

$handle = fopen($csvFile, "r");
if ($handle === false) {
    die("Cannot open file.\n");
}

$header = fgetcsv($handle, 0, ";");
// Expected indices based on review:
// 4: NIK
// 22: TAHUN LULUS SELEKSI

$countLulus = 0;
$countTidakLulus = 0;
$lulusNIKs = [];

while (($data = fgetcsv($handle, 0, ";")) !== false) {
    // Skip empty rows
    if (empty($data[0]) && empty($data[4])) continue;

    $nik = trim($data[4] ?? '');
    $tahunLulus = trim($data[22] ?? '');

    // Check if Lulus
    if (!empty($tahunLulus)) {
        $countLulus++;
        if (!empty($nik)) {
            $lulusNIKs[] = $nik;
        }
    } else {
        $countTidakLulus++;
    }
}
fclose($handle);

echo "Total Lulus (CSV): $countLulus\n";
echo "Total Tidak Lulus (CSV): $countTidakLulus\n";

// Check Database for Missing Lulus
$missingCount = 0;
$missingNIKs = [];

echo "Checking Database for " . count($lulusNIKs) . " Lulus candidates...\n";

// Fetch all NIKs from DB to optimize
$dbNIKs = Database::query("SELECT nik FROM hafiz");
$existingNIKs = array_map(function ($row) {
    return trim($row['nik']);
}, $dbNIKs);
$existingNIKsMap = array_flip($existingNIKs);

foreach ($lulusNIKs as $nik) {
    if (!isset($existingNIKsMap[$nik])) {
        $missingCount++;
        if ($missingCount <= 10) {
            $missingNIKs[] = $nik;
        }
    }
}

echo "Jumlah Lulus di CSV tapi TIDAK ADA di Database: $missingCount\n";
if ($missingCount > 0) {
    echo "Contoh NIK yang hilang (max 10): " . implode(', ', $missingNIKs) . "\n";
}
