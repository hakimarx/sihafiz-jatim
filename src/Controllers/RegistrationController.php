<?php

/**
 * Registration Controller
 * =======================
 * Skema Registrasi: "KLAIM NIK" + "CARI NAMA" (alternatif)
 * 
 * ALUR UTAMA (NIK VALID):
 * 1. Hafiz input NIK (16 digit)
 * 2. Sistem cek NIK di database (dari data import CSV yang sudah lulus)
 * 3. Jika DITEMUKAN → Tampilkan nama (samaran) untuk konfirmasi
 * 4. Hafiz input Tanggal Lahir sebagai verifikasi identitas
 * 5. Jika cocok → Hafiz set No HP + Password
 * 6. Akun status "pending" → menunggu approval admin kabko
 * 7. Admin kabko approve → akun aktif, bisa login
 * 
 * ALUR ALTERNATIF (NIK TIDAK VALID / KOSONG):
 * 1. Hafiz pilih tab "Cari Berdasarkan Nama"
 * 2. Input Nama + pilih Kabupaten/Kota
 * 3. Sistem tampilkan hasil pencarian (nama disamarkan)
 * 4. Hafiz pilih data mereka → lanjut ke verifikasi TGL Lahir
 * 5. Sama dengan alur utama step 4-7
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
     * Step 1: Form input NIK (+ tab Cari Nama)
     */
    public function index(): void
    {
        if (isLoggedIn()) {
            $this->redirect(APP_URL . '/');
            return;
        }

        $captcha = generateCaptcha();

        // Load list kabupaten/kota untuk form pencarian nama
        $kabkoList = KabupatenKota::getAll();

        $this->view('auth.register', [
            'title' => 'Klaim Akun Hafiz - ' . APP_NAME,
            'captcha' => $captcha,
            'step' => 'nik',
            'kabko_list' => $kabkoList ?? []
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
            setFlash('error', 'NIK harus 16 digit angka. Jika NIK Anda tidak sesuai format, gunakan tab <b>"Cari Berdasarkan Nama"</b> untuk mendaftar.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Cari NIK di database (dari data import)
        $hafiz = Hafiz::findByNik($nik);

        if (!$hafiz) {
            setFlash('error', 'NIK tidak ditemukan dalam database hafiz. Coba gunakan tab <b>"Cari Berdasarkan Nama"</b> atau hubungi admin kabupaten/kota Anda.');
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
     * ALTERNATIVE: Cari hafiz berdasarkan Nama + Kabupaten/Kota
     * Untuk hafiz yang NIK-nya tidak valid atau kosong
     */
    public function checkNama(): void
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

        $nama = trim($this->input('nama_cari'));
        $kabkoId = intval($this->input('kabko_id'));
        $captchaInput = $this->input('captcha');

        // Validate Captcha
        if (!validateCaptcha($captchaInput)) {
            setFlash('error', 'Jawaban captcha salah. Silakan coba lagi.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Validate nama input
        if (empty($nama) || strlen($nama) < 3) {
            setFlash('error', 'Nama harus diisi minimal 3 karakter.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        if (empty($kabkoId)) {
            setFlash('error', 'Pilih Kabupaten/Kota Anda.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Cari hafiz berdasarkan nama dan kabko (LIKE search)
        $results = Database::query(
            "SELECT h.id, h.nama, h.nik, h.tanggal_lahir, h.user_id,
                    kk.nama AS kabupaten_kota_nama
             FROM hafiz h
             LEFT JOIN kabupaten_kota kk ON h.kabupaten_kota_id = kk.id
             WHERE h.nama LIKE :nama 
             AND h.kabupaten_kota_id = :kabko_id
             AND h.is_aktif = 1
             LIMIT 10",
            ['nama' => '%' . $nama . '%', 'kabko_id' => $kabkoId]
        );

        if (empty($results)) {
            setFlash('error', 'Nama tidak ditemukan di Kabupaten/Kota yang dipilih. Pastikan nama sesuai dengan data yang terdaftar atau hubungi admin.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Samarkan dan siapkan list pilihan
        $choices = [];
        foreach ($results as $r) {
            // Skip yang sudah punya akun
            if (!empty($r['user_id'])) continue;

            $choices[] = [
                'id' => $r['id'],
                'nama_samaran' => $this->maskName($r['nama']),
                'nik_samaran' => $this->maskNik($r['nik']),
                'kabupaten' => $r['kabupaten_kota_nama'] ?? ''
            ];
        }

        if (empty($choices)) {
            setFlash('error', 'Semua data yang cocok sudah memiliki akun. Silakan login atau hubungi admin.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        // Simpan di session
        $_SESSION['reg_nama_search'] = $nama;
        $_SESSION['reg_choices'] = $choices;
        $_SESSION['reg_step'] = 'choose';
        $_SESSION['reg_time'] = time();

        $captcha = generateCaptcha();

        $this->view('auth.register_verify', [
            'title' => 'Pilih Data Anda - ' . APP_NAME,
            'choices' => $choices,
            'captcha' => $captcha,
            'step' => 'choose'
        ]);
    }

    /**
     * Step Choose: Hafiz memilih data mereka dari list pencarian nama
     * Lalu lanjut ke verifikasi tanggal lahir
     */
    public function chooseHafiz(): void
    {
        if (!$this->isPost()) {
            $this->redirect(APP_URL . '/register');
            return;
        }

        if (!$this->validateCsrf()) {
            setFlash('error', 'Sesi tidak valid. Silakan coba lagi.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        $hafizId = intval($this->input('hafiz_id'));

        if (empty($hafizId)) {
            setFlash('error', 'Pilih data Anda.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Check session
        if (empty($_SESSION['reg_choices']) || $_SESSION['reg_step'] !== 'choose') {
            setFlash('error', 'Sesi tidak valid. Silakan mulai ulang.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Verify the chosen ID is in the results
        $valid = false;
        foreach ($_SESSION['reg_choices'] as $c) {
            if ($c['id'] == $hafizId) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            setFlash('error', 'Data tidak valid. Silakan mulai ulang.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Get hafiz data
        $hafiz = Hafiz::findById($hafizId);
        if (!$hafiz) {
            setFlash('error', 'Data tidak ditemukan. Silakan mulai ulang.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Update session for verify step
        $_SESSION['reg_nik'] = $hafiz['nik'];
        $_SESSION['reg_hafiz_id'] = $hafiz['id'];
        $_SESSION['reg_step'] = 'verify';
        $_SESSION['reg_time'] = time();

        unset($_SESSION['reg_choices'], $_SESSION['reg_nama_search']);

        $captcha = generateCaptcha();

        $this->view('auth.register_verify', [
            'title' => 'Verifikasi Identitas - ' . APP_NAME,
            'nama_samaran' => $this->maskName($hafiz['nama']),
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

            setFlash(
                'success',
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
     * Samarkan NIK: "3510211110760003" → "3510****0003"
     */
    private function maskNik(string $nik): string
    {
        if (strlen($nik) < 8) return str_repeat('*', strlen($nik));
        return substr($nik, 0, 4) . str_repeat('*', strlen($nik) - 8) . substr($nik, -4);
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
    /**
     * Step: Fresh Register (Hafiz not in DB)
     */
    public function freshRegister(): void
    {
        if (!$this->isPost()) {
            $this->redirect(APP_URL . '/register');
            return;
        }

        if (!$this->validateCsrf()) {
            setFlash('error', 'Sesi tidak valid.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        $nama = trim($this->input('nama'));
        $nik = sanitizeNik($this->input('nik'));
        $kabkoId = intval($this->input('kabko_id'));
        $telepon = sanitizePhone($this->input('telepon'));

        // Basic Validation
        $errors = [];
        if (empty($nama) || strlen($nama) < 3) $errors[] = "Nama tidak valid.";
        if (!isValidNik($nik)) $errors[] = "NIK harus 16 digit angka.";
        if (empty($kabkoId)) $errors[] = "Pilih Kabupaten/Kota.";
        if (empty($telepon) || strlen($telepon) < 10) $errors[] = "Nomor HP tidak valid.";

        // Check if NIK already exists in Hafiz
        if (Hafiz::findByNik($nik)) {
            setFlash('error', 'NIK sudah terdaftar. Silakan gunakan tab <b>"Cari NIK"</b> untuk mengklaim akun Anda.');
            $this->redirect(APP_URL . '/register');
            return;
        }

        // Check if Phone already exists in Users
        if (User::usernameExists($telepon)) {
            $errors[] = "Nomor HP sudah terdaftar. Silakan login atau gunakan nomor lain.";
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect(APP_URL . '/register');
            return;
        }

        try {
            Database::beginTransaction();

            $sso = $_SESSION['sso_register'] ?? null;
            $email = $sso ? $sso['email'] : ($telepon . '@hafizjatim.id'); // Placeholder email if not SSO
            $googleId = ($sso && $sso['type'] === 'google') ? $sso['google_id'] : null;

            // 1. Create User
            $password = substr($nik, -6); // Default password: last 6 digits of NIK
            $userId = User::create([
                'username' => $telepon,
                'password' => $password,
                'role' => ROLE_HAFIZ,
                'kabupaten_kota_id' => $kabkoId,
                'nama' => $nama,
                'email' => $email,
                'telepon' => $telepon,
                'google_id' => $googleId,
                'foto' => $sso['foto'] ?? null,
                'is_active' => 0 // Pending approval
            ]);

            // 2. Create Hafiz
            Database::execute(
                "INSERT INTO hafiz (nama, nik, kabupaten_kota_id, telepon, user_id, is_aktif, tahun_tes) 
                 VALUES (:nama, :nik, :kabko_id, :telepon, :user_id, 1, :tahun)",
                [
                    'nama' => $nama,
                    'nik' => $nik,
                    'kabko_id' => $kabkoId,
                    'telepon' => $telepon,
                    'user_id' => $userId,
                    'tahun' => TAHUN_ANGGARAN
                ]
            );

            Database::commit();

            // Clear SSO Session
            if ($sso) unset($_SESSION['sso_register']);

            setFlash(
                'success',
                '<strong>Pendaftaran Berhasil!</strong><br>' .
                    'Akun Anda sedang menunggu persetujuan admin. <br>' .
                    'Silakan login nanti menggunakan:<br>' .
                    'Username: <b>' . $telepon . '</b><br>' .
                    'Password: <b>' . $password . '</b> (6 digit terakhir NIK)'
            );
            $this->redirect(APP_URL . '/login');
        } catch (Exception $e) {
            Database::rollback();
            error_log("Fresh Register Error: " . $e->getMessage());
            setFlash('error', 'Terjadi kesalahan saat pendaftaran.');
            $this->redirect(APP_URL . '/register');
        }
    }
}
