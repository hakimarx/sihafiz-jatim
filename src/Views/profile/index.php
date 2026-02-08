<?php
// Profile View
// d:\Seleksi Huffadz aplikasi data hafidz 2023\sihafiz-jatim\src\Views\profile\index.php
?>
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profil Saya</h4>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <?php if (!empty($user['foto_profil'])): ?>
                        <img src="<?= APP_URL . $user['foto_profil'] ?>" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                            <i class="bi bi-person-fill text-secondary" style="font-size: 5rem;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h5><?= htmlspecialchars($user['nama'] ?? $user['username']) ?></h5>
                <p class="text-muted mb-1"><?= ucfirst(str_replace('_', ' ', $user['role'])) ?></p>
                <?php if ($user['kabupaten_kota_nama']): ?>
                    <span class="badge bg-secondary"><?= htmlspecialchars($user['kabupaten_kota_nama']) ?></span>
                <?php endif; ?>

                <hr>

                <div class="text-start">
                    <p class="mb-1"><i class="bi bi-envelope me-2"></i><?= htmlspecialchars($user['email'] ?? '-') ?></p>
                    <p class="mb-1"><i class="bi bi-telephone me-2"></i><?= htmlspecialchars($user['telepon'] ?? '-') ?></p>
                    <p class="mb-0"><i class="bi bi-calendar me-2"></i>Terdaftar: <?= date('d M Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Edit Profil</h5>
            </div>
            <div class="card-body">
                <form action="<?= APP_URL ?>/profile/update" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled readonly>
                        <div class="form-text">Username tidak dapat diubah.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telepon" class="form-label">No. Telepon / WA</label>
                            <input type="tel" class="form-control" id="telepon" name="telepon" value="<?= htmlspecialchars($user['telepon'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="foto_profil" class="form-label">Foto Profil</label>
                        <input type="file" class="form-control" id="foto_profil" name="foto_profil" accept="image/*">
                        <div class="form-text">Format: JPG, PNG, GIF. Maks 2MB.</div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Ganti Password (Opsional)</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Biarkan kosong jika tidak diganti">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control border-start-0" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>