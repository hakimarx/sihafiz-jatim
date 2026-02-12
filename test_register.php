<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

// Direct registration bypassing web form
Database::beginTransaction();

try {
    $nama = 'Muhammad Fauzan';
    $nik = '3578012345678901';
    $telepon = '081234567890';
    $password = substr($nik, -6);
    
    // Get first kabko ID
    $kabko = Database::queryOne('SELECT id FROM kabupaten_kota LIMIT 1');
    $kabkoId = $kabko['id'];
    echo "Kabko ID: " . $kabkoId . "\n";
    
    // Create user
    $hashedPw = hashPassword($password);
    Database::execute(
        "INSERT INTO users (username, password, role, kabupaten_kota_id, nama, email, telepon, is_active) VALUES (:u, :p, :r, :k, :n, :e, :t, :a)",
        ['u' => $telepon, 'p' => $hashedPw, 'r' => 'hafiz', 'k' => $kabkoId, 'n' => $nama, 'e' => $telepon.'@hafizjatim.id', 't' => $telepon, 'a' => 0]
    );
    $userId = Database::lastInsertId();
    echo "User ID: " . $userId . "\n";
    
    // Create hafiz
    Database::execute(
        "INSERT INTO hafiz (nama, nik, kabupaten_kota_id, telepon, user_id, is_aktif, tahun_tes) VALUES (:nama, :nik, :kabko_id, :telepon, :user_id, 1, :tahun)",
        ['nama' => $nama, 'nik' => $nik, 'kabko_id' => $kabkoId, 'telepon' => $telepon, 'user_id' => $userId, 'tahun' => TAHUN_ANGGARAN]
    );
    $hafizId = Database::lastInsertId();
    echo "Hafiz ID: " . $hafizId . "\n";
    
    Database::commit();
    echo "Registration successful!\n";
    echo "Username: " . $telepon . "\n";
    echo "Password: " . $password . " (6 digit terakhir NIK)\n";
} catch (Exception $e) {
    Database::rollback();
    echo "Error: " . $e->getMessage() . "\n";
}
