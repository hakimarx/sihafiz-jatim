<?php

/**
 * SiHafiz Jatim - Single Entry Point
 * ===================================
 * Semua request masuk melalui file ini.
 */

// ============================================
// LOAD CONFIGURATION
// ============================================
require_once __DIR__ . '/../config/app.php';

// Error reporting disesuaikan dengan environment
error_reporting(E_ALL);
ini_set('display_errors', APP_ENV === 'development' ? 1 : 0);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

// ============================================
// LOAD CORE CLASSES
// ============================================
require_once __DIR__ . '/../src/Core/Router.php';
require_once __DIR__ . '/../src/Core/ImageProcessor.php';
require_once __DIR__ . '/../src/Core/Controller.php';
require_once __DIR__ . '/../src/Core/GoogleAuth.php';
require_once __DIR__ . '/../src/Core/Mailer.php';

// ============================================
// LOAD MODELS
// ============================================
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Hafiz.php';
require_once __DIR__ . '/../src/Models/LaporanHarian.php';
require_once __DIR__ . '/../src/Models/KabupatenKota.php';
require_once __DIR__ . '/../src/Models/Seleksi.php';
require_once __DIR__ . '/../src/Models/Setting.php';
require_once __DIR__ . '/../src/Models/Mutasi.php';
require_once __DIR__ . '/../src/Models/PasswordReset.php';

// ============================================
// LOAD CORE UTILITIES
// ============================================
require_once __DIR__ . '/../src/Core/ExcelExport.php';

// ============================================
// LOAD CONTROLLERS
// ============================================
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';
require_once __DIR__ . '/../src/Controllers/HafizController.php';
require_once __DIR__ . '/../src/Controllers/SeleksiController.php';
require_once __DIR__ . '/../src/Controllers/RegistrationController.php';
require_once __DIR__ . '/../src/Controllers/ProfileController.php';
require_once __DIR__ . '/../src/Controllers/ReportController.php';
require_once __DIR__ . '/../src/Controllers/MutasiController.php';

// ============================================
// INITIALIZE SESSION FROM COOKIE
// ============================================
checkRememberMe();

// ============================================
// INITIALIZE ROUTER
// ============================================
$router = new Router();

// ============================================
// DEFINE ROUTES
// ============================================

// Home (redirect to login or dashboard)
$router->get('/', function () {
    if (isLoggedIn()) {
        $role = getCurrentUserRole();
        if ($role === ROLE_HAFIZ) {
            header('Location: ' . APP_URL . '/hafiz/dashboard');
        } else {
            header('Location: ' . APP_URL . '/admin/dashboard');
        }
    } else {
        header('Location: ' . APP_URL . '/login');
    }
    exit;
});

// Auth Routes
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', function () {
    (new AuthController())->login();
});
$router->get('/logout', [AuthController::class, 'logout']);

// Google SSO Routes
$router->get('/login/google', [AuthController::class, 'loginWithGoogle']);
$router->get('/login/google/callback', [AuthController::class, 'googleCallback']);

// Forgot/Reset Password Routes
$router->get('/forgot-password', [AuthController::class, 'forgotPasswordForm']);
$router->post('/forgot-password', function () {
    (new AuthController())->forgotPasswordSubmit();
});
$router->get('/reset-password', [AuthController::class, 'resetPasswordForm']);
$router->post('/reset-password', function () {
    (new AuthController())->resetPasswordSubmit();
});

// Profile Routes (All Roles)
$router->get('/profile', [ProfileController::class, 'index']);
$router->post('/profile/update', [ProfileController::class, 'update']);

// Registration Routes (Klaim NIK - 3 Steps + Cari Nama alternatif)
$router->get('/register', [RegistrationController::class, 'index']);
$router->post('/register/check-nik', [RegistrationController::class, 'checkNik']);
$router->post('/register/check-nama', [RegistrationController::class, 'checkNama']);
$router->post('/register/choose', [RegistrationController::class, 'chooseHafiz']);
$router->post('/register/verify', [RegistrationController::class, 'verify']);
$router->post('/register/fresh', [RegistrationController::class, 'freshRegister']);

// Admin Routes
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/hafiz', [AdminController::class, 'hafizList']);
$router->get('/admin/hafiz/create', [AdminController::class, 'hafizCreate']);
$router->post('/admin/hafiz', [AdminController::class, 'hafizStore']);
$router->get('/admin/hafiz/{id}/edit', [AdminController::class, 'hafizEdit']);
$router->post('/admin/hafiz/{id}/update', [AdminController::class, 'hafizUpdate']);
$router->post('/admin/hafiz/{id}/delete', [AdminController::class, 'hafizDelete']);
$router->post('/admin/hafiz/{id}/mark-deceased', [AdminController::class, 'hafizMarkDeceased']);
$router->get('/admin/laporan', [AdminController::class, 'laporanList']);
$router->post('/admin/laporan/{id}/verify', [AdminController::class, 'laporanVerify']);

// Admin Management (Users)
$router->get('/admin/users', [AdminController::class, 'userList']);
$router->get('/admin/users/create', [AdminController::class, 'userCreate']);
$router->post('/admin/users', [AdminController::class, 'userStore']);
$router->get('/admin/users/{id}/edit', [AdminController::class, 'userEdit']);
$router->post('/admin/users/{id}/update', [AdminController::class, 'userUpdate']);
$router->post('/admin/users/{id}/delete', [AdminController::class, 'userDelete']);

// Admin Settings
$router->get('/admin/settings', [AdminController::class, 'settings']);
$router->post('/admin/settings', [AdminController::class, 'settingsUpdate']);
$router->post('/admin/import', [AdminController::class, 'importProcess']);

// Admin Pending Approval (Registrasi Klaim NIK)
$router->get('/admin/pending', [AdminController::class, 'pendingApproval']);
$router->post('/admin/pending/{id}/approve', [AdminController::class, 'approveUser']);
$router->post('/admin/pending/batch-approve', [AdminController::class, 'batchApprove']);
$router->post('/admin/pending/{id}/reject', [AdminController::class, 'rejectUser']);

// Mutasi Hafiz Routes
$router->get('/admin/mutasi', [MutasiController::class, 'index']);
$router->get('/admin/mutasi/create', [MutasiController::class, 'create']);
$router->post('/admin/mutasi/store', [MutasiController::class, 'store']);
$router->get('/admin/mutasi/{id}/approve', [MutasiController::class, 'approve']);
$router->get('/admin/mutasi/{id}/reject', [MutasiController::class, 'reject']);

// Hafiz Routes
$router->get('/hafiz/dashboard', [HafizController::class, 'dashboard']);
$router->get('/hafiz/laporan', [HafizController::class, 'laporanList']);
$router->get('/hafiz/laporan/create', [HafizController::class, 'laporanCreate']);
$router->post('/hafiz/laporan', [HafizController::class, 'laporanStore']);
$router->get('/hafiz/laporan/{id}/edit', [HafizController::class, 'laporanEdit']);
$router->post('/hafiz/laporan/{id}/update', [HafizController::class, 'laporanUpdate']);
$router->post('/hafiz/laporan/{id}/delete', [HafizController::class, 'laporanDelete']);
$router->get('/hafiz/profil', [HafizController::class, 'profil']);
$router->get('/hafiz/profil/edit', [HafizController::class, 'profilEdit']);
$router->post('/hafiz/profil/update', [HafizController::class, 'profilUpdate']);
$router->get('/hafiz/password', [HafizController::class, 'passwordEdit']);
$router->post('/hafiz/password/update', [HafizController::class, 'passwordUpdate']);

// Report / Cetak Laporan Routes
$router->get('/admin/reports', [ReportController::class, 'index']);
$router->get('/admin/reports/print/harian', [ReportController::class, 'printHarian']);
$router->get('/admin/reports/print/hafiz', [ReportController::class, 'printHafiz']);
$router->get('/admin/reports/print/absensi', [ReportController::class, 'printAbsensi']);

// Seleksi Routes
$router->get('/seleksi', [SeleksiController::class, 'index']);
$router->get('/seleksi/export', [SeleksiController::class, 'export']);
$router->get('/seleksi/{id}/nilai', [SeleksiController::class, 'inputNilai']);
$router->post('/seleksi/{id}/nilai', [SeleksiController::class, 'saveNilai']);
$router->get('/admin/laporan/export', [SeleksiController::class, 'exportLaporan']);

// ============================================
// RUN ROUTER
// ============================================
$router->run();
