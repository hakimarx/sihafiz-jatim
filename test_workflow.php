<?php
require 'config/app.php';
require 'config/database.php';
require 'config/security.php';
require 'src/Models/User.php';
require 'src/Models/Hafiz.php';
require 'src/Models/KabupatenKota.php';
require 'src/Models/LaporanHarian.php';

echo "--- DATA MASTER ---\n";
$kabko = KabupatenKota::getAll();
if (empty($kabko)) {
    echo "No Kabko found!\n";
    exit;
}
$targetKabko = $kabko[0];
echo "Target Kabko: " . $targetKabko['nama'] . " (ID: " . $targetKabko['id'] . ")\n";

echo "\n--- 1. REGISTER HAFIZ BARU ---\n";
$nik = "3501" . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
$nama = "Hafiz " . rand(100, 999);
$telepon = "0812" . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

$hafizData = [
    'nik' => $nik,
    'nama' => $nama,
    'tempat_lahir' => 'Surabaya',
    'tanggal_lahir' => '1995-05-15',
    'jenis_kelamin' => 'L',
    'alamat' => 'Jl. Kebenaran No. 1',
    'desa_kelurahan' => 'Desa A',
    'kecamatan' => 'Kecamatan B',
    'kabupaten_kota_id' => $targetKabko['id'],
    'telepon' => $telepon,
    'email' => strtolower(str_replace(' ', '', $nama)) . '@example.com',
    'sertifikat_tahfidz' => 'LPTQ Jatim',
    'mengajar' => 1,
    'tmt_mengajar' => '2020-01-01',
    'tempat_mengajar' => 'TPQ Al-Ikhlas',
    'tahun_tes' => 2024
];

$hafizId = Hafiz::create($hafizData);
$newHafiz = Hafiz::findById($hafizId);
$userId = $newHafiz['user_id'];
echo "Hafiz Created: $nama (ID: $hafizId, UserID: $userId)\n";
echo "Username: $telepon, Password: $nik\n";

echo "\n--- 2. VERIFIKASI OLEH ADMIN KABKO ---\n";
// Ambil admin kabko atau buat jika tidak ada
$adminKabko = User::getByRole(ROLE_ADMIN_KABKO, $targetKabko['id']);
if (empty($adminKabko)) {
    echo "No Admin Kabko for this region. Creating one...\n";
    $adminKabkoId = User::create([
        'username' => 'admin_' . strtolower(str_replace(' ', '_', $targetKabko['nama'])) . '_' . rand(10, 99),
        'password' => 'admin123',
        'role' => ROLE_ADMIN_KABKO,
        'kabupaten_kota_id' => $targetKabko['id'],
        'nama' => 'Admin ' . $targetKabko['nama']
    ]);
} else {
    $adminKabkoId = $adminKabko[0]['id'];
}
echo "Admin Kabko ID: $adminKabkoId\n";

// Verifikasi (Status Kelulusan)
Hafiz::update($hafizId, ['status_kelulusan' => 'lulus']);
echo "Hafiz verified (Status: Lulus)\n";

echo "\n--- 3. LOGIN & UPDATE PROFIL BY HAFIZ ---\n";
$user = User::authenticate($telepon, $nik);
if ($user) {
    echo "Login successful for $nama\n";
    Hafiz::update($hafizId, [
        'nama_bank' => 'BANK JATIM',
        'nomor_rekening' => '1234567890'
    ]);
    echo "Profile updated (Bank Jatim)\n";
} else {
    echo "Login FAILED!\n";
}

echo "\n--- 4. CRUD LAPORAN HARIAN BY HAFIZ ---\n";
$laporanId = LaporanHarian::create([
    'hafiz_id' => $hafizId,
    'tanggal' => date('Y-m-d'),
    'jenis_kegiatan' => 'mengajar',
    'deskripsi' => 'Mengajar santri TPQ sore hari'
]);
echo "Laporan Created (ID: $laporanId)\n";

LaporanHarian::update($laporanId, [
    'deskripsi' => 'Mengajar santri TPQ sore hari - SELESAI'
]);
echo "Laporan Updated\n";

echo "\n--- 5. ADMIN KABKO CRUD LAPORAN (VERIFY) ---\n";
// Di sistem ini, Admin CRUD laporan biasanya berupa Verifikasi
LaporanHarian::verify($laporanId, 'disetujui', $adminKabkoId);
echo "Laporan verified by Admin (Status: Disetujui)\n";

$finalLaporan = LaporanHarian::findById($laporanId);
echo "Final Laporan Status: " . $finalLaporan['status_verifikasi'] . "\n";

echo "\n--- SELESAI ---\n";
