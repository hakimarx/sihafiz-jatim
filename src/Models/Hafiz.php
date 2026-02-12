<?php

/**
 * Hafiz Model
 * ============
 * Model untuk manajemen data Hafiz (CRUD)
 */

class Hafiz
{
    /**
     * Find Hafiz by ID
     */
    public static function findById(int $id): ?array
    {
        return Database::queryOne(
            "SELECT h.*, k.nama as kabupaten_kota_nama 
             FROM hafiz h 
             LEFT JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id 
             WHERE h.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Find Hafiz by User ID
     */
    public static function findByUserId(int $userId): ?array
    {
        return Database::queryOne(
            "SELECT h.*, k.nama as kabupaten_kota_nama 
             FROM hafiz h 
             LEFT JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id 
             WHERE h.user_id = :user_id AND h.is_aktif = 1
             ORDER BY h.tahun_tes DESC LIMIT 1",
            ['user_id' => $userId]
        );
    }

    /**
     * Find Hafiz by NIK
     */
    public static function findByNik(string $nik, ?int $tahun = null): ?array
    {
        $sql = "SELECT h.*, k.nama as kabupaten_kota_nama 
                FROM hafiz h 
                LEFT JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id 
                WHERE h.nik = :nik";
        $params = ['nik' => $nik];

        if ($tahun) {
            $sql .= " AND h.tahun_tes = :tahun";
            $params['tahun'] = $tahun;
        }

        $sql .= " ORDER BY h.tahun_tes DESC LIMIT 1";

        return Database::queryOne($sql, $params);
    }

    /**
     * Get all Hafiz with pagination and filters
     */
    public static function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $where = ["h.is_aktif = 1"];
        $params = [];

        // Apply filters
        if (!empty($filters['kabupaten_kota_id'])) {
            $where[] = "h.kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $filters['kabupaten_kota_id'];
        }

        if (!empty($filters['tahun_tes'])) {
            $where[] = "h.tahun_tes = :tahun_tes";
            $params['tahun_tes'] = $filters['tahun_tes'];
        }

        if (!empty($filters['status_kelulusan'])) {
            $where[] = "h.status_kelulusan = :status_kelulusan";
            $params['status_kelulusan'] = $filters['status_kelulusan'];
        }

        if (!empty($filters['status_data'])) {
            $where[] = "h.status_data = :status_data";
            $params['status_data'] = $filters['status_data'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(h.nama LIKE :search OR h.nik LIKE :search2)";
            $params['search'] = "%{$filters['search']}%";
            $params['search2'] = "%{$filters['search']}%";
        }

        $whereClause = implode(' AND ', $where);

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM hafiz h WHERE {$whereClause}";
        $total = Database::queryOne($countSql, $params)['total'];

        // Get data
        $sql = "SELECT h.*, k.nama as kabupaten_kota_nama 
                FROM hafiz h 
                LEFT JOIN kabupaten_kota k ON h.kabupaten_kota_id = k.id 
                WHERE {$whereClause}
                ORDER BY h.created_at DESC
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
     * Create new Hafiz
     */
    public static function create(array $data): int
    {
        // Prioritize NIK as username for Hafiz to ensure uniqueness and prevent account collisions
        $username = $data['nik'];

        // Find existing user (including inactive/pending)
        $user = User::findByUsernameAll($username);

        if (!$user && !empty($data['telepon'])) {
            // Check if user exists with phone number as username
            $user = User::findByUsernameAll($data['telepon']);
        }

        if ($user) {
            $userId = $user['id'];
            // If user exists but role is not hafiz, update it? 
            // Better not touch role if it's admin, but usually NIK-based users will be hafiz.
            if ($user['role'] !== ROLE_HAFIZ) {
                User::update($userId, ['role' => ROLE_HAFIZ]);
            }
        } else {
            // Create user account for Hafiz (password = NIK)
            $userId = User::create([
                'username' => $username,
                'password' => $data['nik'], // Default password = NIK
                'role' => ROLE_HAFIZ,
                'nama' => $data['nama'],
                'email' => $data['email'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'is_active' => 1, // Admin-created users are active by default
            ]);
        }

        // Create Hafiz record
        Database::execute(
            "INSERT INTO hafiz (
                user_id, nik, nama, tempat_lahir, tanggal_lahir, jenis_kelamin,
                alamat, rt, rw, desa_kelurahan, kecamatan, kabupaten_kota_id,
                telepon, email, sertifikat_tahfidz, mengajar, tmt_mengajar,
                tempat_mengajar, tahun_tes, tahun_lulus, lokasi_seleksi, is_meninggal, tanggal_kematian, is_aktif
            ) VALUES (
                :user_id, :nik, :nama, :tempat_lahir, :tanggal_lahir, :jenis_kelamin,
                :alamat, :rt, :rw, :desa_kelurahan, :kecamatan, :kabupaten_kota_id,
                :telepon, :email, :sertifikat_tahfidz, :mengajar, :tmt_mengajar,
                :tempat_mengajar, :tahun_tes, :tahun_lulus, :lokasi_seleksi, :is_meninggal, :tanggal_kematian, 1
            )",
            [
                'user_id' => $userId,
                'nik' => $data['nik'],
                'nama' => $data['nama'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'alamat' => $data['alamat'],
                'rt' => $data['rt'] ?? null,
                'rw' => $data['rw'] ?? null,
                'desa_kelurahan' => $data['desa_kelurahan'],
                'kecamatan' => $data['kecamatan'],
                'kabupaten_kota_id' => $data['kabupaten_kota_id'],
                'telepon' => $data['telepon'] ?? null,
                'email' => $data['email'] ?? null,
                'sertifikat_tahfidz' => $data['sertifikat_tahfidz'] ?? null,
                'mengajar' => !empty($data['tempat_mengajar']) ? 1 : 0,
                'tmt_mengajar' => $data['tmt_mengajar'] ?? null,
                'tempat_mengajar' => $data['tempat_mengajar'] ?? null,
                'tahun_tes' => $data['tahun_tes'] ?? TAHUN_ANGGARAN,
                'tahun_lulus' => $data['tahun_lulus'] ?? null,
                'lokasi_seleksi' => $data['lokasi_seleksi'] ?? null,
                'is_meninggal' => $data['is_meninggal'] ?? 0,
                'tanggal_kematian' => $data['tanggal_kematian'] ?? null,
            ]
        );

        return (int) Database::lastInsertId();
    }

    /**
     * Update Hafiz
     */
    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = [
            'nik',
            'nama',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'alamat',
            'rt',
            'rw',
            'desa_kelurahan',
            'kecamatan',
            'telepon',
            'email',
            'nama_bank',
            'nomor_rekening',
            'sertifikat_tahfidz',
            'mengajar',
            'tmt_mengajar',
            'tempat_mengajar',
            'status_kelulusan',
            'nilai_tahfidz',
            'nilai_wawasan',
            'status_insentif',
            'keterangan',
            'foto_profil',
            'foto_ktp',
            'tanda_tangan',
            'status_data',
            'tahun_lulus',
            'lokasi_seleksi',
            'is_meninggal',
            'tanggal_kematian'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE hafiz SET " . implode(', ', $fields) . " WHERE id = :id";
        return Database::execute($sql, $params) > 0;
    }

    /**
     * Delete (soft delete)
     */
    public static function delete(int $id): bool
    {
        return Database::execute(
            "UPDATE hafiz SET is_aktif = 0 WHERE id = :id",
            ['id' => $id]
        ) > 0;
    }

    /**
     * Get statistics by Kabupaten/Kota
     */
    public static function getStatsByKabko(?int $tahun = null, ?int $kabkoId = null): array
    {
        $tahun = $tahun ?? TAHUN_ANGGARAN;
        $where = "h.tahun_tes = :tahun AND h.is_aktif = 1";
        $params = ['tahun' => $tahun];

        $kbWhere = "";
        if ($kabkoId) {
            $where .= " AND k.id = :kabko_id";
            $kbWhere = " WHERE k.id = :kabko_id";
            $params['kabko_id'] = $kabkoId;
        }

        return Database::query(
            "SELECT 
                k.id, k.nama, k.kode,
                COUNT(h.id) as total_pendaftar,
                SUM(CASE WHEN h.status_kelulusan = 'lulus' THEN 1 ELSE 0 END) as total_lulus,
                SUM(CASE WHEN h.status_kelulusan = 'pending' THEN 1 ELSE 0 END) as total_pending
              FROM kabupaten_kota k
              LEFT JOIN hafiz h ON k.id = h.kabupaten_kota_id AND {$where}
              {$kbWhere}
              GROUP BY k.id
              ORDER BY k.nama",
            $params
        );
    }

    /**
     * Check if NIK already registered for a year
     */
    public static function nikExists(string $nik, int $tahun, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM hafiz WHERE nik = :nik AND tahun_tes = :tahun";
        $params = ['nik' => $nik, 'tahun' => $tahun];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $result = Database::queryOne($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Count pending hafiz by kabupaten_kota for notifications
     */
    public static function countPendingByKabko(?int $kabkoId): int
    {
        if (!$kabkoId) return 0;

        $result = Database::queryOne(
            "SELECT COUNT(*) as count FROM hafiz 
             WHERE kabupaten_kota_id = :kabko_id 
             AND status_kelulusan = 'pending' 
             AND is_aktif = 1 
             AND tahun_tes = :tahun",
            ['kabko_id' => $kabkoId, 'tahun' => TAHUN_ANGGARAN]
        );
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Get additional teaching locations
     */
    public static function getMengajarList(int $hafizId): array
    {
        return Database::query(
            "SELECT * FROM hafiz_mengajar WHERE hafiz_id = :id ORDER BY tmt_mengajar DESC",
            ['id' => $hafizId]
        );
    }

    /**
     * Update teaching locations list
     */
    public static function updateMengajarList(int $hafizId, array $list): void
    {
        // Clear existing
        Database::execute("DELETE FROM hafiz_mengajar WHERE hafiz_id = :id", ['id' => $hafizId]);

        // Insert new ones
        foreach ($list as $item) {
            if (empty($item['tempat']) || empty($item['tmt'])) continue;

            Database::execute(
                "INSERT INTO hafiz_mengajar (hafiz_id, tempat_mengajar, tmt_mengajar) 
                 VALUES (:id, :tempat, :tmt)",
                [
                    'id' => $hafizId,
                    'tempat' => $item['tempat'],
                    'tmt' => $item['tmt']
                ]
            );
        }
    }

    /**
     * Get statistics by gender per kabupaten/kota
     */
    public static function getStatsByGender(?int $kabkoId = null): array
    {
        $where = "h.is_aktif = 1";
        $params = [];

        $kbWhere = "";
        if ($kabkoId) {
            $where .= " AND h.kabupaten_kota_id = :kabko_id";
            $kbWhere = " WHERE k.id = :kabko_id";
            $params['kabko_id'] = $kabkoId;
        }

        return Database::query(
            "SELECT 
                k.id, k.nama,
                SUM(CASE WHEN h.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN h.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan,
                COUNT(h.id) as total
             FROM kabupaten_kota k
             LEFT JOIN hafiz h ON k.id = h.kabupaten_kota_id AND {$where}
             {$kbWhere}
             GROUP BY k.id
             ORDER BY k.nama",
            $params
        );
    }

    /**
     * Get statistics by graduation year
     */
    public static function getStatsByTahunKelulusan(?int $kabkoId = null): array
    {
        $where = "h.is_aktif = 1 AND h.status_kelulusan = 'lulus'";
        $params = [];

        if ($kabkoId) {
            $where .= " AND h.kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $kabkoId;
        }

        return Database::query(
            "SELECT 
                h.tahun_tes as tahun,
                COUNT(h.id) as total_lulus,
                SUM(CASE WHEN h.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN h.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan
             FROM hafiz h
             WHERE {$where}
             GROUP BY h.tahun_tes
             ORDER BY h.tahun_tes DESC",
            $params
        );
    }

    /**
     * Count hafiz who have submitted laporan harian
     */
    public static function getStatsLaporan(?int $kabkoId = null): array
    {
        $where = "h.is_aktif = 1";
        $params = [];

        if ($kabkoId) {
            $where .= " AND h.kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $kabkoId;
        }

        return Database::queryOne(
            "SELECT 
                COUNT(DISTINCT h.id) as total_hafiz,
                COUNT(DISTINCT CASE WHEN lh.id IS NOT NULL THEN h.id END) as hafiz_sudah_laporan,
                COUNT(DISTINCT CASE WHEN lh.id IS NULL THEN h.id END) as hafiz_belum_laporan,
                COUNT(lh.id) as total_laporan,
                SUM(CASE WHEN lh.status_verifikasi = 'pending' THEN 1 ELSE 0 END) as laporan_pending,
                SUM(CASE WHEN lh.status_verifikasi = 'disetujui' THEN 1 ELSE 0 END) as laporan_disetujui,
                SUM(CASE WHEN lh.status_verifikasi = 'ditolak' THEN 1 ELSE 0 END) as laporan_ditolak
             FROM hafiz h
             LEFT JOIN laporan_harian lh ON h.id = lh.hafiz_id
             WHERE {$where}",
            $params
        ) ?? [
            'total_hafiz' => 0,
            'hafiz_sudah_laporan' => 0,
            'hafiz_belum_laporan' => 0,
            'total_laporan' => 0,
            'laporan_pending' => 0,
            'laporan_disetujui' => 0,
            'laporan_ditolak' => 0,
        ];
    }
}
