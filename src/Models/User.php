<?php

/**
 * User Model
 * ===========
 * Model untuk manajemen user (login, register, CRUD)
 */

class User
{
    /**
     * Find user by username (NIK/NoHP)
     */
    public static function findByUsername(string $username): ?array
    {
        return Database::queryOne(
            "SELECT * FROM users WHERE username = :username AND is_active = 1",
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
        $user = self::findByUsername($username);

        if ($user && verifyPassword($password, $user['password'])) {
            // Update last login
            self::updateLastLogin($user['id']);
            return $user;
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
            "INSERT INTO users (username, password, role, kabupaten_kota_id, nama, email, telepon) 
             VALUES (:username, :password, :role, :kabupaten_kota_id, :nama, :email, :telepon)",
            [
                'username' => $data['username'],
                'password' => hashPassword($data['password']),
                'role' => $data['role'],
                'kabupaten_kota_id' => $data['kabupaten_kota_id'] ?? null,
                'nama' => $data['nama'] ?? null,
                'email' => $data['email'] ?? null,
                'telepon' => $data['telepon'] ?? null,
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
}
