<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #198754 0%, #0d6efd 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-book-half text-success fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-success">SiHafiz Jatim</h4>
                            <p class="text-muted small">Sistem Informasi & Pelaporan Huffadz</p>
                        </div>

                        <!-- Flash Message -->
                        <?php $flash = getFlash(); ?>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> py-2">
                                <small><?= $flash['message'] ?></small>
                            </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form action="<?= APP_URL ?>/login" method="POST">
                            <?= csrfField() ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="username" name="username"
                                        placeholder="NIK / No. HP" required autofocus>
                                </div>
                                <div class="form-text">Masukkan NIK atau No. HP yang terdaftar</div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password"
                                        placeholder="••••••••" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center text-muted small">
                            <p class="mb-0">&copy; <?= date('Y') ?> LPTQ Jawa Timur</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>