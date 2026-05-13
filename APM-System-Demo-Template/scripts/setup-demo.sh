#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

SOURCE_ENV="/home/apmserver/Desktop/APM-System/.env"
DEMO_DB_NAME="${DEMO_DB_NAME:-apm_demo_template}"
MYSQL_HOST="${MYSQL_HOST:-127.0.0.1}"
MYSQL_PORT="${MYSQL_PORT:-3306}"
MYSQL_USER="${MYSQL_USER:-root}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-}"

if [[ -f "$SOURCE_ENV" ]]; then
  SOURCE_DB_NAME="$(grep -E '^DB_DATABASE=' "$SOURCE_ENV" | head -n1 | cut -d '=' -f2- | tr -d '"' || true)"
  SOURCE_DB_USER="$(grep -E '^DB_USERNAME=' "$SOURCE_ENV" | head -n1 | cut -d '=' -f2- | tr -d '"' || true)"
  SOURCE_DB_PASSWORD="$(grep -E '^DB_PASSWORD=' "$SOURCE_ENV" | head -n1 | cut -d '=' -f2- | tr -d '"' || true)"
else
  SOURCE_DB_NAME=""
  SOURCE_DB_USER=""
  SOURCE_DB_PASSWORD=""
fi

if [[ -n "${SOURCE_DB_USER:-}" ]]; then
  MYSQL_USER="${MYSQL_USER:-$SOURCE_DB_USER}"
fi
if [[ -n "${SOURCE_DB_PASSWORD:-}" ]]; then
  MYSQL_PASSWORD="${MYSQL_PASSWORD:-$SOURCE_DB_PASSWORD}"
fi

echo "==> Preparing demo environment"
cp -f .env .env.local.backup >/dev/null 2>&1 || true
php artisan key:generate --force

echo "==> Creating demo database: $DEMO_DB_NAME"
mysql_cmd=(mysql -h "$MYSQL_HOST" -P "$MYSQL_PORT" -u "$MYSQL_USER")
if [[ -n "$MYSQL_PASSWORD" ]]; then
  mysql_cmd+=("-p$MYSQL_PASSWORD")
fi

"${mysql_cmd[@]}" -e "CREATE DATABASE IF NOT EXISTS \`$DEMO_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [[ -n "${SOURCE_DB_NAME:-}" ]]; then
  echo "==> Cloning source database ($SOURCE_DB_NAME) to demo database ($DEMO_DB_NAME)"
  dump_cmd=(mysqldump -h "$MYSQL_HOST" -P "$MYSQL_PORT" -u "$MYSQL_USER")
  if [[ -n "$MYSQL_PASSWORD" ]]; then
    dump_cmd+=("-p$MYSQL_PASSWORD")
  fi
  "${dump_cmd[@]}" --single-transaction --quick --routines --triggers "$SOURCE_DB_NAME" | "${mysql_cmd[@]}" "$DEMO_DB_NAME"
else
  echo "==> Source DB not detected. Running migrations + seeders instead."
  php artisan migrate:fresh --seed
fi

echo "==> Running final Laravel setup"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link || true

echo
echo "Demo template is ready."
echo "Run:"
echo "  cd $ROOT_DIR"
echo "  php artisan serve --host=127.0.0.1 --port=8088"
