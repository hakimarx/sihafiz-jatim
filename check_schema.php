<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

ob_start();
$cols = Database::query('SHOW COLUMNS FROM users');
echo "=== USERS TABLE ===\n";
foreach($cols as $c) {
    echo $c['Field'] . '|' . $c['Type'] . '|' . $c['Null'] . '|' . ($c['Default'] ?? 'NONE') . "\n";
}

echo "\n=== HAFIZ TABLE ===\n";
$cols2 = Database::query('SHOW COLUMNS FROM hafiz');
foreach($cols2 as $c) {
    echo $c['Field'] . '|' . $c['Type'] . '|' . $c['Null'] . '|' . ($c['Default'] ?? 'NONE') . "\n";
}
$output = ob_get_clean();
file_put_contents(__DIR__ . '/schema_output.txt', $output);
echo "Done - check schema_output.txt\n";
