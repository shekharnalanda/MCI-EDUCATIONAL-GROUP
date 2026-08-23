#!/bin/bash
set -euo pipefail

REPO="/home4/mcied45x/repositories/MCI-EDUCATIONAL-GROUP"
PHP="/opt/cpanel/ea-php83/root/usr/bin/php"
BACKUP_ROOT="/home4/mcied45x/backups/mci-educational-group"
STAMP="$(date '+%Y%m%d_%H%M%S')"
DEST="$BACKUP_ROOT/$STAMP"

cd "$REPO"
mkdir -p "$DEST"
chmod 700 "$BACKUP_ROOT" "$DEST"

DB_LINE=$($PHP artisan tinker --execute="echo base64_encode(config('database.connections.mysql.host')).'|'.base64_encode(config('database.connections.mysql.port')).'|'.base64_encode(config('database.connections.mysql.database')).'|'.base64_encode(config('database.connections.mysql.username')).'|'.base64_encode(config('database.connections.mysql.password'));" 2>/dev/null | tail -1)
IFS='|' read -r DB_HOST_B64 DB_PORT_B64 DB_NAME_B64 DB_USER_B64 DB_PASS_B64 <<< "$DB_LINE"

DB_HOST=$(printf '%s' "$DB_HOST_B64" | base64 -d)
DB_PORT=$(printf '%s' "$DB_PORT_B64" | base64 -d)
DB_NAME=$(printf '%s' "$DB_NAME_B64" | base64 -d)
DB_USER=$(printf '%s' "$DB_USER_B64" | base64 -d)
DB_PASS=$(printf '%s' "$DB_PASS_B64" | base64 -d)

if [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
  echo "BACKUP_FAIL: database configuration unavailable"
  rm -rf "$DEST"
  exit 1
fi

MYSQL_PWD="$DB_PASS" /bin/mysqldump \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --user="$DB_USER" \
  --single-transaction \
  --quick \
  --skip-lock-tables \
  "$DB_NAME" | gzip -9 > "$DEST/database.sql.gz"

if [ -d storage/app/public ]; then
  /bin/tar -czf "$DEST/uploads.tar.gz" -C storage/app public
fi

cp .env "$DEST/env.snapshot"
chmod 600 "$DEST/env.snapshot" "$DEST/database.sql.gz" 2>/dev/null || true

cat > "$DEST/manifest.txt" <<EOF
Created: $(date '+%Y-%m-%d %H:%M:%S %z')
Repository HEAD: $(git rev-parse HEAD)
Database: $DB_NAME
Database archive: database.sql.gz
Uploads archive: uploads.tar.gz
Environment snapshot: env.snapshot
EOF
chmod 600 "$DEST/manifest.txt"

# Keep the most recent 14 days of daily/on-demand backups.
find "$BACKUP_ROOT" -mindepth 1 -maxdepth 1 -type d -mtime +14 -exec rm -rf {} +

echo "MCI_BACKUP=PASS"
echo "BACKUP_PATH=$DEST"
ls -lh "$DEST"
