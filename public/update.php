<?php
/**
 * ============================================
 * SIHAFIZ ZIP-BASED UPDATER (GIT-LESS)
 * ============================================
 * Digunakan jika server memblokir Git / shell_exec.
 * Mendownload kode terbaru dari GitHub sebagai ZIP dan mengekstraknya.
 */

$secretKey = 'sihafiz-update-2026';

if (($_GET['key'] ?? '') !== $secretKey) {
    die("Akses ditolak. Gunakan ?key=$secretKey");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$repoZipUrl = 'https://github.com/hakimarx/sihafiz-jatim/archive/refs/heads/main.zip';
$tempZipFile = __DIR__ . '/latest_update.zip';
$extractTo = dirname(__DIR__); // Root folder (public_html)

echo "<pre style='background:#1a1a2e; color:#e0e0e0; padding:20px; font-family:monospace;'>";
echo "<h2>🚀 SiHafiz Zip-Updater (Git-Less)</h2>";

// 1. Download ZIP
echo "📥 Mendownload update dari GitHub...\n";
$zipData = @file_get_contents($repoZipUrl);
if (!$zipData) {
    echo "❌ GAGAL: Tidak bisa mendownload ZIP. Mencoba menggunakan CURL...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $repoZipUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $zipData = curl_exec($ch);
    curl_close($ch);
}

if (!$zipData) {
    die("❌ FATAL: Server tidak bisa terhubung ke GitHub.");
}

file_put_contents($tempZipFile, $zipData);
echo "✅ ZIP berhasil didownload (" . round(filesize($tempZipFile)/1024, 2) . " KB)\n";

// 2. Extract ZIP
echo "📦 Mengekstrak update...\n";
$zip = new ZipArchive();
if ($zip->open($tempZipFile) === TRUE) {
    // GitHub ZIP has a root folder: sihafiz-jatim-main/
    $zipRootFolder = $zip->getNameIndex(0);
    
    // Extract everything to temp folder
    $tempExtractDir = __DIR__ . '/temp_update_' . time();
    mkdir($tempExtractDir);
    $zip->extractTo($tempExtractDir);
    $zip->close();
    
    echo "✅ ZIP diekstrak.\n";
    
    // 3. Move files
    echo "🚚 Memindahkan file ke root...\n";
    $sourceDir = $tempExtractDir . '/' . trim($zipRootFolder, '/');
    
    function moveFiles($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    moveFiles($src . '/' . $file, $dst . '/' . $file);
                } else {
                    rename($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    
    moveFiles($sourceDir, $extractTo);
    echo "✅ Semua file berhasil dipindahkan.\n";
    
    // 4. Cleanup
    echo "🧹 Membersihkan file sementara...\n";
    unlink($tempZipFile);
    // Recursive delete temp dir
    function deleteDir($dirPath) {
        if (!is_dir($dirPath)) return;
        $files = array_diff(scandir($dirPath), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dirPath/$file")) ? deleteDir("$dirPath/$file") : unlink("$dirPath/$file");
        }
        return rmdir($dirPath);
    }
    deleteDir($tempExtractDir);
    
    echo "<h2>✅ UPDATE SELESAI!</h2>";
    echo "<p>Silakan buka <a href='/' style='color:#00ff88;'>Halaman Utama</a>.</p>";
    echo "<p>Hapus file <strong>update.php</strong> ini demi keamanan.</p>";
} else {
    echo "❌ GAGAL: Tidak bisa membuka ZIP. Pastikan ekstensi ZipArchive PHP aktif.";
}

echo "</pre>";
