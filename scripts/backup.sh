#!/bin/bash
set -euo pipefail

REPO="/home4/mcied45x/repositories/MCI-EDUCATIONAL-GROUP"
PHP="/opt/cpanel/ea-php83/root/usr/bin/php"
BACKUP_ROOT="/home4/mcied45x/backups/mci-educational-group"
STAMP="$(date '+%Y%m%d_%H%M%S')"
TMP_DEST="$BACKUP_ROOT/.incomplete-$STAMP"
DEST="$BACKUP_ROOT/$STAMP"

cd "$REPO"
mkdir -p "$BACKUP_ROOT" "$TMP_DEST"
chmod 700 "$BACKUP_ROOT" "$TMP_DEST"

cleanup_failed_backup() {
  rm -rf "$TMP_DEST"
}
trap cleanup_failed_backup ERR INT TERM

DB_LINE=$($PHP artisan tinker --execute="echo base64_encode(config('database.connections.mysql.host')).'|'.base64_encode(config('database.connections.mysql.port')).'|'.base64_encode(config('database.connections.mysql.database')).'|'.base64_encode(config('database.connections.mysql.username')).'|'.base64_encode(config('database.connections.mysql.password'));" 2>/dev/null | tail -1)
IFS='|' read -r DB_HOST_B64 DB_PORT_B64 DB_NAME_B64 DB_USER_B64 DB_PASS_B64 <<< "$DB_LINE"

DB_HOST=$(printf '%s' "$DB_HOST_B64" | base64 -d)
DB_PORT=$(printf '%s' "$DB_PORT_B64" | base64 -d)
DB_NAME=$(printf '%s' "$DB_NAME_B64" | base64 -d)
DB_USER=$(printf '%s' "$DB_USER_B64" | base64 -d)
DB_PASS=$(printf '%s' "$DB_PASS_B64" | base64 -d)

if [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
  echo "BACKUP_FAIL: database configuration unavailable"
  exit 1
fi

MYSQL_PWD="$DB_PASS" /bin/mysqldump \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --user="$DB_USER" \
  --single-transaction \
  --quick \
  --skip-lock-tables \
  "$DB_NAME" | gzip -9 > "$TMP_DEST/database.sql.gz"

gzip -t "$TMP_DEST/database.sql.gz"

if [ -d storage/app/public ]; then
  /bin/tar -czf "$TMP_DEST/uploads.tar.gz" -C storage/app public
  /bin/tar -tzf "$TMP_DEST/uploads.tar.gz" >/dev/null
fi

cp .env "$TMP_DEST/env.snapshot"
chmod 600 "$TMP_DEST/env.snapshot" "$TMP_DEST/database.sql.gz" 2>/dev/null || true
[ ! -f "$TMP_DEST/uploads.tar.gz" ] || chmod 600 "$TMP_DEST/uploads.tar.gz"

(
  cd "$TMP_DEST"
  sha256sum database.sql.gz env.snapshot > checksums.sha256
  [ ! -f uploads.tar.gz ] || sha256sum uploads.tar.gz >> checksums.sha256
)
chmod 600 "$TMP_DEST/checksums.sha256"

cat > "$TMP_DEST/manifest.txt" <<EOF
Created: $(date '+%Y-%m-%d %H:%M:%S %z')
Repository HEAD: $(git rev-parse HEAD)
Database: $DB_NAME
Database archive: database.sql.gz
Uploads archive: $([ -f "$TMP_DEST/uploads.tar.gz" ] && echo uploads.tar.gz || echo none)
Environment snapshot: env.snapshot
Checksum file: checksums.sha256
Retention: 14 days
EOF
chmod 600 "$TMP_DEST/manifest.txt"

(
  cd "$TMP_DEST"
  sha256sum -c checksums.sha256 >/dev/null
)

touch "$TMP_DEST/BACKUP_COMPLETE"
chmod 600 "$TMP_DEST/BACKUP_COMPLETE"

mv "$TMP_DEST" "$DEST"
trap - ERR INT TERM

# Keep completed backups for 14 days; incomplete directories older than one day are discarded.
find "$BACKUP_ROOT" -mindepth 1 -maxdepth 1 -type d -name '.incomplete-*' -mtime +1 -exec rm -rf {} +
find "$BACKUP_ROOT" -mindepth 1 -maxdepth 1 -type d ! -name '.incomplete-*' -mtime +14 -exec rm -rf {} +

echo "MCI_BACKUP=PASS"
echo "BACKUP_PATH=$DEST"
echo "BACKUP_INTEGRITY=PASS"
ls -lh "$DEST"
