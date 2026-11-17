@echo off
chcp 65001 >nul
echo ============================================
echo   Настройка сайта 2048 (создание базы)
echo ============================================
echo.

REM === 1. Ищем psql (входит в PostgreSQL) ===
set PSQL=
if exist "C:\Program Files\PostgreSQL\18\bin\psql.exe" set PSQL="C:\Program Files\PostgreSQL\18\bin\psql.exe"
if not defined PSQL if exist "C:\Program Files\PostgreSQL\17\bin\psql.exe" set PSQL="C:\Program Files\PostgreSQL\17\bin\psql.exe"
if not defined PSQL if exist "C:\Program Files\PostgreSQL\16\bin\psql.exe" set PSQL="C:\Program Files\PostgreSQL\16\bin\psql.exe"
if not defined PSQL set PSQL=psql

REM === 2. Спрашиваем пароль от PostgreSQL ===
set /p PGPASSWORD=Введи пароль от PostgreSQL (то, что задавал при установке): 
set PGPASSWORD=%PGPASSWORD%

echo.
echo === Создаю базу game2048 ===
%PSQL% -U postgres -h 127.0.0.1 -c "CREATE DATABASE game2048;"
REM если пишет "already exists" — база уже есть, это нормально, продолжаем

echo.
echo === Создаю таблицы и админа (admin / admin123) ===
call php artisan key:generate --force >nul 2>&1
call php artisan migrate:fresh --seed --force
if errorlevel 1 (
    echo.
    echo ОШИБКА: не получилось создать таблицы.
    echo Проверь пароль в файле .env (строка DB_PASSWORD) и повтори.
    pause
    exit /b 1
)

echo.
echo ============================================
echo   Готово! Запусти сайт командой:
echo   php artisan serve
echo   и открой http://localhost:8000
echo ============================================
pause
