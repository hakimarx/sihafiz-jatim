-- ============================================
-- Migration: Add missing columns to users and hafiz table
-- ============================================

-- Tambahkan kolom status_data ke hafiz jika belum ada
-- MySQL compatible: gunakan SET untuk cek kolom
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hafiz' AND COLUMN_NAME = 'status_data');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `hafiz` ADD COLUMN `status_data` ENUM(''valid'', ''perlu_perbaikan'') DEFAULT ''valid'' COMMENT ''Status kualitas data'' AFTER `keterangan`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambahkan kolom google_id jika belum ada
SET @col_g_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'google_id');
SET @sql_g = IF(@col_g_exists = 0, 'ALTER TABLE `users` ADD COLUMN `google_id` VARCHAR(255) DEFAULT NULL COMMENT ''Google OAuth ID'' AFTER `telepon`', 'SELECT 1');
PREPARE stmt_g FROM @sql_g;
EXECUTE stmt_g;
DEALLOCATE PREPARE stmt_g;

-- Tambahkan kolom remember_token jika belum ada
SET @col_r_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'remember_token');
SET @sql_r = IF(@col_r_exists = 0, 'ALTER TABLE `users` ADD COLUMN `remember_token` VARCHAR(255) DEFAULT NULL COMMENT ''Remember Me Token'' AFTER `google_id`', 'SELECT 1');
PREPARE stmt_r FROM @sql_r;
EXECUTE stmt_r;
DEALLOCATE PREPARE stmt_r;

-- Tambahkan kolom foto_profil jika belum ada
SET @col_f_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'foto_profil');
SET @sql_f = IF(@col_f_exists = 0, 'ALTER TABLE `users` ADD COLUMN `foto_profil` VARCHAR(255) DEFAULT NULL AFTER `remember_token`', 'SELECT 1');
PREPARE stmt_f FROM @sql_f;
EXECUTE stmt_f;
DEALLOCATE PREPARE stmt_f;

-- ============================================
-- Pastikan admin kabko BKL (Bangkalan) sudah ada
-- ============================================
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.bkl', '$2y$12$ulx0yY5aI0.ah92vyi4vmeCuH30ErtlZf/qrYK03p516KxYxLUOPe', 'admin_kabko', id, 'Admin Kabupaten Bangkalan', 1 
FROM kabupaten_kota WHERE kode = 'BKL';
