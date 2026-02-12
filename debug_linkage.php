<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Hafiz.php';

$users = Database::query("SELECT id, username, role, is_active FROM users ORDER BY id DESC LIMIT 5");
echo "Latest 5 Users:\n";
print_r($users);

foreach ($users as $user) {
    $hafiz = Database::query("SELECT id, user_id, nik, nama, tahun_tes, is_aktif FROM hafiz WHERE user_id = :uid", ['uid' => $user['id']]);
    echo "\nHafiz for User ID {$user['id']} ({$user['username']}):\n";
    print_r($hafiz);
}

$allHafiz = Database::query("SELECT id, user_id, nik, nama, tahun_tes, is_aktif FROM hafiz ORDER BY id DESC LIMIT 5");
echo "\nLatest 5 Hafiz Records:\n";
print_r($allHafiz);
