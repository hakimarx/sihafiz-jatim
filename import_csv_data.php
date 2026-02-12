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

// Read header
fgetcsv($handle, 0, ";");

$updatedCount = 0;
$notFoundCount = 0;
$processedCount = 0;

echo "Loading existing NIKs...\n";
$rows = Database::query("SELECT id, nik FROM hafiz");
$nikMap = [];
foreach ($rows as $r) {
    $nikMap[trim($r['nik'])] = $r['id'];
}
echo "Loaded " . count($nikMap) . " NIKs from DB.\n";

echo "Starting data import from CSV...\n";

while (($data = fgetcsv($handle, 0, ";")) !== false) {
    // Skip empty lines or empty NIK
    if (empty($data[4])) continue;

    $processedCount++;
    $nik = trim($data[4]);
    $lokasi = trim($data[3] ?? '');
    $tempatMengajar = trim($data[18] ?? '');
    $tmtRaw = trim($data[19] ?? '');
    $tahunLulus = trim($data[22] ?? '');

    // Parse TMT Date
    $tmtDate = null;
    if (!empty($tmtRaw)) {
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $tmtRaw, $m)) {
            $tmtDate = "{$m[3]}-{$m[2]}-{$m[1]}";
        } elseif (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $tmtRaw, $m)) {
            $tmtDate = "{$m[1]}-{$m[2]}-{$m[3]}";
        }
    }

    // Parse Tahun Lulus
    $tahunLulusVal = null;
    if (is_numeric($tahunLulus) && (int)$tahunLulus > 2000) {
        $tahunLulusVal = (int)$tahunLulus;
    }

    if (isset($nikMap[$nik])) {
        try {
            $updates = [];
            $params = ['id' => $nikMap[$nik]];

            if ($lokasi) {
                $updates[] = "lokasi_seleksi = :lokasi";
                $params['lokasi'] = $lokasi;
            }
            if ($tahunLulusVal) {
                $updates[] = "tahun_lulus = :tahun_lulus";
                $params['tahun_lulus'] = $tahunLulusVal;
            }
            // Fill gaps for teaching info, or overwrite? Let's overwrite if CSV has data suitable for enrichment.
            if ($tempatMengajar) {
                $updates[] = "tempat_mengajar = :tempat_mengajar";
                $params['tempat_mengajar'] = $tempatMengajar;
            }
            if ($tmtDate) {
                $updates[] = "tmt_mengajar = :tmt_mengajar";
                $params['tmt_mengajar'] = $tmtDate;
            }

            if (!empty($updates)) {
                $sql = "UPDATE hafiz SET " . implode(', ', $updates) . " WHERE id = :id";
                Database::execute($sql, $params);
                $updatedCount++;

                if ($updatedCount % 1000 == 0) echo "Updated $updatedCount records...\n";
            }
        } catch (Exception $e) {
            echo "Error updating ID {$nikMap[$nik]}: " . $e->getMessage() . "\n";
        }
    } else {
        $notFoundCount++;
    }
}

fclose($handle);

echo "Import Complete.\n";
echo "Processed Rows: $processedCount\n";
echo "Updated Records: $updatedCount\n";
echo "NIK Not Found in DB: $notFoundCount\n";
