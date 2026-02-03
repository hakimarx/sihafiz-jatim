<?php
/**
 * Database Configuration & PDO Wrapper
 * =====================================
 * File ini berisi class untuk koneksi database menggunakan PDO.
 * Konfigurasi diambil dari environment variables (tidak hardcoded).
 */

class Database {
    private static ?PDO $instance = null;
    
    /**
     * Mendapatkan instance koneksi PDO (Singleton Pattern)
     * Menggunakan singleton untuk efisiensi koneksi
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
                $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
                $name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'sihafiz_jatim');
                $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
                $pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');
                
                $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    // Batasi pool koneksi untuk shared hosting
                    PDO::ATTR_PERSISTENT         => false,
                ];
                
                self::$instance = new PDO($dsn, $user, $pass, $options);
                
            } catch (PDOException $e) {
                // Log error, jangan tampilkan detail ke user di production
                error_log("Database Connection Error: " . $e->getMessage());
                throw new Exception("Koneksi database gagal. Hubungi administrator.");
            }
        }
        
        return self::$instance;
    }
    
    /**
     * Helper untuk execute query SELECT
     */
    public static function query(string $sql, array $params = []): array {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Helper untuk execute query SELECT single row
     */
    public static function queryOne(string $sql, array $params = []): ?array {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }
    
    /**
     * Helper untuk execute query INSERT/UPDATE/DELETE
     */
    public static function execute(string $sql, array $params = []): int {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    /**
     * Get last insert ID
     */
    public static function lastInsertId(): string {
        return self::getConnection()->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool {
        return self::getConnection()->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public static function commit(): bool {
        return self::getConnection()->commit();
    }
    
    /**
     * Rollback transaction
     */
    public static function rollback(): bool {
        return self::getConnection()->rollBack();
    }
}
