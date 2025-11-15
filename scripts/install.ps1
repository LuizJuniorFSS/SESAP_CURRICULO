param(
  [switch]$OpenBrowser = $true
)

$ErrorActionPreference = 'Stop'

function Test-Command($cmd) {
  $null -ne (Get-Command $cmd -ErrorAction SilentlyContinue)
}

function Invoke-HttpStatus($url) {
  try { (Invoke-WebRequest -UseBasicParsing -TimeoutSec 10 -Uri $url).StatusCode } catch { 0 }
}

if (-not (Test-Command docker)) { Write-Error 'Docker não encontrado. Instale o Docker Desktop.'; exit 1 }
try { docker compose version | Out-Null } catch { Write-Error 'Docker Compose não encontrado.'; exit 1 }
try { docker info | Out-Null } catch { Write-Error 'Docker não está em execução. Abra o Docker Desktop.'; exit 1 }

$projectDir = (Resolve-Path "$PSScriptRoot\..").Path
Set-Location $projectDir

if (-not (Test-Path '.env')) {
  Set-Content -Path '.env' -Value @"
MYSQL_ROOT_PASSWORD=secret-root
MYSQL_DATABASE=sesap_curriculo
MYSQL_USER=sesap
MYSQL_PASSWORD=sesap123

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=seu_email@gmail.com
SMTP_PASSWORD=sua_senha_app
EMAIL_FROM=seu_email@gmail.com
EMAIL_TO=rh@sesap.rn.gov.br
"@
}

docker compose up -d --build

$appUrl = 'http://localhost:8080/public/index.html'
$pmaUrl = 'http://localhost:8081/'

$status = 0
for ($i=0; $i -lt 30; $i++) { $status = Invoke-HttpStatus $appUrl; if ($status -eq 200) { break }; Start-Sleep -Seconds 2 }
$status2 = 0
for ($i=0; $i -lt 30; $i++) { $status2 = Invoke-HttpStatus $pmaUrl; if ($status2 -eq 200) { break }; Start-Sleep -Seconds 2 }

Write-Host "App: $appUrl"
Write-Host 'Admin: http://localhost:8080/pages/admin/login.php'
Write-Host 'Usuário: http://localhost:8080/pages/user/user_login.php'
Write-Host 'phpMyAdmin: http://localhost:8081/ (Servidor: db, Usuário: sesap, Senha: sesap123)'

if ($OpenBrowser) { Start-Process $appUrl; Start-Process $pmaUrl }