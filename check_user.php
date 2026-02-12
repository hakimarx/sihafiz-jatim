<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$username = '081234567890';
echo "Checking user '$username':\n";
$u = Database::queryOne("SELECT * FROM users WHERE username = :u", ['u' => $username]);
if (!$u) {
    echo "User not found.\n";
    exit;
}
echo "User found: ID {$u['id']} | Active: {$u['is_active']} | Role: {$u['role']}\n";

$h = Database::queryOne("SELECT * FROM hafiz WHERE user_id = :uid", ['uid' => $u['id']]);
if (!$h) {
    echo "Hafiz record NOT found for User ID {$u['id']}.\n";
} else {
    echo "Hafiz record found: ID {$h['id']} | Active: {$h['is_aktif']} | User ID: {$h['user_id']}\n";
}
