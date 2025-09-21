@echo off
title INICIAR XAMPP - Apache e MySQL
color 0A

echo ========================================
echo    INICIANDO XAMPP - Apache e MySQL
echo ========================================
echo.

REM Verificar se o XAMPP está instalado
if not exist "C:\xampp\xampp_start.exe" (
    echo [ERRO] XAMPP nao encontrado em C:\xampp\
    echo.
    echo Por favor, instale o XAMPP primeiro:
    echo https://www.apachefriends.org/download.html
    echo.
    pause
    exit /b 1
)

echo [INFO] XAMPP encontrado. Iniciando servicos...
echo.

REM Parar serviços existentes (caso estejam rodando)
echo [1/4] Parando servicos existentes...
taskkill /f /im "httpd.exe" >nul 2>&1
taskkill /f /im "mysqld.exe" >nul 2>&1
timeout /t 2 >nul

REM Iniciar Apache
echo [2/4] Iniciando Apache...
start "" "C:\xampp\apache\bin\httpd.exe"
timeout /t 3

REM Verificar se Apache iniciou
tasklist /fi "imagename eq httpd.exe" 2>nul | find /i "httpd.exe" >nul
if "%ERRORLEVEL%"=="0" (
    echo [OK] Apache iniciado com sucesso!
) else (
    echo [ERRO] Falha ao iniciar Apache
)

REM Iniciar MySQL
echo [3/4] Iniciando MySQL...
start "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone --console
timeout /t 3

REM Verificar se MySQL iniciou
tasklist /fi "imagename eq mysqld.exe" 2>nul | find /i "mysqld.exe" >nul
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL iniciado com sucesso!
) else (
    echo [ERRO] Falha ao iniciar MySQL
)

echo.
echo [4/4] Verificando status dos servicos...
echo.

REM Status final
echo ========================================
echo           STATUS DOS SERVICOS
echo ========================================

REM Verificar Apache
netstat -an | find ":80 " >nul
if "%ERRORLEVEL%"=="0" (
    echo [✓] Apache: RODANDO na porta 80
) else (
    echo [✗] Apache: NAO ESTA RODANDO
)

REM Verificar MySQL
netstat -an | find ":3306 " >nul
if "%ERRORLEVEL%"=="0" (
    echo [✓] MySQL: RODANDO na porta 3306
) else (
    echo [✗] MySQL: NAO ESTA RODANDO
)

echo.
echo ========================================
echo            LINKS UTEIS
echo ========================================
echo.
echo Painel XAMPP:     http://localhost/
echo phpMyAdmin:       http://localhost/phpmyadmin/
echo Sistema:          http://localhost/sesap_curriculo/
echo.
echo ========================================

REM Perguntar se quer abrir o navegador
echo.
set /p choice="Deseja abrir o sistema no navegador? (S/N): "
if /i "%choice%"=="S" (
    start http://localhost/sesap_curriculo/
)

echo.
echo Pressione qualquer tecla para fechar...
pause >nul