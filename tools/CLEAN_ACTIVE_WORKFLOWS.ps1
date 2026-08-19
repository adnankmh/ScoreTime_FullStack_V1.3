param(
  [string]$RepoRoot = "."
)

$ErrorActionPreference = "Stop"
$RepoRoot = (Resolve-Path $RepoRoot).Path
$WorkflowDir = Join-Path $RepoRoot ".github\workflows"

Write-Host "ScoreTime workflow cleanup" -ForegroundColor Cyan
Write-Host "Repository: $RepoRoot"

if (Test-Path $WorkflowDir) {
  Get-ChildItem $WorkflowDir -File -ErrorAction SilentlyContinue | ForEach-Object {
    Write-Host "Removing old active workflow: $($_.Name)" -ForegroundColor Yellow
    Remove-Item $_.FullName -Force
  }
} else {
  New-Item -ItemType Directory -Force $WorkflowDir | Out-Null
}

$SourceWorkflow = Join-Path $PSScriptRoot "..\.github\workflows\scoretime.yml"
$TargetWorkflow = Join-Path $WorkflowDir "scoretime.yml"

if (-not (Test-Path $SourceWorkflow)) {
  throw "Current ScoreTime workflow not found: $SourceWorkflow"
}

Copy-Item $SourceWorkflow $TargetWorkflow -Force
Write-Host "[OK] Only scoretime.yml remains active locally." -ForegroundColor Green
Write-Host ""
Write-Host "Next in GitHub Desktop:" -ForegroundColor Cyan
Write-Host "1. Review Changes"
Write-Host "2. Commit: Clean ScoreTime workflows"
Write-Host "3. Push origin"
