<?php

/**
 * View Edit Profil Hafiz dengan OCR KTP Tingkat Lanjut
 */
?>
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Profil Saya</h4>
</div>

<form action="<?= APP_URL ?>/hafiz/profil/update" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <!-- Kolom Kiri: Foto & KTP -->
        <div class="col-lg-4 mb-4">
            <div class="card mb-4 text-center shadow-sm">
                <div class="card-header bg-white fw-bold">Foto Profil</div>
                <div class="card-body">
                    <div class="mb-3">
                        <?php if (!empty($hafiz['foto_profil'])): ?>
                            <img id="preview-profil" src="<?= APP_URL . htmlspecialchars($hafiz['foto_profil']) ?>" class="img-thumbnail rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <img id="preview-profil" src="https://via.placeholder.com/150" class="img-thumbnail rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control form-control-sm" name="foto_profil" accept="image/*" id="input-profil">
                    <small class="text-muted">Format: JPG/PNG. Auto-compress 400KB.</small>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span>Upload KTP (OCR Pintar)</span>
                    <span id="ocr-status" class="badge bg-secondary" style="display:none;">Menganalisa...</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <?php if (!empty($hafiz['foto_ktp'])): ?>
                            <img id="preview-ktp" src="<?= APP_URL . htmlspecialchars($hafiz['foto_ktp']) ?>" class="img-fluid rounded border mb-2">
                        <?php else: ?>
                            <div id="ktp-placeholder" class="bg-light border rounded py-4 text-muted small text-center">
                                <i class="bi bi-card-image d-block fs-1"></i>
                                Belum ada foto KTP
                            </div>
                            <img id="preview-ktp" src="" class="img-fluid rounded border mb-2" style="display:none;">
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control form-control-sm" name="foto_ktp" accept="image/*" id="input-ktp">
                    <div class="form-text small mt-2">
                        <i class="bi bi-magic me-1"></i> Sistem akan memindai NIK, Nama, TTL, Kelamin, dan Alamat dari KTP.
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Data Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3 fw-bold"><i class="bi bi-person-vcard me-2"></i>Informasi Kartu Identitas</h6>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">NIK</label>
                            <input type="text" class="form-control" name="nik" id="field_nik" value="<?= htmlspecialchars($hafiz['nik'] ?? '') ?>" placeholder="16 Digit NIK">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Nama Lengkap (Sesuai KTP)</label>
                            <input type="text" class="form-control" name="nama" id="field_nama" value="<?= htmlspecialchars($hafiz['nama'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Tempat Lahir</label>
                            <input type="text" class="form-control" name="tempat_lahir" id="field_tempat_lahir" value="<?= htmlspecialchars($hafiz['tempat_lahir'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" id="field_tanggal_lahir" value="<?= htmlspecialchars($hafiz['tanggal_lahir'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Jenis Kelamin</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="gender_l" value="L" <?= ($hafiz['jenis_kelamin'] ?? '') == 'L' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gender_l">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="gender_p" value="P" <?= ($hafiz['jenis_kelamin'] ?? '') == 'P' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gender_p">Perempuan</label>
                            </div>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-4 fw-bold"><i class="bi bi-geo-alt me-2"></i>Alamat Tinggal (Sesuai KTP)</h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alamat Lengkap (Jalan/Dusun)</label>
                        <textarea class="form-control" name="alamat" id="field_alamat" rows="2"><?= htmlspecialchars($hafiz['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Desa/Kelurahan</label>
                            <input type="text" class="form-control" name="desa_kelurahan" id="field_desa" value="<?= htmlspecialchars($hafiz['desa_kelurahan'] ?? '') ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Kecamatan</label>
                            <input type="text" class="form-control" name="kecamatan" id="field_kecamatan" value="<?= htmlspecialchars($hafiz['kecamatan'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">RT/RW</label>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 text-center" name="rt" id="field_rt" placeholder="RT" value="<?= htmlspecialchars($hafiz['rt'] ?? '') ?>">
                                <input type="text" class="form-control p-1 text-center" name="rw" id="field_rw" placeholder="RW" value="<?= htmlspecialchars($hafiz['rw'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-4 fw-bold"><i class="bi bi-telephone me-2"></i>Kontak & Perbankan</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">HP / WhatsApp</label>
                            <input type="text" class="form-control" name="telepon" value="<?= htmlspecialchars($hafiz['telepon'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($hafiz['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nama Bank</label>
                            <input type="text" class="form-control" name="nama_bank" value="<?= htmlspecialchars($hafiz['nama_bank'] ?? 'BANK JATIM') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nomor Rekening</label>
                            <input type="text" class="form-control" name="nomor_rekening" value="<?= htmlspecialchars($hafiz['nomor_rekening'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="<?= APP_URL ?>/hafiz/profil" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>

<script>
    function setupPreview(inputId, previewId, placeholderId = null) {
        document.getElementById(inputId).addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById(previewId);
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                    if (placeholderId) document.getElementById(placeholderId).style.display = 'none';
                };
                reader.readAsDataURL(file);
                if (inputId === 'input-ktp') runOCR(file);
            }
        });
    }

    setupPreview('input-profil', 'preview-profil');
    setupPreview('input-ktp', 'preview-ktp', 'ktp-placeholder');

    async function runOCR(file) {
        const status = document.getElementById('ocr-status');
        status.style.display = 'inline-block';
        status.className = 'badge bg-warning text-dark';
        status.textContent = 'Memindai KTP...';

        try {
            const worker = await Tesseract.createWorker('ind');
            const ret = await worker.recognize(file);
            const text = ret.data.text;
            console.log("Full OCR Result:", text);

            const lines = text.split('\n').map(l => l.trim().toUpperCase());
            const dataFound = {};

            // 1. NIK Detection
            const nikMatch = text.match(/\b\d{16}\b/);
            if (nikMatch) dataFound.nik = nikMatch[0];

            // 2. Parselines for fields
            lines.forEach((line, index) => {
                // Nama
                if (line.includes('NAMA') && !dataFound.nama) {
                    let val = line.split(/[:=]/).pop()?.trim().replace(/[^A-Z\s]/g, '');
                    if (val && val.length > 3) dataFound.nama = val;
                }

                // Tempat/Tgl Lahir
                if ((line.includes('LAHIR') || line.includes('TEMPAT')) && !dataFound.ttl) {
                    let val = line.split(/[:=]/).pop()?.trim();
                    if (val) {
                        let parts = val.split(',');
                        if (parts.length >= 2) {
                            dataFound.tempat = parts[0].trim();
                            let dateStr = parts[1].trim().match(/\d{2}-\d{2}-\d{4}/);
                            if (dateStr) {
                                let d = dateStr[0].split('-');
                                dataFound.tanggal = `${d[2]}-${d[1]}-${d[0]}`;
                            }
                        }
                    }
                }

                // Jenis Kelamin
                if (line.includes('KELAMIN')) {
                    if (line.includes('LAKI')) dataFound.gender = 'L';
                    else if (line.includes('PEREMPUAN')) dataFound.gender = 'P';
                }

                // Alamat (Biasanya baris setelah kata ALAMAT)
                if (line.includes('ALAMAT') && lines[index + 1]) {
                    dataFound.alamat = lines[index + 1].replace(/[:=]/, '').trim();
                }

                // RT/RW
                if (line.includes('RT/RW') || line.includes('RT / RW')) {
                    let rtrw = line.match(/(\d+)\s*[/]\s*(\d+)/);
                    if (rtrw) {
                        dataFound.rt = rtrw[1];
                        dataFound.rw = rtrw[2];
                    }
                }

                // Kecamatan & Desa
                if (line.includes('KELURAHAN') || line.includes('DESA')) {
                    dataFound.desa = line.split(/[:=]/).pop()?.trim();
                }
                if (line.includes('KECAMATAN')) {
                    dataFound.kecamatan = line.split(/[:=]/).pop()?.trim();
                }
            });

            // Map data to fields with confirmation
            const fieldsMap = {
                'field_nik': dataFound.nik,
                'field_nama': dataFound.nama,
                'field_tempat_lahir': dataFound.tempat,
                'field_tanggal_lahir': dataFound.tanggal,
                'field_alamat': dataFound.alamat,
                'field_desa': dataFound.desa,
                'field_kecamatan': dataFound.kecamatan,
                'field_rt': dataFound.rt,
                'field_rw': dataFound.rw
            };

            let count = 0;
            for (let id in fieldsMap) {
                if (fieldsMap[id]) {
                    let el = document.getElementById(id);
                    if (el && (!el.value || el.value === 'BANK JATIM')) { // special case bank
                        el.value = fieldsMap[id];
                        count++;
                    }
                }
            }

            if (dataFound.gender) {
                document.getElementById(dataFound.gender === 'L' ? 'gender_l' : 'gender_p').checked = true;
                count++;
            }

            status.className = 'badge bg-success';
            status.textContent = count > 0 ? `Berhasil mengisi ${count} data!` : 'Scan Selesai';
            setTimeout(() => {
                status.style.display = 'none';
            }, 5000);

            await worker.terminate();
        } catch (error) {
            console.error(error);
            status.className = 'badge bg-danger';
            status.textContent = 'Gagal OCR';
        }
    }
</script>