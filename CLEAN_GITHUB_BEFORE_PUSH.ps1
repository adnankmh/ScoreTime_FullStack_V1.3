$ErrorActionPreference = "Stop"
$Root = $PSScriptRoot

$Allowed = @(
  "01-laravel.yml",
  "02-web-pages.yml",
  "03-android-apk-aab.yml",
  "04-ios.yml"
)

$WorkflowDir = Join-Path $Root ".github\workflows"
if (-not (Test-Path $WorkflowDir)) {
    New-Item -ItemType Directory -Force $WorkflowDir | Out-Null
}

Get-ChildItem $WorkflowDir -File | Where-Object {
    $_.Name -notin $Allowed
} | ForEach-Object {
    Write-Host "Removing stale workflow: $($_.Name)" -ForegroundColor Yellow
    Remove-Item $_.FullName -Force
}

$OldTest = Join-Path $Root "mobile-flutter\test\widget_test.dart"
if (Test-Path $OldTest) {
    $content = Get-Content $OldTest -Raw
    if ($content -match '\bMyApp\b') {
        $content = $content -replace '\bMyApp\b', 'ScoreTimeApp'
        Set-Content $OldTest $content -Encoding UTF8
    }
}

Write-Host "[OK] Only the four current ScoreTime workflows remain." -ForegroundColor Green
