<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Hafiz.php';
require_once __DIR__ . '/src/Models/KabupatenKota.php';

try {
    // Check if we can connect
    Database::getConnection();
    echo "Connected to database.\n";

    // 1. Ensure Kabupaten Kota exists (Kota Surabaya - ID 1 in schema.sql)
    $kabko = Database::queryOne("SELECT id FROM kabupaten_kota WHERE id = 1");
    if (!$kabko) {
        Database::execute("INSERT INTO kabupaten_kota (id, nama, kode) VALUES (1, 'Kota Surabaya', 'SBY')");
        $kabkoId = 1;
    } else {
        $kabkoId = $kabko['id'];
    }

    // 2. Create Admin Prov (usually exists as 'admin', but let's check)
    $adminProv = Database::queryOne("SELECT * FROM users WHERE role = 'admin_prov' LIMIT 1");
    if (!$adminProv) {
        User::create([
            'username' => 'admin_prov_test',
            'password' => 'password123',
            'role' => ROLE_ADMIN_PROV,
            'nama' => 'Admin Prov Test'
        ]);
        echo "Created Admin Prov: admin_prov_test / password123\n";
    } else {
        echo "Admin Prov exists: " . $adminProv['username'] . "\n";
    }

    // 3. Create Admin Kab/Ko for Surabaya
    $adminKabko = Database::queryOne("SELECT * FROM users WHERE role = 'admin_kabko' AND kabupaten_kota_id = :kabko_id LIMIT 1", ['kabko_id' => $kabkoId]);
    if (!$adminKabko) {
        User::create([
            'username' => 'admin_sby',
            'password' => 'password123',
            'role' => ROLE_ADMIN_KABKO,
            'kabupaten_kota_id' => $kabkoId,
            'nama' => 'Admin Surabaya'
        ]);
        echo "Created Admin Kabko: admin_sby / password123\n";
    } else {
        echo "Admin Kabko exists: " . $adminKabko['username'] . "\n";
    }

    // 4. Create a Hafiz for Surabaya
    $hafizUser = Database::queryOne("SELECT * FROM users WHERE role = 'hafiz' LIMIT 1");
    if (!$hafizUser) {
        // Create Hafiz record along with user account
        $hafizId = Hafiz::create([
            'nik' => '1234567890123456',
            'nama' => 'Hafiz Test User',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Test No. 1',
            'desa_kelurahan' => 'Test',
            'kecamatan' => 'Test',
            'kabupaten_kota_id' => $kabkoId,
            'telepon' => '081234567890',
            'tahun_tes' => 2024
        ]);
        echo "Created Hafiz User: 081234567890 (nik as password) / 1234567890123456\n";
    } else {
        $hafiz = Database::queryOne("SELECT * FROM hafiz WHERE user_id = :user_id", ['user_id' => $hafizUser['id']]);
        echo "Hafiz User exists: " . $hafizUser['username'] . " (NIK: " . ($hafiz['nik'] ?? 'unknown') . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
