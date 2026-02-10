-- ============================================
-- INSERT DEFAULT ADMIN KABUPATEN/KOTA
-- ============================================
-- Password default: admin + kode kabupaten (lowercase)
-- Contoh: Kota Surabaya -> username: admin.sby, password: adminsby
-- 
-- PENTING: Admin harus GANTI PASSWORD setelah login pertama!
-- ============================================

-- Password hash for 'admin123' (temporary - akan di-generate unique per kabko)
-- Menggunakan hash yang sama untuk semua, admin WAJIB ganti password

-- Kota Surabaya
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.sby', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Surabaya', 1 
FROM kabupaten_kota WHERE kode = 'SBY';

-- Kota Malang
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.mlg', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Malang', 1 
FROM kabupaten_kota WHERE kode = 'MLG';

-- Kota Kediri
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.kdr', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Kediri', 1 
FROM kabupaten_kota WHERE kode = 'KDR';

-- Kota Blitar
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.blt', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Blitar', 1 
FROM kabupaten_kota WHERE kode = 'BLT';

-- Kota Mojokerto
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.mjk', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Mojokerto', 1 
FROM kabupaten_kota WHERE kode = 'MJK';

-- Kota Madiun
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.mdn', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Madiun', 1 
FROM kabupaten_kota WHERE kode = 'MDN';

-- Kota Pasuruan
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.psr', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Pasuruan', 1 
FROM kabupaten_kota WHERE kode = 'PSR';

-- Kota Probolinggo
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.pbl', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Probolinggo', 1 
FROM kabupaten_kota WHERE kode = 'PBL';

-- Kota Batu
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.btu', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kota Batu', 1 
FROM kabupaten_kota WHERE kode = 'BTU';

-- Kabupaten Gresik
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.grs', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Gresik', 1 
FROM kabupaten_kota WHERE kode = 'GRS';

-- Kabupaten Sidoarjo
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.sda', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Sidoarjo', 1 
FROM kabupaten_kota WHERE kode = 'SDA';

-- Kabupaten Mojokerto
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.kmjk', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Mojokerto', 1 
FROM kabupaten_kota WHERE kode = 'KMJK';

-- Kabupaten Jombang
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.jbg', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Jombang', 1 
FROM kabupaten_kota WHERE kode = 'JBG';

-- Kabupaten Bojonegoro
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.bjn', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Bojonegoro', 1 
FROM kabupaten_kota WHERE kode = 'BJN';

-- Kabupaten Tuban
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.tbn', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Tuban', 1 
FROM kabupaten_kota WHERE kode = 'TBN';

-- Kabupaten Lamongan
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.lmg', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Lamongan', 1 
FROM kabupaten_kota WHERE kode = 'LMG';

-- Kabupaten Madiun
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.kmdn', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Madiun', 1 
FROM kabupaten_kota WHERE kode = 'KMDN';

-- Kabupaten Magetan
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.mgt', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Magetan', 1 
FROM kabupaten_kota WHERE kode = 'MGT';

-- Kabupaten Ngawi
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.ngw', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Ngawi', 1 
FROM kabupaten_kota WHERE kode = 'NGW';

-- Kabupaten Ponorogo
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.png', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Ponorogo', 1 
FROM kabupaten_kota WHERE kode = 'PNG';

-- Kabupaten Pacitan
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.pct', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Pacitan', 1 
FROM kabupaten_kota WHERE kode = 'PCT';

-- Kabupaten Kediri
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.kkdr', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Kediri', 1 
FROM kabupaten_kota WHERE kode = 'KKDR';

-- Kabupaten Nganjuk
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.njk', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Nganjuk', 1 
FROM kabupaten_kota WHERE kode = 'NJK';

-- Kabupaten Blitar
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.kblt', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Blitar', 1 
FROM kabupaten_kota WHERE kode = 'KBLT';

-- Kabupaten Tulungagung
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.tla', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Tulungagung', 1 
FROM kabupaten_kota WHERE kode = 'TLA';

-- Kabupaten Trenggalek
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.tgk', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Trenggalek', 1 
FROM kabupaten_kota WHERE kode = 'TGK';

-- Kabupaten Malang
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.kmlg', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Malang', 1 
FROM kabupaten_kota WHERE kode = 'KMLG';

-- Kabupaten Pasuruan
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.kpsr', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Pasuruan', 1 
FROM kabupaten_kota WHERE kode = 'KPSR';

-- Kabupaten Probolinggo
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.kpbl', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Probolinggo', 1 
FROM kabupaten_kota WHERE kode = 'KPBL';

-- Kabupaten Lumajang
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.lmj', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Lumajang', 1 
FROM kabupaten_kota WHERE kode = 'LMJ';

-- Kabupaten Jember
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.jbr', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Jember', 1 
FROM kabupaten_kota WHERE kode = 'JBR';

-- Kabupaten Bondowoso
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.bdw', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Bondowoso', 1 
FROM kabupaten_kota WHERE kode = 'BDW';

-- Kabupaten Situbondo
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.stb', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Situbondo', 1 
FROM kabupaten_kota WHERE kode = 'STB';

-- Kabupaten Banyuwangi
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.bwi', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Banyuwangi', 1 
FROM kabupaten_kota WHERE kode = 'BWI';

-- Kabupaten Sampang
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.spg', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Sampang', 1 
FROM kabupaten_kota WHERE kode = 'SPG';

-- Kabupaten Pamekasan
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.pmk', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Pamekasan', 1 
FROM kabupaten_kota WHERE kode = 'PMK';

-- Kabupaten Sumenep
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.smp', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Sumenep', 1 
FROM kabupaten_kota WHERE kode = 'SMP';

-- Kabupaten Bangkalan
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `kabupaten_kota_id`, `nama`, `is_active`) 
SELECT 'admin.bkl', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4TnE5h1nWkD9.XW2', 'admin_kabko', id, 'Admin Kabupaten Bangkalan', 1 
FROM kabupaten_kota WHERE kode = 'BKL';
