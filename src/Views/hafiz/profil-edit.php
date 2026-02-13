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
            <!-- Upload KTP (Prioritas Utama) -->
            <div class="card mb-4 shadow-sm border-2 border-primary">
                <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-card-heading me-2"></i>Upload KTP <small class="text-warning fw-normal">(Wajib)</small></span>
                    <span id="ocr-status" class="badge bg-light text-dark" style="display:none;">Menganalisa...</span>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <?php if (!empty($hafiz['foto_ktp'])): ?>
                            <img id="preview-ktp" src="<?= APP_URL . htmlspecialchars($hafiz['foto_ktp']) ?>" class="img-fluid rounded border mb-2 shadow-sm">
                        <?php else: ?>
                            <div id="ktp-placeholder" class="bg-light border rounded py-5 text-muted small text-center">
                                <i class="bi bi-card-image d-block fs-1 mb-2 text-primary"></i>
                                <span class="fw-bold">Belum ada foto KTP</span><br>
                                <span class="text-muted fst-italic">Upload foto KTP yang jelas untuk auto-fill data</span>
                            </div>
                            <img id="preview-ktp" src="" class="img-fluid rounded border mb-2" style="display:none;">
                        <?php endif; ?>
                    </div>
                    <div class="d-grid gap-2">
                        <label for="input-ktp" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-upload me-2"></i>Pilih File KTP
                        </label>
                        <input type="file" class="d-none" name="foto_ktp" accept="image/*" id="input-ktp">
                    </div>
                    <div class="alert alert-info small mt-3 mb-0 py-2">
                        <i class="bi bi-magic me-1"></i> Sistem akan memindai <strong>NIK, Nama, TTL dan Alamat</strong> otomatis dari KTP Anda.
                    </div>
                </div>
            </div>

            <!-- Foto Profil -->
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Foto Profil</div>
                <div class="card-body text-center">
                    <div class="mb-3 position-relative d-inline-block">
                        <?php if (!empty($hafiz['foto_profil'])): ?>
                            <img id="preview-profil" src="<?= APP_URL . htmlspecialchars($hafiz['foto_profil']) ?>" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <img id="preview-profil" src="https://via.placeholder.com/150" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <label for="input-profil" class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow p-2" style="cursor: pointer;">
                            <i class="bi bi-camera-fill text-primary"></i>
                        </label>
                    </div>
                    <input type="file" class="d-none" name="foto_profil" accept="image/*" id="input-profil">
                    <div class="text-muted small">Klik ikon kamera untuk mengganti foto.</div>
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

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Kabupaten/Kota</label>
                        <select class="form-select" name="kabupaten_kota_id" required>
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            <?php foreach ($kabkoList as $id => $nama): ?>
                                <option value="<?= $id ?>" <?= ($hafiz['kabupaten_kota_id'] ?? '') == $id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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

                    <h6 class="border-bottom pb-2 mb-3 mt-4 fw-bold"><i class="bi bi-building me-2"></i>Riwayat Lokasi Mengajar</h6>
                    <div class="alert alert-info small py-2">
                        <i class="bi bi-info-circle me-1"></i> Tambahkan semua tempat Anda mengajar saat ini.
                    </div>

                    <div id="teaching-locations-container">
                        <?php
                        $list = $mengajarList ?? [];
                        // Fallback ke kolom lama jika kosong (opsional, tapi bagus untuk migrasi data lama)
                        if (empty($list) && !empty($hafiz['tempat_mengajar'])) {
                            $list[] = [
                                'tempat_mengajar' => $hafiz['tempat_mengajar'],
                                'tmt_mengajar' => $hafiz['tmt_mengajar']
                            ];
                        }

                        if (empty($list)): ?>
                            <div class="row mb-2 location-row">
                                <div class="col-md-7 mb-2">
                                    <input type="text" class="form-control form-control-sm" name="mengajar_tempat[]" placeholder="Nama Lembaga / Masjid / TPQ" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <input type="date" class="form-control form-control-sm" name="mengajar_tmt[]" required>
                                </div>
                                <div class="col-md-1 mb-2">
                                </div>
                            </div>
                            <?php else:
                            foreach ($list as $idx => $m): ?>
                                <div class="row mb-2 location-row" id="loc-row-<?= $idx ?>">
                                    <div class="col-md-7 mb-2">
                                        <input type="text" class="form-control form-control-sm" name="mengajar_tempat[]" value="<?= htmlspecialchars($m['tempat_mengajar'] ?? '') ?>" placeholder="Nama Lembaga / Masjid / TPQ" required>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <input type="date" class="form-control form-control-sm" name="mengajar_tmt[]" value="<?= htmlspecialchars($m['tmt_mengajar'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-1 mb-2">
                                        <?php if ($idx > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLocation('loc-row-<?= $idx ?>')"><i class="bi bi-trash"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                        <?php endforeach;
                        endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addLocation()"><i class="bi bi-plus-circle"></i> Tambah Lokasi</button>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="<?= APP_URL ?>/hafiz/profil" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-success px-4" onclick="return saveSignature()">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-5 fw-bold"><i class="bi bi-pencil me-2"></i>Tanda Tangan Digital</h6>
                    <div class="alert alert-light border small text-muted mb-2">
                        <i class="bi bi-info-circle me-1"></i> Silakan tanda tangan di dalam kotak. Gunakan Mouse atau Layar Sentuh.
                    </div>
                    <div class="signature-wrapper border rounded bg-white mb-2" style="max-width: 400px; height: 160px; position: relative; touch-action: none;">
                        <canvas id="signature-pad" class="signature-pad" style="width: 100%; height: 100%; cursor: crosshair;"></canvas>
                        <button type="button" class="btn btn-sm btn-link p-1 text-danger" style="position: absolute; top: 0; right: 0;" onclick="signaturePad.clear()">
                            <i class="bi bi-x-circle"></i> Bersihkan
                        </button>
                    </div>
                    <input type="hidden" name="tanda_tangan" id="tanda_tangan_input">
                    <?php if (!empty($hafiz['tanda_tangan'])): ?>
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-check-circle-fill text-success"></i> Tanda tangan sudah tersimpan.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@5.0.2/dist/signature_pad.umd.min.js"></script>
<script>
    // Signature Pad Initialization
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    function saveSignature() {
        if (!signaturePad.isEmpty()) {
            document.getElementById('tanda_tangan_input').value = signaturePad.toDataURL();
        }
        return true;
    }

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
                // Nama - Look for line after NIK or containing NAMA
                if (line.includes('NAMA') || (index > 0 && lines[index - 1].match(/\b\d{16}\b/))) {
                    let val = line.split(/[:=]/).pop()?.trim().replace(/[^A-Z\s]/g, '');
                    if (val && val.length > 3 && !dataFound.nama) dataFound.nama = val;
                }

                // Tempat/Tgl Lahir - Support comma, dot, or space
                if (line.includes('LAHIR') || line.includes('TEMPAT')) {
                    let val = line.split(/[:=]/).pop()?.trim();
                    if (val) {
                        let parts = val.split(/[,\.\s]/).filter(p => p.trim().length > 0);
                        if (parts.length >= 2) {
                            // First part is usually place
                            if (!dataFound.tempat) dataFound.tempat = parts[0].trim();

                            // Look for date in any part
                            let dateStrMatch = val.match(/(\d{2})[-/](\d{2})[-/](\d{4})/);
                            if (dateStrMatch && !dataFound.tanggal) {
                                dataFound.tanggal = `${dateStrMatch[3]}-${dateStrMatch[2]}-${dateStrMatch[1]}`;
                            }
                        }
                    }
                }

                // Jenis Kelamin
                if (line.includes('KELAMIN') || line.includes('JENIS')) {
                    if (line.includes('LAKI')) dataFound.gender = 'L';
                    else if (line.includes('PEREMPUAN') || line.includes('PEREM')) dataFound.gender = 'P';
                }

                // Alamat (Biasanya baris setelah kata ALAMAT atau mengandung ALAMAT)
                if (line.includes('ALAMAT')) {
                    let suffix = line.split(/[:=]/).pop()?.trim();
                    if (suffix && suffix.length > 5) {
                        dataFound.alamat = suffix;
                    } else if (lines[index + 1]) {
                        dataFound.alamat = lines[index + 1].replace(/[:=]/, '').trim();
                    }
                }

                // RT/RW
                if (line.includes('RT/RW') || line.includes('RT / RW') || line.match(/\d{3}\s*[/]\s*\d{3}/)) {
                    let rtrw = line.match(/(\d{3})\s*[/]\s*(\d{3})/) || line.match(/(\d+)\s*[/]\s*(\d+)/);
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

            // Map data to fields
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
                    // allow overwrite if empty or '-' or default value
                    if (el && (!el.value || el.value === '-' || el.value === 'BANK JATIM')) {
                        el.value = fieldsMap[id];
                        count++;
                    }
                }
            }

            if (dataFound.gender) {
                let radioL = document.getElementById('gender_l');
                let radioP = document.getElementById('gender_p');
                if (dataFound.gender === 'L') radioL.checked = true;
                else radioP.checked = true;
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



    function addLocation() {
        const container = document.getElementById('teaching-locations-container');
        const id = 'loc-row-' + Date.now();
        const html = `
            <div class="row mb-2 location-row" id="${id}">
                <div class="col-md-7 mb-2">
                    <input type="text" class="form-control form-control-sm" name="mengajar_tempat[]" placeholder="Nama Lembaga / Masjid / TPQ" required>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="date" class="form-control form-control-sm" name="mengajar_tmt[]" required>
                </div>
                <div class="col-md-1 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLocation('${id}')"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function removeLocation(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }
</script>