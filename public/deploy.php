<?php
/**
 * ============================================
 * MANUAL DEPLOY (via browser, harus login admin)
 * ============================================
 * URL: https://hafizjatim.my.id/deploy.php?key=SECRET
 * 
 * Untuk trigger deploy manual tanpa harus SSH ke server.
 * Dilindungi dengan secret key.
 * ============================================
 */

// Secret key untuk akses manual deploy
$deployKey = 'sihafiz-manual-deploy-2026';

// Validate key
if (($_GET['key'] ?? '') !== $deployKey) {
    http_response_code(403);
    die('❌ Access denied. Invalid deploy key.');
}

// ===== DIAGNOSTIC ACTIONS =====
$action = $_GET['action'] ?? 'deploy';

if ($action !== 'deploy') {
    require_once __DIR__ . '/../config/app.php';
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/security.php';
    header('Content-Type: application/json');
    
    try {
        $result = [];
        
        if ($action === 'status') {
            $result['db_connected'] = true;
            $users = Database::query("SELECT id, username, role, nama, is_active, last_login FROM users ORDER BY id ASC LIMIT 20");
            $result['users'] = $users;
            $hafiz_count = Database::queryOne("SELECT COUNT(*) as count FROM hafiz");
            $result['hafiz_count'] = $hafiz_count['count'];
            $pending = Database::queryOne("SELECT COUNT(*) as count FROM users WHERE is_active = 0 AND role = 'hafiz'");
            $result['pending_count'] = $pending['count'];
            
            // Check register view version
            $registerContent = @file_get_contents(__DIR__ . '/../src/Views/auth/register.php');
            $result['register_version'] = (strpos($registerContent, 'Klaim Akun Hafiz') !== false) ? 'new (Klaim NIK)' : 'old (Pendaftaran Hafiz)';
            
            // File check
            $result['files'] = [
                'check_status_exists' => file_exists(__DIR__ . '/check_status.php'),
                'index_exists' => file_exists(__DIR__ . '/index.php'),
                'dir_listing' => scandir(__DIR__),
            ];
        }
        
        if ($action === 'verify-password') {
            $user = Database::queryOne("SELECT id, username, password, role, is_active FROM users WHERE username = 'admin'");
            if ($user) {
                $result['user'] = $user;
                $result['verify_admin123'] = password_verify('admin123', $user['password']);
            } else {
                $result['error'] = 'Admin user not found';
            }
        }
        
        if ($action === 'reset-admin') {
            $newHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
            Database::execute(
                "UPDATE users SET password = :password, is_active = 1 WHERE username = 'admin'",
                ['password' => $newHash]
            );
            $result['message'] = 'Admin password reset to admin123';
        }
        
        if ($action === 'list-files') {
            $result['public_dir'] = scandir(__DIR__);
            $result['root_dir'] = scandir(__DIR__ . '/..');
            $result['views_auth'] = scandir(__DIR__ . '/../src/Views/auth');
        }
        
        echo json_encode($result, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
    }
    exit;
}

$repoDir = __DIR__ . '/..';
$logFile = __DIR__ . '/../deploy.log';
$branch = 'main';
$timestamp = date('Y-m-d H:i:s');

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>Manual Deploy</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;} ";
echo ".success{color:#00ff88;} .error{color:#ff4444;} .info{color:#4488ff;} ";
echo "pre{background:#0f0f23;padding:15px;border-radius:8px;overflow-x:auto;}</style>";
echo "</head><body>";
echo "<h2>🚀 SiHafiz Manual Deploy</h2>";
echo "<p class='info'>Timestamp: {$timestamp}</p>";
echo "<p class='info'>Branch: {$branch}</p><hr>";

chdir($repoDir);

$commands = [
    'git status' => 'Cek status repository',
    "git fetch origin {$branch} 2>&1" => 'Fetch perubahan terbaru',
    "git reset --hard origin/{$branch} 2>&1" => 'Update ke commit terbaru',
    'git log --oneline -5' => 'Riwayat 5 commit terakhir',
];

$allSuccess = true;
$log = "============================================\n";
$log .= "MANUAL DEPLOY: {$timestamp}\n";
$log .= "============================================\n";

foreach ($commands as $cmd => $desc) {
    echo "<h3>{$desc}</h3>";
    echo "<pre>$ {$cmd}\n";
    
    $result = shell_exec($cmd);
    echo htmlspecialchars($result ?? 'Command failed!');
    echo "</pre>";
    
    $log .= "$ {$cmd}\n{$result}\n";
    
    if ($result === null) {
        $allSuccess = false;
        echo "<p class='error'>❌ Command gagal!</p>";
    }
}

$status = $allSuccess ? 'SUCCESS' : 'FAILED';
$log .= "Status: {$status}\n\n";
file_put_contents($logFile, $log, FILE_APPEND);

echo "<hr>";
if ($allSuccess) {
    echo "<h2 class='success'>✅ Deploy berhasil!</h2>";
} else {
    echo "<h2 class='error'>❌ Deploy gagal. Periksa output di atas.</h2>";
}

echo "<p><a href='/' style='color:#4488ff;'>← Kembali ke halaman utama</a></p>";
echo "</body></html>";
