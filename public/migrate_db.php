<?php
/**
 * Database Migration Script
 * =========================
 * Menambahkan kolom dan data yang dibutuhkan ke database yang sudah ada.
 * Jalankan script ini sekali via browser: https://domain.com/migrate_db.php
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/html; charset=utf-8');

echo "<pre style='font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; border-radius: 8px;'>";
echo "<h2 style='color: #4ec9b0;'>🔄 Database Migration</h2>\n";

function columnExists(string $table, string $column): bool {
    $columns = Database::query("DESCRIBE `$table`");
    foreach ($columns as $col) {
        if ($col['Field'] === $column) return true;
    }
    return false;
}

function addColumnIfNotExists(string $table, string $column, string $definition, string $after = ''): void {
    if (columnExists($table, $column)) {
        echo "<span style='color: #6a9955;'>✓</span> Column <span style='color: #9cdcfe;'>{$table}.{$column}</span> already exists.\n";
    } else {
        $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}";
        if ($after) $sql .= " AFTER `{$after}`";
        Database::execute($sql);
        echo "<span style='color: #4ec9b0;'>✚</span> Added column <span style='color: #9cdcfe;'>{$table}.{$column}</span>.\n";
    }
}

try {
    echo "\n<span style='color: #ce9178;'>--- Users Table ---</span>\n";
    addColumnIfNotExists('users', 'google_id', "VARCHAR(255) DEFAULT NULL COMMENT 'Google OAuth ID'", 'telepon');
    addColumnIfNotExists('users', 'remember_token', "VARCHAR(255) DEFAULT NULL COMMENT 'Remember Me Token'", 'google_id');
    addColumnIfNotExists('users', 'foto_profil', "VARCHAR(255) DEFAULT NULL", 'remember_token');

    // Ensure admin.bkl exists
    echo "\n<span style='color: #ce9178;'>--- Admin KabKo Accounts ---</span>\n";
    $adminBkl = Database::queryOne("SELECT id FROM users WHERE username = 'admin.bkl'");
    if (!$adminBkl) {
        $kabko = Database::queryOne("SELECT id FROM kabupaten_kota WHERE kode = 'BKL'");
        if ($kabko) {
            Database::execute(
                "INSERT INTO users (username, password, role, kabupaten_kota_id, nama, is_active) 
                 VALUES (:username, :password, 'admin_kabko', :kabko_id, :nama, 1)",
                [
                    'username' => 'admin.bkl',
                    'password' => password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]),
                    'kabko_id' => $kabko['id'],
                    'nama' => 'Admin Kabupaten Bangkalan'
                ]
            );
            echo "<span style='color: #4ec9b0;'>✚</span> Created user <span style='color: #9cdcfe;'>admin.bkl</span> (password: admin123)\n";
        } else {
            echo "<span style='color: #f44747;'>✗</span> Kabupaten Bangkalan (BKL) not found in kabupaten_kota table.\n";
        }
    } else {
        // Ensure is_active = 1
        Database::execute("UPDATE users SET is_active = 1 WHERE username = 'admin.bkl'");
        echo "<span style='color: #6a9955;'>✓</span> User admin.bkl exists. Ensured is_active = 1.\n";
        
        // Reset password to admin123 if needed
        Database::execute(
            "UPDATE users SET password = :password WHERE username = 'admin.bkl'",
            ['password' => password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12])]
        );
        echo "<span style='color: #4ec9b0;'>↻</span> Reset admin.bkl password to: admin123\n";
    }

    // Run all admin kabko seeds (insert ignore)
    $seedFile = __DIR__ . '/../config/seed_admin_kabko.sql';
    if (file_exists($seedFile)) {
        echo "\n<span style='color: #ce9178;'>--- Running Admin KabKo Seeds ---</span>\n";
        $sql = file_get_contents($seedFile);
        $statements = array_filter(array_map('trim', explode(';', $sql)), function($s) {
            return !empty($s) && strpos($s, '--') !== 0;
        });
        $count = 0;
        foreach ($statements as $stmt) {
            if (stripos($stmt, 'INSERT') !== false) {
                try {
                    Database::execute($stmt);
                    $count++;
                } catch (Exception $e) {
                    // Ignore duplicate entries
                }
            }
        }
        echo "<span style='color: #4ec9b0;'>✚</span> Processed {$count} admin kabko seed statements.\n";
    }

    echo "\n<span style='color: #4ec9b0; font-size: 1.2em;'>✅ Migration completed successfully!</span>\n";
    echo "\n<span style='color: #dcdcaa;'>Admin Kabupaten Bangkalan:</span>\n";
    echo "  Username: <span style='color: #ce9178;'>admin.bkl</span>\n";
    echo "  Password: <span style='color: #ce9178;'>admin123</span>\n";

} catch (Exception $e) {
    echo "\n<span style='color: #f44747;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</span>\n";
}

echo "</pre>";
