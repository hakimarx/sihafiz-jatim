<?php

/**
 * User Model
 * ===========
 * Model untuk manajemen user (login, register, CRUD)
 */

class User
{
    public static function findByUsername(string $username): ?array
    {
        return Database::queryOne(
            "SELECT * FROM users WHERE username = :username AND is_active = 1",
            ['username' => $username]
        );
    }

    /**
     * Find user by username or NIK (for login)
     */
    public static function findByUsernameOrNik(string $identity): ?array
    {
        // 1. Try username exactly (active users)
        $user = Database::queryOne(
            "SELECT * FROM users WHERE username = :identity AND is_active = 1",
            ['identity' => $identity]
        );
        if ($user) return $user;

        // 2. Try telepon column in users table
        $user = Database::queryOne(
            "SELECT * FROM users WHERE telepon = :identity AND is_active = 1",
            ['identity' => $identity]
        );
        if ($user) return $user;

        // 3. Try NIK in hafiz table
        $hafiz = Database::queryOne(
            "SELECT user_id FROM hafiz WHERE nik = :identity AND is_aktif = 1 LIMIT 1",
            ['identity' => $identity]
        );

        if ($hafiz && $hafiz['user_id']) {
            return self::findById($hafiz['user_id']);
        }

        return null;
    }

    /**
     * Find user by username (including inactive/pending)
     */
    public static function findByUsernameAll(string $username): ?array
    {
        return Database::queryOne(
            "SELECT * FROM users WHERE username = :username",
            ['username' => $username]
        );
    }

    /**
     * Find user by ID
     */
    public static function findById(int $id): ?array
    {
        return Database::queryOne(
            "SELECT u.*, k.nama as kabupaten_kota_nama 
             FROM users u 
             LEFT JOIN kabupaten_kota k ON u.kabupaten_kota_id = k.id 
             WHERE u.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Authenticate user
     */
    public static function authenticate(string $username, string $password): ?array
    {
        // First check active users (by username or NIK)
        $user = self::findByUsernameOrNik($username);

        if ($user && verifyPassword($password, $user['password'])) {
            // Update last login
            self::updateLastLogin($user['id']);
            return $user;
        }

        // Check if this is a pending (inactive) account (by username or NIK)
        $inactiveUser = self::findByUsernameAll($username);
        if (!$inactiveUser) {
             $hafiz = Database::queryOne("SELECT user_id FROM hafiz WHERE nik = :nik LIMIT 1", ['nik' => $username]);
             if ($hafiz && $hafiz['user_id']) {
                 $inactiveUser = Database::queryOne("SELECT * FROM users WHERE id = :id", ['id' => $hafiz['user_id']]);
             }
        }

        if ($inactiveUser && !$inactiveUser['is_active'] && verifyPassword($password, $inactiveUser['password'])) {
            return ['pending' => true, 'nama' => $inactiveUser['nama']];
        }

        return null;
    }

    /**
     * Update last login timestamp
     */
    public static function updateLastLogin(int $userId): void
    {
        Database::execute(
            "UPDATE users SET last_login = NOW() WHERE id = :id",
            ['id' => $userId]
        );
    }

    /**
     * Create new user
     */
    public static function create(array $data): int
    {
        Database::execute(
            "INSERT INTO users (username, password, role, kabupaten_kota_id, nama, email, telepon, is_active) 
             VALUES (:username, :password, :role, :kabupaten_kota_id, :nama, :email, :telepon, :is_active)",
            [
                'username' => $data['username'],
                'password' => hashPassword($data['password']),
                'role' => $data['role'],
                'kabupaten_kota_id' => $data['kabupaten_kota_id'] ?? null,
                'nama' => $data['nama'] ?? null,
                'email' => $data['email'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'is_active' => $data['is_active'] ?? 1,
            ]
        );

        return (int) Database::lastInsertId();
    }

    /**
     * Get users by role
     */
    public static function getByRole(string $role, ?int $kabkoId = null): array
    {
        $sql = "SELECT u.*, k.nama as kabupaten_kota_nama 
                FROM users u 
                LEFT JOIN kabupaten_kota k ON u.kabupaten_kota_id = k.id 
                WHERE u.role = :role";
        $params = ['role' => $role];

        if ($kabkoId) {
            $sql .= " AND u.kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $kabkoId;
        }

        $sql .= " ORDER BY u.nama ASC";

        return Database::query($sql, $params);
    }

    /**
     * Get all users with pagination and optional filters
     */
    public static function getAll(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT u.*, k.nama as kabupaten_kota_nama 
                FROM users u 
                LEFT JOIN kabupaten_kota k ON u.kabupaten_kota_id = k.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['role'])) {
            $sql .= " AND u.role = :role";
            $params['role'] = $filters['role'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (u.nama LIKE :search OR u.username LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }

        if (!empty($filters['kabupaten_kota_id'])) {
            $sql .= " AND u.kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $filters['kabupaten_kota_id'];
        }

        // Count total
        $countSql = str_replace("u.*, k.nama as kabupaten_kota_nama", "COUNT(*) as count", $sql);
        $total = Database::queryOne($countSql, $params)['count'];

        $sql .= " ORDER BY u.id DESC LIMIT $limit OFFSET $offset";
        $data = Database::query($sql, $params);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }

    /**
     * Update user
     */
    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params['username'] = $data['username'];
        }

        if (!empty($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = hashPassword($data['password']);
        }

        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params['role'] = $data['role'];
        }

        if (array_key_exists('kabupaten_kota_id', $data)) {
            $fields[] = "kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $data['kabupaten_kota_id'];
        }

        if (isset($data['nama'])) {
            $fields[] = "nama = :nama";
            $params['nama'] = $data['nama'];
        }

        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }

        if (isset($data['telepon'])) {
            $fields[] = "telepon = :telepon";
            $params['telepon'] = $data['telepon'];
        }

        if (isset($data['is_active'])) {
            $fields[] = "is_active = :is_active";
            $params['is_active'] = $data['is_active'];
        }

        if (isset($data['foto_profil'])) {
            $fields[] = "foto_profil = :foto_profil";
            $params['foto_profil'] = $data['foto_profil'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = :id";
        return Database::execute($sql, $params);
    }

    /**
     * Delete user
     */
    public static function delete(int $id): bool
    {
        return Database::execute("DELETE FROM users WHERE id = :id", ['id' => $id]);
    }

    /**
     * Update user password
     */
    public static function updatePassword(int $id, string $password): bool
    {
        return Database::execute(
            "UPDATE users SET password = :password WHERE id = :id",
            [
                'id' => $id,
                'password' => hashPassword($password)
            ]
        ) > 0;
    }

    /**
     * Check if username exists
     */
    public static function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $result = Database::queryOne($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Get pending approval users (is_active = 0) with optional kabko filter
     */
    public static function getPendingApproval(?int $kabkoId = null): array
    {
        $sql = "SELECT u.*, k.nama as kabupaten_kota_nama,
                       h.nama as hafiz_nama, h.nik as hafiz_nik, h.telepon as hafiz_telepon
                FROM users u 
                LEFT JOIN kabupaten_kota k ON u.kabupaten_kota_id = k.id
                LEFT JOIN hafiz h ON h.user_id = u.id
                WHERE u.is_active = 0 AND u.role = 'hafiz'";
        $params = [];

        if ($kabkoId) {
            $sql .= " AND u.kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $kabkoId;
        }

        $sql .= " ORDER BY u.created_at DESC";

        return Database::query($sql, $params);
    }

    /**
     * Count pending approval users
     */
    public static function countPendingApproval(?int $kabkoId = null): int
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE is_active = 0 AND role = 'hafiz'";
        $params = [];

        if ($kabkoId) {
            $sql .= " AND kabupaten_kota_id = :kabko_id";
            $params['kabko_id'] = $kabkoId;
        }

        $result = Database::queryOne($sql, $params);
        return (int) ($result['count'] ?? 0);
    }
}
