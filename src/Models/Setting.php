<?php

/**
 * Setting Model
 * ==============
 * Model untuk manajemen pengaturan aplikasi (key-value store)
 */

class Setting
{
    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $result = Database::queryOne(
            "SELECT value FROM settings WHERE `key` = :key",
            ['key' => $key]
        );

        return $result ? $result['value'] : $default;
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value, ?string $description = null): bool
    {
        $exists = self::exists($key);

        if ($exists) {
            return Database::execute(
                "UPDATE settings SET value = :value WHERE `key` = :key",
                ['key' => $key, 'value' => $value]
            );
        } else {
            return Database::execute(
                "INSERT INTO settings (`key`, value, description) VALUES (:key, :value, :description)",
                ['key' => $key, 'value' => $value, 'description' => $description]
            );
        }
    }

    /**
     * Check if setting key exists
     */
    public static function exists(string $key): bool
    {
        $result = Database::queryOne(
            "SELECT COUNT(*) as count FROM settings WHERE `key` = :key",
            ['key' => $key]
        );

        return $result['count'] > 0;
    }

    /**
     * Get all settings as associative array
     */
    public static function getAll(): array
    {
        $rows = Database::query("SELECT * FROM settings");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }
}
