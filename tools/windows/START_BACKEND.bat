@echo off
cd /d "%~dp0\..\..\backend-laravel"
php artisan optimize:clear
php artisan serve
pause
