$ErrorActionPreference = "Stop"
$Root = $PSScriptRoot
$Backend = Join-Path $Root "backend-laravel"
$EnvFile = Join-Path $Backend ".env"

function Set-EnvValue([string]$Path, [string]$Key, [string]$Value) {
  $text = Get-Content $Path -Raw
  $escaped = [Regex]::Escape($Key)
  if ($text -match "(?m)^$escaped=.*$") {
    $text = [Regex]::Replace($text, "(?m)^$escaped=.*$", "$Key=$Value")
  } else {
    $text += "`r`n$Key=$Value`r`n"
  }
  Set-Content $Path $text -Encoding UTF8
}

if (-not (Test-Path $EnvFile)) {
  Copy-Item (Join-Path $Backend ".env.example") $EnvFile
}

Write-Host ""
Write-Host "ScoreTime Live Global Data Setup" -ForegroundColor Cyan
Write-Host "Keys stay in Laravel .env and are never embedded in the mobile/web app." -ForegroundColor DarkGray
Write-Host ""

$footballKey = Read-Host "API-Football key (leave blank to keep Demo mode)"
$newsKey = Read-Host "NewsAPI key (leave blank to disable live news)"

if ($footballKey) {
  Set-EnvValue $EnvFile "FOOTBALL_PROVIDER" "api-football"
  Set-EnvValue $EnvFile "FOOTBALL_DATA_API_KEY" $footballKey
  Set-EnvValue $EnvFile "FOOTBALL_SYNC_ENABLED" "true"
  Set-EnvValue $EnvFile "FOOTBALL_LIVE_SCHEDULER" "true"
  Set-EnvValue $EnvFile "FOOTBALL_TODAY_SCHEDULER" "true"
} else {
  Set-EnvValue $EnvFile "FOOTBALL_PROVIDER" "demo"
}

if ($newsKey) {
  Set-EnvValue $EnvFile "NEWS_API_KEY" $newsKey
  Set-EnvValue $EnvFile "NEWS_SYNC_SCHEDULER" "true"
}

Push-Location $Backend
try {
  php artisan optimize:clear

  if ($footballKey) {
    Write-Host "Synchronizing current countries and competitions..." -ForegroundColor Cyan
    php artisan football:sync-global catalog
    Write-Host "Synchronizing today's global fixtures..." -ForegroundColor Cyan
    php artisan football:sync-today
    Write-Host "Synchronizing live fixtures/events..." -ForegroundColor Cyan
    php artisan football:sync-live --events
  }

  if ($newsKey) {
    Write-Host "Synchronizing global football news..." -ForegroundColor Cyan
    php artisan news:sync-global
  }
} finally {
  Pop-Location
}

Write-Host ""
Write-Host "[OK] ScoreTime live data configuration completed." -ForegroundColor Green
Write-Host "For continuous syncing, run: php artisan schedule:work" -ForegroundColor Yellow
