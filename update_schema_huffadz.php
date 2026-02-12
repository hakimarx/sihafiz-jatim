<?php
require_once __DIR__ . '/config/database.php';

echo "Updating schema for Huffadz features...\n";

function addColumnIfNotExists($table, $column, $definition)
{
    try {
        $result = Database::queryOne("SHOW COLUMNS FROM $table LIKE '$column'");
        if (!$result) {
            Database::execute("ALTER TABLE $table ADD COLUMN $column $definition");
            echo "Added column '$column' to '$table'.\n";
        } else {
            echo "Column '$column' already exists in '$table'.\n";
        }
    } catch (Exception $e) {
        echo "Error checking/adding column '$column': " . $e->getMessage() . "\n";
    }
}

addColumnIfNotExists('hafiz', 'tahun_lulus', "YEAR DEFAULT NULL COMMENT 'Tahun Lulus Seleksi' AFTER tahun_tes");
addColumnIfNotExists('hafiz', 'is_meninggal', "TINYINT(1) DEFAULT 0 COMMENT '1=Meninggal' AFTER is_aktif");
addColumnIfNotExists('hafiz', 'tanggal_kematian', "DATE DEFAULT NULL COMMENT 'Tanggal Wafat' AFTER is_meninggal");
addColumnIfNotExists('hafiz', 'lokasi_seleksi', "VARCHAR(100) DEFAULT NULL COMMENT 'Lokasi Seleksi Manual' AFTER kabupaten_kota_id");

echo "Schema update complete.\n";
