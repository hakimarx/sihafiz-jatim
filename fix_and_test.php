<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// Fix hafiz table - make personal detail columns nullable for 2-step registration
$alterStatements = [
    "ALTER TABLE hafiz MODIFY COLUMN tempat_lahir VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE hafiz MODIFY COLUMN tanggal_lahir DATE DEFAULT NULL",
    "ALTER TABLE hafiz MODIFY COLUMN jenis_kelamin ENUM('L', 'P') DEFAULT NULL",
    "ALTER TABLE hafiz MODIFY COLUMN alamat TEXT DEFAULT NULL",
    "ALTER TABLE hafiz MODIFY COLUMN desa_kelurahan VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE hafiz MODIFY COLUMN kecamatan VARCHAR(100) DEFAULT NULL",
];

foreach ($alterStatements as $sql) {
    try {
        Database::execute($sql);
        echo "OK: $sql\n";
    } catch (Exception $e) {
        echo "FAIL: $sql => " . $e->getMessage() . "\n";
    }
}

echo "\nAll columns modified. Now testing fresh registration...\n";

// Now test registration
require_once __DIR__ . '/config/security.php';

Database::beginTransaction();
try {
    $nama = 'Muhammad Fauzan';
    $nik = '3578012345678901';
    $telepon = '081234567890';
    $password = substr($nik, -6);

    $kabko = Database::queryOne('SELECT id FROM kabupaten_kota LIMIT 1');
    $kabkoId = $kabko['id'];

    // Create user
    $hashedPw = hashPassword($password);
    Database::execute(
        "INSERT INTO users (username, password, role, kabupaten_kota_id, nama, email, telepon, is_active) VALUES (:u, :p, :r, :k, :n, :e, :t, :a)",
        ['u' => $telepon, 'p' => $hashedPw, 'r' => 'hafiz', 'k' => $kabkoId, 'n' => $nama, 'e' => $telepon.'@hafizjatim.id', 't' => $telepon, 'a' => 0]
    );
    $userId = Database::lastInsertId();
    echo "User created: ID=$userId\n";

    // Create hafiz (only basic data)
    Database::execute(
        "INSERT INTO hafiz (nama, nik, kabupaten_kota_id, telepon, user_id, is_aktif, tahun_tes) VALUES (:nama, :nik, :kabko_id, :telepon, :user_id, 1, :tahun)",
        ['nama' => $nama, 'nik' => $nik, 'kabko_id' => $kabkoId, 'telepon' => $telepon, 'user_id' => $userId, 'tahun' => TAHUN_ANGGARAN]
    );
    $hafizId = Database::lastInsertId();
    echo "Hafiz created: ID=$hafizId\n";

    Database::commit();
    echo "\n=== Registration Successful! ===\n";
    echo "Username: $telepon\n";
    echo "Password: $password (6 digit terakhir NIK)\n";
} catch (Exception $e) {
    Database::rollback();
    echo "ERROR: " . $e->getMessage() . "\n";
}
