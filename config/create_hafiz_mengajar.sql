-- Add hafiz_mengajar table
CREATE TABLE IF NOT EXISTS `hafiz_mengajar` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `hafiz_id` INT(11) NOT NULL,
    `tempat_mengajar` VARCHAR(255) NOT NULL,
    `tmt_mengajar` DATE NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_hafiz` (`hafiz_id`),
    CONSTRAINT `fk_mengajar_hafiz` FOREIGN KEY (`hafiz_id`) 
        REFERENCES `hafiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
