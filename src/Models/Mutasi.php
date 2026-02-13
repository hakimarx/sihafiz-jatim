<?php

class Mutasi
{
    public static function all()
    {
        return Database::query("
            SELECT m.*, 
                   h.nama as nama_hafiz, h.nik,
                   k_asal.nama as asal_kabko,
                   k_tujuan.nama as tujuan_kabko,
                   u.nama as pemohon,
                   u_app.nama as approver
            FROM mutasi_hafiz m
            JOIN hafiz h ON m.hafiz_id = h.id
            JOIN kabupaten_kota k_asal ON m.asal_kabko_id = k_asal.id
            JOIN kabupaten_kota k_tujuan ON m.tujuan_kabko_id = k_tujuan.id
            JOIN users u ON m.created_by = u.id
            LEFT JOIN users u_app ON m.approved_by = u_app.id
            ORDER BY m.created_at DESC
        ");
    }

    public static function find($id)
    {
        return Database::queryOne("
            SELECT m.*, 
                   h.nama as nama_hafiz, h.nik,
                   k_asal.nama as asal_kabko,
                   k_tujuan.nama as tujuan_kabko
            FROM mutasi_hafiz m
            JOIN hafiz h ON m.hafiz_id = h.id
            JOIN kabupaten_kota k_asal ON m.asal_kabko_id = k_asal.id
            JOIN kabupaten_kota k_tujuan ON m.tujuan_kabko_id = k_tujuan.id
            WHERE m.id = :id
        ", ['id' => $id]);
    }

    public static function getByKabko($kabkoId)
    {
        // Show if origin OR destination is this Kabko
        return Database::query("
            SELECT m.*, 
                   h.nama as nama_hafiz, h.nik,
                   k_asal.nama as asal_kabko,
                   k_tujuan.nama as tujuan_kabko,
                   u.nama as pemohon
            FROM mutasi_hafiz m
            JOIN hafiz h ON m.hafiz_id = h.id
            JOIN kabupaten_kota k_asal ON m.asal_kabko_id = k_asal.id
            JOIN kabupaten_kota k_tujuan ON m.tujuan_kabko_id = k_tujuan.id
            JOIN users u ON m.created_by = u.id
            WHERE m.asal_kabko_id = :kid1 OR m.tujuan_kabko_id = :kid2
            ORDER BY m.created_at DESC
        ", ['kid1' => $kabkoId, 'kid2' => $kabkoId]);
    }

    public static function create($data)
    {
        $sql = "INSERT INTO mutasi_hafiz (hafiz_id, asal_kabko_id, tujuan_kabko_id, alasan, created_by, status, created_at) 
                VALUES (:hafiz_id, :asal_kabko_id, :tujuan_kabko_id, :alasan, :created_by, 'pending', NOW())";
        Database::execute($sql, $data);
        return Database::lastInsertId();
    }

    public static function approve($id, $adminId, $catatan)
    {
        $mutasi = self::find($id);
        if (!$mutasi || $mutasi['status'] !== 'pending') return false;

        Database::beginTransaction();
        try {
            // Update Mutasi Status
            Database::execute("
                UPDATE mutasi_hafiz 
                SET status = 'approved', approved_by = :uid, approved_at = NOW(), catatan_approval = :cat
                WHERE id = :id
            ", ['uid' => $adminId, 'cat' => $catatan, 'id' => $id]);

            // Update Hafiz Kabko
            Database::execute("
                UPDATE hafiz SET kabupaten_kota_id = :tujuan WHERE id = :hid
            ", ['tujuan' => $mutasi['tujuan_kabko_id'], 'hid' => $mutasi['hafiz_id']]);

            // Also update User kabko if exists?
            // User table has `kabupaten_kota_id`. Usually linked via `user_id` in hafiz table.
            $hafiz = Database::queryOne("SELECT user_id FROM hafiz WHERE id = :id", ['id' => $mutasi['hafiz_id']]);
            if ($hafiz && $hafiz['user_id']) {
                Database::execute("
                    UPDATE users SET kabupaten_kota_id = :tujuan WHERE id = :uid
                ", ['tujuan' => $mutasi['tujuan_kabko_id'], 'uid' => $hafiz['user_id']]);
            }

            Database::commit();
            return true;
        } catch (Exception $e) {
            Database::rollback();
            return false;
        }
    }

    public static function reject($id, $adminId, $catatan)
    {
        return Database::execute("
            UPDATE mutasi_hafiz 
            SET status = 'rejected', approved_by = :uid, approved_at = NOW(), catatan_approval = :cat
            WHERE id = :id
        ", ['uid' => $adminId, 'cat' => $catatan, 'id' => $id]);
    }
}
