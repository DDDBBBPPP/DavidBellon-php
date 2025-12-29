#!/bin/sh
set -e

# Railway recomienda escuchar en $PORT. En local suele ser 80.
PORT_TO_USE="${PORT:-80}"

if [ "$PORT_TO_USE" != "80" ]; then
  sed -i "s/Listen 80/Listen ${PORT_TO_USE}/" /etc/apache2/ports.conf
  sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT_TO_USE}>/" /etc/apache2/sites-available/000-default.conf
fi

# Seed idempotente: si hay variables de BD y no existe la tabla `usuario`, importamos hotel.sql
if [ -n "$DB_HOST" ] && [ -n "$DB_USER" ] && [ -n "$DB_PASS" ] && [ -n "$DB_NAME" ] && [ -f /app/hotel.sql ]; then
  echo "[entrypoint] Checking MySQL availability..."
  i=0
  until mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" --silent; do
    i=$((i+1))
    if [ "$i" -ge 30 ]; then
      echo "[entrypoint] MySQL not reachable after ~60s, skipping seed."
      break
    fi
    sleep 2
  done

  if mysql -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" -N -e "SELECT 1 FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='usuario' LIMIT 1;" | grep -q 1; then
    echo "[entrypoint] DB already initialized, skipping seed."
  else
    echo "[entrypoint] Seeding database from /app/hotel.sql ..."
    mysql -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" < /app/hotel.sql || true
  fi
fi

exec "$@"
