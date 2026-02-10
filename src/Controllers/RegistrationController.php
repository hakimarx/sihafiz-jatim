<?php

/**
 * Registration Controller
 * =======================
 * Skema Registrasi: "KLAIM NIK"
 * 
 * ALUR:
 * 1. Hafiz input NIK (16 digit)
 * 2. Sistem cek NIK di database (dari data import CSV yang sudah lulus)
 * 3. Jika DITEMUKAN → Tampilkan nama (samaran) untuk konfirmasi
 * 4. Hafiz input Tanggal Lahir sebagai verifikasi identitas
 * 5. Jika cocok → Hafiz set No HP + Password
 * 6. Akun status "pending" → menunggu approval admin kabko
 * 7. Admin kabko approve → akun aktif, bisa login
 * 
 * KEAMANAN:
 * - NIK harus sudah ada di database (anti spam registrasi asal)
 * - Tanggal lahir sebagai verifikasi (data pribadi yang hanya pemilik tahu)
 * - Approval admin kabko (double check)
 * - Rate limiting via captcha
 * - CSRF protection
 */

class RegistrationController extends Controller
{
    /**
     * Step 1: Form input NIK
     */
    public function index(): void
    {
        if (isLoggedIn()) {
            $this->redirect(APP_URL . '/');
            return;
        }

        $captcha = generateCaptcha();

        $this->view('auth.register', [
            'title' => 'Klaim Akun Hafiz - ' . APP_NAME,
            'captcha' => $captcha,
            'step' => 'nik'
        ]);
    }

    /**
     * Step 2: Cek NIK dan tampilkan konfirmasi
     */
    public function checkNik(): void
    {
        if (!$this->isPost()) {
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Validate CSRF
        if (!$this->validateCsrf()) {
            setFlash('error', 'Sesi tidak valid. Silakan coba lagi.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        $nik = sanitizeNik($this->input('nik'));
        $captchaInput = $this->input('captcha');

        // Validate Captcha
        if (!validateCaptcha($captchaInput)) {
            setFlash('error', 'Jawaban captcha salah. Silakan coba lagi.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Validate NIK format
        if (!isValidNik($nik)) {
            setFlash('error', 'NIK harus 16 digit angka.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Cari NIK di database (dari data import)
        $hafiz = Hafiz::findByNik($nik);

        if (!$hafiz) {
            setFlash('error', 'NIK tidak ditemukan dalam database hafiz yang telah lulus seleksi. Pastikan NIK Anda benar atau hubungi admin kabupaten/kota Anda.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Cek apakah sudah punya akun user
        if (!empty($hafiz['user_id'])) {
            setFlash('error', 'NIK ini sudah memiliki akun. Silakan login menggunakan akun Anda, atau hubungi admin jika lupa password.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        // Samarkan nama: "MO** FA**** RO****"
        $namaSamaran = $this->maskName($hafiz['nama']);

        // Simpan NIK di session untuk step selanjutnya
        $_SESSION['reg_nik'] = $nik;
        $_SESSION['reg_hafiz_id'] = $hafiz['id'];
        $_SESSION['reg_step'] = 'verify';
        $_SESSION['reg_time'] = time(); // timeout 10 menit

        $captcha = generateCaptcha();

        $this->view('auth.register_verify', [
            'title' => 'Verifikasi Identitas - ' . APP_NAME,
            'nama_samaran' => $namaSamaran,
            'kabupaten' => $hafiz['kabupaten_kota_nama'] ?? '',
            'captcha' => $captcha,
            'step' => 'verify'
        ]);
    }

    /**
     * Step 3: Verifikasi tanggal lahir dan set password
     */
    public function verify(): void
    {
        if (!$this->isPost()) {
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Validate CSRF
        if (!$this->validateCsrf()) {
            setFlash('error', 'Sesi tidak valid. Silakan coba lagi.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Check session
        if (empty($_SESSION['reg_nik']) || empty($_SESSION['reg_hafiz_id']) || $_SESSION['reg_step'] !== 'verify') {
            setFlash('error', 'Sesi pendaftaran tidak valid. Silakan mulai ulang.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Check timeout (10 menit)
        if (time() - $_SESSION['reg_time'] > 600) {
            unset($_SESSION['reg_nik'], $_SESSION['reg_hafiz_id'], $_SESSION['reg_step'], $_SESSION['reg_time']);
            setFlash('error', 'Sesi pendaftaran telah habis (10 menit). Silakan mulai ulang.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        $nik = $_SESSION['reg_nik'];
        $hafizId = $_SESSION['reg_hafiz_id'];
        $tanggalLahirInput = $this->input('tanggal_lahir');
        $telepon = sanitizePhone($this->input('telepon'));
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');

        $errors = [];

        // Validasi tanggal lahir
        $hafiz = Hafiz::findById($hafizId);
        if (!$hafiz) {
            setFlash('error', 'Data tidak ditemukan. Silakan mulai ulang.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Bandingkan tanggal lahir (format input: YYYY-MM-DD atau DD/MM/YYYY)
        $tglLahirDb = $hafiz['tanggal_lahir']; // format YYYY-MM-DD
        $tglLahirUser = $this->parseInputDate($tanggalLahirInput);

        if (empty($tglLahirUser) || $tglLahirUser !== $tglLahirDb) {
            $errors[] = 'Tanggal lahir tidak cocok dengan data kami. Pastikan sesuai dengan KTP/NIK Anda.';
        }

        // Validasi no HP  
        if (empty($telepon) || strlen($telepon) < 10) {
            $errors[] = 'Nomor HP harus diisi (minimal 10 digit).';
        }

        // Validasi password
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        // Cek no HP sudah dipakai
        if (!empty($telepon) && User::usernameExists($telepon)) {
            $errors[] = 'Nomor HP sudah terdaftar untuk akun lain.';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            
            // Tampilkan ulang form
            $namaSamaran = $this->maskName($hafiz['nama']);
            $captcha = generateCaptcha();

            $this->view('auth.register_verify', [
                'title' => 'Verifikasi Identitas - ' . APP_NAME,
                'nama_samaran' => $namaSamaran,
                'kabupaten' => $hafiz['kabupaten_kota_nama'] ?? '',
                'captcha' => $captcha,
                'step' => 'verify',
                'old_telepon' => $telepon
            ]);
            return;
        }

        try {
            // Create user account
            $userId = User::create([
                'username' => $telepon,
                'password' => $password,
                'role' => ROLE_HAFIZ,
                'kabupaten_kota_id' => $hafiz['kabupaten_kota_id'],
                'nama' => $hafiz['nama'],
                'telepon' => $telepon,
                'is_active' => 0 // PENDING approval admin
            ]);

            // Link hafiz ke user
            Hafiz::update($hafizId, []);
            Database::execute(
                "UPDATE hafiz SET user_id = :user_id WHERE id = :id",
                ['user_id' => $userId, 'id' => $hafizId]
            );

            // Clear registration session
            unset($_SESSION['reg_nik'], $_SESSION['reg_hafiz_id'], $_SESSION['reg_step'], $_SESSION['reg_time']);

            setFlash('success', 
                '<strong>Pendaftaran berhasil!</strong><br><br>' .
                'Nama: <b>' . htmlspecialchars($hafiz['nama']) . '</b><br>' .
                'Username (No HP): <b>' . htmlspecialchars($telepon) . '</b><br><br>' .
                '<span class="text-warning">⏳ Akun Anda sedang menunggu persetujuan admin kabupaten/kota.</span><br>' .
                'Anda akan dapat login setelah admin memverifikasi dan mengaktifkan akun Anda.'
            );
            $this->redirect(APP_URL . '/login');
        } catch (Exception $e) {
            error_log("Error in registration: " . $e->getMessage());
            setFlash('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi atau hubungi admin.');
            $this->redirect(APP_URL . '/register');
        }
    }

    /**
     * Samarkan nama: "MUHAMMAD FATHUR ROHMAN" → "MU**** FA**** RO****"
     */
    private function maskName(string $nama): string
    {
        $words = explode(' ', trim($nama));
        $masked = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) <= 2) {
                $masked[] = $word;
            } else {
                $visible = substr($word, 0, 2);
                $masked[] = $visible . str_repeat('*', min(strlen($word) - 2, 4));
            }
        }
        return implode(' ', $masked);
    }

    /**
     * Parse tanggal dari input (YYYY-MM-DD atau DD/MM/YYYY)
     */
    private function parseInputDate(string $input): ?string 
    {
        $input = trim($input);
        
        // Format YYYY-MM-DD (dari input type="date")
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
            return $input;
        }
        
        // Format DD/MM/YYYY
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $input, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        
        return null;
    }
}
