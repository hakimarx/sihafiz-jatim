<?php
// seed_test.php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Hafiz.php'; // Updated include path

echo "Seeding test users...\n";

// 1. Admin Provinsi (sudah ada di schema.sql, tapi pastikan)
$admin = User::findByUsernameAll('admin');
if (!$admin) {
    echo "Creating admin...\n";
    User::create([
        'username' => 'admin',
        'password' => 'admin123',
        'role' => ROLE_ADMIN_PROV,
        'nama' => 'Administrator Provinsi',
        'is_active' => 1
    ]);
} else {
    echo "Admin exists.\n";
}

// 2. Admin Kabko (Surabaya)
$adminKabko = User::findByUsernameAll('admin_sby');
if (!$adminKabko) {
    echo "Creating admin_sby...\n";
    $sby = Database::queryOne("SELECT id FROM kabupaten_kota WHERE nama LIKE '%Surabaya%' LIMIT 1");
    if ($sby) {
        User::create([
            'username' => 'admin_sby',
            'password' => 'sby123',
            'role' => ROLE_ADMIN_KABKO,
            'kabupaten_kota_id' => $sby['id'],
            'nama' => 'Admin Surabaya',
            'is_active' => 1
        ]);
    }
} else {
    echo "Admin Surabaya exists.\n";
}

// 3. Penguji
$penguji = User::findByUsernameAll('penguji01');
if (!$penguji) {
    echo "Creating penguji01...\n";
    User::create([
        'username' => 'penguji01',
        'password' => 'penguji123',
        'role' => ROLE_PENGUJI,
        'nama' => 'Ustadz Penguji',
        'is_active' => 1
    ]);
} else {
    echo "Penguji exists.\n";
}

// 4. Hafiz (Test User)
$nik = '1234567890123456';
$hafizUser = User::findByUsernameAll($nik);
if (!$hafizUser) {
    echo "Creating hafiz user...\n";
    $sby = Database::queryOne("SELECT id FROM kabupaten_kota WHERE nama LIKE '%Surabaya%' LIMIT 1");

    // Create Hafiz via Hafiz::create which handles User creation too
    try {
        Hafiz::create([
            'nik' => $nik,
            'nama' => 'Ahmad Hafiz Testing',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Test No. 1',
            'desa_kelurahan' => 'Test Village',
            'kecamatan' => 'Test District',
            'kabupaten_kota_id' => $sby['id'],
            'telepon' => '081234567890',
            'email' => 'hafiz@test.com',
            'sertifikat_tahfidz' => '30 Juz',
            'mengajar' => 1,
            'tmt_mengajar' => '2020-01-01',
            'tempat_mengajar' => 'TPQ Al-Test',
            'tahun_tes' => 2026 // Ensure consistent with TAHUN_ANGGARAN if needed, but safe to hardcode testing year
        ]);

        // Set password explicitly just in case
        $user = User::findByUsernameAll($nik);
        if ($user) {
            User::updatePassword($user['id'], 'hafiz123'); // Custom password for testing
        }
        echo "Hafiz created.\n";
    } catch (Exception $e) {
        echo "Error creating Hafiz: " . $e->getMessage() . "\n";
    }
} else {
    echo "Hafiz user exists.\n";
}

echo "Seeding completed.\n";
