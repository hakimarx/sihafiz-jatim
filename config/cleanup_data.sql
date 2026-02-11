-- ============================================
-- SIHAFIZ JATIM - DATA CLEANUP SCRIPT
-- ============================================
-- Jalankan di phpMyAdmin SQL tab (cPanel)
-- Script ini memperbaiki data yang sudah ada
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- ============================================
-- 1. BERSIHKAN SPASI DI TABEL kabupaten_kota
-- ============================================
UPDATE `kabupaten_kota` SET `nama` = TRIM(`nama`);
UPDATE `kabupaten_kota` SET `kode` = TRIM(`kode`);

-- ============================================
-- 2. BERSIHKAN NIK DI TABEL hafiz
-- ============================================
-- Hapus spasi leading/trailing dari NIK
UPDATE `hafiz` SET `nik` = TRIM(`nik`) WHERE `nik` != TRIM(`nik`);

-- Hapus karakter X di awal NIK
UPDATE `hafiz` SET `nik` = SUBSTRING(`nik`, 2) 
WHERE `nik` LIKE 'X%' AND LENGTH(`nik`) = 17;

-- Hapus karakter X di akhir NIK
UPDATE `hafiz` SET `nik` = LEFT(`nik`, LENGTH(`nik`) - 1) 
WHERE `nik` LIKE '%X' AND LENGTH(`nik`) = 17;

-- Hapus karakter ? di akhir NIK
UPDATE `hafiz` SET `nik` = LEFT(`nik`, LENGTH(`nik`) - 1) 
WHERE `nik` LIKE '%?' AND LENGTH(`nik`) = 17;

-- ============================================
-- 3. BERSIHKAN USERNAME (NIK) DI TABEL users
-- ============================================
-- Sinkronkan username dengan NIK yang sudah dibersihkan
UPDATE `users` u
INNER JOIN `hafiz` h ON u.id = h.user_id
SET u.`username` = h.`nik`
WHERE u.`role` = 'hafiz'
AND u.`username` != h.`nik`;

-- ============================================
-- 4. PERBAIKI PEMETAAN kabupaten_kota_id
-- ============================================
-- Update hafiz yang kabupaten_kota_id-nya NULL atau mengarah ke ID 1 (default fallback)
-- berdasarkan data alamat yang ada

-- Bersihkan nama-nama di field hafiz juga
UPDATE `hafiz` SET `nama` = TRIM(`nama`) WHERE `nama` != TRIM(`nama`);
UPDATE `hafiz` SET `tempat_lahir` = TRIM(`tempat_lahir`) WHERE `tempat_lahir` != TRIM(`tempat_lahir`);
UPDATE `hafiz` SET `desa_kelurahan` = TRIM(`desa_kelurahan`) WHERE `desa_kelurahan` != TRIM(`desa_kelurahan`);
UPDATE `hafiz` SET `kecamatan` = TRIM(`kecamatan`) WHERE `kecamatan` != TRIM(`kecamatan`);
UPDATE `hafiz` SET `alamat` = TRIM(`alamat`) WHERE `alamat` != TRIM(`alamat`);

-- ============================================
-- 5. UPDATE kabupaten_kota_id DI users
-- ============================================
-- Pastikan semua user hafiz punya kabupaten_kota_id yang sama dengan data hafiz-nya
UPDATE `users` u
INNER JOIN `hafiz` h ON u.id = h.user_id
SET u.`kabupaten_kota_id` = h.`kabupaten_kota_id`
WHERE u.`role` = 'hafiz'
AND (u.`kabupaten_kota_id` IS NULL OR u.`kabupaten_kota_id` != h.`kabupaten_kota_id`);

-- ============================================
-- 6. VERIFIKASI HASIL
-- ============================================
-- Cek distribusi hafiz per kabupaten/kota
SELECT 
    kk.nama AS kabupaten_kota,
    COUNT(h.id) AS jumlah_hafiz
FROM hafiz h
LEFT JOIN kabupaten_kota kk ON h.kabupaten_kota_id = kk.id
WHERE h.is_aktif = 1
GROUP BY kk.nama
ORDER BY kk.nama;

-- Cek NIK yang masih bermasalah
SELECT 
    h.id, h.nik, h.nama, kk.nama AS kabupaten_kota,
    CASE 
        WHEN LENGTH(h.nik) != 16 THEN 'Bukan 16 digit'
        WHEN h.nik REGEXP '[^0-9]' THEN 'Mengandung non-digit'
        WHEN h.nik LIKE 'TEMP%' THEN 'NIK sementara'
        ELSE 'OK'
    END AS status_nik
FROM hafiz h
LEFT JOIN kabupaten_kota kk ON h.kabupaten_kota_id = kk.id
WHERE h.nik NOT REGEXP '^[0-9]{16}$'
ORDER BY h.nama;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
