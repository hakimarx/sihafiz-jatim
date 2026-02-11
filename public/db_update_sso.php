<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php'; // For env vars if needed

echo "<h1>Database Update for SSO</h1>";

try {
    $pdo = Database::getConnection();

    // Check if google_id column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL AFTER email");
        echo "<p style='color: green;'>[SUCCESS] Added 'google_id' column to 'users' table.</p>";
    } else {
        echo "<p style='color: orange;'>[INFO] 'google_id' column already exists.</p>";
    }

    echo "<p>Database update completed successfully.</p>";
    echo "<a href='" . APP_URL . "'>Go to Home</a>";
} catch (Exception $e) {
    echo "<p style='color: red;'>[ERROR] " . $e->getMessage() . "</p>";
}
