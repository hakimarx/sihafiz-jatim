<?php
/**
 * ============================================
 * SIHAFIZ AUTO-DEPLOY INSTALLER
 * ============================================
 * Untuk hosting cPanel TANPA Terminal/SSH
 * 
 * CARA PAKAI:
 * 1. Upload file ini ke public_html/ via File Manager
 * 2. Buka di browser: https://hafizjatim.my.id/setup-deploy.php
 * 3. Ikuti instruksi di layar
 * 4. HAPUS file ini setelah setup selesai!
 * ============================================
 */

// ===== KONFIGURASI =====
$installKey = 'install-sihafiz-2026';
$githubRepo = 'https://github.com/hakimarx/sihafiz-jatim.git';
$branch = 'main';

// ===== SECURITY =====
if (($_GET['key'] ?? '') !== $installKey) {
    http_response_code(403);
    die('❌ Akses ditolak. Tambahkan ?key=' . $installKey . ' di URL.');
}

header('Content-Type: text/html; charset=utf-8');

$step = $_GET['step'] ?? 'check';
$baseDir = dirname(__FILE__); // public_html
$token = $_GET['token'] ?? '';
$username = $_GET['username'] ?? '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiHafiz Deploy Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', system-ui, sans-serif; 
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); 
            color: #e0e0e0; 
            min-height: 100vh; 
            padding: 30px; 
        }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #00ff88; margin-bottom: 10px; font-size: 1.8rem; }
        h2 { color: #4488ff; margin: 20px 0 10px; font-size: 1.3rem; }
        .card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 25px;
            margin: 15px 0;
            backdrop-filter: blur(10px);
        }
        .success { color: #00ff88; }
        .error { color: #ff4444; }
        .warning { color: #ffaa00; }
        .info { color: #4488ff; }
        pre {
            background: #0a0a1a;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.85rem;
            line-height: 1.6;
            margin: 10px 0;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s;
        }
        .btn-primary { background: #00ff88; color: #000; }
        .btn-primary:hover { background: #00cc6e; transform: translateY(-1px); }
        .btn-danger { background: #ff4444; color: #fff; }
        .btn-danger:hover { background: #cc3333; }
        .btn-info { background: #4488ff; color: #fff; }
        .btn-info:hover { background: #3366cc; }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            background: rgba(0,0,0,0.3);
            color: #fff;
            font-size: 1rem;
            margin: 5px 0 15px;
        }
        label { display: block; margin-top: 10px; color: #aaa; font-size: 0.9rem; }
        .step-indicator { 
            display: flex; gap: 10px; margin-bottom: 20px; 
        }
        .step-dot {
            width: 35px; height: 35px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 0.85rem;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
        }
        .step-dot.active { background: #00ff88; color: #000; border-color: #00ff88; }
        .step-dot.done { background: #4488ff; color: #fff; border-color: #4488ff; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        td { padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        td:first-child { color: #aaa; width: 200px; }
        .badge { 
            display: inline-block; padding: 3px 10px; border-radius: 20px; 
            font-size: 0.8rem; font-weight: 600; 
        }
        .badge-ok { background: rgba(0,255,136,0.15); color: #00ff88; }
        .badge-fail { background: rgba(255,68,68,0.15); color: #ff4444; }
        .badge-warn { background: rgba(255,170,0,0.15); color: #ffaa00; }
    </style>
</head>
<body>
<div class="container">
    <h1>🚀 SiHafiz Auto-Deploy Setup</h1>
    <p style="color:#aaa; margin-bottom: 20px;">Installer untuk hosting tanpa Terminal/SSH</p>

    <div class="step-indicator">
        <div class="step-dot <?= $step === 'check' ? 'active' : ($step !== 'check' ? 'done' : '') ?>">1</div>
        <div class="step-dot <?= $step === 'setup' ? 'active' : (in_array($step, ['clone','done']) ? 'done' : '') ?>">2</div>
        <div class="step-dot <?= $step === 'clone' ? 'active' : ($step === 'done' ? 'done' : '') ?>">3</div>
        <div class="step-dot <?= $step === 'done' ? 'active' : '' ?>">4</div>
    </div>

<?php if ($step === 'check'): ?>
    <!-- STEP 1: SYSTEM CHECK -->
    <div class="card">
        <h2>📋 Step 1: Cek Sistem</h2>
        <p>Memeriksa apakah server mendukung auto-deploy...</p>
        
        <table>
            <?php
            // Check PHP version
            $phpOk = version_compare(PHP_VERSION, '7.4', '>=');
            echo "<tr><td>PHP Version</td><td>" . PHP_VERSION . " ";
            echo $phpOk ? '<span class="badge badge-ok">✓ OK</span>' : '<span class="badge badge-fail">✗ Terlalu lama</span>';
            echo "</td></tr>";

            // Check shell_exec
            $shellExecOk = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));
            echo "<tr><td>shell_exec()</td><td>";
            echo $shellExecOk ? '<span class="badge badge-ok">✓ Tersedia</span>' : '<span class="badge badge-fail">✗ Diblokir</span>';
            echo "</td></tr>";

            // Check exec
            $execOk = function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));
            echo "<tr><td>exec()</td><td>";
            echo $execOk ? '<span class="badge badge-ok">✓ Tersedia</span>' : '<span class="badge badge-fail">✗ Diblokir</span>';
            echo "</td></tr>";

            // Check git
            $gitVersion = null;
            if ($shellExecOk) {
                $gitVersion = trim(shell_exec('git --version 2>&1') ?? '');
            } elseif ($execOk) {
                exec('git --version 2>&1', $out, $ret);
                $gitVersion = $ret === 0 ? implode("\n", $out) : null;
            }
            $gitOk = $gitVersion && strpos($gitVersion, 'git version') !== false;
            echo "<tr><td>Git</td><td>";
            echo $gitOk ? htmlspecialchars($gitVersion) . ' <span class="badge badge-ok">✓ OK</span>' : '<span class="badge badge-fail">✗ Tidak ditemukan</span>';
            echo "</td></tr>";

            // Check write permission
            $writeOk = is_writable($baseDir);
            echo "<tr><td>Write Permission</td><td>";
            echo $writeOk ? '<span class="badge badge-ok">✓ Writable</span>' : '<span class="badge badge-fail">✗ Read-only</span>';
            echo "</td></tr>";

            // Check .git folder exists
            $gitExists = is_dir($baseDir . '/.git');
            echo "<tr><td>Git Repository</td><td>";
            echo $gitExists ? '<span class="badge badge-ok">✓ Sudah ada .git</span>' : '<span class="badge badge-warn">⚠ Belum ada</span>';
            echo "</td></tr>";

            // Current directory
            echo "<tr><td>Working Directory</td><td><code>" . htmlspecialchars($baseDir) . "</code></td></tr>";

            $canProceed = ($shellExecOk || $execOk) && $gitOk && $writeOk;
            ?>
        </table>
    </div>

    <?php if ($canProceed): ?>
        <div class="card" style="border-color: rgba(0,255,136,0.3);">
            <p class="success"><strong>✅ Server mendukung auto-deploy!</strong></p>
            <p style="margin-top:10px;">Lanjutkan ke langkah berikutnya untuk setup Git credentials.</p>
            <a href="?key=<?= $installKey ?>&step=setup" class="btn btn-primary" style="margin-top:15px;">
                Lanjutkan ke Step 2 →
            </a>
        </div>
    <?php elseif (!$gitOk): ?>
        <div class="card" style="border-color: rgba(255,68,68,0.3);">
            <p class="error"><strong>❌ Git tidak tersedia di server!</strong></p>
            <h2>Alternatif: Gunakan cPanel Git Version Control</h2>
            <ol style="margin-left:20px; line-height:2;">
                <li>Login ke cPanel</li>
                <li>Cari menu <strong>"Git™ Version Control"</strong></li>
                <li>Klik <strong>Create</strong></li>
                <li>Clone URL: <code><?= $githubRepo ?></code></li>
                <li>Repository Path: <code><?= $baseDir ?></code></li>
                <li>Klik <strong>Create</strong></li>
            </ol>
            <p style="margin-top:15px" class="info">Jika tidak ada menu Git Version Control, hubungi provider hosting Anda untuk mengaktifkan Git.</p>
        </div>
    <?php elseif (!$shellExecOk && !$execOk): ?>
        <div class="card" style="border-color: rgba(255,68,68,0.3);">
            <p class="error"><strong>❌ shell_exec dan exec diblokir oleh hosting!</strong></p>
            <h2>Alternatif: Gunakan cPanel Git Version Control</h2>
            <ol style="margin-left:20px; line-height:2;">
                <li>Login ke cPanel → cari <strong>"Git™ Version Control"</strong></li>
                <li>Klik <strong>Create</strong></li>
                <li>Clone URL: <code><?= $githubRepo ?></code></li>
                <li>Repository Path: <code><?= $baseDir ?></code></li>
            </ol>
            <p style="margin-top:15px">Atau hubungi hosting untuk mengaktifkan <code>shell_exec</code>.</p>
        </div>
    <?php endif; ?>

<?php elseif ($step === 'setup'): ?>
    <!-- STEP 2: GITHUB CREDENTIALS -->
    <div class="card">
        <h2>🔑 Step 2: GitHub Credentials</h2>
        <p>Masukkan credential GitHub untuk mengakses repository.</p>
        
        <div style="background:rgba(68,136,255,0.1); border-radius:8px; padding:15px; margin:15px 0;">
            <p class="info"><strong>📌 Anda butuh Personal Access Token GitHub:</strong></p>
            <ol style="margin-left:20px; line-height:1.8; margin-top:8px;">
                <li>Buka <a href="https://github.com/settings/tokens" target="_blank" style="color:#00ff88;">github.com/settings/tokens</a></li>
                <li>Klik <strong>"Generate new token (classic)"</strong></li>
                <li>Nama: <code>SiHafiz Deploy</code></li>
                <li>Expiration: <strong>No expiration</strong> (atau sesuai kebutuhan)</li>
                <li>Centang scope: <strong>✅ repo</strong> (Full control)</li>
                <li>Klik <strong>Generate token</strong></li>
                <li>Copy token yang muncul (hanya ditampilkan sekali!)</li>
            </ol>
        </div>

        <form method="GET" action="">
            <input type="hidden" name="key" value="<?= $installKey ?>">
            <input type="hidden" name="step" value="clone">
            
            <label for="username">GitHub Username</label>
            <input type="text" name="username" id="username" placeholder="contoh: hakimarx" required>
            
            <label for="token">Personal Access Token</label>
            <input type="password" name="token" id="token" placeholder="ghp_xxxxxxxxxxxxxxxxxxxx" required>
            
            <button type="submit" class="btn btn-primary" style="margin-top:10px;">
                🔄 Setup Git & Clone Repository →
            </button>
        </form>
    </div>

<?php elseif ($step === 'clone'): ?>
    <!-- STEP 3: CLONE & SETUP -->
    <div class="card">
        <h2>⚡ Step 3: Setup Repository</h2>
        <?php
        if (empty($token) || empty($username)) {
            echo '<p class="error">❌ Username dan token harus diisi!</p>';
            echo '<a href="?key=' . $installKey . '&step=setup" class="btn btn-info">← Kembali</a>';
        } else {
            $repoUrl = "https://{$username}:{$token}@github.com/hakimarx/sihafiz-jatim.git";
            $results = [];
            $allOk = true;

            // Function to run command
            function runCmd($cmd) {
                if (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
                    return shell_exec($cmd . ' 2>&1');
                } elseif (function_exists('exec')) {
                    exec($cmd . ' 2>&1', $output, $ret);
                    return implode("\n", $output);
                }
                return null;
            }

            chdir($baseDir);

            // Check if .git already exists
            $gitExists = is_dir($baseDir . '/.git');

            if (!$gitExists) {
                // Initialize git
                $steps = [
                    ['git init', 'Inisialisasi Git'],
                    ["git remote add origin {$repoUrl}", 'Set remote origin'],
                    ["git fetch origin {$branch}", 'Fetch dari GitHub'],
                    ["git checkout -b {$branch} origin/{$branch}", 'Checkout branch main'],
                ];
            } else {
                // Git already exists, just update remote and pull
                $steps = [
                    ["git remote set-url origin {$repoUrl}", 'Update remote URL'],
                    ["git fetch origin {$branch}", 'Fetch dari GitHub'],
                    ["git reset --hard origin/{$branch}", 'Update ke versi terbaru'],
                ];
            }

            // Store credentials
            $steps[] = ['git config credential.helper store', 'Simpan credentials'];

            foreach ($steps as $s) {
                $cmd = $s[0];
                $desc = $s[1];
                $safeCmd = str_replace($token, '***TOKEN***', $cmd);
                
                echo "<h3>{$desc}</h3><pre>$ {$safeCmd}\n";
                $output = runCmd($cmd);
                echo htmlspecialchars($output ?? 'Failed');
                echo "</pre>";

                if ($output === null) {
                    $allOk = false;
                    echo '<p class="error">❌ Gagal!</p>';
                }
            }

            // Show last 5 commits
            echo "<h3>📜 Riwayat Commit</h3><pre>";
            echo htmlspecialchars(runCmd('git log --oneline -5') ?? 'Failed');
            echo "</pre>";

            // Security: remove token from remote URL (use credential store instead)
            runCmd("git remote set-url origin {$githubRepo}");

            if ($allOk) {
                echo '<div style="background:rgba(0,255,136,0.1); padding:15px; border-radius:8px; margin:15px 0;">';
                echo '<p class="success"><strong>✅ Repository berhasil di-setup!</strong></p>';
                echo '<p style="margin-top:8px;">Git credentials tersimpan. Auto-deploy siap digunakan.</p>';
                echo '</div>';
                echo '<a href="?key=' . $installKey . '&step=done" class="btn btn-primary">Lanjut ke Step 4: Webhook →</a>';
            } else {
                echo '<p class="error">⚠️ Ada langkah yang gagal. Periksa output di atas.</p>';
                echo '<a href="?key=' . $installKey . '&step=setup" class="btn btn-info">← Coba lagi</a>';
            }
        }
        ?>
    </div>

<?php elseif ($step === 'done'): ?>
    <!-- STEP 4: WEBHOOK SETUP -->
    <div class="card">
        <h2>🎉 Step 4: Setup GitHub Webhook</h2>
        <p>Langkah terakhir! Setup webhook di GitHub agar deploy otomatis setiap push.</p>

        <div style="background:rgba(68,136,255,0.1); border-radius:8px; padding:15px; margin:15px 0;">
            <p class="info"><strong>📌 Lakukan langkah ini di GitHub:</strong></p>
            <ol style="margin-left:20px; line-height:2; margin-top:8px;">
                <li>Buka <a href="https://github.com/hakimarx/sihafiz-jatim/settings/hooks/new" target="_blank" style="color:#00ff88;">GitHub Webhook Settings</a></li>
                <li>Isi form berikut:</li>
            </ol>

            <table style="margin-top:10px;">
                <tr><td><strong>Payload URL</strong></td><td><code>https://hafizjatim.my.id/webhook-deploy.php</code></td></tr>
                <tr><td><strong>Content type</strong></td><td><code>application/json</code></td></tr>
                <tr><td><strong>Secret</strong></td><td><code>sihafiz-deploy-2026-secret-key</code></td></tr>
                <tr><td><strong>Events</strong></td><td>✅ Just the push event</td></tr>
                <tr><td><strong>Active</strong></td><td>✅ Centang</td></tr>
            </table>

            <ol start="3" style="margin-left:20px; line-height:2; margin-top:10px;">
                <li>Klik <strong>Add webhook</strong></li>
            </ol>
        </div>

        <div style="background:rgba(0,255,136,0.1); padding:15px; border-radius:8px; margin:15px 0;">
            <h3 class="success" style="margin-bottom:10px;">✅ Selesai!</h3>
            <p>Setelah webhook disetup, setiap <code>git push</code> ke GitHub akan otomatis mengupdate website.</p>
            <p style="margin-top:10px;">URL untuk deploy manual (jika perlu):</p>
            <pre>https://hafizjatim.my.id/deploy.php?key=sihafiz-manual-deploy-2026</pre>
        </div>

        <div style="background:rgba(255,68,68,0.1); padding:15px; border-radius:8px; margin:15px 0; border:1px solid rgba(255,68,68,0.2);">
            <p class="error"><strong>⚠️ PENTING: Hapus file ini setelah selesai!</strong></p>
            <p style="margin-top:5px;">File <code>setup-deploy.php</code> mengandung informasi sensitif. 
            Hapus via cPanel File Manager setelah setup berhasil.</p>
        </div>

        <a href="/" class="btn btn-primary">🏠 Buka Website</a>
        <a href="https://github.com/hakimarx/sihafiz-jatim/settings/hooks/new" target="_blank" class="btn btn-info">🔗 Buka GitHub Webhook</a>
    </div>

<?php endif; ?>

</div>
</body>
</html>
