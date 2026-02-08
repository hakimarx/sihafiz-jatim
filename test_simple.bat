@echo off
setlocal
echo Testing script start...
set "LARAGON_EXE=C:\laragon\laragon.exe"
if exist "%LARAGON_EXE%" (
    echo Laragon found.
) else (
    echo Laragon not found.
)
pause
