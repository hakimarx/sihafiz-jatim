@echo off
echo ==========================================
echo    SIHAFIZ JATIM - DOCKER LAUNCHER
echo ==========================================
echo.
echo Pastikan Docker Desktop sudah berjalan di background!
echo.
echo Memulai container...
docker compose up -d --build
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
pause
