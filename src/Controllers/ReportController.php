<?php

/**
 * Report Controller
 * =================
 * Mengelola pencetakan laporan
 */

class ReportController extends Controller
{
    private $allowedLimit = 500; // Limit baris untuk cetak agar aman

    public function __construct()
    {
        // Hanya Admin Provinsi dan Admin KabKo yang boleh akses
        if (!hasRole(ROLE_ADMIN_PROV) && !hasRole(ROLE_ADMIN_KABKO)) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    /**
     * Halaman Utama Menu Laporan
     */
    public function index()
    {
        $kabupatenKota = KabupatenKota::getAll();

        // Filter KabKo jika user adalah Admin KabKo
        if (hasRole(ROLE_ADMIN_KABKO)) {
            $currentUser = User::findById(getCurrentUserId());
            $userKabKoId = $currentUser['kabupaten_kota_id'];
            $kabupatenKota = array_filter($kabupatenKota, function ($k) use ($userKabKoId) {
                return $k['id'] == $userKabKoId;
            });
        }

        $this->view('admin/reports/index', [
            'kabupatenKota' => $kabupatenKota,
            'title' => 'Menu Cetak Laporan'
        ]);
    }

    /**
     * Halaman Cetak Laporan Harian
     */
    public function printHarian()
    {
        $kabkoId = $_GET['kabupaten_kota_id'] ?? null;
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');

        if (!$kabkoId) {
            die("Kabupaten/Kota harus dipilih.");
        }

        // Ambil Data KabKo
        $kabko = KabupatenKota::findById($kabkoId);

        // Query Laporan
        // Join ke Hafiz untuk filter kabko
        $sql = "SELECT lh.*, h.nama as hafiz_nama, h.desa_kelurahan 
                FROM laporan_harian lh
                JOIN hafiz h ON lh.hafiz_id = h.id
                WHERE h.kabupaten_kota_id = :kabko_id
                AND MONTH(lh.tanggal) = :bulan
                AND YEAR(lh.tanggal) = :tahun
                AND h.is_aktif = 1
                AND (h.is_meninggal = 0 OR h.is_meninggal IS NULL)
                ORDER BY lh.tanggal ASC, h.nama ASC";

        $laporan = Database::query($sql, [
            'kabko_id' => $kabkoId,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);

        $this->renderPrint('admin/reports/print-harian', [
            'laporan' => $laporan,
            'kabko' => $kabko,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'title' => 'Laporan Harian Hafiz'
        ]);
    }

    /**
     * Halaman Cetak Data Hafiz
     */
    public function printHafiz()
    {
        $kabkoId = $_GET['kabupaten_kota_id'] ?? null;
        $status = $_GET['status_kelulusan'] ?? null;

        if (!$kabkoId) {
            die("Kabupaten/Kota harus dipilih.");
        }

        $kabko = KabupatenKota::findById($kabkoId);

        // Query Hafiz
        $sql = "SELECT h.*, k.nama as kabko_nama
                FROM hafiz h
                JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id
                WHERE h.kabupaten_kota_id = :kabko_id
                AND h.is_aktif = 1
                AND (h.is_meninggal = 0 OR h.is_meninggal IS NULL)";

        $params = ['kabko_id' => $kabkoId];

        if ($status && $status != 'all') {
            $sql .= " AND h.status_kelulusan = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY h.nama ASC";

        $hafiz = Database::query($sql, $params);

        $this->renderPrint('admin/reports/print-hafiz', [
            'hafiz' => $hafiz,
            'kabko' => $kabko,
            'status' => $status,
            'title' => 'Data Hafiz'
        ]);
    }

    /**
     * Halaman Cetak Absensi
     */
    public function printAbsensi()
    {
        $kabkoId = $_GET['kabupaten_kota_id'] ?? null;
        $namaKegiatan = $_GET['nama_kegiatan'] ?? 'Kegiatan Pembinaan';
        $tanggalKegiatan = $_GET['tanggal_kegiatan'] ?? date('Y-m-d');

        if (!$kabkoId) {
            die("Kabupaten/Kota harus dipilih.");
        }

        $kabko = KabupatenKota::findById($kabkoId);

        // Query Hafiz dalam KabKo tersebut
        $sql = "SELECT h.nik, h.nama, h.alamat, h.desa_kelurahan, h.kecamatan
                FROM hafiz h
                WHERE h.kabupaten_kota_id = :kabko_id
                AND h.is_aktif = 1
                AND (h.is_meninggal = 0 OR h.is_meninggal IS NULL)
                AND h.status_kelulusan = 'lulus' 
                ORDER BY h.nama ASC";
        // Default hanya yang lulus yang ikut kegiatan, atau bisa semua? Asumsi yang lulus/aktif.

        $hafiz = Database::query($sql, ['kabko_id' => $kabkoId]);

        $this->renderPrint('admin/reports/print-absensi', [
            'hafiz' => $hafiz,
            'kabko' => $kabko,
            'namaKegiatan' => $namaKegiatan,
            'tanggalKegiatan' => $tanggalKegiatan,
            'title' => 'Absensi Kegiatan'
        ]);
    }

    /**
     * Helper untuk render view cetak tanpa layout admin
     */
    private function renderPrint($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }
}
