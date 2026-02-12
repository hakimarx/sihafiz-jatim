<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

ob_start();

$users = Database::query("SELECT id, username, role, nama, is_active FROM users ORDER BY id DESC LIMIT 10");
echo "=== RECENT USERS ===\n";
foreach ($users as $u) {
    echo "ID:{$u['id']} | {$u['username']} | {$u['role']} | {$u['nama']} | active:{$u['is_active']}\n";
}

$hafiz = Database::query("SELECT id, nama, nik, user_id, is_aktif FROM hafiz ORDER BY id DESC LIMIT 5");
echo "\n=== RECENT HAFIZ ===\n";
foreach ($hafiz as $h) {
    echo "ID:{$h['id']} | {$h['nama']} | NIK:{$h['nik']} | user:{$h['user_id']} | aktif:{$h['is_aktif']}\n";
}

$totalUsers = Database::queryOne("SELECT COUNT(*) as c FROM users");
$totalHafiz = Database::queryOne("SELECT COUNT(*) as c FROM hafiz");
$pendingUsers = Database::queryOne("SELECT COUNT(*) as c FROM users WHERE is_active = 0");
echo "\nTotal Users: {$totalUsers['c']} | Total Hafiz: {$totalHafiz['c']} | Pending: {$pendingUsers['c']}\n";

$output = ob_get_clean();
file_put_contents(__DIR__ . '/data_output.txt', $output);
echo "Done\n";
