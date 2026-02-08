@echo off
setlocal
title SiHafiz Jatim (Docker Launcher)
cd /d "%~dp0"

echo ==========================================
echo    SIHAFIZ JATIM - DOCKER LAUNCHER
echo ==========================================
echo.

:: Menjalankan script docker yang sudah ada
echo [INFO] Menjalankan Docker Compose via start-docker.bat...
call start-docker.bat

:: start-docker.bat memiliki 'pause', jadi ini akan dijalankan setelah user menekan tombol
echo [INFO] Membuka aplikasi di Opera...
set "OPERA_PATH=C:\Users\PENAISZAWA\AppData\Local\Programs\Opera\opera.exe"

if exist "%OPERA_PATH%" (
    start "" "%OPERA_PATH%" "http://localhost:8080"
) else (
    start "" "http://localhost:8080"
)

if %ERRORLEVEL% neq 0 (
    echo [PENTING] Gagal membuka browser otomatis.
    echo Akses manual: http://localhost:8080
    pause
) else (
    echo Selesai!
    timeout /t 5 >nul
)
exit
