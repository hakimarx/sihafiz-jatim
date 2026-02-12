<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

try {
    $result = Database::query("SHOW TABLES");
    foreach ($result as $row) {
        echo current($row) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
