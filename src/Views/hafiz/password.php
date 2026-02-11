<?php

/**
 * View Ubah Password Hafiz
 */
?>
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-key me-2"></i>Ubah Password</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="<?= APP_URL ?>/hafiz/password/update" method="POST">
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Saat Ini</label>
                        <input type="password" class="form-control" name="old_password" required placeholder="Masukkan password sekarang">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="new_password" required placeholder="Minimal 6 karakter" id="new_pw">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePw('new_pw', 'eye_new')">
                                <i class="bi bi-eye" id="eye_new"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="confirm_password" required placeholder="Ulangi password baru" id="confirm_pw">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePw('confirm_pw', 'eye_conf')">
                                <i class="bi bi-eye" id="eye_conf"></i>
                            </button>
                        </div>
                        <div id="pw_error" class="text-danger small mt-1" style="display:none;">Password tidak cocok!</div>
                    </div>

                    <script>
                        function togglePw(id, iconId) {
                            const pass = document.getElementById(id);
                            const icon = document.getElementById(iconId);
                            if (pass.type === 'password') {
                                pass.type = 'text';
                                icon.classList.replace('bi-eye', 'bi-eye-slash');
                            } else {
                                pass.type = 'password';
                                icon.classList.replace('bi-eye-slash', 'bi-eye');
                            }
                        }
                    </script>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="<?= APP_URL ?>/hafiz/profil" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary" id="btn_submit">
                            <i class="bi bi-shield-lock me-2"></i>Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="bi bi-shield-check me-2"></i>Keamanan Akun</h6>
                <ul class="small text-muted mt-3">
                    <li>Gunakan password yang unik dan tidak mudah menebak.</li>
                    <li>Jangan beritahukan password Anda kepada siapapun.</li>
                    <li>Password minimal terdiri dari 6 karakter.</li>
                    <li>Setelah mengubah password, Anda mungkin perlu melakukan login ulang di perangkat lain.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    const newPw = document.getElementById('new_pw');
    const confirmPw = document.getElementById('confirm_pw');
    const errorMsg = document.getElementById('pw_error');
    const btnSubmit = document.getElementById('btn_submit');

    function validate() {
        if (confirmPw.value && newPw.value !== confirmPw.value) {
            errorMsg.style.display = 'block';
            btnSubmit.disabled = true;
        } else {
            errorMsg.style.display = 'none';
            btnSubmit.disabled = false;
        }
    }

    newPw.addEventListener('keyup', validate);
    confirmPw.addEventListener('keyup', validate);
</script>