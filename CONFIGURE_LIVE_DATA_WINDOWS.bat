@echo off
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0CONFIGURE_LIVE_DATA_WINDOWS.ps1"
pause
