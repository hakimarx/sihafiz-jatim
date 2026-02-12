<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

echo "Checking for active users with role 'hafiz' but associated hafiz record is INACTIVE...\n";

$sql = "
    SELECT u.id, u.username, u.nama, h.id as hafiz_id, h.is_aktif
    FROM users u
    JOIN hafiz h ON u.id = h.user_id
    WHERE u.role = 'hafiz' 
    AND u.is_active = 1 
    AND h.is_aktif = 0
    LIMIT 20
";

$result = Database::query($sql);

if (empty($result)) {
    echo "GOOD: No users with inactive hafiz records found.\n";
} else {
    echo "FOUND " . count($result) . " ACTIVE USERS WITH INACTIVE HAFIZ RECORD:\n";
    foreach ($result as $u) {
        echo "User ID: {$u['id']} | Name: {$u['nama']} | Hafiz ID: {$u['hafiz_id']} (is_aktif: 0)\n";
    }
}
