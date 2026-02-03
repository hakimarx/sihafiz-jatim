# Panduan Instalasi & Penggunaan SiHafiz Jatim
# ===============================================

## ✅ STATUS SAAT INI (LARAGON)
Aplikasi sudah terinstall dan berjalan di Laragon local server Anda.

**Detail Akses:**
- **URL Aplikasi**: http://localhost/sihafiz-jatim/public/
- **Database**: `sihafiz_jatim` (di MySQL Laragon)
- **PhpMyAdmin**: http://localhost/phpmyadmin

**Login Administrator:**
- **Username**: `admin`
- **Password**: `admin123`

---

## 📂 Lokasi File
- **Source Code**: `C:\laragon\www\sihafiz-jatim`
- **Konfigurasi**: `C:\laragon\www\sihafiz-jatim\.env`
- **Uploads**: `C:\laragon\www\sihafiz-jatim\public\uploads`

---

## 🛠️ Cara Menjalankan (Jika Restart Laptop)
1. Buka aplikasi **Laragon**.
2. Klik tombol **Start All**.
3. Buka browser dan akses URL di atas.

---

## 🐳 Panduan Docker (Opsional)
Jika Anda ingin menggunakan Docker di masa depan:

1. Pastikan Docker Desktop sudah berjalan.
2. Buka terminal di folder project (`D:\Seleksi Huffadz aplikasi data hafidz 2023\sihafiz-jatim`).
3. Jalankan: `docker-compose up -d --build`
4. Akses via: http://localhost:8080

**Catatan:** Karena Anda sudah menggunakan Laragon, Docker tidak wajib digunakan kecuali untuk simulasi environment server production sesungguhnya.

**Troubleshooting Docker:** Jika muncul error `The system cannot find the file specified` saat menjalankan Docker, pastikan aplikasi **Docker Desktop** sudah dibuka dan statusnya "Running" (ikon paus hijau di system tray).

