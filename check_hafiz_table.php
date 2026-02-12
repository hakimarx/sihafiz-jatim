<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

try {
    $result = Database::query("DESCRIBE hafiz");
    foreach ($result as $row) {
        $default = $row['Default'] === null ? 'NULL' : $row['Default'];
        echo $row['Field'] . " - " . $row['Type'] . " - " . $default . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
