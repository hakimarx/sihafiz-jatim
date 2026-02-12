<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

echo "Checking for active users with role 'hafiz' but no corresponding hafiz record...\n";

$sql = "
    SELECT u.id, u.username, u.nama, u.is_active
    FROM users u
    LEFT JOIN hafiz h ON u.id = h.user_id
    WHERE u.role = 'hafiz' 
    AND u.is_active = 1 
    AND h.id IS NULL
    LIMIT 20
";

$orphans = Database::query($sql);

if (empty($orphans)) {
    echo "GOOD: No orphaned active users found.\n";
} else {
    echo "FOUND " . count($orphans) . " ORPHANED ACTIVE USERS:\n";
    foreach ($orphans as $u) {
        echo "User ID: {$u['id']} | Username: {$u['username']} | Name: {$u['nama']}\n";
    }
    echo "\nThese users will be redirected to /hafiz/profil and see 'Profile Incomplete' error.\n";
}

echo "\nChecking for Hafiz records with NO user_id...\n";
$unlinkedHafiz = Database::query("SELECT id, nama, nik FROM hafiz WHERE user_id IS NULL OR user_id = 0 LIMIT 20");

if (empty($unlinkedHafiz)) {
    echo "GOOD: All Hafiz records are linked to a user.\n";
} else {
    echo "FOUND UNLINKED HAFIZ RECORDS:\n";
    foreach ($unlinkedHafiz as $h) {
        echo "Hafiz ID: {$h['id']} | Name: {$h['nama']} | NIK: {$h['nik']}\n";
    }
}
