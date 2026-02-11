<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$csvFile = __DIR__ . '/../../Book1.csv';
if (!file_exists($csvFile)) {
    die("File not found: $csvFile");
}

$handle = fopen($csvFile, "r");
if ($handle) {
    // Coba deteksi delimiter
    $line = fgets($handle);
    rewind($handle);

    echo "First line raw: " . substr($line, 0, 100) . "...\n";

    // Asumsi semicolon
    $data = fgetcsv($handle, 1000, ";");
    echo "Headers:\n";
    print_r($data);

    $row = fgetcsv($handle, 1000, ";");
    echo "First row:\n";
    print_r($row);

    fclose($handle);
}

// Cek koneksi DB untuk list kabupaten
require_once __DIR__ . '/../../src/config/database.php';
try {
    $db = new PDO("mysql:host=localhost;dbname=sihafiz_jatim", "root", "");
    // Note: kredensial mungkin beda di env user, tapi kita coba standard dulu atau baca .env
} catch (PDOException $e) {
    echo "DB Connection failed: " . $e->getMessage() . "\n";
}
