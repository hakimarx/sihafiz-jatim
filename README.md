# SiHafiz Jatim

**Sistem Informasi & Pelaporan Huffadz Jawa Timur**

Aplikasi PHP Native untuk manajemen data Huffadz, seleksi, dan pelaporan kegiatan harian. Dioptimasi untuk cPanel Shared Hosting dengan RAM 3GB.

---

## 📋 Fitur Utama

### Untuk Admin Provinsi
- ✅ Dashboard statistik per Kabupaten/Kota
- ✅ Manajemen semua data Hafiz
- ✅ Verifikasi laporan harian

### Untuk Admin Kab/Ko
- ✅ Input data Hafiz baru
- ✅ Edit/Hapus data Hafiz di wilayahnya
- ✅ Verifikasi laporan Hafiz wilayahnya

### Untuk Hafiz
- ✅ Login dengan NIK/No.HP (password default: NIK)
- ✅ Input laporan harian dengan foto
- ✅ Lihat riwayat laporan dan status verifikasi
- ✅ Lihat profil lengkap

---

## 🛠️ Teknologi

- **Backend**: PHP 8.1+ Native (tanpa framework)
- **Database**: MySQL 8.0 / MariaDB 10.5+
- **Frontend**: Bootstrap 5 (CDN)
- **Architecture**: MVC Pattern

---

## 📁 Struktur Direktori

```
sihafiz-jatim/
├── config/
│   ├── app.php          # Konfigurasi aplikasi
│   ├── database.php     # Database connection (PDO)
│   ├── security.php     # Security helpers
│   └── schema.sql       # Database schema
├── docker/
│   └── Dockerfile       # Docker configuration
├── public/              # Document Root
│   ├── index.php        # Single entry point
│   ├── .htaccess        # Apache rewrite rules
│   └── uploads/         # File uploads
├── src/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── AdminController.php
│   │   └── HafizController.php
│   ├── Core/
│   │   ├── Controller.php
│   │   └── Router.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Hafiz.php
│   │   ├── LaporanHarian.php
│   │   └── KabupatenKota.php
│   └── Views/
│       ├── layouts/
│       ├── admin/
│       ├── auth/
│       └── hafiz/
├── .env.example
├── docker-compose.yml
└── README.md
```

---

## 🚀 Instalasi

### Opsi 1: Docker (Recommended untuk Development)

```bash
# 1. Clone repository
git clone https://github.com/hakimarx/sihafiz-jatim.git
cd sihafiz-jatim

# 2. Copy environment file
cp .env.example .env

# 3. Start containers
docker-compose up -d

# 4. Akses aplikasi
# - App: http://localhost:8080
# - PhpMyAdmin: http://localhost:8081
```

### Opsi 2: cPanel Shared Hosting

1. **Upload Files**
   - Upload seluruh isi folder (kecuali `docker/` dan `docker-compose.yml`) ke hosting
   - Pastikan folder `public/` adalah document root

2. **Setup Database**
   - Buat database baru di cPanel (MySQL Databases)
   - Jalankan `config/schema.sql` via phpMyAdmin

3. **Konfigurasi Environment**
   - Rename `.env.example` menjadi `.env`
   - Edit kredensial database:
     ```
     DB_HOST=localhost
     DB_NAME=nama_database_anda
     DB_USER=username_database
     DB_PASS=password_database
     ```

4. **Set Permission**
   ```bash
   chmod 755 public/uploads
   chmod 755 public/uploads/foto-kegiatan
   ```

5. **Atur Document Root**
   - Di cPanel → Domains, set document root ke folder `public/`

---

## 🔐 Login Default

Setelah instalasi, gunakan kredensial berikut:

| Role | Username | Password |
|------|----------|----------|
| Admin Provinsi | `admin` | `password |

> ⚠️ **PENTING**: Segera ganti password default setelah login!

---

## 📊 Database

### Tabel Utama
- `users` - Data user login (admin, penguji, hafiz)
- `hafiz` - Data lengkap calon penerima insentif
- `seleksi` - Nilai tes seleksi
- `laporan_harian` - SPJ kegiatan hafiz
- `kabupaten_kota` - Master wilayah
- `periode_tes` - Periode tahun anggaran

### Indexes (Optimasi Query)
- `kab_ko_id` - Filter per kabupaten/kota
- `tahun_anggaran` - Filter per tahun
- `tanggal` - Filter laporan per tanggal
- `status_kelulusan`, `status_verifikasi` - Filter status

---

## 🔒 Keamanan

- ✅ Password di-hash dengan bcrypt (cost 12)
- ✅ CSRF protection di semua form
- ✅ Prepared statements (PDO) untuk semua query
- ✅ Input sanitization
- ✅ Session regeneration setelah login
- ✅ Role-based access control

---

## 📝 Catatan Development

### Menambah Route Baru

1. Tambahkan route di `public/index.php`:
   ```php
   $router->get('/path', [ControllerClass::class, 'method']);
   ```

2. Buat method di controller:
   ```php
   public function method(): void {
       $this->view('folder.view-name', ['data' => $data]);
   }
   ```

3. Buat view di `src/Views/folder/view-name.php`

### Environment Variables

| Variable | Deskripsi | Default |
|----------|-----------|---------|
| `DB_HOST` | MySQL host | localhost |
| `DB_PORT` | MySQL port | 3306 |
| `DB_NAME` | Database name | sihafiz_jatim |
| `DB_USER` | Database user | root |
| `DB_PASS` | Database password | - |
| `APP_NAME` | Nama aplikasi | SiHafiz Jatim |
| `APP_URL` | Base URL | http://localhost:8080 |
| `APP_ENV` | Environment | development |
| `TAHUN_ANGGARAN` | Tahun aktif | (tahun sekarang) |
| `KUOTA_TOTAL` | Kuota penerima | 1000 |

---

## 📄 License

Copyright © 2024 LPTQ Jawa Timur. All Rights Reserved.
