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

    Set-Content -Path $Path -Value $text -Encoding UTF8
}

function Resolve-PHP {
    foreach ($candidate in @("C:\xampp\php\php.exe", "C:\php\php.exe")) {
        if (Test-Path $candidate) { return $candidate }
    }
    $command = Get-Command php -ErrorAction SilentlyContinue
    if ($command) { return $command.Source }
    throw "PHP was not found. Run START_SCORETIME_WINDOWS.bat first or install XAMPP/PHP 8.2+."
}

if (-not (Test-Path $Backend)) {
    throw "backend-laravel is missing."
}

if (-not (Test-Path $EnvFile)) {
    Copy-Item (Join-Path $Backend ".env.example") $EnvFile
}

Write-Host ""
Write-Host "==============================================" -ForegroundColor Cyan
Write-Host " ScoreTime - FREE Real Football API Setup" -ForegroundColor Cyan
Write-Host "==============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Recommended: API-Football Free plan (100 requests/day)." -ForegroundColor White
Write-Host "The key remains only in Laravel .env, never in APK/Web." -ForegroundColor DarkGray
Write-Host ""

$key = Read-Host "Paste your API-Football free API key"
$newsKey = Read-Host "Optional NewsAPI key for development news (press Enter to skip)"

if ([string]::IsNullOrWhiteSpace($key)) {
    throw "A free API key is required for real football data."
}

Set-EnvValue $EnvFile "FOOTBALL_PROVIDER" "auto"
Set-EnvValue $EnvFile "FOOTBALL_DATA_API_KEY" $key.Trim()
Set-EnvValue $EnvFile "FOOTBALL_FREE_PLAN_MODE" "true"
Set-EnvValue $EnvFile "FOOTBALL_FREE_DAILY_LIMIT" "100"
Set-EnvValue $EnvFile "FOOTBALL_FREE_DAILY_RESERVE" "20"
Set-EnvValue $EnvFile "FOOTBALL_FREE_LIVE_DAILY_CAP" "40"
Set-EnvValue $EnvFile "FOOTBALL_FREE_DETAIL_DAILY_CAP" "20"
Set-EnvValue $EnvFile "FOOTBALL_FREE_CATALOG_DAILY_CAP" "8"
Set-EnvValue $EnvFile "FOOTBALL_SYNC_ENABLED" "true"
Set-EnvValue $EnvFile "FOOTBALL_LIVE_SCHEDULER" "true"
Set-EnvValue $EnvFile "FOOTBALL_TODAY_SCHEDULER" "true"
Set-EnvValue $EnvFile "FOOTBALL_CATALOG_SCHEDULER" "true"
if (-not [string]::IsNullOrWhiteSpace($newsKey)) {
    Set-EnvValue $EnvFile "NEWS_API_KEY" $newsKey.Trim()
    Set-EnvValue $EnvFile "NEWS_SYNC_SCHEDULER" "true"
}

$php = Resolve-PHP

Push-Location $Backend
try {
    & $php artisan optimize:clear

    Write-Host "Verifying the key and syncing today's real fixtures (one request)..." -ForegroundColor Cyan
    & $php artisan football:sync-today
    if ($LASTEXITCODE -ne 0) {
        throw "API key verification or today's fixture sync failed."
    }

    if (-not [string]::IsNullOrWhiteSpace($newsKey)) {
        Write-Host "Synchronizing licensed/development news once..." -ForegroundColor Cyan
        & $php artisan news:sync-global
        if ($LASTEXITCODE -ne 0) {
            Write-Host "News synchronization failed; football setup remains active." -ForegroundColor Yellow
        }
    }

    & $php artisan optimize:clear
} finally {
    Pop-Location
}

Write-Host ""
Write-Host "[OK] ScoreTime is connected to the FREE real football API." -ForegroundColor Green
Write-Host ""
Write-Host "START_SCORETIME_WINDOWS.bat now starts the safe scheduler automatically." -ForegroundColor Yellow
