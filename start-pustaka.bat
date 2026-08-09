@echo off
title PustakaManis - Sistem Perpustakaan
color 0B

echo ==========================================
echo   PUSTAKAMANIS - Sistem Perpustakaan
echo   %SCHOOL_NAME%
echo ==========================================
echo.

cd /d %~dp0

echo [1/4] Memeriksa environment...
if not exist ".env" (
    echo      .env tidak ditemukan! Menyalin dari template...
    copy .env.example .env >nul
    php artisan key:generate --force
)

echo [2/4] Menjalankan migrasi (jika perlu)...
php artisan migrate --force

echo [3/4] Membangun asset frontend (jika perlu)...
if not exist "public\build\manifest.json" (
    call npm install
    call npm run build
)

echo [4/4] Membuat backup harian...
php artisan db:backup

echo.
echo ==========================================
echo   Server berjalan di:
echo   http://localhost:8000
echo.
echo   Akses dari perangkat lain:
echo   http://[IP-KOMPUTER-INI]:8000
echo.
echo   Tekan Ctrl+C untuk menghentikan server
echo ==========================================
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause