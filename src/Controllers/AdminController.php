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
     * Dashboard
     */
    public function dashboard(): void
    {
        $stats = Hafiz::getStatsByKabko();

        // Hitung total
        $totalPendaftar = array_sum(array_column($stats, 'total_pendaftar'));
        $totalLulus = array_sum(array_column($stats, 'total_lulus'));
        $totalPending = array_sum(array_column($stats, 'total_pending'));

        $this->view('admin.dashboard', [
            'title' => 'Dashboard Admin - ' . APP_NAME,
            'stats' => $stats,
            'totalPendaftar' => $totalPendaftar,
            'totalLulus' => $totalLulus,
            'totalPending' => $totalPending,
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

        $this->view('admin.hafiz-form', [
            'title' => 'Edit Data Hafiz - ' . APP_NAME,
            'hafiz' => $hafiz,
            'kabkoList' => $kabkoList,
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
     * List Laporan Harian (untuk verifikasi)
     */
    public function laporanList(): void
    {
        $page = (int) ($this->input('page') ?: 1);
        $filters = [
            'status_verifikasi' => $this->input('status') ?: 'pending',
            'tanggal_dari' => $this->input('tanggal_dari'),
            'tanggal_sampai' => $this->input('tanggal_sampai'),
        ];

        // Jika admin kabko, filter by kabupaten_kota_id
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $user = User::findById(getCurrentUserId());
            $filters['kabupaten_kota_id'] = $user['kabupaten_kota_id'];
        }

        $result = LaporanHarian::getAll($filters, $page);

        $this->view('admin.laporan-list', [
            'title' => 'Verifikasi Laporan - ' . APP_NAME,
            'laporanList' => $result['data'],
            'pagination' => $result,
            'filters' => $filters,
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
}
