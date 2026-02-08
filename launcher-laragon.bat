@echo off
setlocal
title SiHafiz Jatim Launcher
cd /d "%~dp0"

echo.
echo ==========================================
echo    SIHAFIZ JATIM - LARAGON LAUNCHER
echo ==========================================
echo.

REM 1. Jalankan Laragon
set "LARAGON_EXE=C:\laragon\laragon.exe"
if exist "%LARAGON_EXE%" (
    echo [OK] Menjalankan Laragon...
    start "" "%LARAGON_EXE%"
) else (
    echo [ERROR] Laragon tidak ditemukan di C:\laragon\laragon.exe
    echo Pastikan Laragon sudah terinstall di C:
    pause
    exit /b
)

REM 2. Tunggu sebentar agar servis siap
echo [INFO] Menunggu sistem siap (8 detik)...
timeout /t 8 /nobreak >nul

REM 3. Buka Browser (Prioritas Opera)
set "OPERA_PATH=C:\Users\PENAISZAWA\AppData\Local\Programs\Opera\opera.exe"
set "URL=http://localhost/sihafiz-jatim/public"

if exist "%OPERA_PATH%" (
    echo [OK] Membuka aplikasi di Opera...
    start "" "%OPERA_PATH%" "%URL%"
) else (
    echo [INFO] Opera tidak ditemukan, menggunakan browser default...
    start "" "%URL%"
)

echo.
echo [SELESAI] Aplikasi siap digunakan.
echo Jendela ini akan tertutup otomatis dalam 5 detik.
timeout /t 5
exit
