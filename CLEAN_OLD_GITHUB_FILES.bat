@echo off
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0CLEAN_OLD_GITHUB_FILES.ps1"
pause
