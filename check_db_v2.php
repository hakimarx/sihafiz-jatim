<?php
$envFile = __DIR__ . '/.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $env[trim($parts[0])] = trim($parts[1], " \t\n\r\0\x0B\"'");
        }
    }
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db   = $env['DB_NAME'] ?? 'sihafiz_jatim';
$user = $env['DB_USER'] ?? 'root';
$pass = $env['DB_PASS'] ?? '';

echo "Testing connection to $host:$port, DB: $db, User: $user\n";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "SUCCESS connecting with .env settings!\n";
} catch (PDOException $e) {
    echo "FAILED with .env settings: " . $e->getMessage() . "\n";
    
    // Try 3306 if it was something else
    if ($port != '3306') {
        echo "Retrying on port 3306...\n";
        try {
            $dsn = "mysql:host=$host;port=3306;dbname=$db;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            echo "SUCCESS connecting with port 3306!\n";
            echo "Please update your .env to use DB_PORT=3306\n";
        } catch (PDOException $e2) {
            echo "FAILED on port 3306: " . $e2->getMessage() . "\n";
        }
    }
    
    // Try empty password if it was 'root'
    if ($pass != '') {
        echo "Retrying with empty password...\n";
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            echo "SUCCESS connecting with empty password!\n";
            echo "Please update your .env to use empty DB_PASS\n";
        } catch (PDOException $e3) {
            echo "FAILED with empty password: " . $e3->getMessage() . "\n";
        }
    }
}
