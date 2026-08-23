#!/bin/bash
set -euo pipefail

REPO="/home4/mcied45x/repositories/MCI-EDUCATIONAL-GROUP"
LOG_DIR="/home4/mcied45x/.cpanel/logs"
LOCK_FILE="/home4/mcied45x/.mci-auto-deploy.lock"
LOG_FILE="$LOG_DIR/mci-auto-deploy.log"

mkdir -p "$LOG_DIR"
exec 9>"$LOCK_FILE"
flock -n 9 || exit 0

{
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] Auto-deploy check started"
  cd "$REPO"

  if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
    echo "Tracked working tree changes detected; skipping automatic reset/deploy."
    exit 0
  fi

  OLD_SHA="$(git rev-parse HEAD)"
  git fetch origin main
  NEW_SHA="$(git rev-parse origin/main)"

  if [ "$OLD_SHA" = "$NEW_SHA" ]; then
    echo "No update. HEAD=$OLD_SHA"
    exit 0
  fi

  echo "Updating $OLD_SHA -> $NEW_SHA"
  git reset --hard origin/main

  echo "Starting cPanel deployment"
  uapi --output=json VersionControlDeployment create repository_root="$REPO"
  echo
  echo "Deployment queued for $NEW_SHA"
} >> "$LOG_FILE" 2>&1
