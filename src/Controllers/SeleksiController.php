<?php

/**
 * Seleksi Controller
 * ===================
 * Handle penilaian seleksi hafiz (untuk Admin dan Penguji)
 */

class SeleksiController extends Controller
{
    /**
     * Constructor - require admin or penguji role
     */
    public function __construct()
    {
        requireRole([ROLE_ADMIN_PROV, ROLE_ADMIN_KABKO, ROLE_PENGUJI]);
    }

    /**
     * List peserta seleksi
     */
    public function index(): void
    {
        // Coming Soon untuk non-admin_prov
        if (!hasRole(ROLE_ADMIN_PROV)) {
            $this->view('seleksi.coming-soon', [
                'title' => 'Seleksi & Penilaian - ' . APP_NAME,
            ]);
            return;
        }

        $page = (int) ($this->input('page') ?: 1);
        $tahun = (int) ($this->input('tahun') ?: TAHUN_ANGGARAN);

        $filters = [
            'tahun_anggaran' => $tahun,
            'search' => $this->input('search'),
            'kabupaten_kota_id' => $this->input('kabupaten_kota_id'),
            'status_lulus' => $this->input('status'),
        ];

        // Filter by kabupaten_kota for admin kabko or penguji
        if (hasRole(ROLE_ADMIN_KABKO) || hasRole(ROLE_PENGUJI)) {
            $user = User::findById(getCurrentUserId());
            $filters['kabupaten_kota_id'] = $user['kabupaten_kota_id'];
        }

        $result = Seleksi::getAll($filters, $page);
        $stats = Seleksi::getStats($tahun, $filters['kabupaten_kota_id'] ?? null);
        $kabkoList = KabupatenKota::getForDropdown();

        $this->view('seleksi.index', [
            'title' => 'Seleksi Hafiz - ' . APP_NAME,
            'pesertaList' => $result['data'],
            'pagination' => $result,
            'stats' => $stats,
            'filters' => $filters,
            'kabkoList' => $kabkoList,
            'tahun' => $tahun,
        ]);
    }

    /**
     * Form input nilai
     */
    public function inputNilai(string $hafizId): void
    {
        $hafiz = Hafiz::findById((int) $hafizId);

        if (!$hafiz) {
            setFlash('error', 'Data hafiz tidak ditemukan.');
            $this->redirect(APP_URL . '/seleksi');
            return;
        }

        $tahun = (int) ($this->input('tahun') ?: TAHUN_ANGGARAN);
        $seleksi = Seleksi::findByHafizAndTahun((int) $hafizId, $tahun);

        $this->view('seleksi.input-nilai', [
            'title' => 'Input Nilai - ' . $hafiz['nama'],
            'hafiz' => $hafiz,
            'seleksi' => $seleksi,
            'tahun' => $tahun,
        ]);
    }

    /**
     * Save nilai
     */
    public function saveNilai(string $hafizId): void
    {
        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/seleksi');
            return;
        }

        $hafiz = Hafiz::findById((int) $hafizId);
        if (!$hafiz) {
            setFlash('error', 'Data hafiz tidak ditemukan.');
            $this->redirect(APP_URL . '/seleksi');
            return;
        }

        $tahun = (int) ($this->input('tahun') ?: TAHUN_ANGGARAN);
        $nilaiWawasan = $this->input('nilai_wawasan');
        $nilaiHafalan = $this->input('nilai_hafalan');

        // Validation
        $errors = [];

        if ($nilaiWawasan === '' || $nilaiWawasan === null) {
            $errors[] = 'Nilai wawasan harus diisi.';
        } elseif ($nilaiWawasan < 0 || $nilaiWawasan > 100) {
            $errors[] = 'Nilai wawasan harus antara 0-100.';
        }

        if ($nilaiHafalan === '' || $nilaiHafalan === null) {
            $errors[] = 'Nilai hafalan harus diisi.';
        } elseif ($nilaiHafalan < 0 || $nilaiHafalan > 100) {
            $errors[] = 'Nilai hafalan harus antara 0-100.';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect(APP_URL . '/seleksi/' . $hafizId . '/nilai?tahun=' . $tahun);
            return;
        }

        try {
            Seleksi::saveNilai([
                'hafiz_id' => (int) $hafizId,
                'tahun_anggaran' => $tahun,
                'penguji_id' => getCurrentUserId(),
                'nilai_wawasan' => (float) $nilaiWawasan,
                'nilai_hafalan' => (float) $nilaiHafalan,
                'catatan' => $this->input('catatan'),
                'tanggal_tes' => $this->input('tanggal_tes') ?: date('Y-m-d H:i:s'),
            ]);

            setFlash('success', 'Nilai berhasil disimpan untuk ' . $hafiz['nama']);
            $this->redirect(APP_URL . '/seleksi?tahun=' . $tahun);
        } catch (Exception $e) {
            error_log("Error saving nilai: " . $e->getMessage());
            setFlash('error', 'Gagal menyimpan nilai.');
            $this->redirect(APP_URL . '/seleksi/' . $hafizId . '/nilai?tahun=' . $tahun);
        }
    }

    /**
     * Export data ke Excel
     */
    public function export(): void
    {
        $tahun = (int) ($this->input('tahun') ?: TAHUN_ANGGARAN);
        $kabkoId = $this->input('kabupaten_kota_id');
        $format = $this->input('format') ?: 'excel';

        // Filter by kabupaten_kota for admin kabko or penguji
        if (hasRole(ROLE_ADMIN_KABKO) || hasRole(ROLE_PENGUJI)) {
            $user = User::findById(getCurrentUserId());
            $kabkoId = $user['kabupaten_kota_id'];
        }

        $data = Seleksi::getForExport($tahun, $kabkoId ? (int) $kabkoId : null);

        if (empty($data)) {
            setFlash('error', 'Tidak ada data untuk diekspor.');
            $this->redirect(APP_URL . '/seleksi?tahun=' . $tahun);
            return;
        }

        ExcelExport::exportHafiz($data, $format);
    }

    /**
     * Export laporan harian ke Excel
     */
    public function exportLaporan(): void
    {
        $filters = [
            'status_verifikasi' => $this->input('status') ?: 'disetujui',
            'tanggal_dari' => $this->input('tanggal_dari'),
            'tanggal_sampai' => $this->input('tanggal_sampai'),
        ];
        $format = $this->input('format') ?: 'excel';

        // Filter by kabupaten_kota for admin kabko
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $user = User::findById(getCurrentUserId());
            $filters['kabupaten_kota_id'] = $user['kabupaten_kota_id'];
        }

        $result = LaporanHarian::getAll($filters, 1, 10000); // Get all

        if (empty($result['data'])) {
            setFlash('error', 'Tidak ada data untuk diekspor.');
            $this->redirect(APP_URL . '/admin/laporan');
            return;
        }

        ExcelExport::exportLaporan($result['data'], $format);
    }
}
