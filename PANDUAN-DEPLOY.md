# 🔄 Auto-Deploy dari GitHub ke hafizjatim.my.id

## Cara Kerja

```
Developer push ke GitHub
    ↓
GitHub kirim webhook POST ke server
    ↓
Server jalankan git pull otomatis
    ↓
Website terupdate otomatis! ✅
```

---

## 📋 LANGKAH SETUP

### Step 1: Setup Git di Server (cPanel)

Login ke cPanel → **Terminal** (atau SSH), lalu jalankan:

```bash
cd ~/public_html

# Clone repository (pertama kali)
git clone https://github.com/hakimarx/sihafiz-jatim.git .

# Atau jika sudah ada folder, init git:
git init
git remote add origin https://github.com/hakimarx/sihafiz-jatim.git
git fetch origin main
git reset --hard origin/main

# Set git credentials agar bisa pull tanpa password
git config credential.helper store
git pull origin main
# (Masukkan username & token GitHub saat diminta - hanya sekali)
```

> ⚠️ **PENTING**: Gunakan **Personal Access Token** GitHub, bukan password biasa.
> Buat di: https://github.com/settings/tokens → Generate new token (classic) → Centang `repo`

### Step 2: Konfigurasi GitHub Webhook

1. Buka repository di GitHub: `https://github.com/hakimarx/sihafiz-jatim`
2. Klik **Settings** → **Webhooks** → **Add webhook**
3. Isi form:

| Field | Nilai |
|-------|-------|
| **Payload URL** | `https://hafizjatim.my.id/webhook-deploy.php` |
| **Content type** | `application/json` |
| **Secret** | `sihafiz-deploy-2026-secret-key` |
| **Events** | ✅ Just the push event |
| **Active** | ✅ Centang |

4. Klik **Add webhook**

### Step 3: Test Webhook

1. Lakukan perubahan kecil dan push ke GitHub
2. Cek di GitHub → Settings → Webhooks → klik webhook → lihat **Recent Deliveries**
3. Response harus `200 OK` dengan status `SUCCESS`

### Step 4: (Opsional) Manual Deploy via Browser

Jika webhook tidak bekerja, bisa deploy manual:

```
https://hafizjatim.my.id/deploy.php?key=sihafiz-manual-deploy-2026
```

---

## 🔐 KEAMANAN

| Fitur | Deskripsi |
|-------|-----------|
| **Secret Token** | Webhook divalidasi dengan HMAC SHA-256 |
| **Branch Filter** | Hanya push ke `main` yang di-deploy |
| **Log** | Semua deploy tercatat di `deploy.log` |
| **Block Access** | `deploy.log` diblok dari akses publik via .htaccess |
| **Manual Key** | Deploy manual dilindungi secret key |

---

## 📁 FILE TERKAIT

| File | Fungsi |
|------|--------|
| `public/webhook-deploy.php` | Endpoint webhook GitHub (otomatis) |
| `public/deploy.php` | Deploy manual via browser |
| `deploy.log` | Log riwayat deploy |
| `.htaccess.root` | Security rules |

---

## 🔧 TROUBLESHOOTING

### Webhook tidak bekerja?
1. Cek apakah `shell_exec` diizinkan oleh hosting → hubungi hosting provider
2. Cek apakah `git` tersedia di server: `which git`
3. Cek di GitHub webhook → Recent Deliveries → lihat response body

### Git pull gagal?
1. Pastikan credential sudah tersimpan: `git config credential.helper store`
2. Pastikan tidak ada conflict: `git reset --hard origin/main`
3. Pastikan permission folder benar: `chmod -R 755 .`

### Alternative jika shell_exec diblok hosting:
Gunakan **cPanel Git Version Control**:
1. cPanel → Git™ Version Control → Create
2. Clone URL: `https://github.com/hakimarx/sihafiz-jatim.git`
3. Repository Path: `/home/hafizjat/public_html`
4. Klik Create → Deploy akan otomatis via cPanel

---

*Dokumen dibuat: 10 Feb 2026*
