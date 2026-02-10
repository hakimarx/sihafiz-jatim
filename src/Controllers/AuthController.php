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

        if ($user) {
            // Check if this is a pending account
            if (isset($user['pending']) && $user['pending'] === true) {
                setFlash('error', 
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
