<?php

/**
 * Laporan Harian Model
 * =====================
 * Model untuk manajemen laporan harian (SPJ) Hafiz
 */

class LaporanHarian
{
    /**
     * Find by ID
     */
    public static function findById(int $id): ?array
    {
        return Database::queryOne(
            "SELECT lh.*, h.nama as hafiz_nama, u.nama as verifier_nama 
             FROM laporan_harian lh 
             LEFT JOIN hafiz h ON lh.hafiz_id = h.id 
             LEFT JOIN users u ON lh.verified_by = u.id 
             WHERE lh.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Get laporan by Hafiz ID with pagination
     */
    public static function getByHafizId(int $hafizId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as total FROM laporan_harian WHERE hafiz_id = :hafiz_id";
        $total = Database::queryOne($countSql, ['hafiz_id' => $hafizId])['total'];

        $data = Database::query(
            "SELECT lh.*, u.nama as verifier_nama 
             FROM laporan_harian lh 
             LEFT JOIN users u ON lh.verified_by = u.id 
             WHERE lh.hafiz_id = :hafiz_id 
             ORDER BY lh.tanggal DESC, lh.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            ['hafiz_id' => $hafizId]
        );

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get all laporan with filters (untuk admin)
     */
    public static function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['hafiz_id'])) {
            $where[] = "lh.hafiz_id = :hafiz_id";
            $params['hafiz_id'] = $filters['hafiz_id'];
        }

        if (!empty($filters['status_verifikasi'])) {
            $where[] = "lh.status_verifikasi = :status";
            $params['status'] = $filters['status_verifikasi'];
        }

        if (!empty($filters['jenis_kegiatan'])) {
            $where[] = "lh.jenis_kegiatan = :jenis";
            $params['jenis'] = $filters['jenis_kegiatan'];
        }

        if (!empty($filters['tanggal_dari'])) {
            $where[] = "lh.tanggal >= :tanggal_dari";
            $params['tanggal_dari'] = $filters['tanggal_dari'];
        }

        if (!empty($filters['tanggal_sampai'])) {
            $where[] = "lh.tanggal <= :tanggal_sampai";
            $params['tanggal_sampai'] = $filters['tanggal_sampai'];
        }

        if (!empty($filters['kabupaten_kota_id'])) {
            $where[] = "h.kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $filters['kabupaten_kota_id'];
        }

        $whereClause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) as total 
                     FROM laporan_harian lh 
                     LEFT JOIN hafiz h ON lh.hafiz_id = h.id 
                     WHERE {$whereClause}";
        $total = Database::queryOne($countSql, $params)['total'];

        $data = Database::query(
            "SELECT lh.*, h.nama as hafiz_nama, h.nik as hafiz_nik, 
                    k.nama as kabupaten_kota_nama, u.nama as verifier_nama 
             FROM laporan_harian lh 
             LEFT JOIN hafiz h ON lh.hafiz_id = h.id 
             LEFT JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id 
             LEFT JOIN users u ON lh.verified_by = u.id 
             WHERE {$whereClause}
             ORDER BY lh.tanggal DESC, lh.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Create new laporan
     */
    public static function create(array $data): int
    {
        Database::execute(
            "INSERT INTO laporan_harian (
                hafiz_id, tanggal, jenis_kegiatan, deskripsi, foto, lokasi, durasi_menit
            ) VALUES (
                :hafiz_id, :tanggal, :jenis_kegiatan, :deskripsi, :foto, :lokasi, :durasi_menit
            )",
            [
                'hafiz_id' => $data['hafiz_id'],
                'tanggal' => $data['tanggal'],
                'jenis_kegiatan' => $data['jenis_kegiatan'],
                'deskripsi' => $data['deskripsi'],
                'foto' => $data['foto'] ?? null,
                'lokasi' => $data['lokasi'] ?? null,
                'durasi_menit' => $data['durasi_menit'] ?? null,
            ]
        );

        return (int) Database::lastInsertId();
    }

    /**
     * Update laporan (hanya jika masih pending)
     */
    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = ['tanggal', 'jenis_kegiatan', 'deskripsi', 'foto', 'lokasi', 'durasi_menit'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE laporan_harian SET " . implode(', ', $fields) . " 
                WHERE id = :id AND status_verifikasi = 'pending'";
        return Database::execute($sql, $params) > 0;
    }

    /**
     * Verify laporan (untuk admin)
     */
    public static function verify(int $id, string $status, int $verifierId, ?string $catatan = null): bool
    {
        return Database::execute(
            "UPDATE laporan_harian SET 
                status_verifikasi = :status,
                verified_by = :verifier_id,
                verified_at = NOW(),
                catatan_verifikasi = :catatan
             WHERE id = :id",
            [
                'id' => $id,
                'status' => $status,
                'verifier_id' => $verifierId,
                'catatan' => $catatan
            ]
        ) > 0;
    }

    /**
     * Delete laporan (hanya jika masih pending)
     */
    public static function delete(int $id): bool
    {
        return Database::execute(
            "DELETE FROM laporan_harian WHERE id = :id AND status_verifikasi = 'pending'",
            ['id' => $id]
        ) > 0;
    }

    /**
     * Get summary for a Hafiz (untuk dashboard)
     */
    public static function getSummary(int $hafizId, ?string $bulan = null): array
    {
        $bulan = $bulan ?? date('Y-m');

        return Database::queryOne(
            "SELECT 
                COUNT(*) as total_laporan,
                SUM(CASE WHEN status_verifikasi = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN status_verifikasi = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status_verifikasi = 'ditolak' THEN 1 ELSE 0 END) as ditolak,
                SUM(CASE WHEN status_verifikasi = 'disetujui' THEN durasi_menit ELSE 0 END) as total_durasi
             FROM laporan_harian 
             WHERE hafiz_id = :hafiz_id AND DATE_FORMAT(tanggal, '%Y-%m') = :bulan",
            ['hafiz_id' => $hafizId, 'bulan' => $bulan]
        ) ?? [
            'total_laporan' => 0,
            'disetujui' => 0,
            'pending' => 0,
            'ditolak' => 0,
            'total_durasi' => 0
        ];
    }
}
