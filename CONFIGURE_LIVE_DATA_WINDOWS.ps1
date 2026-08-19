$ErrorActionPreference = "Stop"

Write-Host "ScoreTime V1.7 uses one safe configuration flow." -ForegroundColor Cyan
Write-Host "Opening the protected free-plan setup..." -ForegroundColor DarkGray

& (Join-Path $PSScriptRoot "SETUP_FREE_API_WINDOWS.ps1")
