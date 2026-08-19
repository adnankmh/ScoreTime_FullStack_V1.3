$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Write-Host "ScoreTime clean-checkout preparation" -ForegroundColor Cyan
$staleLock = Join-Path $root "backend-laravel\composer.lock"
if (Test-Path $staleLock) {
  Remove-Item $staleLock -Force
  Write-Host "Removed stale backend-laravel/composer.lock; composer install/update will regenerate it." -ForegroundColor Yellow
}
$legacy = Join-Path $root ".github\workflows"
Write-Host "Active workflows:" -ForegroundColor Green
Get-ChildItem $legacy -File | ForEach-Object { Write-Host " - $($_.Name)" }
Write-Host "Done." -ForegroundColor Green
