# Start all 3 projects
# Usage: from anywhere → & "path\to\javier\start-all.ps1"

$ROOT = Split-Path $PSScriptRoot -Parent
$LOGS = "$PSScriptRoot\logs"
New-Item -ItemType Directory -Force -Path $LOGS | Out-Null

Write-Host "[1/3] php-only -> http://localhost:8001"
$p1 = Start-Process -FilePath "php" -ArgumentList "-S", "localhost:8001", "-t", "$ROOT\javier\php-only" `
    -RedirectStandardOutput "$LOGS\php-only.log" -RedirectStandardError "$LOGS\php-only-err.log" `
    -PassThru -WindowStyle Hidden

Write-Host "[2/3] php-react -> http://localhost:8002"
$p2 = Start-Process -FilePath "php" -ArgumentList "-S", "localhost:8002", "-t", "$ROOT\javier\php-react" `
    -RedirectStandardOutput "$LOGS\php-react.log" -RedirectStandardError "$LOGS\php-react-err.log" `
    -PassThru -WindowStyle Hidden

Write-Host "[3/3] react-component PHP backend -> http://localhost:8003"
$p3 = Start-Process -FilePath "php" -ArgumentList "-S", "localhost:8003", "-t", "$ROOT\javier\react-component" `
    -RedirectStandardOutput "$LOGS\react-php.log" -RedirectStandardError "$LOGS\react-php-err.log" `
    -PassThru -WindowStyle Hidden

Write-Host "[3/3] react-component Vite dev -> http://localhost:5173"
$p4 = Start-Process -FilePath "cmd" -ArgumentList "/c", "cd /d `"$ROOT\javier\react-component`" && npm run dev" `
    -RedirectStandardOutput "$LOGS\react-vite.log" -RedirectStandardError "$LOGS\react-vite-err.log" `
    -PassThru -WindowStyle Hidden

Write-Host ""
Write-Host "All started. PIDs: php-only=$($p1.Id) php-react=$($p2.Id) react-php=$($p3.Id) vite=$($p4.Id)"
Write-Host "Logs in: $LOGS"
Write-Host ""
Write-Host "Press Enter to stop all..."
Read-Host

Write-Host "Stopping..."
@($p1, $p2, $p3, $p4) | ForEach-Object {
    if (-not $_.HasExited) { $_.Kill() }
}
