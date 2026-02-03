<?php

/**
 * Kabupaten Kota Model
 * =====================
 * Model untuk master data wilayah
 */

class KabupatenKota
{
    /**
     * Get all kabupaten/kota
     */
    public static function getAll(): array
    {
        return Database::query(
            "SELECT * FROM kabupaten_kota ORDER BY nama ASC"
        );
    }

    /**
     * Find by ID
     */
    public static function findById(int $id): ?array
    {
        return Database::queryOne(
            "SELECT * FROM kabupaten_kota WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Find by kode
     */
    public static function findByKode(string $kode): ?array
    {
        return Database::queryOne(
            "SELECT * FROM kabupaten_kota WHERE kode = :kode",
            ['kode' => $kode]
        );
    }

    /**
     * Get for dropdown (key-value pairs)
     */
    public static function getForDropdown(): array
    {
        $result = [];
        $data = self::getAll();

        foreach ($data as $item) {
            $result[$item['id']] = $item['nama'];
        }

        return $result;
    }
}
