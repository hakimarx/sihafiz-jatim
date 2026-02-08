<?php
require_once __DIR__ . '/../config/database.php';

try {
    echo "Checking for remember_token column...\n";
    $columns = Database::query("DESCRIBE users");
    $exists = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'remember_token') {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        echo "Adding remember_token column...\n";
        Database::execute("ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) DEFAULT NULL AFTER is_active");
        Database::execute("CREATE INDEX idx_remember_token ON users(remember_token)");
        echo "Successfully added remember_token column.\n";
    } else {
        echo "Column remember_token already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
