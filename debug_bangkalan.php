<?php
require 'config/database.php';
$bangkalanId = 38;

$pending = Database::query("SELECT id, username, nama, kabupaten_kota_id FROM users WHERE is_active = 0 AND role = 'hafiz'");
echo "Total Pending Users: " . count($pending) . "\n";
foreach ($pending as $p) {
    echo "ID: {$p['id']}, Username: {$p['username']}, Kabko: " . ($p['kabupaten_kota_id'] ?? 'NULL') . "\n";
}

$hafizInBangkalan = Database::query("SELECT id, nama, nik, user_id FROM hafiz WHERE kabupaten_kota_id = $bangkalanId AND user_id IS NULL");
echo "\nHafiz in Bangkalan with NO User Account (Imported): " . count($hafizInBangkalan) . "\n";

$hafizWithPendingUser = Database::query("SELECT h.id, h.nama, h.nik, u.id as user_id, u.is_active 
                                       FROM hafiz h 
                                       JOIN users u ON h.user_id = u.id 
                                       WHERE h.kabupaten_kota_id = $bangkalanId AND u.is_active = 0");
echo "\nHafiz in Bangkalan with PENDING User Account: " . count($hafizWithPendingUser) . "\n";
foreach ($hafizWithPendingUser as $h) {
    echo "Hafiz: {$h['nama']}, User ID: {$h['user_id']}\n";
}
