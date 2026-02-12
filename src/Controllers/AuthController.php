<?php

/**
 * Auth Controller
 * ================
 * Handle login, logout, dan authentication
 */

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function loginForm(): void
    {
        // Redirect jika sudah login
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $captcha = generateCaptcha();

        $this->view('auth.login', [
            'title' => 'Login - ' . APP_NAME,
            'captcha' => $captcha
        ]);
    }

    /**
     * Process login
     */
    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect(APP_URL . '/login');
            return;
        }

        // Validate CSRF
        if (!$this->validateCsrf()) {
            setFlash('error', 'Sesi tidak valid. Silakan coba lagi.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        $username = $this->input('username');
        $password = $this->input('password');
        $captchaInput = $this->input('captcha');

        // Validate Captcha
        if (!validateCaptcha($captchaInput)) {
            setFlash('error', 'Jawaban captcha salah. Silakan coba lagi.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        // Validate input
        if (empty($username) || empty($password)) {
            setFlash('error', 'Username dan password harus diisi.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        // Authenticate
        $user = User::authenticate($username, $password);

        if (!$user) {
            // Check if this is a NIK trying to login for the first time (Klaim NIK)
            $hafiz = Hafiz::findByNik($username);
            if ($hafiz && empty($hafiz['user_id'])) {
                setFlash('info', 'Data NIK <b>' . $username . '</b> ditemukan. Silakan lakukan <b>Aktivasi Akun</b> terlebih dahulu.');
                $this->redirect(APP_URL . '/register?nik=' . $username);
                return;
            }
        }

        if ($user) {
            // Check if this is a pending account
            if (isset($user['pending']) && $user['pending'] === true) {
                setFlash(
                    'error',
                    '<strong>Akun Anda belum diaktifkan.</strong><br>' .
                        'Akun atas nama <b>' . htmlspecialchars($user['nama'] ?? '') . '</b> sedang menunggu persetujuan admin kabupaten/kota.<br>' .
                        '<small class="text-muted">Silakan hubungi admin kabupaten/kota Anda untuk informasi lebih lanjut.</small>'
                );
                $this->redirect(APP_URL . '/login');
                return;
            }

            // Get nama from hafiz if role is hafiz
            if ($user['role'] === ROLE_HAFIZ) {
                $hafiz = Hafiz::findByUserId($user['id']);
                if ($hafiz) {
                    $user['nama'] = $hafiz['nama'];
                    $user['foto_profil'] = $hafiz['foto_profil'];
                }
            }

            setUserSession($user);

            // Handle Remember Me
            if ($this->input('remember')) {
                setRememberMe($user['id']);
            }

            setFlash('success', 'Selamat datang, ' . ($user['nama'] ?? $user['username']) . '!');
            $this->redirectToDashboard();
        } else {
            setFlash('error', 'Username atau password salah.');
            $this->redirect(APP_URL . '/login');
        }
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        logout();
        setFlash('success', 'Anda telah berhasil logout.');
        $this->redirect(APP_URL . '/login');
    }

    /**
     * Tampilkan form lupa password
     */
    public function forgotPasswordForm(): void
    {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $this->view('auth.forgot-password', [
            'title' => 'Lupa Password - ' . APP_NAME
        ]);
    }

    /**
     * Proses kirim email lupa password
     */
    public function forgotPasswordSubmit(): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            $this->redirect(APP_URL . '/forgot-password');
            return;
        }

        $email = trim($this->input('email'));

        if (empty($email)) {
            setFlash('error', 'Silakan masukkan email Anda.');
            $this->redirect(APP_URL . '/forgot-password');
            return;
        }

        $user = User::findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            PasswordReset::create($email, $token);

            if (Mailer::sendResetPassword($email, $token)) {
                setFlash('success', 'Instruksi reset password telah dikirim ke email Anda.');
            } else {
                setFlash('error', 'Gagal mengirim email. Silakan coba lagi nanti.');
            }
        } else {
            // Kita tetap tampilkan pesan sukses demi keamanan agar orang tidak menebak email
            setFlash('success', 'Jika email tersebut terdaftar, instruksi reset password telah dikirim.');
        }

        $this->redirect(APP_URL . '/forgot-password');
    }

    /**
     * Tampilkan form reset password
     */
    public function resetPasswordForm(): void
    {
        $token = $_GET['token'] ?? '';
        $reset = PasswordReset::findToken($token);

        if (!$reset) {
            setFlash('error', 'Link reset password tidak valid atau sudah kadaluarsa.');
            $this->redirect(APP_URL . '/forgot-password');
            return;
        }

        $this->view('auth.reset-password', [
            'title' => 'Reset Password - ' . APP_NAME,
            'token' => $token
        ]);
    }

    /**
     * Proses update password baru
     */
    public function resetPasswordSubmit(): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            $this->redirect(APP_URL . '/login');
            return;
        }

        $token = $this->input('token');
        $password = $this->input('password');
        $confirm = $this->input('confirm_password');

        $reset = PasswordReset::findToken($token);
        if (!$reset) {
            setFlash('error', 'Permintaan reset tidak valid.');
            $this->redirect(APP_URL . '/forgot-password');
            return;
        }

        if (empty($password) || strlen($password) < 6) {
            setFlash('error', 'Password minimal 6 karakter.');
            $this->redirect(APP_URL . '/reset-password?token=' . $token);
            return;
        }

        if ($password !== $confirm) {
            setFlash('error', 'Konfirmasi password tidak cocok.');
            $this->redirect(APP_URL . '/reset-password?token=' . $token);
            return;
        }

        $user = User::findByEmail($reset['email']);
        if ($user) {
            User::updatePassword((int)$user['id'], $password);
            PasswordReset::markAsUsed($token);
            setFlash('success', 'Password Anda berhasil diperbarui. Silakan login.');
            $this->redirect(APP_URL . '/login');
        } else {
            setFlash('error', 'Gagal mereset password.');
            $this->redirect(APP_URL . '/forgot-password');
        }
    }

    /**
     * Login with Google
     */
    public function loginWithGoogle(): void
    {
        if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
            setFlash('error', 'Konfigurasi Google Login belum diatur oleh Admin.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        $google = new GoogleAuth(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI);
        header('Location: ' . $google->getAuthUrl());
        exit;
    }

    /**
     * Google Callback
     */
    public function googleCallback(): void
    {
        if (isset($_GET['error'])) {
            setFlash('error', 'Login Google dibatalkan.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        if (!isset($_GET['code'])) {
            $this->redirect(APP_URL . '/login');
            return;
        }

        try {
            $google = new GoogleAuth(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI);
            $userInfo = $google->getUserInfo($_GET['code']);

            if (!$userInfo || empty($userInfo['email'])) {
                throw new Exception('Gagal mendapatkan informasi email dari Google.');
            }

            $email = $userInfo['email'];
            $googleId = $userInfo['sub'];

            // 1. Cek User by Google ID
            $user = User::findByGoogleId($googleId);

            if ($user) {
                $this->completeLogin($user);
                return;
            }

            // 2. Cek User by Email (Auto Link)
            $user = User::findByEmail($email);
            if ($user) {
                // Update Google ID
                User::updateGoogleId((int)$user['id'], $googleId);
                $this->completeLogin($user);
                return;
            }

            // 3. User tidak ditemukan
            $_SESSION['sso_register'] = [
                'type' => 'google',
                'email' => $email,
                'google_id' => $googleId,
                'nama' => $userInfo['name'] ?? '',
                'foto' => $userInfo['picture'] ?? ''
            ];

            setFlash('info', 'Email Google <strong>' . htmlspecialchars($email) . '</strong> belum terdaftar. Silakan lengkapi data pendaftaran Hafiz Baru.');
            $this->redirect(APP_URL . '/register');
        } catch (Exception $e) {
            error_log("Google Login Error: " . $e->getMessage());
            setFlash('error', 'Terjadi kesalahan saat login dengan Google.');
            $this->redirect(APP_URL . '/login');
        }
    }

    /**
     * Complete login process (creates session)
     */
    private function completeLogin(array $user): void
    {
        // Check if this is a pending account
        if (isset($user['pending']) && $user['pending'] === true) {
            setFlash(
                'error',
                '<strong>Akun Anda belum diaktifkan.</strong><br>' .
                    'Akun atas nama <b>' . htmlspecialchars($user['nama'] ?? '') . '</b> sedang menunggu persetujuan admin kabupaten/kota.<br>' .
                    '<small class="text-muted">Silakan hubungi admin kabupaten/kota Anda untuk informasi lebih lanjut.</small>'
            );
            $this->redirect(APP_URL . '/login');
            return;
        }

        if (!$user['is_active']) {
            setFlash('error', 'Akun Anda dinonaktifkan.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        // Get nama from hafiz if role is hafiz
        if ($user['role'] === ROLE_HAFIZ) {
            $hafiz = Hafiz::findByUserId($user['id']);
            if ($hafiz) {
                $user['nama'] = $hafiz['nama'];
                $user['foto_profil'] = $hafiz['foto_profil'];
            }
        }

        setUserSession($user);
        User::updateLastLogin($user['id']);

        // Handle Remember Me (only for manual login usually, but okay here implicitly if we want, or skip)
        // Note: For SSO we don't have a specific "remember me" checkbox in request usually, unless we add one to the initial state.
        // Skipping remember me for SSO for simplicity.

        setFlash('success', 'Selamat datang, ' . ($user['nama'] ?? $user['username']) . '!');
        $this->redirectToDashboard();
    }

    /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToDashboard(): void
    {
        $role = getCurrentUserRole();

        switch ($role) {
            case ROLE_ADMIN_PROV:
                $this->redirect(APP_URL . '/admin/dashboard');
                break;
            case ROLE_ADMIN_KABKO:
                $this->redirect(APP_URL . '/admin/dashboard');
                break;
            case ROLE_PENGUJI:
                $this->redirect(APP_URL . '/seleksi');
                break;
            case ROLE_HAFIZ:
                $this->redirect(APP_URL . '/hafiz/dashboard');
                break;
            default:
                $this->redirect(APP_URL);
        }
    }
}
