@echo off
title ScoreTime - FREE Real Football API
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0SETUP_FREE_API_WINDOWS.ps1"
if errorlevel 1 pause
