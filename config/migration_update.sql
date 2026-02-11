-- Create table hafiz_mengajar if not exists
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

-- Add remember_token to users if not exists
-- Only attempt if column doesn't exist
DROP PROCEDURE IF EXISTS AddColumnIfNotExists;
DELIMITER //
CREATE PROCEDURE AddColumnIfNotExists(
    IN tableName VARCHAR(255), 
    IN colName VARCHAR(255), 
    IN colDef VARCHAR(255)
)
BEGIN
    DECLARE colCount INT;
    SELECT COUNT(*) INTO colCount 
    FROM information_schema.columns 
    WHERE table_schema = DATABASE() 
    AND table_name = tableName 
    AND column_name = colName;
    
    IF colCount = 0 THEN
        SET @ddl = CONCAT('ALTER TABLE ', tableName, ' ADD COLUMN ', colName, ' ', colDef);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL AddColumnIfNotExists('users', 'remember_token', 'VARCHAR(255) DEFAULT NULL AFTER last_login');
CALL AddColumnIfNotExists('hafiz', 'foto_profil', 'VARCHAR(255) DEFAULT NULL');
CALL AddColumnIfNotExists('hafiz', 'foto_ktp', 'VARCHAR(255) DEFAULT NULL');

DROP PROCEDURE IF EXISTS AddColumnIfNotExists;
