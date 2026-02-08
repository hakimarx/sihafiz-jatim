<?php

/**
 * Registration Controller
 * =======================
 * Handle self-registration for new Hafiz
 */

class RegistrationController extends Controller
{
    /**
     * Show registration form
     */
    public function index(): void
    {
        if (isLoggedIn()) {
            $this->redirect(APP_URL . '/');
            return;
        }

        $kabkoList = KabupatenKota::getForDropdown();
        $captcha = generateCaptcha();

        $this->view('auth.register', [
            'title' => 'Pendaftaran Hafiz Baru - ' . APP_NAME,
            'kabkoList' => $kabkoList,
            'captcha' => $captcha
        ]);
    }

    /**
     * Process registration
     */
    public function store(): void
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
        $nama = strtoupper(trim($this->input('nama')));
        $telepon = sanitizePhone($this->input('telepon'));
        $email = sanitizeEmail($this->input('email'));
        $kabkoId = (int)$this->input('kabupaten_kota_id');
        $captchaInput = $this->input('captcha');

        // Validate Captcha
        if (!validateCaptcha($captchaInput)) {
            setFlash('error', 'Jawaban captcha salah. Silakan coba lagi.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        $errors = [];

        if (!isValidNik($nik)) {
            $errors[] = 'NIK harus 16 digit angka.';
        }

        if (empty($nama)) {
            $errors[] = 'Nama harus diisi.';
        }

        if (empty($kabkoId)) {
            $errors[] = 'Wilayah (Kabupaten/Kota) harus dipilih.';
        }

        if (Hafiz::nikExists($nik, TAHUN_ANGGARAN)) {
            $errors[] = 'NIK sudah terdaftar untuk tahun ini.';
        }

        if (User::usernameExists($telepon)) {
            $errors[] = 'Nomor HP sudah terdaftar. Silakan login.';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect(APP_URL . '/register');
            return;
        }

        try {
            // Create Hafiz (this also creates User account)
            // Default password will be NIK
            Hafiz::create([
                'nik' => $nik,
                'nama' => $nama,
                'telepon' => $telepon,
                'email' => $email,
                'kabupaten_kota_id' => $kabkoId,
                'tempat_lahir' => '',
                'tanggal_lahir' => date('Y-m-d'),
                'jenis_kelamin' => 'L',
                'alamat' => '',
                'desa_kelurahan' => '',
                'kecamatan' => '',
                'tahun_tes' => TAHUN_ANGGARAN
            ]);

            setFlash('success', 'Pendaftaran berhasil! Silakan login menggunakan <b>Username: Nomor HP</b> dan <b>Password: NIK</b> Anda.');
            $this->redirect(APP_URL . '/login');
        } catch (Exception $e) {
            error_log("Error in registration: " . $e->getMessage());
            setFlash('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi atau hubungi admin.');
            $this->redirect(APP_URL . '/register');
        }
    }
}
