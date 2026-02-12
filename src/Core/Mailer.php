<?php

/**
 * Mailer Utility
 * ==============
 * Sederhana menggunakan PHP mail() atau bisa dikembangkan ke SMTP.
 */

class Mailer
{
    /**
     * Kirim email reset password
     */
    public static function sendResetPassword(string $email, string $token): bool
    {
        $resetLink = APP_URL . "/reset-password?token=" . $token;
        $appName = APP_NAME;

        $subject = "Reset Password - $appName";

        $message = "
        <html>
        <head>
            <title>Reset Password</title>
        </head>
        <body>
            <h3>Halo,</h3>
            <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>
            <p>Klik tombol di bawah ini untuk mereset password Anda:</p>
            <p>
                <a href='$resetLink' style='background-color: #198754; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                    Reset Password
                </a>
            </p>
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            <hr>
            <p><small>Email ini dikirim secara otomatis oleh sistem $appName.</small></p>
        </body>
        </html>
        ";

        // Headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . getenv('MAIL_FROM_NAME') . " <" . getenv('MAIL_FROM_ADDRESS') . ">" . "\r\n";

        // Jika pakai mail() bawaan server
        return @mail($email, $subject, $message, $headers);
    }
}
