<?php

class MutasiController extends Controller
{
    public function index()
    {
        $role = getCurrentUserRole();
        $userId = getCurrentUserId();

        if ($role === ROLE_ADMIN_PROV) {
            $mutasi = Database::query("
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
                ORDER BY m.created_at DESC
            ");
        } else {
            // Admin Kabko - show those FROM or TO this kabko
            $user = User::findById($userId);
            $kabkoId = $user['kabupaten_kota_id'];

            $mutasi = Database::query("
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
                WHERE m.asal_kabko_id = :kid OR m.tujuan_kabko_id = :kid
                ORDER BY m.created_at DESC
            ", ['kid' => $kabkoId]);
        }

        $this->view('admin/mutasi/index', ['mutasi' => $mutasi]);
    }

    public function create()
    {
        // Only Admin Kabko can initiate? Or Prov too? usually Kabko Asal.
        $role = getCurrentUserRole();
        if ($role !== ROLE_ADMIN_KABKO) {
            setFlash('error', 'Hanya Admin Kabupaten/Kota yang dapat mengajukan mutasi.');
            $this->redirect(APP_URL . '/admin/mutasi');
            return;
        }

        $user = User::findById(getCurrentUserId());
        $kabkoId = $user['kabupaten_kota_id'];

        // Get Hafiz in this Kabko
        $hafizList = Database::query("SELECT id, nama, nik FROM hafiz WHERE kabupaten_kota_id = :kid AND is_aktif = 1 ORDER BY nama", ['kid' => $kabkoId]);

        // Get all Kabko for destination
        $kabkoList = Database::query("SELECT id, nama FROM kabupaten_kota WHERE id != :kid ORDER BY nama", ['kid' => $kabkoId]);

        $this->view('admin/mutasi/create', [
            'hafizList' => $hafizList,
            'kabkoList' => $kabkoList,
            'currentKabko' => $kabkoId
        ]);
    }

    public function store()
    {
        $role = getCurrentUserRole();
        if ($role !== ROLE_ADMIN_KABKO) {
            $this->redirect(APP_URL . '/admin/mutasi');
            return;
        }

        $user = User::findById(getCurrentUserId());
        $asalKabkoId = $user['kabupaten_kota_id'];

        $hafizId = $_POST['hafiz_id'] ?? '';
        $tujuanKabkoId = $_POST['tujuan_kabko_id'] ?? '';
        $alasan = $_POST['alasan'] ?? '';

        if (empty($hafizId) || empty($tujuanKabkoId) || empty($alasan)) {
            setFlash('error', 'Semua field harus diisi.');
            $this->redirect(APP_URL . '/admin/mutasi/create');
            return;
        }

        // Verify Hafiz belongs to Asal
        $hafiz = Database::queryOne("SELECT id FROM hafiz WHERE id = :hid AND kabupaten_kota_id = :kid", ['hid' => $hafizId, 'kid' => $asalKabkoId]);
        if (!$hafiz) {
            setFlash('error', 'Hafiz tidak ditemukan di wilayah Anda.');
            $this->redirect(APP_URL . '/admin/mutasi/create');
            return;
        }

        $sql = "INSERT INTO mutasi_hafiz (hafiz_id, asal_kabko_id, tujuan_kabko_id, alasan, created_by, status, created_at) 
                VALUES (:hid, :asal, :tujuan, :alasan, :uid, 'pending', NOW())";

        Database::execute($sql, [
            'hid' => $hafizId,
            'asal' => $asalKabkoId,
            'tujuan' => $tujuanKabkoId,
            'alasan' => $alasan,
            'uid' => getCurrentUserId()
        ]);

        setFlash('success', 'Pengajuan mutasi berhasil dibuat.');
        $this->redirect(APP_URL . '/admin/mutasi');
    }

    public function approve($id)
    {
        // Only Admin Prov
        if (getCurrentUserRole() !== ROLE_ADMIN_PROV) {
            setFlash('error', 'Akses ditolak.');
            $this->redirect(APP_URL . '/admin/mutasi');
            return;
        }

        // Logic handled in Model presumably, or here inside transaction
        // Let's do it here for simplicity/control as I didn't verify Mutasi model import fully

        $mutasi = Database::queryOne("SELECT * FROM mutasi_hafiz WHERE id = :id", ['id' => $id]);
        if (!$mutasi || $mutasi['status'] !== 'pending') {
            setFlash('error', 'Data tidak valid.');
            $this->redirect(APP_URL . '/admin/mutasi');
            return;
        }

        try {
            Database::beginTransaction();

            // 1. Update Mutasi
            Database::execute("
                UPDATE mutasi_hafiz 
                SET status = 'approved', approved_by = :uid, approved_at = NOW() 
                WHERE id = :id
            ", ['uid' => getCurrentUserId(), 'id' => $id]);

            // 2. Update Hafiz Kabko
            Database::execute("
                UPDATE hafiz SET kabupaten_kota_id = :kid WHERE id = :hid
            ", ['kid' => $mutasi['tujuan_kabko_id'], 'hid' => $mutasi['hafiz_id']]);

            // 3. Update User Kabko if linked
            // Find user_id of hafiz first
            $h = Database::queryOne("SELECT user_id FROM hafiz WHERE id = :id", ['id' => $mutasi['hafiz_id']]);
            if ($h && !empty($h['user_id'])) {
                Database::execute("
                    UPDATE users SET kabupaten_kota_id = :kid WHERE id = :uid
                ", ['kid' => $mutasi['tujuan_kabko_id'], 'uid' => $h['user_id']]);
            }

            Database::commit();
            setFlash('success', 'Mutasi berhasil disetujui.');
        } catch (Exception $e) {
            Database::rollback();
            setFlash('error', 'Gagal memproses: ' . $e->getMessage());
        }

        $this->redirect(APP_URL . '/admin/mutasi');
    }

    public function reject($id)
    {
        if (getCurrentUserRole() !== ROLE_ADMIN_PROV) {
            setFlash('error', 'Akses ditolak.');
            $this->redirect(APP_URL . '/admin/mutasi');
            return;
        }

        Database::execute("
            UPDATE mutasi_hafiz 
            SET status = 'rejected', approved_by = :uid, approved_at = NOW() 
            WHERE id = :id
        ", ['uid' => getCurrentUserId(), 'id' => $id]);

        setFlash('success', 'Mutasi ditolak.');
        $this->redirect(APP_URL . '/admin/mutasi');
    }
}
