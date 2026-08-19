@echo off
setlocal
cd /d "%~dp0\..\..\backend-laravel"
echo =============================================
echo ScoreTime - Prepare Laravel backend
ECHO =============================================
if not exist bootstrap\cache mkdir bootstrap\cache
if not exist storage\framework\cache\data mkdir storage\framework\cache\data
if not exist storage\framework\sessions mkdir storage\framework\sessions
if not exist storage\framework\views mkdir storage\framework\views
if not exist storage\logs mkdir storage\logs
if not exist .env copy .env.example .env
composer install
if errorlevel 1 goto :fail
php artisan key:generate
php artisan optimize:clear
echo.
echo Backend prepared. Next: php artisan migrate:fresh --seed
pause
exit /b 0
:fail
echo Preparation failed. Read the error above.
pause
exit /b 1
