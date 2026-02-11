<div class="page-header">
    <h4 class="mb-0">
        <i class="bi bi-person-plus me-2"></i>
        <?= $isEdit ? 'Edit User' : 'Tambah User Baru' ?>
    </h4>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?= APP_URL ?>/admin/users<?= $isEdit ? '/' . $user['id'] . '/update' : '' ?>" method="POST">
                    <?= csrfField() ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Username (NIK/No.HP)</label>
                            <input type="text" class="form-control" name="username" required
                                value="<?= htmlspecialchars($user['username'] ?? '') ?>"
                                <?= $isEdit ? 'readonly' : '' ?>>
                            <small class="text-muted">Digunakan untuk login.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <?= $isEdit ? '(Kosongkan jika tidak diubah)' : '' ?></label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="password_input" <?= $isEdit ? '' : 'required' ?>>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordForm()">
                                    <i class="bi bi-eye" id="toggleIconForm"></i>
                                </button>
                            </div>
                        </div>

                        <script>
                            function togglePasswordForm() {
                                const pass = document.getElementById('password_input');
                                const icon = document.getElementById('toggleIconForm');
                                if (pass.type === 'password') {
                                    pass.type = 'text';
                                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                                } else {
                                    pass.type = 'password';
                                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                                }
                            }
                        </script>

                        <div class="col-md-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" required
                                value="<?= htmlspecialchars($user['nama'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required id="roleSelect">
                                <option value="<?= ROLE_ADMIN_PROV ?>" <?= ($user['role'] ?? '') === ROLE_ADMIN_PROV ? 'selected' : '' ?>>Admin Provinsi</option>
                                <option value="<?= ROLE_ADMIN_KABKO ?>" <?= ($user['role'] ?? '') === ROLE_ADMIN_KABKO ? 'selected' : '' ?>>Admin Kab/Ko</option>
                                <option value="<?= ROLE_PENGUJI ?>" <?= ($user['role'] ?? '') === ROLE_PENGUJI ? 'selected' : '' ?>>Penguji</option>
                                <option value="<?= ROLE_HAFIZ ?>" <?= ($user['role'] ?? '') === ROLE_HAFIZ ? 'selected' : '' ?>>Hafiz (Penerima)</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="kabkoContainer">
                            <label class="form-label">Kabupaten/Kota</label>
                            <select class="form-select" name="kabupaten_kota_id">
                                <option value="">-- Pilih Wilayah --</option>
                                <?php foreach ($kabkoList as $id => $nama): ?>
                                    <option value="<?= $id ?>" <?= ($user['kabupaten_kota_id'] ?? '') == $id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Wajib jika role Admin Kab/Ko atau Penguji.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telepon/WhatsApp</label>
                            <input type="text" class="form-control" name="telepon"
                                value="<?= htmlspecialchars($user['telepon'] ?? '') ?>">
                        </div>

                        <?php if ($isEdit): ?>
                            <div class="col-md-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                                        <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isActive">User Aktif</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Data User
                        </button>
                        <a href="<?= APP_URL ?>/admin/users" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="bi bi-info-circle me-2"></i>Petunjuk</h6>
                <ul class="small mb-0">
                    <li class="mb-2"><strong>Admin Provinsi</strong> memiliki akses penuh ke sistem.</li>
                    <li class="mb-2"><strong>Admin Kab/Ko</strong> memiliki akses terbatas pada wilayah masing-masing.</li>
                    <li class="mb-2"><strong>Penguji</strong> hanya dapat menginput nilai seleksi.</li>
                    <li><strong>Hafiz</strong> hanya dapat mengakses dashboard laporan harian.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('roleSelect').addEventListener('change', function() {
        const kabkoContainer = document.getElementById('kabkoContainer');
        if (this.value === '<?= ROLE_ADMIN_PROV ?>' || this.value === '<?= ROLE_HAFIZ ?>') {
            kabkoContainer.style.opacity = '0.5';
        } else {
            kabkoContainer.style.opacity = '1';
        }
    });
</script>