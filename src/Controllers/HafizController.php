<?php

/**
 * Hafiz Controller
 * =================
 * Handle dashboard dan laporan harian untuk role Hafiz
 */

class HafizController extends Controller
{
    private ?array $hafiz = null;

    /**
     * Constructor - require hafiz role
     */
    public function __construct()
    {
        requireRole(ROLE_HAFIZ);

        // Get hafiz data
        $this->hafiz = Hafiz::findByUserId(getCurrentUserId());
        if (!$this->hafiz) {
            // Jika data hafiz belum lengkap (misal dibuat dari Manajemen User), 
            // jangan langsung logout, tapi arahkan agar lapor ke admin atau lengkapi data.
            $currentUri = $_SERVER['REQUEST_URI'];
            if (strpos($currentUri, '/hafiz/profil') === false) {
                setFlash('error', 'Profil Anda belum lengkap. Silakan hubungi administrator untuk melengkapi data NIK dan wilayah anda agar bisa mengisi laporan.');
                header('Location: ' . APP_URL . '/hafiz/profil');
                exit;
            }

            // Create dummy object so profile view doesn't crash
            $user = User::findById(getCurrentUserId());
            $this->hafiz = [
                'id' => 0,
                'nama' => $user['nama'] ?: $user['username'],
                'kabupaten_kota_nama' => 'Belum Diatur',
                'status_kelulusan' => 'pending',
                'nik' => '-',
                'tempat_lahir' => '-',
                'tanggal_lahir' => date('Y-m-d'),
                'jenis_kelamin' => 'L',
                'alamat' => 'Belum Lengkap',
                'desa_kelurahan' => '-',
                'kecamatan' => '-',
                'telepon' => $user['telepon'],
                'email' => $user['email'],
                'sertifikat_tahfidz' => '-',
                'mengajar' => 0,
                'tahun_tes' => TAHUN_ANGGARAN,
                'status_insentif' => 'tidak_aktif',
                'nama_bank' => '-',
                'nomor_rekening' => '-',
                'rt' => '',
                'rw' => ''
            ];
        }
    }

    /**
     * Dashboard Hafiz
     */
    public function dashboard(): void
    {
        $summary = LaporanHarian::getSummary($this->hafiz['id']);
        $recentLaporan = LaporanHarian::getByHafizId($this->hafiz['id'], 1, 5);

        $this->view('hafiz.dashboard', [
            'title' => 'Dashboard - ' . APP_NAME,
            'hafiz' => $this->hafiz,
            'summary' => $summary,
            'recentLaporan' => $recentLaporan['data'],
        ]);
    }

    /**
     * List Laporan Harian
     */
    public function laporanList(): void
    {
        $page = (int) ($this->input('page') ?: 1);
        $result = LaporanHarian::getByHafizId($this->hafiz['id'], $page);

        $this->view('hafiz.laporan-list', [
            'title' => 'Laporan Harian - ' . APP_NAME,
            'hafiz' => $this->hafiz,
            'laporanList' => $result['data'],
            'pagination' => $result,
        ]);
    }

    /**
     * Form tambah laporan
     */
    public function laporanCreate(): void
    {
        $this->view('hafiz.laporan-form', [
            'title' => 'Input Laporan Harian - ' . APP_NAME,
            'hafiz' => $this->hafiz,
            'laporan' => null,
            'isEdit' => false,
            'jenisKegiatan' => KEGIATAN_TYPES,
        ]);
    }

    /**
     * Store new laporan
     */
    public function laporanStore(): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/hafiz/laporan');
            return;
        }

        $tanggal = $this->input('tanggal');
        $jenisKegiatan = $this->input('jenis_kegiatan');
        $deskripsi = $this->input('deskripsi');
        $lokasi = trim($this->input('lokasi'));
        if ($lokasi === '') $lokasi = null;
        $durasiMenit = (int) $this->input('durasi_menit');

        // Validation
        $errors = [];

        if (empty($tanggal)) {
            $errors[] = 'Tanggal harus diisi.';
        }

        if (!in_array($jenisKegiatan, KEGIATAN_TYPES)) {
            $errors[] = 'Jenis kegiatan tidak valid.';
        }

        if (empty($deskripsi)) {
            $errors[] = 'Deskripsi harus diisi.';
        }

        // Handle foto upload
        $fotoPath = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fotoPath = $this->handleFotoUploadCompressed($_FILES['foto'], 'foto-kegiatan');
            if ($fotoPath === false) {
                $errors[] = 'Gagal mengupload foto. Pastikan format JPG/PNG.';
            } else {
                // Auto-detect location & date from EXIF (Ambil dari file asli sebelum kompresi)
                $exif = $this->extractExifData($_FILES['foto']['tmp_name']);
                if ($exif['date'] && empty($tanggal)) {
                    $tanggal = $exif['date'];
                }
                if ($exif['location_str'] && empty($lokasi)) {
                    $lokasi = $exif['location_str'];
                }
            }
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect(APP_URL . '/hafiz/laporan/create');
            return;
        }

        try {
            LaporanHarian::create([
                'hafiz_id' => $this->hafiz['id'],
                'tanggal' => $tanggal,
                'jenis_kegiatan' => $jenisKegiatan,
                'deskripsi' => $deskripsi,
                'foto' => $fotoPath,
                'lokasi' => $lokasi,
                'durasi_menit' => null, // Dihapus sesuai permintaan
            ]);

            setFlash('success', 'Laporan harian berhasil disimpan.');
            $this->redirect(APP_URL . '/hafiz/laporan');
        } catch (Exception $e) {
            error_log("Error creating laporan: " . $e->getMessage());
            setFlash('error', 'Gagal menyimpan laporan. Silakan coba lagi.');
            $this->redirect(APP_URL . '/hafiz/laporan/create');
        }
    }

    /**
     * Form edit laporan (hanya jika masih pending)
     */
    public function laporanEdit(string $id): void
    {
        $laporan = LaporanHarian::findById((int) $id);

        if (!$laporan || $laporan['hafiz_id'] !== $this->hafiz['id']) {
            setFlash('error', 'Data tidak ditemukan.');
            $this->redirect(APP_URL . '/hafiz/laporan');
            return;
        }

        if ($laporan['status_verifikasi'] !== VERIFIKASI_PENDING) {
            setFlash('error', 'Laporan yang sudah diverifikasi tidak dapat diedit.');
            $this->redirect(APP_URL . '/hafiz/laporan');
            return;
        }

        $this->view('hafiz.laporan-form', [
            'title' => 'Edit Laporan Harian - ' . APP_NAME,
            'hafiz' => $this->hafiz,
            'laporan' => $laporan,
            'isEdit' => true,
            'jenisKegiatan' => KEGIATAN_TYPES,
        ]);
    }

    /**
     * Update laporan
     */
    public function laporanUpdate(string $id): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/hafiz/laporan');
            return;
        }

        $laporan = LaporanHarian::findById((int) $id);

        if (!$laporan || $laporan['hafiz_id'] !== $this->hafiz['id']) {
            setFlash('error', 'Data tidak ditemukan.');
            $this->redirect(APP_URL . '/hafiz/laporan');
            return;
        }

        $updateData = [
            'tanggal' => $this->input('tanggal'),
            'jenis_kegiatan' => $this->input('jenis_kegiatan'),
            'deskripsi' => $this->input('deskripsi'),
            'lokasi' => trim($this->input('lokasi')) !== '' ? trim($this->input('lokasi')) : null,
            'durasi_menit' => (int) $this->input('durasi_menit') ?: null,
        ];

        // Handle foto upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fotoPath = $this->handleFotoUploadCompressed($_FILES['foto'], 'foto-kegiatan');
            if ($fotoPath !== false) {
                $updateData['foto'] = $fotoPath;

                // Auto-detect metadata if empty (Ambil dari file asli sebelum kompresi)
                $exif = $this->extractExifData($_FILES['foto']['tmp_name']);
                if ($exif['date'] && empty($updateData['tanggal'])) {
                    $updateData['tanggal'] = $exif['date'];
                }
                if ($exif['location_str'] && empty($updateData['lokasi'])) {
                    $updateData['lokasi'] = $exif['location_str'];
                }
            }
        }

        try {
            LaporanHarian::update((int) $id, $updateData);
            setFlash('success', 'Laporan berhasil diperbarui.');
            $this->redirect(APP_URL . '/hafiz/laporan');
        } catch (Exception $e) {
            setFlash('error', 'Gagal memperbarui laporan.');
            $this->redirect(APP_URL . '/hafiz/laporan/' . $id . '/edit');
        }
    }

    /**
     * Delete laporan
     */
    public function laporanDelete(string $id): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/hafiz/laporan');
            return;
        }

        $laporan = LaporanHarian::findById((int) $id);

        if (!$laporan || $laporan['hafiz_id'] !== $this->hafiz['id']) {
            setFlash('error', 'Data tidak ditemukan.');
            $this->redirect(APP_URL . '/hafiz/laporan');
            return;
        }

        try {
            LaporanHarian::delete((int) $id);
            setFlash('success', 'Laporan berhasil dihapus.');
        } catch (Exception $e) {
            setFlash('error', 'Gagal menghapus laporan. Laporan yang sudah diverifikasi tidak dapat dihapus.');
        }

        $this->redirect(APP_URL . '/hafiz/laporan');
    }

    /**
     * Handle foto upload with compression
     */
    private function handleFotoUploadCompressed(array $file, string $subDir): string|false
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }

        // Generate unique filename
        $extension = 'jpg'; // Always save as jpg for compression
        $filename = uniqid('img_') . '_' . time() . '.' . $extension;
        $targetDir = UPLOAD_PATH . '/' . $subDir . '/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $destination = $targetDir . $filename;

        // Compress to 400KB
        if ($this->compressImage($file['tmp_name'], $destination, 409600)) {
            return '/uploads/' . $subDir . '/' . $filename;
        }

        return false;
    }

    /**
     * Profil Hafiz
     */
    public function profil(): void
    {
        $mengajarList = Hafiz::getMengajarList($this->hafiz['id']);

        $this->view('hafiz.profil', [
            'title' => 'Profil Saya - ' . APP_NAME,
            'hafiz' => $this->hafiz,
            'mengajarList' => $mengajarList
        ]);
    }

    /**
     * Form Edit Profil
     */
    public function profilEdit(): void
    {
        $kabkoList = KabupatenKota::getForDropdown();
        $mengajarList = Hafiz::getMengajarList($this->hafiz['id']);

        $this->view('hafiz.profil-edit', [
            'title' => 'Edit Profil - ' . APP_NAME,
            'hafiz' => $this->hafiz,
            'kabkoList' => $kabkoList,
            'mengajarList' => $mengajarList
        ]);
    }

    /**
     * Update Profil
     */
    public function profilUpdate(): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/hafiz/profil/edit');
            return;
        }

        $updateData = [
            'nama' => $this->input('nama'),
            'nik' => $this->input('nik'),
            'tempat_lahir' => $this->input('tempat_lahir'),
            'tanggal_lahir' => $this->input('tanggal_lahir'),
            'jenis_kelamin' => $this->input('jenis_kelamin'),
            'alamat' => $this->input('alamat'),
            'desa_kelurahan' => $this->input('desa_kelurahan'),
            'kecamatan' => $this->input('kecamatan'),
            'telepon' => $this->input('telepon'),
            'email' => $this->input('email'),
            'nama_bank' => $this->input('nama_bank'),
            'nomor_rekening' => $this->input('nomor_rekening'),
        ];

        // Handle Profile Photo
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
            $path = $this->handleFotoUploadCompressed($_FILES['foto_profil'], 'foto-profil');
            if ($path) $updateData['foto_profil'] = $path;
        }

        // Handle KTP Photo
        if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] === UPLOAD_ERR_OK) {
            $path = $this->handleFotoUploadCompressed($_FILES['foto_ktp'], 'foto-ktp');
            if ($path) $updateData['foto_ktp'] = $path;
        }

        try {
            $hafizId = $this->hafiz['id'];

            if ($hafizId && $hafizId > 0) {
                Hafiz::update($hafizId, $updateData);
            } else {
                // Create New Hafiz Record linked to current user
                $updateData['user_id'] = getCurrentUserId();
                $updateData['tahun_tes'] = TAHUN_ANGGARAN;

                // Ensure required fields
                if (empty($updateData['nik'])) $updateData['nik'] = $_SESSION['username'] ?? rand(100000, 999999); // Fallback
                if (empty($updateData['kabupaten_kota_id'])) {
                    // Try get from user
                    $user = User::findById(getCurrentUserId());
                    $updateData['kabupaten_kota_id'] = $user['kabupaten_kota_id'];
                }

                // Helper to create without recreating user
                // Direct insert via Database class since Hafiz::create logic is for Admin
                // But simplified, let's just use raw insert to be safe
                Database::execute(
                    "INSERT INTO hafiz (
                        user_id, nik, nama, tempat_lahir, tanggal_lahir, jenis_kelamin,
                        alamat, desa_kelurahan, kecamatan, kabupaten_kota_id,
                        telepon, email, nama_bank, nomor_rekening, tahun_tes, is_aktif
                    ) VALUES (
                        :user_id, :nik, :nama, :tempat_lahir, :tanggal_lahir, :jenis_kelamin,
                        :alamat, :desa_kelurahan, :kecamatan, :kabupaten_kota_id,
                        :telepon, :email, :nama_bank, :nomor_rekening, :tahun_tes, 1
                    )",
                    [
                        'user_id' => $updateData['user_id'],
                        'nik' => $updateData['nik'],
                        'nama' => $updateData['nama'],
                        'tempat_lahir' => $updateData['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $updateData['tanggal_lahir'] ?? null,
                        'jenis_kelamin' => $updateData['jenis_kelamin'] ?? 'L',
                        'alamat' => $updateData['alamat'] ?? null,
                        'desa_kelurahan' => $updateData['desa_kelurahan'] ?? null,
                        'kecamatan' => $updateData['kecamatan'] ?? null,
                        'kabupaten_kota_id' => $updateData['kabupaten_kota_id'] ?? 1, // Default to avoid crash
                        'telepon' => $updateData['telepon'] ?? null,
                        'email' => $updateData['email'] ?? null,
                        'nama_bank' => $updateData['nama_bank'] ?? 'BANK JATIM',
                        'nomor_rekening' => $updateData['nomor_rekening'] ?? null,
                        'tahun_tes' => $updateData['tahun_tes'],
                    ]
                );

                $hafizId = Database::lastInsertId();
                if ($updateData['foto_profil'] ?? false) {
                    Database::execute("UPDATE hafiz SET foto_profil = :fp WHERE id = :id", ['fp' => $updateData['foto_profil'], 'id' => $hafizId]);
                }
                if ($updateData['foto_ktp'] ?? false) {
                    Database::execute("UPDATE hafiz SET foto_ktp = :fp WHERE id = :id", ['fp' => $updateData['foto_ktp'], 'id' => $hafizId]);
                }
            }

            // Handle Additional Teaching Locations
            $mengajarTempat = $this->input('mengajar_tempat'); // Array
            $mengajarTmt = $this->input('mengajar_tmt'); // Array

            $mengajarList = [];
            if (is_array($mengajarTempat)) {
                foreach ($mengajarTempat as $i => $tempat) {
                    if (!empty($tempat) && !empty($mengajarTmt[$i])) {
                        $mengajarList[] = [
                            'tempat' => sanitize($tempat),
                            'tmt' => sanitize($mengajarTmt[$i])
                        ];
                    }
                }
            }
            Hafiz::updateMengajarList((int)$hafizId, $mengajarList);

            // Sync Nama & Foto to session

            // Sync Nama & Foto to session
            if (!empty($updateData['nama'])) {
                User::update(getCurrentUserId(), ['nama' => $updateData['nama']]);
                $_SESSION['nama'] = $updateData['nama'];
            }
            if (!empty($updateData['foto_profil'])) {
                $_SESSION['foto_profil'] = $updateData['foto_profil'];
            }

            setFlash('success', 'Profil berhasil diperbarui.');
            $this->redirect(APP_URL . '/hafiz/profil');
        } catch (Exception $e) {
            setFlash('error', 'Gagal memperbarui profil: ' . $e->getMessage());
            $this->redirect(APP_URL . '/hafiz/profil/edit');
        }
    }

    /**
     * Form Change Password
     */
    public function passwordEdit(): void
    {
        $this->view('hafiz.password', [
            'title' => 'Ubah Password - ' . APP_NAME,
            'hafiz' => $this->hafiz
        ]);
    }

    /**
     * Process Change Password
     */
    public function passwordUpdate(): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/hafiz/password');
            return;
        }

        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword)) {
            setFlash('error', 'Password lama dan baru harus diisi.');
            $this->redirect(APP_URL . '/hafiz/password');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            setFlash('error', 'Konfirmasi password baru tidak cocok.');
            $this->redirect(APP_URL . '/hafiz/password');
            return;
        }

        if (strlen($newPassword) < 6) {
            setFlash('error', 'Password minimal 6 karakter.');
            $this->redirect(APP_URL . '/hafiz/password');
            return;
        }

        $user = User::findById(getCurrentUserId());
        if (!verifyPassword($oldPassword, $user['password'])) {
            setFlash('error', 'Password lama Anda salah.');
            $this->redirect(APP_URL . '/hafiz/password');
            return;
        }

        try {
            User::updatePassword(getCurrentUserId(), $newPassword);
            setFlash('success', 'Password berhasil diubah.');
            $this->redirect(APP_URL . '/hafiz/profil');
        } catch (Exception $e) {
            setFlash('error', 'Gagal mengubah password.');
            $this->redirect(APP_URL . '/hafiz/password');
        }
    }
}
