@echo off
title ScoreTime V1.7.1 - One Click Setup and Run
cd /d "%~dp0"
echo.
echo ============================================================
echo   ScoreTime V1.7.1 - Automatic Local Setup + Browser Run
echo ============================================================
echo.
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0START_SCORETIME_WINDOWS.ps1"
if errorlevel 1 (
  echo.
  echo ============================================================
  echo  ScoreTime setup stopped because of an environment problem.
  echo  Nothing was deleted or reset.
  echo  Log: runtime-logs\scoretime-local-setup.log
  echo ============================================================
  echo.
  pause
)
