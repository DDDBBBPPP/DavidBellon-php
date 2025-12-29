#!/bin/sh
set -e

# --- Fix Apache MPM (ya te funciona) ---
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf
a2enmod mpm_prefork 2>/dev/null || true

# --- Seed DB ---
if [ -n "$DB_HOST" ] && [ -n "$DB_USER" ] && [ -n "$DB_PASS" ] && [ -n "$DB_NAME" ] && [ -f /app/hotel.sql ]; then
  echo "[entrypoint] Waiting for MySQL..."

  i=0
  until mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" --silent; do
    i=$((i+1))
    if [ "$i" -ge 60 ]; then
      echo "[entrypoint] MySQL not reachable after ~120s, skipping seed."
      break
    fi
    sleep 2
  done

  if mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" --silent; then
    echo "[entrypoint] Checking if table 'usuario' exists in DB '$DB_NAME'..."

    set +e
    HAS_TABLE=$(mysql -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" -N \
      -e "SELECT 1 FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='usuario' LIMIT 1;")
    set -e

    if [ "$HAS_TABLE" = "1" ]; then
      echo "[entrypoint] DB already initialized, skipping seed."
    else
      echo "[entrypoint] Seeding database into '$DB_NAME' from /app/hotel.sql ..."

      # Importante: ejecutar el SQL dentro de la DB correcta
      # (si el hotel.sql ya hace CREATE DATABASE/USE, no pasa nada; si no, esto lo fija)
      mysql -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < /app/hotel.sql || true

      echo "[entrypoint] Seed done."
    fi
  fi
fi

exec "$@"
