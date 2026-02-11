<?php

/**
 * Admin Controller
 * =================
 * Handle admin dashboard dan management (untuk Admin Provinsi & Admin Kab/Ko)
 */

class AdminController extends Controller
{
    /**
     * Constructor - require admin role
     */
    public function __construct()
    {
        requireRole([ROLE_ADMIN_PROV, ROLE_ADMIN_KABKO]);
    }

    /**
     * Dashboard - Statistik
     */
    public function dashboard(): void
    {
        $kabkoId = null;
        $pendingApproval = 0;

        if (hasRole(ROLE_ADMIN_KABKO)) {
            $user = User::findById(getCurrentUserId());
            $kabkoId = $user['kabupaten_kota_id'];
        }

        // Count pending user registrations (is_active = 0)
        $pendingApproval = User::countPendingApproval($kabkoId);

        $stats = Hafiz::getStatsByKabko(null, $kabkoId);

        // Hitung total
        $totalPendaftar = array_sum(array_column($stats, 'total_pendaftar'));
        $totalLulus = array_sum(array_column($stats, 'total_lulus'));
        $totalPending = array_sum(array_column($stats, 'total_pending'));

        // Statistik tambahan
        $statsByGender = Hafiz::getStatsByGender($kabkoId);
        $totalLakiLaki = array_sum(array_column($statsByGender, 'laki_laki'));
        $totalPerempuan = array_sum(array_column($statsByGender, 'perempuan'));

        $statsByTahun = Hafiz::getStatsByTahunKelulusan($kabkoId);
        $statsLaporan = Hafiz::getStatsLaporan($kabkoId);

        $this->view('admin.dashboard', [
            'title' => 'Dashboard Statistik - ' . APP_NAME,
            'stats' => $stats,
            'totalPendaftar' => $totalPendaftar,
            'totalLulus' => $totalLulus,
            'totalPending' => $totalPending,
            'pendingApproval' => $pendingApproval,
            'statsByGender' => $statsByGender,
            'totalLakiLaki' => $totalLakiLaki,
            'totalPerempuan' => $totalPerempuan,
            'statsByTahun' => $statsByTahun,
            'statsLaporan' => $statsLaporan,
        ]);
    }

    /**
     * List Hafiz
     */
    public function hafizList(): void
    {
        $page = (int) ($this->input('page') ?: 1);
        $filters = [
            'search' => $this->input('search'),
            'kabupaten_kota_id' => $this->input('kabupaten_kota_id'),
            'status_kelulusan' => $this->input('status_kelulusan'),
            'tahun_tes' => $this->input('tahun_tes') ?: TAHUN_ANGGARAN,
        ];

        // Jika admin kabko, filter by kabupaten_kota_id
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $user = User::findById(getCurrentUserId());
            $filters['kabupaten_kota_id'] = $user['kabupaten_kota_id'];
        }

        $result = Hafiz::getAll($filters, $page);
        $kabkoList = KabupatenKota::getForDropdown();

        $this->view('admin.hafiz-list', [
            'title' => 'Data Hafiz - ' . APP_NAME,
            'hafizList' => $result['data'],
            'pagination' => $result,
            'filters' => $filters,
            'kabkoList' => $kabkoList,
        ]);
    }

    /**
     * Form tambah Hafiz
     */
    public function hafizCreate(): void
    {
        $kabkoList = KabupatenKota::getForDropdown();

        // Jika admin kabko, default kabupaten_kota_id
        $defaultKabko = null;
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $user = User::findById(getCurrentUserId());
            $defaultKabko = $user['kabupaten_kota_id'];
        }

        $this->view('admin.hafiz-form', [
            'title' => 'Tambah Data Hafiz - ' . APP_NAME,
            'hafiz' => null,
            'kabkoList' => $kabkoList,
            'mengajarList' => [],
            'defaultKabko' => $defaultKabko,
            'isEdit' => false,
        ]);
    }

    /**
     * Store new Hafiz
     */
    public function hafizStore(): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/hafiz');
            return;
        }

        // Validate required fields
        $nik = sanitizeNik($this->input('nik'));
        $nama = $this->input('nama');
        $tempatLahir = $this->input('tempat_lahir');
        $tanggalLahir = $this->input('tanggal_lahir');
        $jenisKelamin = $this->input('jenis_kelamin');
        $alamat = $this->input('alamat');
        $desaKelurahan = $this->input('desa_kelurahan');
        $kecamatan = $this->input('kecamatan');
        $kabkoId = (int) $this->input('kabupaten_kota_id');

        $errors = [];

        if (!isValidNik($nik)) {
            $errors[] = 'NIK harus 16 digit angka.';
        }

        if (empty($nama)) {
            $errors[] = 'Nama harus diisi.';
        }

        if (Hafiz::nikExists($nik, TAHUN_ANGGARAN)) {
            $errors[] = 'NIK sudah terdaftar untuk tahun ini.';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect(APP_URL . '/admin/hafiz/create');
            return;
        }

        try {
            $hafizId = Hafiz::create([
                'nik' => $nik,
                'nama' => $nama,
                'tempat_lahir' => $tempatLahir,
                'tanggal_lahir' => $tanggalLahir,
                'jenis_kelamin' => $jenisKelamin,
                'alamat' => $alamat,
                'rt' => $this->input('rt'),
                'rw' => $this->input('rw'),
                'desa_kelurahan' => $desaKelurahan,
                'kecamatan' => $kecamatan,
                'kabupaten_kota_id' => $kabkoId,
                'telepon' => sanitizePhone($this->input('telepon')),
                'email' => sanitizeEmail($this->input('email')),
                'sertifikat_tahfidz' => $this->input('sertifikat_tahfidz'),
                'mengajar' => $this->input('mengajar') ? 1 : 0,
                'tmt_mengajar' => $this->input('tmt_mengajar') ?: null,
                'tempat_mengajar' => $this->input('tempat_mengajar'),
            ]);

            // Handle additional teaching locations
            $mengajarTempat = $this->input('mengajar_tempat');
            $mengajarTmt = $this->input('mengajar_tmt');
            if (is_array($mengajarTempat)) {
                $mengajarList = [];
                foreach ($mengajarTempat as $i => $tempat) {
                    if (!empty($tempat) && !empty($mengajarTmt[$i])) {
                        $mengajarList[] = [
                            'tempat' => $tempat,
                            'tmt' => $mengajarTmt[$i]
                        ];
                    }
                }
                if (!empty($mengajarList)) {
                    Hafiz::updateMengajarList($hafizId, $mengajarList);
                }
            }

            setFlash('success', 'Data Hafiz berhasil ditambahkan. Password default: NIK');
            $this->redirect(APP_URL . '/admin/hafiz');
        } catch (Exception $e) {
            error_log("Error creating hafiz: " . $e->getMessage());
            setFlash('error', 'Gagal menyimpan data. Silakan coba lagi.');
            $this->redirect(APP_URL . '/admin/hafiz/create');
        }
    }

    /**
     * Form edit Hafiz
     */
    public function hafizEdit(string $id): void
    {
        $hafiz = Hafiz::findById((int) $id);

        if (!$hafiz) {
            setFlash('error', 'Data tidak ditemukan.');
            $this->redirect(APP_URL . '/admin/hafiz');
            return;
        }

        $kabkoList = KabupatenKota::getForDropdown();
        $mengajarList = Hafiz::getMengajarList((int) $id);

        $this->view('admin.hafiz-form', [
            'title' => 'Edit Data Hafiz - ' . APP_NAME,
            'hafiz' => $hafiz,
            'kabkoList' => $kabkoList,
            'mengajarList' => $mengajarList,
            'defaultKabko' => $hafiz['kabupaten_kota_id'],
            'isEdit' => true,
        ]);
    }

    /**
     * Update Hafiz
     */
    public function hafizUpdate(string $id): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/hafiz');
            return;
        }

        $hafizId = (int) $id;
        $hafiz = Hafiz::findById($hafizId);

        if (!$hafiz) {
            setFlash('error', 'Data tidak ditemukan.');
            $this->redirect(APP_URL . '/admin/hafiz');
            return;
        }

        try {
            Hafiz::update($hafizId, [
                'nama' => $this->input('nama'),
                'tempat_lahir' => $this->input('tempat_lahir'),
                'tanggal_lahir' => $this->input('tanggal_lahir'),
                'jenis_kelamin' => $this->input('jenis_kelamin'),
                'alamat' => $this->input('alamat'),
                'rt' => $this->input('rt'),
                'rw' => $this->input('rw'),
                'desa_kelurahan' => $this->input('desa_kelurahan'),
                'kecamatan' => $this->input('kecamatan'),
                'telepon' => sanitizePhone($this->input('telepon')),
                'email' => sanitizeEmail($this->input('email')),
                'nama_bank' => $this->input('nama_bank'),
                'nomor_rekening' => $this->input('nomor_rekening'),
                'sertifikat_tahfidz' => $this->input('sertifikat_tahfidz'),
                'mengajar' => $this->input('mengajar') ? 1 : 0,
                'tmt_mengajar' => $this->input('tmt_mengajar') ?: null,
                'tempat_mengajar' => $this->input('tempat_mengajar'),
                'keterangan' => $this->input('keterangan'),
            ]);

            // Handle additional teaching locations
            $mengajarTempat = $this->input('mengajar_tempat');
            $mengajarTmt = $this->input('mengajar_tmt');
            $mengajarList = [];
            if (is_array($mengajarTempat)) {
                foreach ($mengajarTempat as $i => $tempat) {
                    if (!empty($tempat) && !empty($mengajarTmt[$i])) {
                        $mengajarList[] = [
                            'tempat' => $tempat,
                            'tmt' => $mengajarTmt[$i]
                        ];
                    }
                }
            }
            Hafiz::updateMengajarList($hafizId, $mengajarList);

            setFlash('success', 'Data Hafiz berhasil diperbarui.');
            $this->redirect(APP_URL . '/admin/hafiz');
        } catch (Exception $e) {
            error_log("Error updating hafiz: " . $e->getMessage());
            setFlash('error', 'Gagal memperbarui data.');
            $this->redirect(APP_URL . '/admin/hafiz/' . $id . '/edit');
        }
    }

    /**
     * Delete Hafiz
     */
    public function hafizDelete(string $id): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/hafiz');
            return;
        }

        try {
            Hafiz::delete((int) $id);
            setFlash('success', 'Data Hafiz berhasil dihapus.');
        } catch (Exception $e) {
            setFlash('error', 'Gagal menghapus data.');
        }

        $this->redirect(APP_URL . '/admin/hafiz');
    }

    /**
     * List Laporan Harian Hafiz
     */
    public function laporanList(): void
    {
        $page = (int) ($this->input('page') ?: 1);

        // Build filters from request
        $bulan = $this->input('bulan');
        $tahun = $this->input('tahun');

        // Compute tanggal_dari & tanggal_sampai from bulan/tahun
        $tanggalDari = $this->input('tanggal_dari');
        $tanggalSampai = $this->input('tanggal_sampai');

        if ($bulan && $tahun) {
            $tanggalDari = "{$tahun}-{$bulan}-01";
            $tanggalSampai = date('Y-m-t', strtotime($tanggalDari));
        } elseif ($tahun && !$bulan) {
            $tanggalDari = "{$tahun}-01-01";
            $tanggalSampai = "{$tahun}-12-31";
        }

        $filters = [
            'status_verifikasi' => $this->input('status'),
            'tanggal_dari' => $tanggalDari,
            'tanggal_sampai' => $tanggalSampai,
        ];

        // Filter kabupaten_kota_id
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $user = User::findById(getCurrentUserId());
            $filters['kabupaten_kota_id'] = $user['kabupaten_kota_id'];
        } elseif ($this->input('kabupaten_kota_id')) {
            $filters['kabupaten_kota_id'] = $this->input('kabupaten_kota_id');
        }

        $result = LaporanHarian::getAll($filters, $page);
        $kabkoList = KabupatenKota::getForDropdown();

        $this->view('admin.laporan-list', [
            'title' => 'Laporan Harian Hafiz - ' . APP_NAME,
            'laporanList' => $result['data'],
            'pagination' => $result,
            'filters' => $filters,
            'kabkoList' => $kabkoList,
            'filterBulan' => $bulan,
            'filterTahun' => $tahun,
            'filterKabkoId' => $this->input('kabupaten_kota_id'),
            'filterStatus' => $this->input('status'),
        ]);
    }

    /**
     * Verify Laporan
     */
    public function laporanVerify(string $id): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/laporan');
            return;
        }

        $status = $this->input('status');
        $catatan = $this->input('catatan');

        if (!in_array($status, [VERIFIKASI_DISETUJUI, VERIFIKASI_DITOLAK])) {
            setFlash('error', 'Status tidak valid.');
            $this->redirect(APP_URL . '/admin/laporan');
            return;
        }

        try {
            LaporanHarian::verify((int) $id, $status, getCurrentUserId(), $catatan);
            setFlash('success', 'Laporan berhasil diverifikasi.');
        } catch (Exception $e) {
            setFlash('error', 'Gagal memverifikasi laporan.');
        }

        $this->redirect(APP_URL . '/admin/laporan');
    }

    /**
     * List Users (Admin Provinsi only)
     */
    public function userList(): void
    {
        requireRole(ROLE_ADMIN_PROV);

        $page = (int) ($this->input('page') ?: 1);
        $filters = [
            'search' => $this->input('search'),
            'role' => $this->input('role'),
            'kabupaten_kota_id' => $this->input('kabupaten_kota_id'),
        ];

        $result = User::getAll($filters, $page);
        $kabkoList = KabupatenKota::getForDropdown();

        $this->view('admin.user-list', [
            'title' => 'Manajemen User - ' . APP_NAME,
            'users' => $result['data'],
            'pagination' => $result,
            'filters' => $filters,
            'kabkoList' => $kabkoList,
        ]);
    }

    /**
     * Form tambah user
     */
    public function userCreate(): void
    {
        requireRole(ROLE_ADMIN_PROV);

        $kabkoList = KabupatenKota::getForDropdown();

        $this->view('admin.user-form', [
            'title' => 'Tambah User - ' . APP_NAME,
            'user' => null,
            'kabkoList' => $kabkoList,
            'isEdit' => false,
        ]);
    }

    /**
     * Store new user
     */
    public function userStore(): void
    {
        requireRole(ROLE_ADMIN_PROV);

        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/users');
            return;
        }

        $username = $this->input('username');
        $password = $this->input('password');
        $role = $this->input('role');
        $kabkoId = $this->input('kabupaten_kota_id') ?: null;
        $nama = $this->input('nama');

        if (User::usernameExists($username)) {
            setFlash('error', 'Username sudah digunakan.');
            $this->redirect(APP_URL . '/admin/users/create');
            return;
        }

        User::create([
            'username' => $username,
            'password' => $password,
            'role' => $role,
            'kabupaten_kota_id' => $kabkoId,
            'nama' => $nama,
            'email' => $this->input('email'),
            'telepon' => $this->input('telepon'),
        ]);

        setFlash('success', 'User berhasil ditambahkan.');
        $this->redirect(APP_URL . '/admin/users');
    }

    /**
     * Form edit user
     */
    public function userEdit(string $id): void
    {
        requireRole(ROLE_ADMIN_PROV);

        $user = User::findById((int) $id);
        if (!$user) {
            setFlash('error', 'User tidak ditemukan.');
            $this->redirect(APP_URL . '/admin/users');
            return;
        }

        $kabkoList = KabupatenKota::getForDropdown();

        $this->view('admin.user-form', [
            'title' => 'Edit User - ' . APP_NAME,
            'user' => $user,
            'kabkoList' => $kabkoList,
            'isEdit' => true,
        ]);
    }

    /**
     * Update user
     */
    public function userUpdate(string $id): void
    {
        requireRole(ROLE_ADMIN_PROV);

        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/users');
            return;
        }

        $userId = (int) $id;
        $username = $this->input('username');

        if (User::usernameExists($username, $userId)) {
            setFlash('error', 'Username sudah digunakan oleh user lain.');
            $this->redirect(APP_URL . '/admin/users/' . $id . '/edit');
            return;
        }

        $data = [
            'username' => $username,
            'role' => $this->input('role'),
            'kabupaten_kota_id' => $this->input('kabupaten_kota_id') ?: null,
            'nama' => $this->input('nama'),
            'email' => $this->input('email'),
            'telepon' => $this->input('telepon'),
            'is_active' => $this->input('is_active') ? 1 : 0,
        ];

        if (!empty($this->input('password'))) {
            $data['password'] = $this->input('password');
        }

        User::update($userId, $data);

        setFlash('success', 'User berhasil diperbarui.');
        $this->redirect(APP_URL . '/admin/users');
    }

    /**
     * Delete user
     */
    public function userDelete(string $id): void
    {
        requireRole(ROLE_ADMIN_PROV);

        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/users');
            return;
        }

        if ((int)$id === getCurrentUserId()) {
            setFlash('error', 'Anda tidak bisa menghapus akun sendiri.');
            $this->redirect(APP_URL . '/admin/users');
            return;
        }

        User::delete((int) $id);
        setFlash('success', 'User berhasil dihapus.');
        $this->redirect(APP_URL . '/admin/users');
    }

    /**
     * Settings Page
     */
    public function settings(): void
    {
        requireRole(ROLE_ADMIN_PROV);

        $this->view('admin.settings', [
            'title' => 'Pengaturan Aplikasi - ' . APP_NAME,
            'settings' => Setting::getAll(),
        ]);
    }

    /**
     * Update Settings
     */
    public function settingsUpdate(): void
    {
        requireRole(ROLE_ADMIN_PROV);

        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/settings');
            return;
        }

        // Handle regular settings
        $keys = ['app_name', 'app_address', 'tahun_aktif'];
        foreach ($keys as $key) {
            if ($this->input($key) !== null) {
                Setting::set($key, $this->input($key));
            }
        }

        // Handle File Uploads (Logo & Favicon)
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoPath = $this->handleFileUpload($_FILES['logo'], 'logo');
            if ($logoPath) {
                Setting::set('app_logo', $logoPath);
            }
        }

        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $faviconPath = $this->handleFileUpload($_FILES['favicon'], 'favicon');
            if ($faviconPath) {
                Setting::set('app_favicon', $faviconPath);
            }
        }

        setFlash('success', 'Pengaturan berhasil diperbarui.');
        $this->redirect(APP_URL . '/admin/settings');
    }

    /**
     * Process import from CSV (converted from Excel)
     */
    public function importProcess(): void
    {
        requireRole(ROLE_ADMIN_PROV);

        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/settings');
            return;
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'Silakan unggah file CSV hasil konversi Excel.');
            $this->redirect(APP_URL . '/admin/settings');
            return;
        }

        $filePath = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($filePath, "r");

        // Read header
        $header = fgetcsv($handle, 1000, ";");

        $imported = 0;
        $skipped = 0;
        $total = 0;

        $kabkoList = Database::query("SELECT id, nama FROM kabupaten_kota");
        $kabkoMap = [];
        foreach ($kabkoList as $k) {
            $normalized = trim(str_replace(['kabupaten ', 'kota ', 'kab. ', 'k. '], '', strtolower($k['nama'])));
            $kabkoMap[$normalized] = $k['id'];
        }

        while (($line = fgets($handle)) !== FALSE) {
            if (trim($line) === '') continue;
            $data = explode(';', $line);
            if (count($data) < 17) continue;

            $total++;
            $csvData = [
                'tahun' => preg_replace('/[^0-9]/', '', $data[1] ?? '2023'),
                'kabko_name' => $data[16] ?? '',
                'nik' => preg_replace('/[^0-9]/', '', $data[4] ?? ''),
                'nama' => trim($data[5] ?? ''),
                'tempat_lahir' => trim($data[6] ?? ''),
                'tanggal_lahir' => trim($data[8] ?? ''),
                'jk' => trim($data[10] ?? 'L'),
                'alamat' => trim($data[11] ?? ''),
                'rt' => trim($data[12] ?? ''),
                'rw' => trim($data[13] ?? ''),
                'desa' => trim($data[14] ?? ''),
                'kecamatan' => trim($data[15] ?? ''),
                'sertifikat' => trim($data[17] ?? ''),
                'mengajar' => trim($data[18] ?? ''),
                'telepon' => preg_replace('/[^0-9]/', '', $data[20] ?? ''),
                'lulus' => trim($data[22] ?? '')
            ];

            if (empty($csvData['nik']) || strlen($csvData['nik']) < 10) {
                $skipped++;
                continue;
            }

            $kabkoNormalized = trim(str_replace(['kabupaten ', 'kota ', 'kab. ', 'k. '], '', strtolower($csvData['kabko_name'])));
            $kabkoId = $kabkoMap[$kabkoNormalized] ?? null;

            if (!$kabkoId) {
                // Try column 3 if 16 fails
                $kabkoNormalized2 = trim(str_replace(['kabupaten ', 'kota ', 'kab. ', 'k. '], '', strtolower($data[3] ?? '')));
                $kabkoId = $kabkoMap[$kabkoNormalized2] ?? null;
            }

            if (!$kabkoId) {
                $skipped++;
                continue;
            }

            if (Hafiz::nikExists($csvData['nik'], (int)$csvData['tahun'])) {
                $skipped++;
                continue;
            }

            try {
                // Convert date (DD/MM/YYYY to YYYY-MM-DD)
                $birthDate = null;
                if (!empty($csvData['tanggal_lahir'])) {
                    $p = explode('/', $csvData['tanggal_lahir']);
                    if (count($p) === 3) {
                        $birthDate = "{$p[2]}-{$p[1]}-{$p[0]}";
                    }
                }

                Hafiz::create([
                    'nik' => $csvData['nik'],
                    'nama' => strtoupper($csvData['nama']),
                    'tempat_lahir' => $csvData['tempat_lahir'],
                    'tanggal_lahir' => $birthDate,
                    'jenis_kelamin' => $csvData['jk'] === 'L' ? 'L' : 'P',
                    'alamat' => $csvData['alamat'],
                    'rt' => $csvData['rt'],
                    'rw' => $csvData['rw'],
                    'desa_kelurahan' => $csvData['desa'],
                    'kecamatan' => $csvData['kecamatan'],
                    'kabupaten_kota_id' => $kabkoId,
                    'telepon' => $csvData['telepon'],
                    'email' => null,
                    'sertifikat_tahfidz' => $csvData['sertifikat'],
                    'mengajar' => $csvData['mengajar'] ? 1 : 0,
                    'tmt_mengajar' => null,
                    'tahun_tes' => (int)$csvData['tahun']
                ]);

                $isLulus = strtolower($csvData['lulus']) === 'lulus' ? 'lulus' : 'pending';
                if ($isLulus === 'lulus') {
                    $lastId = Database::lastInsertId();
                    Database::execute("UPDATE hafiz SET status_kelulusan = 'lulus' WHERE id = :id", ['id' => $lastId]);
                }

                $imported++;
            } catch (Exception $e) {
                $skipped++;
            }
        }

        fclose($handle);

        setFlash('success', "Import selesai! Total: $total diproses, $imported berhasil diimport, $skipped dilewati (sudah ada atau data tidak valid).");
        $this->redirect(APP_URL . '/admin/settings');
    }

    /**
     * Helper to handle file upload
     */
    private function handleFileUpload(array $file, string $prefix): ?string
    {
        $targetDir = UPLOAD_PATH . '/settings/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $prefix . '_' . time() . '.' . $extension;
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return '/uploads/settings/' . $fileName;
        }

        return null;
    }

    // ============================================
    // PENDAFTARAN / APPROVAL
    // ============================================

    /**
     * List pending approval registrations
     */
    public function pendingApproval(): void
    {
        $kabkoId = null;
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $user = User::findById(getCurrentUserId());
            $kabkoId = $user['kabupaten_kota_id'];
        }

        $pendingUsers = User::getPendingApproval($kabkoId);

        $this->view('admin.pending-approval', [
            'title' => 'Persetujuan Pendaftaran - ' . APP_NAME,
            'pendingUsers' => $pendingUsers,
        ]);
    }

    /**
     * Approve a pending user registration
     */
    public function approveUser(string $id): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/pending');
            return;
        }

        $userId = (int) $id;
        $user = User::findById($userId);

        if (!$user) {
            setFlash('error', 'User tidak ditemukan.');
            $this->redirect(APP_URL . '/admin/pending');
            return;
        }

        // Check authorization: admin kabko can only approve users in their kabko
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $admin = User::findById(getCurrentUserId());
            if ($user['kabupaten_kota_id'] != $admin['kabupaten_kota_id']) {
                setFlash('error', 'Anda tidak berwenang mengaktifkan user dari kabupaten/kota lain.');
                $this->redirect(APP_URL . '/admin/pending');
                return;
            }
        }

        try {
            User::update($userId, ['is_active' => 1]);
            setFlash('success', 'Akun <strong>' . htmlspecialchars($user['nama'] ?? $user['username']) . '</strong> berhasil diaktifkan. Hafiz sekarang dapat login.');
        } catch (Exception $e) {
            error_log("Error approving user: " . $e->getMessage());
            setFlash('error', 'Gagal mengaktifkan akun.');
        }

        $this->redirect(APP_URL . '/admin/pending');
    }

    /**
     * Reject a pending user registration
     */
    public function rejectUser(string $id): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/pending');
            return;
        }

        $userId = (int) $id;
        $user = User::findById($userId);

        if (!$user) {
            setFlash('error', 'User tidak ditemukan.');
            $this->redirect(APP_URL . '/admin/pending');
            return;
        }

        // Check authorization
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $admin = User::findById(getCurrentUserId());
            if ($user['kabupaten_kota_id'] != $admin['kabupaten_kota_id']) {
                setFlash('error', 'Anda tidak berwenang menolak user dari kabupaten/kota lain.');
                $this->redirect(APP_URL . '/admin/pending');
                return;
            }
        }

        try {
            // Unlink hafiz from this user
            Database::execute(
                "UPDATE hafiz SET user_id = NULL WHERE user_id = :user_id",
                ['user_id' => $userId]
            );

            // Delete user account
            User::delete($userId);

            setFlash('success', 'Pendaftaran <strong>' . htmlspecialchars($user['nama'] ?? $user['username']) . '</strong> telah ditolak dan akun dihapus.');
        } catch (Exception $e) {
            error_log("Error rejecting user: " . $e->getMessage());
            setFlash('error', 'Gagal menolak pendaftaran.');
        }

        $this->redirect(APP_URL . '/admin/pending');
    }

    /**
     * Batch approve multiple pending users
     */
    public function batchApprove(): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/admin/pending');
            return;
        }

        $userIds = $this->input('user_ids');
        if (empty($userIds) || !is_array($userIds)) {
            setFlash('error', 'Pilih minimal satu pendaftaran.');
            $this->redirect(APP_URL . '/admin/pending');
            return;
        }

        $kabkoId = null;
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $admin = User::findById(getCurrentUserId());
            $kabkoId = $admin['kabupaten_kota_id'];
        }

        $successCount = 0;
        foreach ($userIds as $id) {
            $userId = (int) $id;
            $user = User::findById($userId);

            if ($user && $user['is_active'] == 0) {
                // Check authorization for kabko admin
                if ($kabkoId && $user['kabupaten_kota_id'] != $kabkoId) {
                    continue;
                }

                if (User::update($userId, ['is_active' => 1])) {
                    $successCount++;
                }
            }
        }

        if ($successCount > 0) {
            setFlash('success', "Berhasil mengaktifkan <strong>$successCount</strong> akun pendaftaran.");
        } else {
            setFlash('error', 'Gagal mengaktifkan akun pendaftaran.');
        }

        $this->redirect(APP_URL . '/admin/pending');
    }
}
