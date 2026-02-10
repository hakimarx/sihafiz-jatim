<?php
/**
 * ============================================
 * GITHUB WEBHOOK AUTO-DEPLOY
 * ============================================
 * Endpoint: https://hafizjatim.my.id/webhook-deploy.php
 * 
 * Setiap kali push ke GitHub, script ini akan:
 * 1. Validasi request dari GitHub (secret token)
 * 2. Jalankan git pull untuk update kode
 * 3. Log hasil deploy
 * 
 * SETUP:
 * 1. Upload file ini ke public/ folder di server
 * 2. Buat webhook di GitHub repo settings
 * 3. Set secret token yang sama di bawah ini
 * ============================================
 */

// ===== KONFIGURASI =====
$secret = 'sihafiz-deploy-2026-secret-key';  // Ganti dengan secret yang sama di GitHub
$branch = 'main';                             // Branch yang di-monitor
$logFile = __DIR__ . '/../deploy.log';        // File log deploy
$repoDir = __DIR__ . '/..';                   // Root directory project

// ===== SECURITY: Hanya terima POST =====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

// ===== VALIDASI GITHUB SIGNATURE =====
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (!empty($secret)) {
    $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    if (!hash_equals($hash, $signature)) {
        http_response_code(403);
        $msg = date('Y-m-d H:i:s') . " [DENIED] Invalid signature\n";
        file_put_contents($logFile, $msg, FILE_APPEND);
        die(json_encode(['error' => 'Invalid signature']));
    }
}

// ===== PARSE PAYLOAD =====
$data = json_decode($payload, true);

// Cek apakah push ke branch yang benar
$ref = $data['ref'] ?? '';
if ($ref !== "refs/heads/{$branch}") {
    http_response_code(200);
    die(json_encode(['message' => "Ignored: push to {$ref}, not {$branch}"]));
}

// ===== JALANKAN DEPLOY =====
$timestamp = date('Y-m-d H:i:s');
$pusher = $data['pusher']['name'] ?? 'unknown';
$commitMsg = $data['head_commit']['message'] ?? 'no message';

$log = "============================================\n";
$log .= "DEPLOY: {$timestamp}\n";
$log .= "Pusher: {$pusher}\n";
$log .= "Commit: {$commitMsg}\n";
$log .= "============================================\n";

// Pindah ke directory project
chdir($repoDir);

// Jalankan git pull
$commands = [
    'git fetch origin ' . $branch . ' 2>&1',
    'git reset --hard origin/' . $branch . ' 2>&1',
];

$output = [];
$success = true;

foreach ($commands as $cmd) {
    $result = shell_exec($cmd);
    $output[] = "$ {$cmd}\n{$result}";
    $log .= "$ {$cmd}\n{$result}\n";
    
    if ($result === null) {
        $success = false;
        $log .= "[ERROR] Command failed!\n";
    }
}

$status = $success ? 'SUCCESS' : 'FAILED';
$log .= "Status: {$status}\n\n";

// Tulis log
file_put_contents($logFile, $log, FILE_APPEND);

// Response
http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'status' => $status,
    'timestamp' => $timestamp,
    'branch' => $branch,
    'pusher' => $pusher,
    'commit' => substr($commitMsg, 0, 100),
    'output' => $output,
]);
