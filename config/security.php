<?php

/**
 * Security Configuration & Helper Functions
 * ==========================================
 * Fungsi-fungsi keamanan seperti password hashing, CSRF protection, dll.
 */

// ============================================
// PASSWORD HASHING
// ============================================

/**
 * Hash password menggunakan bcrypt dengan cost optimal
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verifikasi password
 */
function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * Cek apakah password perlu di-rehash (misal: cost berubah)
 */
function needsRehash(string $hash): bool
{
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
}

// ============================================
// CSRF PROTECTION
// ============================================

/**
 * Generate CSRF token
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi CSRF token
 */
function validateCsrfToken(?string $token): bool
{
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate hidden input field untuk CSRF
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// ============================================
// INPUT SANITIZATION
// ============================================

/**
 * Sanitize string input
 */
function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize email
 */
function sanitizeEmail(string $email): string
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Validate email
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitize NIK (hanya angka, 16 digit)
 */
function sanitizeNik(string $nik): string
{
    return preg_replace('/[^0-9]/', '', $nik);
}

/**
 * Validate NIK format
 */
function isValidNik(string $nik): bool
{
    $clean = sanitizeNik($nik);
    return strlen($clean) === 16;
}

/**
 * Sanitize phone number
 */
function sanitizePhone(string $phone): string
{
    return preg_replace('/[^0-9+]/', '', $phone);
}

// ============================================
// SESSION MANAGEMENT
// ============================================

/**
 * Set user session setelah login
 */
function setUserSession(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['nama'] = $user['nama'] ?? $user['username'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    // Regenerate session ID untuk prevent session fixation
    session_regenerate_id(true);
}

/**
 * Cek apakah user sudah login
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user ID
 */
function getCurrentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole(): ?string
{
    return $_SESSION['role'] ?? null;
}

/**
 * Cek apakah user memiliki role tertentu
 */
function hasRole(string $role): bool
{
    return getCurrentUserRole() === $role;
}

/**
 * Cek apakah user adalah admin (provinsi atau kabko)
 */
function isAdmin(): bool
{
    $role = getCurrentUserRole();
    return $role === ROLE_ADMIN_PROV || $role === ROLE_ADMIN_KABKO;
}

/**
 * Logout user
 */
function logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Require login - redirect jika belum login
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}

/**
 * Require specific role
 */
function requireRole(string|array $roles): void
{
    requireLogin();

    $roles = (array) $roles;
    if (!in_array(getCurrentUserRole(), $roles)) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.';
        exit;
    }
}

// ============================================
// FLASH MESSAGES
// ============================================

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
