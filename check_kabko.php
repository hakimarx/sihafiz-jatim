<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

ob_start();

$k = Database::query('SELECT id, nama FROM kabupaten_kota ORDER BY nama');
echo count($k) . " kabupaten/kota di database\n";
foreach ($k as $r) {
    echo "  ID:{$r['id']} => {$r['nama']}\n";
}

// Cek unique ASAL values from CSV
$csvFile = __DIR__ . '/Book1_clean.csv';
$handle = fopen($csvFile, 'r');
$header = fgetcsv($handle, 0, ';');
$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
$header = array_map(function($h) { return trim($h, '"'); }, $header);

$asalUnique = [];
while (($row = fgetcsv($handle, 0, ';')) !== false) {
    $data = [];
    foreach ($header as $i => $col) {
        $data[trim($col, '"')] = isset($row[$i]) ? trim($row[$i], '"') : '';
    }
    $asal = trim($data['ASAL'] ?? '');
    if (!empty($asal)) {
        $asalUnique[$asal] = ($asalUnique[$asal] ?? 0) + 1;
    }
}
fclose($handle);

echo "\n" . count($asalUnique) . " unique ASAL values in CSV:\n";
ksort($asalUnique);
foreach ($asalUnique as $asal => $count) {
    echo "  $asal ($count records)\n";
}

$output = ob_get_clean();
file_put_contents(__DIR__ . '/kabko_check.txt', $output);
echo "Done. Check kabko_check.txt\n";
