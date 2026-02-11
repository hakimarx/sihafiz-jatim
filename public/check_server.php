<?php
// check_server_readiness.php
// Upload file ini ke folder public/ di hosting Anda (misal: public_html/public/check.php)
// Akses via browser: http://hafizjatim.my.id/check.php
// SETELAH SELESAI, HAPUS FILE INI!

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

echo "<h1>Server Readiness Check</h1>";

// 1. Cek Koneksi DB
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>[OK] Database Connected.</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>[ERROR] Database Connection Failed: " . $e->getMessage() . "</p>";
    exit;
}

// 2. Cek Tabel Baru
try {
    $stmt = $db->query("SHOW TABLES LIKE 'hafiz_mengajar'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>[OK] Table 'hafiz_mengajar' found.</p>";
    } else {
        echo "<p style='color:red'>[ERROR] Table 'hafiz_mengajar' NOT FOUND! Silakan jalankan SQL Migration.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>[ERROR] Check Table Failed: " . $e->getMessage() . "</p>";
}

// 3. Cek Kolom Baru
try {
    $stmt = $db->query("SHOW COLUMNS FROM hafiz LIKE 'foto_profil'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>[OK] Column 'hafiz.foto_profil' found.</p>";
    } else {
        echo "<p style='color:red'>[ERROR] Column 'hafiz.foto_profil' NOT FOUND! Silakan jalankan SQL Migration.</p>";
    }

    $stmt = $db->query("SHOW COLUMNS FROM hafiz LIKE 'foto_ktp'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>[OK] Column 'hafiz.foto_ktp' found.</p>";
    } else {
        echo "<p style='color:red'>[ERROR] Column 'hafiz.foto_ktp' NOT FOUND!</p>";
    }

    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>[OK] Column 'users.remember_token' found.</p>";
    } else {
        echo "<p style='color:red'>[ERROR] Column 'users.remember_token' NOT FOUND!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>[ERROR] Check Columns Failed: " . $e->getMessage() . "</p>";
}

// 4. Cek Permission Uploads
$uploadPath = __DIR__ . '/uploads';
if (is_writable($uploadPath)) {
    echo "<p style='color:green'>[OK] Folder 'uploads' is writable.</p>";
} else {
    echo "<p style='color:red'>[ERROR] Folder 'uploads' is NOT writable! Change permission to 755 or 777.</p>";
}

echo "<hr>";
echo "<p>Test Date: " . date('Y-m-d H:i:s') . "</p>";
