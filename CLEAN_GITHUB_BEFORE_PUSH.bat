@echo off
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0CLEAN_GITHUB_BEFORE_PUSH.ps1"
pause
