<?php
require_once 'config/app.php';
require_once 'config/database.php';

$sql = "CREATE TABLE IF NOT EXISTS `mutasi_hafiz` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `hafiz_id` INT(11) NOT NULL,
    `asal_kabko_id` INT(11) NOT NULL,
    `tujuan_kabko_id` INT(11) NOT NULL,
    `alasan` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_by` INT(11) NOT NULL COMMENT 'User ID pemohon (Admin Kabko)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `approved_by` INT(11) DEFAULT NULL COMMENT 'User ID Admin Prov',
    `approved_at` DATETIME DEFAULT NULL,
    `catatan_approval` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_hafiz` (`hafiz_id`),
    KEY `idx_asal` (`asal_kabko_id`),
    KEY `idx_tujuan` (`tujuan_kabko_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_mutasi_hafiz` FOREIGN KEY (`hafiz_id`) REFERENCES `hafiz` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mutasi_asal` FOREIGN KEY (`asal_kabko_id`) REFERENCES `kabupaten_kota` (`id`),
    CONSTRAINT `fk_mutasi_tujuan` FOREIGN KEY (`tujuan_kabko_id`) REFERENCES `kabupaten_kota` (`id`),
    CONSTRAINT `fk_mutasi_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

try {
    Database::getConnection()->exec($sql);
    echo "Table 'mutasi_hafiz' created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
