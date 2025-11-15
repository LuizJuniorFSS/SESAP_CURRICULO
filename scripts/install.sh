#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="${SCRIPT_DIR}/.."
cd "${PROJECT_DIR}"

command -v docker >/dev/null || { echo "Docker não encontrado"; exit 1; }
docker compose version >/dev/null || { echo "Docker Compose não encontrado"; exit 1; }

if [ ! -f .env ]; then
cat > .env <<'EOF'
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
EOF
fi

docker compose up -d --build

APP_URL="http://localhost:8080/public/index.html"
PMA_URL="http://localhost:8081/"

try_count=0
until curl -s -o /dev/null -w "%{http_code}" "$APP_URL" | grep -q "200" || [ $try_count -ge 30 ]; do
  sleep 2; try_count=$((try_count+1))
done

try_count=0
until curl -s -o /dev/null -w "%{http_code}" "$PMA_URL" | grep -q "200" || [ $try_count -ge 30 ]; do
  sleep 2; try_count=$((try_count+1))
done

echo "App: $APP_URL"
echo "Admin: http://localhost:8080/pages/admin/login.php"
echo "Usuário: http://localhost:8080/pages/user/user_login.php"
echo "phpMyAdmin: $PMA_URL (Servidor: db, Usuário: sesap, Senha: sesap123)"