#!/bin/sh
set -e

echo "[entrypoint] Applying Apache MPM fix (keep only prefork)..."

# Desactivar MPMs conflictivos (por si Railway/imagen los trae activos)
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true

# Borrar enlaces por si quedan activos igualmente
rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf

# Activar SOLO prefork (mod_php lo necesita)
a2enmod mpm_prefork 2>/dev/null || true

# (Opcional) imprimir qué MPM quedó cargado
apache2ctl -M 2>/dev/null | grep mpm || true

# --- Seed idempotente de la BD (si hay variables y existe el SQL) ---
if [ -n "$DB_HOST" ] && [ -n "$DB_USER" ] && [ -n "$DB_PASS" ] && [ -n "$DB_NAME" ] && [ -f /app/hotel.sql ]; then
  echo "[entrypoint] Waiting for MySQL..."

  i=0
  until mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" --silent; do
    i=$((i+1))
    if [ "$i" -ge 30 ]; then
      echo "[entrypoint] MySQL not reachable after ~60s, skipping seed."
      break
    fi
    sleep 2
  done

  if mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" --silent; then
    set +e
    HAS_TABLE=$(mysql -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" -N \
      -e "SELECT 1 FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='usuario' LIMIT 1;" 2>/dev/null)
    set -e

    if [ "$HAS_TABLE" = "1" ]; then
      echo "[entrypoint] DB already initialized, skipping seed."
    else
      echo "[entrypoint] Seeding database from /app/hotel.sql ..."
      mysql -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" -p"$DB_PASS" < /app/hotel.sql || true
    fi
  fi
fi

exec "$@"
