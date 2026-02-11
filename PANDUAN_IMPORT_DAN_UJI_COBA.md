# Panduan Import Data & Uji Coba Laporan Harian

Panduan ini menjelaskan cara mengimport data hasil pembersihan NIK ke website (hosting) menggunakan metode export/import SQL, serta skenario uji coba fitur Laporan Harian.

---

## BAGIAN 1: Persiapan & Import Data Huffadz

Metode ini menggunakan script khusus untuk mengubah data `Book1.csv` langsung menjadi file `.sql` yang siap diimport ke cPanel/Hosting.

### 1. Generate SQL dari CSV (Di Laptop)
Pastikan file `Book1.csv` (semicolon delimited) berada di folder utama project.

1. Buka terminal (CMD/PowerShell) di folder project.
2. Jalankan perintah berikut:
   ```bash
   C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe config\generate_sql_import.php
   ```
   *(Sesuaikan path PHP jika Anda tidak menggunakan Laragon default)*.
3. Tunggu hingga proses selesai. Script ini akan memproses ribuan data, melakukan hashing password (NIK), dan pemetaan Kabupaten/Kota secara otomatis.
4. Hasil akhirnya adalah file: `config/import_data_huffaz.sql`.

### 2. Import ke Hosting (cPanel)
1. Login ke **cPanel** akun hosting Anda.
2. Pilih menu **phpMyAdmin**.
3. Klik nama database Anda (biasanya `hafizjat_sihafiz`).
4. (Penting) Pastikan tabel `users` dan `hafiz` sudah ada (kosongkan data lama jika perlu).
5. Klik tab **Import** di bagian atas.
6. Klik **Choose File** dan pilih file `config/import_data_huffaz.sql` yang baru saja dibuat.
7. Klik tombol **Go / Import** (di bagian paling bawah).
8. Tunggu hingga selesai. Data Huffadz kini sudah masuk ke database website.

---

## BAGIAN 2: Fitur Baru - Persetujuan Massal (Batch Approval)
Untuk memudahkan Admin Kab/Ko mengaktifkan pendaftar yang membludak:
1. Login sebagai **Admin**.
2. Masuk ke menu **Persetujuan Daftar**.
3. Centang kotak **Pilih Semua** di header tabel, atau pilih pendaftar tertentu secara manual.
4. Klik tombol hijau **Setujui yang Dipilih** di bagian atas tabel.
5. Sistem akan mengaktifkan semua akun yang dipilih sekaligus.

---

## BAGIAN 2: Skenario Uji Coba (User Acceptance Test)

Ikuti langkah-langkah ini untuk memastikan fitur baru berjalan dengan lancar.

### A. Persiapan Akun
1. Pastikan data hafiz (NIK) sudah ada di database (dari proses import di atas).
2. Siapkan satu NIK contoh dari data tersebut. 
   - Contoh NIK: `3515082005920001` (sesuaikan dengan data real).
   - Catat juga Tanggal Lahir dari NIK tersebut (untuk verifikasi klaim).

### B. Alur Registrasi Hafiz (Klaim NIK)
1. **Registrasi Baru**:
   - Buka `https://hafizjatim.my.id/register`.
   - Masukkan NIK (Contoh: `3515082005920001`).
   - Klik **Cek NIK**.
   - Jika NIK ditemukan, sistem akan meminta verifikasi **Tanggal Lahir**.
   - Masukkan tanggal lahir yang sesuai.
   - Buat username/password baru untuk akun ini.
   - **Hasil**: Akun berhasil dibuat, status `pending` (menunggu persetujuan Admin Kab/Ko).

### C. Alur Admin (Verifikasi Pendaftaran)
1. **Login Admin Kab/Ko** (atau Admin Provinsi):
   - Login ke `https://hafizjatim.my.id/login`.
   - Gunakan akun admin wilayah yang sesuai dengan asal NIK tadi.
2. **Setujui Pendaftaran**:
   - Buka menu **Persetujuan Daftar** (sidebar kiri).
   - Anda akan melihat nama pendaftar baru tadi dengan badge "Pending".
   - Klik tombol **Setujui** (Checklist Hijau).
   - **Hasil**: Akun hafiz menjadi `aktif` dan bisa login.

### D. Alur Hafiz (Laporan Harian)
1. **Login Hafiz**:
   - Logout dari akun admin.
   - Login dengan akun Hafiz yang baru saja disetujui.
2. **Lengkapi Profil** (satu kali):
   - Hafiz akan diarahkan/diminta melengkapi foto & data diri.
   - Upload Foto Profil.
   - Klik Simpan.
3. **Buat Laporan Harian**:
   - Buka menu **Laporan Harian**.
   - Klik **Tambah Laporan**.
   - Isi form:
     - Tanggal: (Hari ini)
     - Kegiatan: Misal "Mengajar"
     - Deskripsi: "Mengajar Iqro jilid 4 santri TPQ Al-Hidayah..."
     - Lokasi: "TPQ Al-Hidayah"
     - Durasi: "60 menit"
     - Upload Foto Kegiatan: (Pilih file gambar).
   - Klik **Simpan**.
   - **Hasil**: Laporan muncul di list dengan status "Pending".

### E. Alur Admin (Verifikasi Laporan)
1. **Login Admin**:
   - Login kembali sebagai Admin Kab/Ko atau Provinsi.
2. **Cek Dashboard**:
   - Di Dashboard, lihat widget "Laporan Pending". Angkanya harus bertambah.
   - Lihat widget statistik "Sudah Laporan". Angkanya harus bertambah.
3. **Verifikasi Laporan**:
   - Buka menu **Laporan Harian Hafiz**.
   - Gunakan filter jika perlu (status: Pending).
   - Klik tombol **Setujui** pada laporan yang baru masuk.
   - **Hasil**: Status laporan berubah menjadi "Disetujui".
4. **Cek Dashboard Lagi**:
   - Status di dashboard berubah menjadi "Laporan Disetujui".

---

## Catatan
- Jika terjadi error saat upload gambar, pastikan folder `public/uploads` di hosting memiliki permission **755** atau **777**.
- Jika script import gagal (timeout), coba pecah file CSV menjadi beberapa bagian kecil.
