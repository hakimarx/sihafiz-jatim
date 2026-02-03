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
            setFlash('error', 'Data hafiz tidak ditemukan. Hubungi administrator.');
            logout();
            header('Location: ' . APP_URL . '/login');
            exit;
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
        $lokasi = $this->input('lokasi');
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
            $fotoPath = $this->handleFotoUpload($_FILES['foto']);
            if ($fotoPath === false) {
                $errors[] = 'Gagal mengupload foto. Pastikan format JPG/PNG dan ukuran maksimal 2MB.';
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
                'durasi_menit' => $durasiMenit ?: null,
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
            'lokasi' => $this->input('lokasi'),
            'durasi_menit' => (int) $this->input('durasi_menit') ?: null,
        ];

        // Handle foto upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fotoPath = $this->handleFotoUpload($_FILES['foto']);
            if ($fotoPath !== false) {
                $updateData['foto'] = $fotoPath;
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
     * Handle foto upload
     */
    private function handleFotoUpload(array $file): string|false
    {
        // Validate file size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return false;
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('laporan_') . '_' . time() . '.' . $extension;
        $destination = UPLOAD_PATH . '/foto-kegiatan/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return '/uploads/foto-kegiatan/' . $filename;
        }

        return false;
    }

    /**
     * Profil Hafiz
     */
    public function profil(): void
    {
        $this->view('hafiz.profil', [
            'title' => 'Profil Saya - ' . APP_NAME,
            'hafiz' => $this->hafiz,
        ]);
    }
}
