<?php

/**
 * Seleksi Model
 * ==============
 * Model untuk manajemen nilai seleksi/tes Hafiz
 */

class Seleksi
{
    /**
     * Find by ID
     */
    public static function findById(int $id): ?array
    {
        return Database::queryOne(
            "SELECT s.*, h.nama as hafiz_nama, h.nik as hafiz_nik, 
                    u.nama as penguji_nama, k.nama as kabupaten_kota_nama
             FROM seleksi s
             LEFT JOIN hafiz h ON s.hafiz_id = h.id
             LEFT JOIN users u ON s.penguji_id = u.id
             LEFT JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id
             WHERE s.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Find by Hafiz ID dan Tahun
     */
    public static function findByHafizAndTahun(int $hafizId, int $tahun): ?array
    {
        return Database::queryOne(
            "SELECT s.*, u.nama as penguji_nama
             FROM seleksi s
             LEFT JOIN users u ON s.penguji_id = u.id
             WHERE s.hafiz_id = :hafiz_id AND s.tahun_anggaran = :tahun",
            ['hafiz_id' => $hafizId, 'tahun' => $tahun]
        );
    }

    /**
     * Get all seleksi with filters and pagination
     */
    public static function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $where = ["h.is_aktif = 1"];
        $params = [];

        if (!empty($filters['tahun_anggaran'])) {
            $where[] = "h.tahun_tes = :tahun";
            $params['tahun'] = $filters['tahun_anggaran'];
        }

        if (!empty($filters['kabupaten_kota_id'])) {
            $where[] = "h.kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $filters['kabupaten_kota_id'];
        }

        if (!empty($filters['status_lulus'])) {
            if ($filters['status_lulus'] === 'lulus') {
                $where[] = "s.status_lulus = 1";
            } elseif ($filters['status_lulus'] === 'tidak_lulus') {
                $where[] = "s.status_lulus = 0 AND s.nilai_total IS NOT NULL";
            } else {
                $where[] = "s.nilai_total IS NULL";
            }
        }

        if (!empty($filters['belum_dinilai'])) {
            $where[] = "s.id IS NULL";
        }

        if (!empty($filters['search'])) {
            $where[] = "(h.nama LIKE :search OR h.nik LIKE :search2)";
            $params['search'] = "%{$filters['search']}%";
            $params['search2'] = "%{$filters['search']}%";
        }

        $whereClause = implode(' AND ', $where);
        $tahun = $filters['tahun_anggaran'] ?? TAHUN_ANGGARAN;

        // Count total
        $countSql = "SELECT COUNT(*) as total 
                     FROM hafiz h 
                     LEFT JOIN seleksi s ON h.id = s.hafiz_id AND s.tahun_anggaran = :tahun_join
                     WHERE {$whereClause}";
        $params['tahun_join'] = $tahun;
        $total = Database::queryOne($countSql, $params)['total'];

        // Get data
        $sql = "SELECT h.id as hafiz_id, h.nik, h.nama, h.sertifikat_tahfidz,
                       k.nama as kabupaten_kota_nama,
                       s.id as seleksi_id, s.nilai_wawasan, s.nilai_hafalan, s.nilai_total,
                       s.status_lulus, s.tanggal_tes, s.catatan,
                       u.nama as penguji_nama
                FROM hafiz h
                LEFT JOIN seleksi s ON h.id = s.hafiz_id AND s.tahun_anggaran = :tahun_join
                LEFT JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id
                LEFT JOIN users u ON s.penguji_id = u.id
                WHERE {$whereClause}
                ORDER BY h.nama ASC
                LIMIT {$perPage} OFFSET {$offset}";

        $data = Database::query($sql, $params);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Create or Update nilai seleksi
     */
    public static function saveNilai(array $data): int
    {
        $existing = self::findByHafizAndTahun($data['hafiz_id'], $data['tahun_anggaran']);

        // Calculate total score (average)
        $nilaiTotal = null;
        if ($data['nilai_wawasan'] !== null && $data['nilai_hafalan'] !== null) {
            $nilaiTotal = ($data['nilai_wawasan'] + $data['nilai_hafalan']) / 2;
        }

        // Determine pass/fail (passing score: 70)
        $statusLulus = $nilaiTotal !== null && $nilaiTotal >= 70 ? 1 : 0;

        if ($existing) {
            // Update
            Database::execute(
                "UPDATE seleksi SET 
                    penguji_id = :penguji_id,
                    nilai_wawasan = :nilai_wawasan,
                    nilai_hafalan = :nilai_hafalan,
                    nilai_total = :nilai_total,
                    status_lulus = :status_lulus,
                    catatan = :catatan,
                    tanggal_tes = :tanggal_tes
                 WHERE id = :id",
                [
                    'id' => $existing['id'],
                    'penguji_id' => $data['penguji_id'] ?? null,
                    'nilai_wawasan' => $data['nilai_wawasan'],
                    'nilai_hafalan' => $data['nilai_hafalan'],
                    'nilai_total' => $nilaiTotal,
                    'status_lulus' => $statusLulus,
                    'catatan' => $data['catatan'] ?? null,
                    'tanggal_tes' => $data['tanggal_tes'] ?? date('Y-m-d H:i:s'),
                ]
            );

            // Update hafiz status
            self::updateHafizStatus($data['hafiz_id'], $statusLulus, $nilaiTotal, $data['nilai_wawasan']);

            return $existing['id'];
        } else {
            // Insert
            Database::execute(
                "INSERT INTO seleksi (
                    hafiz_id, tahun_anggaran, penguji_id, nilai_wawasan, nilai_hafalan,
                    nilai_total, status_lulus, catatan, tanggal_tes
                ) VALUES (
                    :hafiz_id, :tahun_anggaran, :penguji_id, :nilai_wawasan, :nilai_hafalan,
                    :nilai_total, :status_lulus, :catatan, :tanggal_tes
                )",
                [
                    'hafiz_id' => $data['hafiz_id'],
                    'tahun_anggaran' => $data['tahun_anggaran'],
                    'penguji_id' => $data['penguji_id'] ?? null,
                    'nilai_wawasan' => $data['nilai_wawasan'],
                    'nilai_hafalan' => $data['nilai_hafalan'],
                    'nilai_total' => $nilaiTotal,
                    'status_lulus' => $statusLulus,
                    'catatan' => $data['catatan'] ?? null,
                    'tanggal_tes' => $data['tanggal_tes'] ?? date('Y-m-d H:i:s'),
                ]
            );

            $id = (int) Database::lastInsertId();

            // Update hafiz status
            self::updateHafizStatus($data['hafiz_id'], $statusLulus, $nilaiTotal, $data['nilai_wawasan']);

            return $id;
        }
    }

    /**
     * Update hafiz status based on seleksi result
     */
    private static function updateHafizStatus(int $hafizId, int $statusLulus, ?float $nilaiTotal, ?float $nilaiWawasan): void
    {
        $status = $statusLulus ? 'lulus' : 'tidak_lulus';

        Database::execute(
            "UPDATE hafiz SET 
                status_kelulusan = :status,
                nilai_tahfidz = :nilai_total,
                nilai_wawasan = :nilai_wawasan
             WHERE id = :id",
            [
                'id' => $hafizId,
                'status' => $status,
                'nilai_total' => $nilaiTotal,
                'nilai_wawasan' => $nilaiWawasan,
            ]
        );
    }

    /**
     * Get statistics for seleksi
     */
    public static function getStats(int $tahun, ?int $kabkoId = null): array
    {
        $statsParams = [
            'tahun_join' => $tahun,
            'tahun_where' => $tahun
        ];

        if ($kabkoId) {
            $statsParams['kabko_id'] = $kabkoId;
        }

        return Database::queryOne(
            "SELECT 
                COUNT(h.id) as total_peserta,
                SUM(CASE WHEN s.id IS NOT NULL THEN 1 ELSE 0 END) as sudah_dinilai,
                SUM(CASE WHEN s.status_lulus = 1 THEN 1 ELSE 0 END) as lulus,
                SUM(CASE WHEN s.status_lulus = 0 AND s.nilai_total IS NOT NULL THEN 1 ELSE 0 END) as tidak_lulus,
                AVG(COALESCE(s.nilai_total, 0)) as rata_rata_nilai
             FROM hafiz h
             LEFT JOIN seleksi s ON h.id = s.hafiz_id AND s.tahun_anggaran = :tahun_join
             WHERE h.tahun_tes = :tahun_where AND h.is_aktif = 1" . ($kabkoId ? " AND h.kabupaten_kota_id = :kabko_id" : ""),
            $statsParams
        ) ?? [
            'total_peserta' => 0,
            'sudah_dinilai' => 0,
            'lulus' => 0,
            'tidak_lulus' => 0,
            'rata_rata_nilai' => 0
        ];
    }

    /**
     * Get data for export
     */
    public static function getForExport(int $tahun, ?int $kabkoId = null): array
    {
        $exportParams = [
            'tahun_join' => $tahun,
            'tahun_where' => $tahun
        ];

        if ($kabkoId) {
            $exportParams['kabko_id'] = $kabkoId;
        }

        return Database::query(
            "SELECT 
                h.nik, h.nama, h.tempat_lahir, h.tanggal_lahir, h.jenis_kelamin,
                h.alamat, h.desa_kelurahan, h.kecamatan, k.nama as kabupaten_kota,
                h.telepon, h.sertifikat_tahfidz,
                s.nilai_wawasan, s.nilai_hafalan, s.nilai_total,
                CASE WHEN s.status_lulus = 1 THEN 'LULUS' ELSE 'TIDAK LULUS' END as status,
                s.tanggal_tes, s.catatan
             FROM hafiz h
             LEFT JOIN seleksi s ON h.id = s.hafiz_id AND s.tahun_anggaran = :tahun_join
             LEFT JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id
             WHERE h.tahun_tes = :tahun_where AND h.is_aktif = 1" . ($kabkoId ? " AND h.kabupaten_kota_id = :kabko_id" : ""),
            $exportParams
        );
    }
}
