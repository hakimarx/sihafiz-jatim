<?php

/**
 * Application Configuration
 * =========================
 * Konstanta dan parameter bisnis disimpan di sini.
 * Semua konfigurasi diambil dari environment, tidak hardcoded.
 */

// Load .env file jika ada (untuk development)
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1], " \t\n\r\0\x0B\"'");

            if (!getenv($key)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load .env dari root project
loadEnv(__DIR__ . '/../.env');

// ============================================
// KONSTANTA APLIKASI
// ============================================

// Nama Aplikasi
define('APP_NAME', getenv('APP_NAME') ?: 'SiHafiz Jatim');

// Base URL Aplikasi
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8080');

// Environment (development/production)
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// ============================================
// PARAMETER BISNIS
// ============================================

// Tahun Anggaran Aktif
define('TAHUN_ANGGARAN', (int)(getenv('TAHUN_ANGGARAN') ?: date('Y')));

// Kuota Total Penerima Insentif
define('KUOTA_TOTAL', (int)(getenv('KUOTA_TOTAL') ?: 1000));

// Maksimal Ukuran Upload (2MB default)
define('MAX_UPLOAD_SIZE', (int)(getenv('MAX_UPLOAD_SIZE') ?: 2097152));

// ============================================
// USER ROLES
// ============================================
define('ROLE_ADMIN_PROV', 'admin_prov');
define('ROLE_ADMIN_KABKO', 'admin_kabko');
define('ROLE_PENGUJI', 'penguji');
define('ROLE_HAFIZ', 'hafiz');

// ============================================
// STATUS KELULUSAN
// ============================================
define('STATUS_LULUS', 'lulus');
define('STATUS_TIDAK_LULUS', 'tidak_lulus');
define('STATUS_PENDING', 'pending');

// ============================================
// JENIS KEGIATAN LAPORAN HARIAN
// ============================================
define('KEGIATAN_TYPES', ['mengajar', 'murojah', 'khataman', 'lainnya']);

// ============================================
// STATUS VERIFIKASI LAPORAN
// ============================================
define('VERIFIKASI_PENDING', 'pending');
define('VERIFIKASI_DISETUJUI', 'disetujui');
define('VERIFIKASI_DITOLAK', 'ditolak');

// ============================================
// PATH CONSTANTS
// ============================================
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('VIEWS_PATH', ROOT_PATH . '/src/Views');

// ============================================
// SESSION CONFIGURATION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', APP_ENV === 'production' ? 1 : 0);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// ============================================
// GOOGLE OAUTH CONFIGURATION
// ============================================
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI', APP_URL . '/login/google/callback');
