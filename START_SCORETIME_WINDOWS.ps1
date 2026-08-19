param(
    [switch]$NoBrowser
)

$ErrorActionPreference = "Stop"
$Root = $PSScriptRoot
$Backend = Join-Path $Root "backend-laravel"
$LogDir = Join-Path $Root "runtime-logs"
New-Item -ItemType Directory -Force $LogDir | Out-Null
$Log = Join-Path $LogDir "scoretime-local-setup.log"

function Log([string]$Message) {
    $line = "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] $Message"
    Write-Host $line
    Add-Content -Path $Log -Value $line
}

function Resolve-PHP {
    $candidates = @(
        "C:\xampp\php\php.exe",
        "C:\php\php.exe"
    )
    foreach ($candidate in $candidates) {
        if (Test-Path $candidate) { return $candidate }
    }
    $cmd = Get-Command php -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }
    throw "PHP was not found. XAMPP/PHP is required."
}

function Resolve-Composer {
    $cmd = Get-Command composer -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }

    $candidates = @(
        "C:\ProgramData\ComposerSetup\bin\composer.bat",
        "$env:APPDATA\Composer\vendor\bin\composer.bat",
        "C:\composer\composer.bat"
    )
    foreach ($candidate in $candidates) {
        if (Test-Path $candidate) { return $candidate }
    }
    return $null
}

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

function Test-TcpPort([string]$HostName, [int]$Port, [int]$TimeoutMs = 800) {
    $client = New-Object System.Net.Sockets.TcpClient
    try {
        $iar = $client.BeginConnect($HostName, $Port, $null, $null)
        if (-not $iar.AsyncWaitHandle.WaitOne($TimeoutMs, $false)) {
            return $false
        }
        $client.EndConnect($iar)
        return $true
    } catch {
        return $false
    } finally {
        $client.Close()
    }
}

function Wait-ForMySQL([int]$Seconds = 30) {
    for ($i = 0; $i -lt $Seconds; $i++) {
        if (Test-TcpPort "127.0.0.1" 3306 600) {
            return $true
        }
        Start-Sleep -Seconds 1
    }
    return $false
}

function Start-MySQLAutomatic {
    if (Test-TcpPort "127.0.0.1" 3306 600) {
        Log "MySQL is already listening on port 3306."
        return $true
    }

    Log "MySQL is not listening on port 3306. Starting it automatically..."

    # Method 1: standard XAMPP batch file.
    $xamppStart = "C:\xampp\mysql_start.bat"
    if (Test-Path $xamppStart) {
        try {
            Log "Trying XAMPP mysql_start.bat..."
            Start-Process -FilePath "cmd.exe" `
                -ArgumentList "/c", "`"$xamppStart`"" `
                -WindowStyle Hidden
            if (Wait-ForMySQL 15) {
                Log "MySQL started successfully via XAMPP."
                return $true
            }
        } catch {
            Log "XAMPP mysql_start.bat attempt failed: $($_.Exception.Message)"
        }
    }

    # Method 2: start mysqld directly with XAMPP configuration.
    $mysqld = "C:\xampp\mysql\bin\mysqld.exe"
    $myini = "C:\xampp\mysql\bin\my.ini"
    if ((Test-Path $mysqld) -and (Test-Path $myini)) {
        try {
            Log "Trying mysqld.exe directly..."
            Start-Process -FilePath $mysqld `
                -ArgumentList "--defaults-file=`"$myini`"", "--standalone" `
                -WindowStyle Hidden
            if (Wait-ForMySQL 20) {
                Log "MySQL started successfully via mysqld.exe."
                return $true
            }
        } catch {
            Log "mysqld.exe direct start failed: $($_.Exception.Message)"
        }
    }

    # Method 3: if XAMPP Control Panel exists, open it to make the problem visible.
    $xamppControl = "C:\xampp\xampp-control.exe"
    if (Test-Path $xamppControl) {
        try {
            Start-Process $xamppControl
            Log "XAMPP Control Panel opened because MySQL could not be started automatically."
        } catch {}
    }

    return $false
}

if (-not (Test-Path $Backend)) {
    throw "backend-laravel folder is missing."
}

$php = Resolve-PHP
$composer = Resolve-Composer

Log "ScoreTime V1.7.0 local setup started."
Log "PHP: $php"

Push-Location $Backend
try {
    # Laravel writable/runtime directories.
    @(
        "bootstrap\cache",
        "storage\framework\cache\data",
        "storage\framework\sessions",
        "storage\framework\views",
        "storage\logs"
    ) | ForEach-Object {
        New-Item -ItemType Directory -Force $_ | Out-Null
    }

    # Composer dependencies.
    if (-not (Test-Path "vendor\autoload.php")) {
        if (-not $composer) {
            throw "Composer is required because vendor/autoload.php is missing. Install Composer for Windows once, then run START_SCORETIME_WINDOWS.bat again."
        }

        Log "vendor/autoload.php is missing. Resolving Laravel dependencies..."

        if (Test-Path "composer.lock") {
            & $composer install --prefer-dist --no-interaction --no-progress --no-scripts
        } else {
            Log "composer.lock is not present. Running composer update once to create it..."
            & $composer update --prefer-dist --no-interaction --no-progress --no-scripts
        }

        if ($LASTEXITCODE -ne 0) {
            throw "Composer dependency resolution failed with exit code $LASTEXITCODE. The full Composer output is shown above."
        }

        if (-not (Test-Path "vendor\autoload.php")) {
            throw "Composer finished but vendor/autoload.php is still missing."
        }

        & $php artisan package:discover --ansi
        if ($LASTEXITCODE -ne 0) {
            throw "Laravel package discovery failed."
        }
    } else {
        Log "Laravel vendor dependencies already exist."
    }

    # Environment file.
    if (-not (Test-Path ".env")) {
        Copy-Item ".env.example" ".env"
        Log ".env created from .env.example."
    }

    Set-EnvValue ".env" "APP_NAME" "ScoreTime"
    Set-EnvValue ".env" "APP_ENV" "local"
    Set-EnvValue ".env" "APP_DEBUG" "true"
    Set-EnvValue ".env" "APP_URL" "http://127.0.0.1:8000"
    Set-EnvValue ".env" "DB_CONNECTION" "mysql"
    Set-EnvValue ".env" "DB_HOST" "127.0.0.1"
    Set-EnvValue ".env" "DB_PORT" "3306"
    Set-EnvValue ".env" "DB_DATABASE" "football_global"
    Set-EnvValue ".env" "DB_USERNAME" "root"
    Set-EnvValue ".env" "DB_PASSWORD" ""
    Set-EnvValue ".env" "CACHE_STORE" "file"
    Set-EnvValue ".env" "SESSION_DRIVER" "file"
    Set-EnvValue ".env" "SESSION_COOKIE" "scoretime_session"
    Set-EnvValue ".env" "QUEUE_CONNECTION" "sync"
    Set-EnvValue ".env" "CORS_ALLOWED_ORIGINS" "http://localhost:3000,http://127.0.0.1:8000,https://adnankmh.github.io"

    # Generate a one-time strong local administrator password when none exists.
    $envText = Get-Content ".env" -Raw
    $generatedAdminPassword = $null
    if ($envText -match "(?m)^ADMIN_PASSWORD=\s*$" -or $envText -notmatch "(?m)^ADMIN_PASSWORD=.+$" -or $envText -match "(?m)^ADMIN_PASSWORD=Adnan123\s*$") {
        $passwordBytes = New-Object byte[] 24
        $passwordRng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
        try { $passwordRng.GetBytes($passwordBytes) } finally { $passwordRng.Dispose() }
        $generatedAdminPassword = [Convert]::ToBase64String($passwordBytes).Replace("+", "-").Replace("/", "_").TrimEnd("=")
        Set-EnvValue ".env" "ADMIN_PASSWORD" $generatedAdminPassword
        Log "A strong local administrator password was generated."
    }

    # APP_KEY.
    $envText = Get-Content ".env" -Raw
    if ($envText -match "(?m)^APP_KEY=\s*$" -or $envText -notmatch "(?m)^APP_KEY=.+$") {
        Log "Generating Laravel APP_KEY..."
        & $php artisan key:generate
        if ($LASTEXITCODE -ne 0) {
            throw "php artisan key:generate failed."
        }
    } else {
        Log "APP_KEY already exists."
    }

    # Start MySQL robustly.
    if (-not (Start-MySQLAutomatic)) {
        throw @"
MySQL could not be started automatically on 127.0.0.1:3306.
The XAMPP Control Panel has been opened if available.
Start MySQL there, verify it becomes green, then run START_SCORETIME_WINDOWS.bat again.
No database was erased or reset.
"@
    }

    # Verify actual MySQL command connectivity, not only TCP port.
    $mysqlExe = "C:\xampp\mysql\bin\mysql.exe"
    if (Test-Path $mysqlExe) {
        Log "Verifying MySQL root connection..."
        & $mysqlExe -h 127.0.0.1 -P 3306 -u root -e "SELECT 1;"
        if ($LASTEXITCODE -ne 0) {
            throw "MySQL is running, but root login without password failed. ScoreTime expects the default local XAMPP root account with no password."
        }

        Log "Ensuring football_global database exists..."
        & $mysqlExe -h 127.0.0.1 -P 3306 -u root -e "CREATE DATABASE IF NOT EXISTS football_global CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        if ($LASTEXITCODE -ne 0) {
            throw "Could not create/check football_global database."
        }
    } else {
        Log "mysql.exe not found; Laravel will perform the final connection check."
    }

    Log "Clearing Laravel caches..."
    & $php artisan optimize:clear
    if ($LASTEXITCODE -ne 0) {
        throw "php artisan optimize:clear failed."
    }

    Log "Running non-destructive migrations..."
    & $php artisan migrate --force
    if ($LASTEXITCODE -ne 0) {
        throw "Laravel migrations failed. Check runtime-logs\scoretime-local-setup.log."
    }

    Log "Running seeders..."
    & $php artisan db:seed --force
    if ($LASTEXITCODE -ne 0) {
        Log "WARNING: Seeder returned an error. Existing data was not wiped."
    }

    & $php artisan optimize:clear

    Log "Starting ScoreTime at http://127.0.0.1:8000"
    $escapedBackend = $Backend.Replace("'", "''")
    $escapedPhp = $php.Replace("'", "''")
    Start-Process powershell.exe -ArgumentList @(
        "-NoExit",
        "-ExecutionPolicy", "Bypass",
        "-Command",
        "Set-Location '$escapedBackend'; & '$escapedPhp' artisan serve --host=127.0.0.1 --port=8000"
    )

    $schedulerRunning = Get-CimInstance Win32_Process -ErrorAction SilentlyContinue | Where-Object {
        $_.CommandLine -like "*artisan schedule:work*" -and $_.CommandLine -like "*$Backend*"
    }
    if (-not $schedulerRunning) {
        Start-Process powershell.exe -ArgumentList @(
            "-NoExit",
            "-ExecutionPolicy", "Bypass",
            "-Command",
            "Set-Location '$escapedBackend'; & '$escapedPhp' artisan schedule:work"
        )
        Log "Automatic football/news scheduler started."
    } else {
        Log "ScoreTime scheduler is already running."
    }

    Start-Sleep -Seconds 3

    if (-not $NoBrowser) {
        Start-Process "http://127.0.0.1:8000"
    }

    Log "Done. ScoreTime should now be open in your browser."
    if ($generatedAdminPassword) {
        Write-Host ""
        Write-Host "ScoreTime administrator (shown once):" -ForegroundColor Yellow
        Write-Host "  Username: Adnan" -ForegroundColor White
        Write-Host "  Password: $generatedAdminPassword" -ForegroundColor White
        Write-Host "Change it from the admin account page after login." -ForegroundColor DarkGray
    }
}
finally {
    Pop-Location
}
