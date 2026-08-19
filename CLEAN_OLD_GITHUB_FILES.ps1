$ErrorActionPreference = "Stop"
$Root = $PSScriptRoot

Write-Host "Cleaning legacy ScoreTime files..." -ForegroundColor Cyan

$workflowDir = Join-Path $Root ".github\workflows"
$allowedWorkflows = @("01-laravel.yml", "02-android-apk-aab.yml", "03-ios.yml")
if (Test-Path $workflowDir) {
    Get-ChildItem $workflowDir -File | Where-Object {
        $_.Name -notin $allowedWorkflows
    } | ForEach-Object {
        Write-Host "Removing old workflow: $($_.Name)" -ForegroundColor Yellow
        Remove-Item $_.FullName -Force
    }
}

$testDir = Join-Path $Root "mobile-flutter\test"
$allowedTests = @("widget_test.dart", "smoke_test.dart")
if (Test-Path $testDir) {
    Get-ChildItem $testDir -File -Filter *.dart | Where-Object {
        $_.Name -notin $allowedTests
    } | ForEach-Object {
        Write-Host "Removing obsolete Flutter test: $($_.Name)" -ForegroundColor Yellow
        Remove-Item $_.FullName -Force
    }
}

Write-Host "[OK] Legacy active files cleaned." -ForegroundColor Green
