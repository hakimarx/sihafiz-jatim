# 🕌 SKENARIO REGISTRASI HAFIZ - SiHafiz Jatim
## Panduan Lengkap: Import Data, Registrasi, dan Manajemen Admin

---

## 📊 HASIL ANALISIS DATA `Book1.csv`

| Kategori | Jumlah |
|----------|--------|
| Total baris mentah | 14.349 |
| NIK Valid (16 digit) | 14.034 |
| NIK Invalid/Kosong | 315 |
| Setelah deduplikasi (unik) | **10.340** |
| Kabupaten/Kota | 39 |
| Rentang Tahun | 2015 - 2023 |

### Data yang DIBUANG (315 baris):
- **NIK kosong** (56): Tidak ada identitas → tidak bisa diverifikasi
- **NIK bukan 16 digit** (258): Format invalid → kemungkinan typo/SIM/paspor
- **Contoh NIK invalid**: `840315331075` (12 digit), `??????450659????`, `X3520031011740002` (prefix X), `3577034103830004X` (suffix X)

### ✅ Keputusan: **YA, hanya NIK valid yang diimport**
Alasan:
1. NIK adalah primary identifier untuk klaim akun
2. NIK invalid tidak bisa divalidasi silang
3. Data dengan NIK invalid bisa ditambahkan manual oleh admin setelah verifikasi KTP fisik

---

## 🔐 SKENARIO REGISTRASI: "KLAIM NIK" (Direkomendasikan)

### Mengapa Klaim NIK?
| Dibandingkan dengan | Klaim NIK (Dipilih) | Open Registration (Sebelumnya) |
|---------------------|---------------------|-------------------------------|
| Anti spam | ✅ Hanya NIK terdaftar | ❌ Siapapun bisa daftar |
| Mudah bagi manula | ✅ Cukup input NIK & tgl lahir | ❌ Harus isi banyak field |
| Akurat | ✅ Data dari database resmi | ❌ User input sendiri (typo) |
| Aman | ✅ 3 layer verifikasi | ❌ 1 layer saja |

### Alur Registrasi (3 Langkah Sederhana):

```
┌──────────────────────────────────────────┐
│ LANGKAH 1: INPUT NIK                     │
│ ─────────────────────                     │
│ Hafiz memasukkan NIK 16 digit            │
│ + Jawab captcha matematika               │
│                                           │
│ Sistem cek:                               │
│ ✓ NIK ada di database? (dari import CSV) │
│ ✓ Belum punya akun?                      │
│                                           │
│ Jika ditemukan → tampilkan nama samaran   │
│ "MU**** FA**** RO****"                   │
└──────────────────┬───────────────────────┘
                   │
                   ▼
┌──────────────────────────────────────────┐
│ LANGKAH 2: VERIFIKASI IDENTITAS          │
│ ─────────────────────────────            │
│ Hafiz melihat nama samarannya            │
│ "Apakah ini Anda?"                       │
│                                           │
│ Input:                                    │
│ • Tanggal Lahir (sesuai KTP)             │
│ • Nomor WhatsApp                          │
│ • Password baru (min 6 karakter)          │
│ • Konfirmasi password                     │
│                                           │
│ Sistem verifikasi tanggal lahir           │
└──────────────────┬───────────────────────┘
                   │
                   ▼
┌──────────────────────────────────────────┐
│ LANGKAH 3: MENUNGGU APPROVAL              │
│ ─────────────────────────                │
│ Akun dibuat dengan status PENDING         │
│                                           │
│ Hafiz menerima notifikasi:                │
│ "Akun Anda sedang ditinjau admin"         │
│                                           │
│ Admin Kabko:                              │
│ • Melihat daftar akun pending             │
│ • Approve → akun aktif, hafiz bisa login  │
│ • Reject → akun dihapus                   │
└──────────────────────────────────────────┘
```

### Keamanan 3 Layer:
1. **Layer 1 - NIK Whitelist**: Hanya NIK yang sudah ada di database (dari import CSV) yang bisa mendaftar. Orang asing TIDAK BISA mendaftar.
2. **Layer 2 - Verifikasi Tanggal Lahir**: Walaupun seseorang mengetahui NIK orang lain, mereka harus tahu tanggal lahir pemilik NIK.
3. **Layer 3 - Approval Admin Kabko**: Admin kabupaten/kota memverifikasi dan menyetujui setiap pendaftaran baru.

---

## 👨‍💼 ADMIN KABUPATEN/KOTA

### Default Login Admin Kabko:

| Wilayah | Username | Password |
|---------|----------|----------|
| Kota Surabaya | `admin.sby` | `admin123` |
| Kota Malang | `admin.mlg` | `admin123` |
| Kota Kediri | `admin.kdr` | `admin123` |
| Kota Blitar | `admin.blt` | `admin123` |
| Kota Mojokerto | `admin.mjk` | `admin123` |
| Kota Madiun | `admin.mdn` | `admin123` |
| Kota Pasuruan | `admin.psr` | `admin123` |
| Kota Probolinggo | `admin.pbl` | `admin123` |
| Kota Batu | `admin.btu` | `admin123` |
| Kab. Gresik | `admin.grs` | `admin123` |
| Kab. Sidoarjo | `admin.sda` | `admin123` |
| Kab. Mojokerto | `admin.kmjk` | `admin123` |
| Kab. Jombang | `admin.jbg` | `admin123` |
| Kab. Bojonegoro | `admin.bjn` | `admin123` |
| Kab. Tuban | `admin.tbn` | `admin123` |
| Kab. Lamongan | `admin.lmg` | `admin123` |
| Kab. Madiun | `admin.kmdn` | `admin123` |
| Kab. Magetan | `admin.mgt` | `admin123` |
| Kab. Ngawi | `admin.ngw` | `admin123` |
| Kab. Ponorogo | `admin.png` | `admin123` |
| Kab. Pacitan | `admin.pct` | `admin123` |
| Kab. Kediri | `admin.kkdr` | `admin123` |
| Kab. Nganjuk | `admin.njk` | `admin123` |
| Kab. Blitar | `admin.kblt` | `admin123` |
| Kab. Tulungagung | `admin.tla` | `admin123` |
| Kab. Trenggalek | `admin.tgk` | `admin123` |
| Kab. Malang | `admin.kmlg` | `admin123` |
| Kab. Pasuruan | `admin.kpsr` | `admin123` |
| Kab. Probolinggo | `admin.kpbl` | `admin123` |
| Kab. Lumajang | `admin.lmj` | `admin123` |
| Kab. Jember | `admin.jbr` | `admin123` |
| Kab. Bondowoso | `admin.bdw` | `admin123` |
| Kab. Situbondo | `admin.stb` | `admin123` |
| Kab. Banyuwangi | `admin.bwi` | `admin123` |
| Kab. Sampang | `admin.spg` | `admin123` |
| Kab. Pamekasan | `admin.pmk` | `admin123` |
| Kab. Sumenep | `admin.smp` | `admin123` |
| Kab. Bangkalan | `admin.bkl` | `admin123` |

> ⚠️ **PENTING**: Admin WAJIB ganti password setelah login pertama!

### Mengapa Admin Default? (Bukan Self-Register untuk Admin)
1. **Menghindari serbuan registrasi** - Admin hanya bisa dibuat oleh sistem
2. **Sosialisasi bertahap** - Bisa diedarkan via grup WA resmi per kabko
3. **Kontrol terpusat** - Admin Provinsi bisa monitor siapa yang sudah aktif
4. **Tidak perlu email institutsi** - Cukup username dan password standar

---

## 🚀 CARA MENJALANKAN

### Step 1: Bersihkan Data CSV
```powershell
# Jalankan dari folder project
powershell -ExecutionPolicy Bypass -File clean_and_export.ps1
```
Menghasilkan:
- `Book1_clean.csv` - 10.340 data bersih (NIK valid, unik)
- `Book1_rejected.csv` - 315 data yang ditolak (untuk referensi)

### Step 2: Buat Admin Kabko
```sql
-- Jalankan di phpMyAdmin atau MySQL console
source config/seed_admin_kabko.sql
```

### Step 3: Import Data Hafiz
```bash
# Via CLI
php import_hafiz_clean.php

# Atau via browser (login sebagai admin terlebih dahulu)
```

### Step 4: Sosialisasi ke Hafiz
Kirim pesan ke grup WA per kabupaten/kota:
```
Assalamu'alaikum,

Untuk hafiz yang sudah LULUS seleksi, silakan klaim akun 
di website SiHafiz Jatim:

🔗 https://hafizjatim.my.id/register

Cara klaim:
1. Masukkan NIK KTP (16 digit)
2. Verifikasi dengan tanggal lahir
3. Set nomor HP dan password
4. Tunggu approval admin

Username login: Nomor HP
Password: yang Anda buat saat klaim

Terima kasih.
Wassalamu'alaikum.
```

---

## 📁 FILE YANG DIBUAT/DIMODIFIKASI

| File | Fungsi |
|------|--------|
| `clean_and_export.ps1` | Script pembersihan data CSV |
| `Book1_clean.csv` | Data bersih siap import |
| `Book1_rejected.csv` | Data yang ditolak |
| `import_hafiz_clean.php` | Script import ke database |
| `config/seed_admin_kabko.sql` | SQL buat admin default per kabko |
| `src/Controllers/RegistrationController.php` | Controller registrasi (Klaim NIK) |
| `src/Views/auth/register.php` | Form Step 1 (input NIK) |
| `src/Views/auth/register_verify.php` | Form Step 2 (verifikasi + set password) |
| `public/index.php` | Update routing |

---

## 🔄 ALUR ADMIN APPROVE

```
Admin Kabko Login
    │
    ├── Dashboard → Melihat "X pendaftaran baru menunggu konfirmasi"
    │
    ├── Menu Hafiz → Filter status "pending"
    │
    └── Per hafiz:
        ├── ✅ Approve → akun aktif (is_active = 1)
        └── ❌ Reject → akun dihapus
```

---

*Dokumen ini dibuat otomatis pada 10 Feb 2026*
