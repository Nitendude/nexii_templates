#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

DEMO_DB_NAME="${DEMO_DB_NAME:-apm_demo_template}"
MYSQL_HOST="${MYSQL_HOST:-127.0.0.1}"
MYSQL_PORT="${MYSQL_PORT:-3306}"
MYSQL_USER="${MYSQL_USER:-root}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-}"

mysql_cmd=(mysql -h "$MYSQL_HOST" -P "$MYSQL_PORT" -u "$MYSQL_USER")
if [[ -n "$MYSQL_PASSWORD" ]]; then
  mysql_cmd+=("-p$MYSQL_PASSWORD")
fi

echo "==> Dropping + recreating demo database: $DEMO_DB_NAME"
"${mysql_cmd[@]}" -e "DROP DATABASE IF EXISTS \`$DEMO_DB_NAME\`; CREATE DATABASE \`$DEMO_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "==> Running migrations + seeders"
php artisan migrate:fresh --seed

echo "==> Clearing caches"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Demo reset complete."
