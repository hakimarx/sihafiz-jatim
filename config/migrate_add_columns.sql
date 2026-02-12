-- ============================================
-- Migration: Add missing columns to users table
-- ============================================
-- Jalankan script ini di database production yang sudah ada
-- untuk menambahkan kolom-kolom baru yang dibutuhkan
-- ============================================

-- Tambahkan kolom google_id jika belum ada
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(255) DEFAULT NULL COMMENT 'Google OAuth ID' AFTER `telepon`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `remember_token` VARCHAR(255) DEFAULT NULL COMMENT 'Remember Me Token' AFTER `google_id`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `foto_profil` VARCHAR(255) DEFAULT NULL AFTER `remember_token`;

-- Index untuk google_id (ignore jika sudah ada)
-- ALTER TABLE `users` ADD UNIQUE KEY `idx_google_id` (`google_id`);

-- ============================================
-- Pastikan admin kabko BKL (Bangkalan) sudah ada
-- ============================================
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.bkl', '$2y$12$ulx0yY5aI0.ah92vyi4vmeCuH30ErtlZf/qrYK03p516KxYxLUOPe', 'admin_kabko', id, 'Admin Kabupaten Bangkalan', 1 
FROM kabupaten_kota WHERE kode = 'BKL';
