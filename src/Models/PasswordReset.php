<?php

/**
 * Password Reset Model
 * ====================
 * Mengelola token reset password
 */

class PasswordReset
{
    /**
     * Create new reset token
     */
    public static function create(string $email, string $token): bool
    {
        // Matikan token lama untuk email ini
        Database::execute(
            "UPDATE password_resets SET is_used = 1 WHERE email = :email",
            ['email' => $email]
        );

        return Database::execute(
            "INSERT INTO password_resets (email, token, created_at, is_used) VALUES (:email, :token, NOW(), 0)",
            ['email' => $email, 'token' => $token]
        );
    }

    /**
     * Find valid token
     */
    public static function findToken(string $token): ?array
    {
        return Database::queryOne(
            "SELECT * FROM password_resets 
             WHERE token = :token 
             AND is_used = 0 
             AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            ['token' => $token]
        );
    }

    /**
     * Mark token as used
     */
    public static function markAsUsed(string $token): bool
    {
        return Database::execute(
            "UPDATE password_resets SET is_used = 1 WHERE token = :token",
            ['token' => $token]
        );
    }
}
