<?php
require_once __DIR__ . '/config/database.php';

try {
    $result = Database::query("DESCRIBE users");
    foreach ($result as $row) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
