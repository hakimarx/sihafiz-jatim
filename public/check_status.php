<?php
/**
 * Check Status Script - Temporary for debugging
 * DELETE THIS FILE AFTER TESTING
 */

// Security key
$key = $_GET['key'] ?? '';
if ($key !== 'sihafiz-check-2026') {
    http_response_code(403);
    die('Forbidden');
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'status';

try {
    $result = [];
    
    if ($action === 'status') {
        // Check database connection
        $result['db_connected'] = true;
        
        // Count users
        $users = Database::query("SELECT id, username, role, nama, is_active, last_login FROM users ORDER BY id ASC");
        $result['users'] = $users;
        
        // Count hafiz
        $hafiz_count = Database::queryOne("SELECT COUNT(*) as count FROM hafiz");
        $result['hafiz_count'] = $hafiz_count['count'];
        
        // Count pending
        $pending = Database::queryOne("SELECT COUNT(*) as count FROM users WHERE is_active = 0 AND role = 'hafiz'");
        $result['pending_count'] = $pending['count'];
        
        // Last git info
        $gitHead = @file_get_contents(__DIR__ . '/../.git/HEAD');
        $result['git_head'] = trim($gitHead ?? 'unknown');
        
        // Check if .git exists
        $result['git_exists'] = is_dir(__DIR__ . '/../.git');
        
        // Check file version (check if register.php has "Klaim Akun Hafiz")
        $registerContent = @file_get_contents(__DIR__ . '/../src/Views/auth/register.php');
        $result['register_version'] = (strpos($registerContent, 'Klaim Akun Hafiz') !== false) ? 'new (Klaim NIK)' : 'old (Pendaftaran Hafiz)';
    }
    
    if ($action === 'reset-admin') {
        // Reset admin password to admin123
        $newHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
        Database::execute(
            "UPDATE users SET password = :password WHERE username = 'admin'",
            ['password' => $newHash]
        );
        $result['message'] = 'Admin password reset to admin123';
        $result['hash'] = $newHash;
    }
    
    if ($action === 'verify-password') {
        $user = Database::queryOne("SELECT password FROM users WHERE username = 'admin'");
        if ($user) {
            $result['hash'] = $user['password'];
            $result['verify_admin123'] = password_verify('admin123', $user['password']);
        } else {
            $result['error'] = 'Admin user not found';
        }
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
}
