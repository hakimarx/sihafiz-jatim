@echo off
setlocal

echo ==========================================
echo    SIHAFIZ JATIM - DOCKER LAUNCHER
echo ==========================================
echo.

:CHECK_DOCKER
:: Cek apakah Docker daemon aktif
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo [PENTING] Docker Desktop belum berjalan atau sedang starting.
    echo.
    echo 1. Pastikan Docker Desktop sudah dibuka (cek di tray icon).
    echo 2. Tunggu sampai icon paus di tray berhenti bergerak (Steady).
    echo 3. Jika baru saja dibuka, biasanya butuh 1-2 menit untuk siap.
    echo.
    echo Silakan tekan tombol apa saja JIKA Docker sudah "Running"...
    pause
    goto CHECK_DOCKER
)

echo Docker terdeteksi aktif.
echo Memulai container (ini mungkin memakan waktu beberapa menit saat pertama kali)...
echo.

:: Menjalankan docker compose
docker compose up -d --build

if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Gagal menjalankan Docker Compose.
    echo Kemungkinan penyebab:
    echo - Port 8080, 8081, atau 3307 sedang dipakai aplikasi lain (misal: Laragon, XAMPP).
    echo - Koneksi internet terputus saat pulling image.
    echo.
    pause
    exit /b
)

echo.
echo ==========================================
echo Aplikasi siap! Akses di:
echo http://localhost:8080
echo.
echo PhpMyAdmin (Docker):
echo http://localhost:8081
echo.
echo Database Port: 3307 (User: root, Pass: root)
echo ==========================================
echo.
echo Catatan: Jika ini pertama kali, tunggu +/- 30 detik 
echo agar MySQL siap menerima koneksi database.
echo ==========================================
echo Menutup jendela ini tidak akan mematikan aplikasi.
echo.
pause

