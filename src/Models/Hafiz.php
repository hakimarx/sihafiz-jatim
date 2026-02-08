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
             WHERE h.user_id = :user_id AND h.is_aktif = 1",
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
        // Check if user already exists
        $username = $data['telepon'] ?: $data['nik'];
        $user = User::findByUsername($username);

        if ($user) {
            $userId = $user['id'];
        } else {
            // Create user account for Hafiz (password = NIK)
            $userId = User::create([
                'username' => $username,
                'password' => $data['nik'], // Default password = NIK
                'role' => ROLE_HAFIZ,
                'nama' => $data['nama'],
                'email' => $data['email'] ?? null,
                'telepon' => $data['telepon'] ?? null,
            ]);
        };

        // Create Hafiz record
        Database::execute(
            "INSERT INTO hafiz (
                user_id, nik, nama, tempat_lahir, tanggal_lahir, jenis_kelamin,
                alamat, rt, rw, desa_kelurahan, kecamatan, kabupaten_kota_id,
                telepon, email, sertifikat_tahfidz, mengajar, tmt_mengajar,
                tempat_mengajar, tahun_tes
            ) VALUES (
                :user_id, :nik, :nama, :tempat_lahir, :tanggal_lahir, :jenis_kelamin,
                :alamat, :rt, :rw, :desa_kelurahan, :kecamatan, :kabupaten_kota_id,
                :telepon, :email, :sertifikat_tahfidz, :mengajar, :tmt_mengajar,
                :tempat_mengajar, :tahun_tes
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
                'mengajar' => $data['mengajar'] ?? 0,
                'tmt_mengajar' => $data['tmt_mengajar'] ?? null,
                'tempat_mengajar' => $data['tempat_mengajar'] ?? null,
                'tahun_tes' => $data['tahun_tes'] ?? TAHUN_ANGGARAN,
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
            'foto_ktp'
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
    public static function getStatsByKabko(int $tahun = null, ?int $kabkoId = null): array
    {
        $tahun = $tahun ?? TAHUN_ANGGARAN;
        $where = "h.tahun_tes = :tahun AND h.is_aktif = 1";
        $params = ['tahun' => $tahun];

        if ($kabkoId) {
            $where .= " AND k.id = :kabko_id";
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
}
