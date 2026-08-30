#!/bin/bash
set -euo pipefail

BACKUP_ROOT="/home4/mcied45x/backups/mci-educational-group"
TARGET="${1:-}"

if [ -z "$TARGET" ]; then
  TARGET="$(find "$BACKUP_ROOT" -mindepth 1 -maxdepth 1 -type d ! -name '.incomplete-*' -printf '%T@ %p\n' 2>/dev/null | sort -nr | head -1 | cut -d' ' -f2-)"
fi

if [ -z "$TARGET" ] || [ ! -d "$TARGET" ]; then
  echo "RESTORE_CHECK=FAIL"
  echo "ERROR=No completed backup found"
  exit 1
fi

for file in database.sql.gz env.snapshot manifest.txt checksums.sha256 BACKUP_COMPLETE; do
  if [ ! -f "$TARGET/$file" ]; then
    echo "RESTORE_CHECK=FAIL"
    echo "ERROR=Missing $file"
    exit 1
  fi
done

(
  cd "$TARGET"
  sha256sum -c checksums.sha256
)

gzip -t "$TARGET/database.sql.gz"

if [ -f "$TARGET/uploads.tar.gz" ]; then
  tar -tzf "$TARGET/uploads.tar.gz" >/dev/null
fi

DB_BYTES="$(gzip -dc "$TARGET/database.sql.gz" | wc -c | tr -d ' ')"
if [ "${DB_BYTES:-0}" -le 0 ]; then
  echo "RESTORE_CHECK=FAIL"
  echo "ERROR=Database dump is empty"
  exit 1
fi

echo "RESTORE_CHECK=PASS"
echo "BACKUP_PATH=$TARGET"
echo "DATABASE_UNCOMPRESSED_BYTES=$DB_BYTES"
echo "ENV_SNAPSHOT=READY"
if [ -f "$TARGET/uploads.tar.gz" ]; then
  echo "UPLOADS_ARCHIVE=READY"
else
  echo "UPLOADS_ARCHIVE=NOT_PRESENT"
fi
