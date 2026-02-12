-- =====================================================
-- SIHAFIZ JATIM - FULL DATABASE DEPLOYMENT SCRIPT
-- Run this in phpMyAdmin SQL tab
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+07:00";

-- Drop existing tables if any (in correct order due to foreign keys)
DROP TABLE IF EXISTS `laporan_harian`;
DROP TABLE IF EXISTS `seleksi`;
DROP TABLE IF EXISTS `hafiz`;
DROP TABLE IF EXISTS `periode_tes`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `kabupaten_kota`;
DROP TABLE IF EXISTS `settings`;

-- =====================================================
-- CREATE TABLES
-- =====================================================

-- Table: kabupaten_kota
CREATE TABLE `kabupaten_kota` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(100) NOT NULL,
    `kode` VARCHAR(10) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_kode` (`kode`),
    KEY `idx_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users
CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin_prov', 'admin_kabko', 'penguji', 'hafiz') NOT NULL,
    `kabupaten_kota_id` INT(11) DEFAULT NULL,
    `nama` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `telepon` VARCHAR(20) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_username` (`username`),
    KEY `idx_role` (`role`),
    KEY `idx_kabko` (`kabupaten_kota_id`),
    KEY `idx_active` (`is_active`),
    CONSTRAINT `fk_users_kabko` FOREIGN KEY (`kabupaten_kota_id`) 
        REFERENCES `kabupaten_kota` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: periode_tes
CREATE TABLE `periode_tes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tahun` INT(11) NOT NULL,
    `nama_periode` VARCHAR(100) NOT NULL,
    `tanggal_mulai` DATE NOT NULL,
    `tanggal_selesai` DATE NOT NULL,
    `kuota_total` INT(11) DEFAULT 1000,
    `status` ENUM('draft', 'pendaftaran', 'tes', 'selesai') DEFAULT 'draft',
    `deskripsi` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_tahun` (`tahun`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hafiz
CREATE TABLE `hafiz` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `nik` VARCHAR(20) NOT NULL,
    `nama` VARCHAR(255) NOT NULL,
    `tempat_lahir` VARCHAR(100) NOT NULL,
    `tanggal_lahir` DATE NOT NULL,
    `jenis_kelamin` ENUM('L', 'P') NOT NULL,
    `alamat` TEXT NOT NULL,
    `rt` VARCHAR(5) DEFAULT NULL,
    `rw` VARCHAR(5) DEFAULT NULL,
    `desa_kelurahan` VARCHAR(100) NOT NULL,
    `kecamatan` VARCHAR(100) NOT NULL,
    `kabupaten_kota_id` INT(11) NOT NULL,
    `telepon` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `nama_bank` VARCHAR(100) DEFAULT NULL,
    `nomor_rekening` VARCHAR(50) DEFAULT NULL,
    `sertifikat_tahfidz` VARCHAR(255) DEFAULT NULL,
    `mengajar` TINYINT(1) DEFAULT 0,
    `tmt_mengajar` DATE DEFAULT NULL,
    `tempat_mengajar` VARCHAR(255) DEFAULT NULL,
    `tahun_tes` INT(11) NOT NULL,
    `periode_tes_id` INT(11) DEFAULT NULL,
    `status_kelulusan` ENUM('lulus', 'tidak_lulus', 'pending') DEFAULT 'pending',
    `nilai_tahfidz` DECIMAL(5,2) DEFAULT NULL,
    `nilai_wawasan` DECIMAL(5,2) DEFAULT NULL,
    `foto_ktp` VARCHAR(255) DEFAULT NULL,
    `foto_profil` VARCHAR(255) DEFAULT NULL,
    `nomor_piagam` VARCHAR(50) DEFAULT NULL,
    `tanggal_lulus` DATE DEFAULT NULL,
    `status_insentif` ENUM('aktif', 'tidak_aktif', 'suspend') DEFAULT 'tidak_aktif',
    `keterangan` TEXT DEFAULT NULL,
    `status_data` ENUM('valid', 'perlu_perbaikan') DEFAULT 'valid' COMMENT 'Status kualitas data',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_aktif` TINYINT(1) DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_nik_tahun` (`nik`, `tahun_tes`),
    KEY `idx_user` (`user_id`),
    KEY `idx_kabko` (`kabupaten_kota_id`),
    KEY `idx_tahun_tes` (`tahun_tes`),
    KEY `idx_periode` (`periode_tes_id`),
    KEY `idx_status_lulus` (`status_kelulusan`),
    KEY `idx_status_insentif` (`status_insentif`),
    KEY `idx_aktif` (`is_aktif`),
    CONSTRAINT `fk_hafiz_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_hafiz_kabko` FOREIGN KEY (`kabupaten_kota_id`) 
        REFERENCES `kabupaten_kota` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_hafiz_periode` FOREIGN KEY (`periode_tes_id`) 
        REFERENCES `periode_tes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: seleksi
CREATE TABLE `seleksi` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `hafiz_id` INT(11) NOT NULL,
    `tahun_anggaran` INT(11) NOT NULL,
    `penguji_id` INT(11) DEFAULT NULL,
    `nilai_wawasan` DECIMAL(5,2) DEFAULT NULL,
    `nilai_hafalan` DECIMAL(5,2) DEFAULT NULL,
    `nilai_total` DECIMAL(5,2) DEFAULT NULL,
    `catatan` TEXT DEFAULT NULL,
    `status_lulus` TINYINT(1) DEFAULT 0,
    `tanggal_tes` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_hafiz_tahun` (`hafiz_id`, `tahun_anggaran`),
    KEY `idx_tahun_anggaran` (`tahun_anggaran`),
    KEY `idx_penguji` (`penguji_id`),
    KEY `idx_status_lulus` (`status_lulus`),
    CONSTRAINT `fk_seleksi_hafiz` FOREIGN KEY (`hafiz_id`) 
        REFERENCES `hafiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_seleksi_penguji` FOREIGN KEY (`penguji_id`) 
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: laporan_harian
CREATE TABLE `laporan_harian` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `hafiz_id` INT(11) NOT NULL,
    `tanggal` DATE NOT NULL,
    `jenis_kegiatan` ENUM('mengajar', 'murojah', 'khataman', 'lainnya') NOT NULL,
    `deskripsi` TEXT NOT NULL,
    `foto` VARCHAR(255) DEFAULT NULL,
    `lokasi` VARCHAR(255) DEFAULT NULL,
    `durasi_menit` INT(11) DEFAULT NULL,
    `status_verifikasi` ENUM('pending', 'disetujui', 'ditolak') DEFAULT 'pending',
    `verified_by` INT(11) DEFAULT NULL,
    `verified_at` DATETIME DEFAULT NULL,
    `catatan_verifikasi` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_hafiz` (`hafiz_id`),
    KEY `idx_tanggal` (`tanggal`),
    KEY `idx_jenis` (`jenis_kegiatan`),
    KEY `idx_status` (`status_verifikasi`),
    KEY `idx_hafiz_tanggal` (`hafiz_id`, `tanggal`),
    CONSTRAINT `fk_laporan_hafiz` FOREIGN KEY (`hafiz_id`) 
        REFERENCES `hafiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_laporan_verifier` FOREIGN KEY (`verified_by`) 
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: settings
CREATE TABLE `settings` (
    `key` VARCHAR(50) NOT NULL,
    `value` LONGTEXT DEFAULT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT INITIAL DATA
-- =====================================================

-- Insert Admin User (password: Admin123!)
INSERT INTO `users` (`username`, `password`, `role`, `nama`, `is_active`) VALUES
('admin', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_prov', 'Administrator Provinsi', 1);

-- Insert Kabupaten/Kota Jawa Timur
INSERT INTO `kabupaten_kota` (`nama`, `kode`) VALUES
('Kota Surabaya', 'SBY'),
('Kota Malang', 'MLG'),
('Kota Kediri', 'KDR'),
('Kota Blitar', 'BLT'),
('Kota Mojokerto', 'MJK'),
('Kota Madiun', 'MDN'),
('Kota Pasuruan', 'PSR'),
('Kota Probolinggo', 'PBL'),
('Kota Batu', 'BTU'),
('Kabupaten Gresik', 'GRS'),
('Kabupaten Sidoarjo', 'SDA'),
('Kabupaten Mojokerto', 'KMJK'),
('Kabupaten Jombang', 'JBG'),
('Kabupaten Bojonegoro', 'BJN'),
('Kabupaten Tuban', 'TBN'),
('Kabupaten Lamongan', 'LMG'),
('Kabupaten Madiun', 'KMDN'),
('Kabupaten Magetan', 'MGT'),
('Kabupaten Ngawi', 'NGW'),
('Kabupaten Ponorogo', 'PNG'),
('Kabupaten Pacitan', 'PCT'),
('Kabupaten Kediri', 'KKDR'),
('Kabupaten Nganjuk', 'NJK'),
('Kabupaten Blitar', 'KBLT'),
('Kabupaten Tulungagung', 'TLA'),
('Kabupaten Trenggalek', 'TGK'),
('Kabupaten Malang', 'KMLG'),
('Kabupaten Pasuruan', 'KPSR'),
('Kabupaten Probolinggo', 'KPBL'),
('Kabupaten Lumajang', 'LMJ'),
('Kabupaten Jember', 'JBR'),
('Kabupaten Bondowoso', 'BDW'),
('Kabupaten Situbondo', 'STB'),
('Kabupaten Banyuwangi', 'BWI'),
('Kabupaten Sampang', 'SPG'),
('Kabupaten Pamekasan', 'PMK'),
('Kabupaten Sumenep', 'SMP'),
('Kabupaten Bangkalan', 'BKL');

-- Insert Periode Tes
INSERT INTO `periode_tes` (`tahun`, `nama_periode`, `tanggal_mulai`, `tanggal_selesai`, `kuota_total`, `status`) VALUES
(2026, 'Periode Tes Tahun 2026', '2026-06-01', '2026-08-31', 1000, 'pendaftaran');

-- Insert Settings
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('app_name', 'SiHafiz Jatim', 'Nama Aplikasi'),
('app_address', 'Islamic Center Jl Dukuh Kupang, Surabaya', 'Alamat Instansi'),
('tahun_aktif', '2026', 'Tahun Anggaran Aktif');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- =====================================================
-- DEPLOYMENT COMPLETE!
-- Login with: admin / Admin123!
-- =====================================================
