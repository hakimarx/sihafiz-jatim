<?php
// Test Full Flow: Registrasi -> Approval -> Profil -> Laporan -> Verifikasi

// Manual Load .env for testing
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1], " \t\n\r\0\x0B\"'");
            
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Debug Env
echo "DB_HOST=" . (getenv('DB_HOST') ?: 'Has no value') . "\n";
echo "DB_NAME=" . (getenv('DB_NAME') ?: 'Has no value') . "\n";

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

// Load Models
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Hafiz.php';
require_once __DIR__ . '/src/Models/LaporanHarian.php';
require_once __DIR__ . '/src/Models/KabupatenKota.php';

// Setup Data
$dummyNik = '9999999999999999';
$dummyPhone = '081299999999';
$dummyName = 'Hafiz Test Flow';
$dummyKabKoId = 204; // Kota Surabaya (SBY)

echo "=== MOLAI TEST FLOW ===\n";

// 1. CLEANUP
echo "[1] Cleanup Data Lama... ";
$user = User::findByUsername($dummyPhone);
if ($user) {
    // Unlink hafiz
    Database::execute("UPDATE hafiz SET user_id = NULL WHERE user_id = :uid", ['uid' => $user['id']]);
    User::delete($user['id']);
}
Database::execute("DELETE FROM hafiz WHERE nik = :nik", ['nik' => $dummyNik]);
echo "OK\n";

// 2. SETUP HAFIZ IMPORT
echo "[2] Setup Data Mentah Hafiz... ";
$hafizId = Hafiz::create([
    'nik' => $dummyNik,
    'nama' => $dummyName,
    'tempat_lahir' => 'Surabaya',
    'tanggal_lahir' => '2000-01-01',
    'jenis_kelamin' => 'L',
    'alamat' => 'Jl. Test No. 1',
    'kecamatan' => 'Test Kec',
    'desa_kelurahan' => 'Test Kel',
    'kabupaten_kota_id' => $dummyKabKoId,
    'telepon' => '', // Kosong awalnya
    'email' => '',
    'mengajar' => 0,
    'tahun_tes' => 2026
]);
echo "OK (ID: $hafizId)\n";

// 3. REGISTRASI (SIMULASI)
echo "[3] Simulasi Registrasi User... ";
// User mengisi form register
$userId = User::create([
    'username' => $dummyPhone,
    'password' => 'password123',
    'role' => 'hafiz',
    'kabupaten_kota_id' => $dummyKabKoId,
    'nama' => $dummyName,
    'telepon' => $dummyPhone,
    'is_active' => 0 // Pending
]);

// Link hafiz to user
Database::execute("UPDATE hafiz SET user_id = :uid WHERE id = :hid", ['uid' => $userId, 'hid' => $hafizId]);
echo "OK (User ID: $userId is Pending)\n";

// 4. ADMIN APPROVAL
echo "[4] Admin KabKo Approval... ";
// Cek pending list
$pending = User::getPendingApproval($dummyKabKoId);
$found = false;
foreach($pending as $p) {
    if ($p['id'] == $userId) $found = true;
}

if (!$found) {
    echo "FAILED: User tidak muncul di pending list admin kabko!\n";
    exit(1);
}

// Admin approve
User::update($userId, ['is_active' => 1]);
$refetchedUser = User::findById($userId);
if ($refetchedUser['is_active'] != 1) {
    echo "FAILED: User gagal diaktifkan!\n";
    exit(1);
}
echo "OK (User Activated)\n";

// 5. HAFIZ UPDATE PROFIL
echo "[5] Hafiz Update Profil... ";
Hafiz::update($hafizId, ['alamat' => 'Jl. Baru No. 99']);
$refetchedHafiz = Hafiz::findById($hafizId);
if ($refetchedHafiz['alamat'] !== 'Jl. Baru No. 99') {
    echo "FAILED: Profil gagal diupdate!\n";
    exit(1);
}
echo "OK\n";

// 6. LAPORAN HARIAN
echo "[6] Hafiz Input Laporan... ";
$laporanId = LaporanHarian::create([
    'hafiz_id' => $hafizId,
    'tanggal' => date('Y-m-d'),
    'jenis_kegiatan' => 'murojah',
    'deskripsi' => 'Test Murojah 30 Juz',
    'durasi_menit' => 60,
    'foto' => null
]);
echo "OK (Laporan ID: $laporanId)\n";

// 7. ADMIN VERIFIKASI LAPORAN
echo "[7] Admin Verifikasi Laporan... ";
// Cek list laporan admin
$filters = ['kabupaten_kota_id' => $dummyKabKoId, 'status_verifikasi' => 'pending'];
$laporanList = LaporanHarian::getAll($filters);
$foundLaporan = false;
foreach($laporanList['data'] as $l) {
    if ($l['id'] == $laporanId) $foundLaporan = true;
}

if (!$foundLaporan) {
    echo "FAILED: Laporan tidak muncul di list admin!\n";
    exit(1);
}

// Admin Verify
// Cari ID admin kabko dummy (atau create jika perlu, tapi kita simulasi aja passing ID admin)
$adminId = 1; // Assert admin prov/kabko
LaporanHarian::verify($laporanId, 'disetujui', $adminId, 'Oke bagus');

$refetchedLaporan = LaporanHarian::findById($laporanId);
if ($refetchedLaporan['status_verifikasi'] !== 'disetujui') {
    echo "FAILED: Laporan gagal diverifikasi!\n";
    exit(1);
}
echo "OK (Status: Disetujui)\n";

echo "\n=== ALL TESTS PASSED ===\n";
