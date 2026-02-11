<?php

/**
 * SIHAFIZ JATIM - Database Cleanup Script
 * ========================================
 * Jalankan file ini di browser:
 * https://hafizjatim.my.id/run_cleanup.php?key=SIHAFIZ_CLEANUP_2026
 * 
 * Script ini akan membersihkan data yang sudah ada:
 * 1. Trim spasi di kabupaten_kota
 * 2. Bersihkan NIK (hapus X, ?, spasi)
 * 3. Sinkronkan kabupaten_kota_id antara users dan hafiz
 * 4. Export laporan hasil cleanup
 * 
 * HAPUS FILE INI SETELAH SELESAI!
 */

// Security: hanya bisa diakses dengan key
if (!isset($_GET['key']) || $_GET['key'] !== 'SIHAFIZ_CLEANUP_2026') {
    http_response_code(403);
    die('Forbidden');
}

// Load environment
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), ';') === 0 || strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            putenv(trim($line));
            list($k, $v) = explode('=', $line, 2);
            $_ENV[trim($k)] = trim(trim($v), '"\'');
        }
    }
}

$host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
$name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'hafizjat_sihafiz');
$user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
$pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die("DB Connection Error: " . $e->getMessage());
}

header('Content-Type: text/html; charset=utf-8');
echo "<html><head><title>SiHafiz Data Cleanup</title>";
echo "<style>body{font-family:monospace;max-width:900px;margin:40px auto;background:#1a1a1a;color:#00ff00;padding:20px;}";
echo "h1{color:#0f0;}h2{color:#ff0;margin-top:30px;}table{border-collapse:collapse;width:100%;}";
echo "td,th{border:1px solid #333;padding:8px;text-align:left;}th{background:#333;color:#0f0;}";
echo "tr:hover{background:#222;}.ok{color:#0f0;}.warn{color:#ff0;}.error{color:#f00;}</style></head><body>";
echo "<h1>🧹 SiHafiz Data Cleanup Report</h1>";
echo "<p>Waktu: " . date('Y-m-d H:i:s') . "</p>";

$pdo->beginTransaction();

try {
    // =============================================
    // 1. BERSIHKAN SPASI DI kabupaten_kota
    // =============================================
    echo "<h2>1. Bersihkan kabupaten_kota</h2>";
    $affected = $pdo->exec("UPDATE kabupaten_kota SET nama = TRIM(nama)");
    $affected2 = $pdo->exec("UPDATE kabupaten_kota SET kode = TRIM(kode)");
    echo "<p class='ok'>✅ Trimmed nama: {$affected} rows, kode: {$affected2} rows</p>";

    // =============================================
    // 2. BERSIHKAN NIK DI hafiz
    // =============================================
    echo "<h2>2. Bersihkan NIK di tabel hafiz</h2>";

    // 2a. Trim spasi
    $a = $pdo->exec("UPDATE hafiz SET nik = TRIM(nik) WHERE nik != TRIM(nik)");
    echo "<p class='ok'>✅ NIK trimmed: {$a} rows</p>";

    // 2b. Hapus X di awal (17 digit dengan X di awal → 16 digit valid)
    $b = $pdo->exec("UPDATE hafiz SET nik = SUBSTRING(nik, 2) WHERE nik LIKE 'X%' AND LENGTH(nik) = 17 AND SUBSTRING(nik, 2) REGEXP '^[0-9]{16}$'");
    echo "<p class='ok'>✅ NIK prefix X dihapus: {$b} rows</p>";

    // 2c. Hapus X di akhir (17 digit dengan X di akhir)
    $c = $pdo->exec("UPDATE hafiz SET nik = LEFT(nik, 16) WHERE nik LIKE '%X' AND LENGTH(nik) = 17 AND LEFT(nik, 16) REGEXP '^[0-9]{16}$'");
    echo "<p class='ok'>✅ NIK suffix X dihapus: {$c} rows</p>";

    // 2d. Hapus ? di akhir
    $d = $pdo->exec("UPDATE hafiz SET nik = LEFT(nik, 16) WHERE nik LIKE '%?' AND LENGTH(nik) = 17 AND LEFT(nik, 16) REGEXP '^[0-9]{16}$'");
    echo "<p class='ok'>✅ NIK suffix ? dihapus: {$d} rows</p>";

    // =============================================
    // 3. SINKRONKAN users.username DENGAN hafiz.nik
    // =============================================
    echo "<h2>3. Sinkronkan username users</h2>";
    $e = $pdo->exec("UPDATE users u INNER JOIN hafiz h ON u.id = h.user_id SET u.username = h.nik WHERE u.role = 'hafiz' AND u.username != h.nik");
    echo "<p class='ok'>✅ Username disinkronkan: {$e} rows</p>";

    // =============================================
    // 4. BERSIHKAN FIELD LAIN
    // =============================================
    echo "<h2>4. Bersihkan field lain</h2>";
    $f1 = $pdo->exec("UPDATE hafiz SET nama = TRIM(nama) WHERE nama != TRIM(nama)");
    $f2 = $pdo->exec("UPDATE hafiz SET tempat_lahir = TRIM(tempat_lahir) WHERE tempat_lahir != TRIM(tempat_lahir)");
    $f3 = $pdo->exec("UPDATE hafiz SET desa_kelurahan = TRIM(desa_kelurahan) WHERE desa_kelurahan != TRIM(desa_kelurahan)");
    $f4 = $pdo->exec("UPDATE hafiz SET kecamatan = TRIM(kecamatan) WHERE kecamatan != TRIM(kecamatan)");
    echo "<p class='ok'>✅ Nama: {$f1}, Tempat Lahir: {$f2}, Desa: {$f3}, Kecamatan: {$f4} rows trimmed</p>";

    // =============================================
    // 5. PERBAIKI kabupaten_kota_id di users
    // =============================================
    echo "<h2>5. Sinkronkan kabupaten_kota_id users ↔ hafiz</h2>";
    $g = $pdo->exec("UPDATE users u INNER JOIN hafiz h ON u.id = h.user_id SET u.kabupaten_kota_id = h.kabupaten_kota_id WHERE u.role = 'hafiz' AND (u.kabupaten_kota_id IS NULL OR u.kabupaten_kota_id != h.kabupaten_kota_id)");
    echo "<p class='ok'>✅ Kabko ID disinkronkan: {$g} rows</p>";

    $pdo->commit();
    echo "<h2 class='ok'>✅ SEMUA PEMBERSIHAN BERHASIL!</h2>";
} catch (Exception $ex) {
    $pdo->rollBack();
    echo "<h2 class='error'>❌ ERROR: " . htmlspecialchars($ex->getMessage()) . "</h2>";
    echo "<p>Transaction di-rollback.</p>";
}

// =============================================
// 6. LAPORAN DISTRIBUSI HAFIZ PER KAB/KO
// =============================================
echo "<h2>6. Distribusi Hafiz per Kabupaten/Kota</h2>";
$stmt = $pdo->query("
    SELECT kk.nama AS kabupaten_kota, COUNT(h.id) AS jumlah_hafiz
    FROM hafiz h
    LEFT JOIN kabupaten_kota kk ON h.kabupaten_kota_id = kk.id
    WHERE h.is_aktif = 1
    GROUP BY kk.nama
    ORDER BY kk.nama
");
$dist = $stmt->fetchAll();
echo "<table><tr><th>Kabupaten/Kota</th><th>Jumlah Hafiz</th></tr>";
$total = 0;
foreach ($dist as $row) {
    $total += $row['jumlah_hafiz'];
    echo "<tr><td>" . htmlspecialchars($row['kabupaten_kota'] ?? 'NULL') . "</td><td>{$row['jumlah_hafiz']}</td></tr>";
}
echo "<tr><th>TOTAL</th><th>{$total}</th></tr>";
echo "</table>";

// =============================================
// 7. LAPORAN NIK BERMASALAH
// =============================================
echo "<h2>7. NIK yang Masih Bermasalah</h2>";
$stmt = $pdo->query("
    SELECT h.id, h.nik, h.nama, kk.nama AS kabupaten_kota,
        CASE 
            WHEN h.nik LIKE 'TEMP%' THEN 'NIK Sementara'
            WHEN LENGTH(h.nik) != 16 THEN CONCAT('Bukan 16 digit (', LENGTH(h.nik), ')')
            WHEN h.nik REGEXP '[^0-9]' THEN 'Mengandung non-digit'
            ELSE 'OK'
        END AS status_nik
    FROM hafiz h
    LEFT JOIN kabupaten_kota kk ON h.kabupaten_kota_id = kk.id
    WHERE h.nik NOT REGEXP '^[0-9]{16}$'
    ORDER BY h.nama
    LIMIT 100
");
$issues = $stmt->fetchAll();
echo "<p>Total: " . count($issues) . " records (max 100 ditampilkan)</p>";
if (!empty($issues)) {
    echo "<table><tr><th>ID</th><th>NIK</th><th>Nama</th><th>Kab/Ko</th><th>Status</th></tr>";
    foreach ($issues as $row) {
        echo "<tr><td>{$row['id']}</td><td>" . htmlspecialchars($row['nik']) . "</td><td>" . htmlspecialchars($row['nama']) . "</td>";
        echo "<td>" . htmlspecialchars($row['kabupaten_kota'] ?? '') . "</td><td class='warn'>{$row['status_nik']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='ok'>✅ Semua NIK sudah valid 16 digit!</p>";
}

echo "<hr><p class='warn'>⚠️ <strong>HAPUS FILE INI SETELAH SELESAI!</strong> Jalankan: rm run_cleanup.php</p>";
echo "</body></html>";
