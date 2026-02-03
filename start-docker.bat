@echo off
setlocal

echo ==========================================
echo    SIHAFIZ JATIM - DOCKER LAUNCHER
echo ==========================================
echo.

:: Cek apakah Docker daemon aktif
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo [PENTING] Docker Desktop belum berjalan atau sedang starting.
    echo.
    echo 1. Pastikan Docker Desktop sudah dibuka (cek di tray icon).
    echo 2. Tunggu sampai icon paus di tray berhenti bergerak (Steady).
    echo 3. Jika belum dibuka, silakan buka Docker Desktop sekarang.
    echo.
    echo Tekan tombol apa saja jika sudah yakin Docker sudah "Green/Running"...
    pause >nul
)

echo.
echo Memulai container...
:: Docker compose up -d --build
:: Menggunakan 'docker compose' (V2) yang sekarang standar
docker compose up -d --build

if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Gagal menjalankan Docker Compose.
    echo Pastikan tidak ada konflik port (8080, 8081, 3307).
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
pause

