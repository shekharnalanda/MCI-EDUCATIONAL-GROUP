#!/bin/bash
set -uo pipefail

REPO="/home4/mcied45x/repositories/MCI-EDUCATIONAL-GROUP"
PHP="/opt/cpanel/ea-php83/root/usr/bin/php"
BASE_URL="https://mciedu.in"
PASS=0
FAIL=0

cd "$REPO" || exit 1

ok() {
  echo "PASS  $1"
  PASS=$((PASS+1))
}

bad() {
  echo "FAIL  $1"
  FAIL=$((FAIL+1))
}

check_code() {
  local path="$1"
  local expected="$2"
  local code
  code=$(curl --retry 2 --retry-delay 1 --connect-timeout 10 --max-time 25 -sS -o /dev/null -w "%{http_code}" "$BASE_URL$path" 2>/dev/null || true)
  if [ "$code" = "$expected" ]; then
    ok "$path HTTP $code"
  else
    bad "$path expected $expected got ${code:-000}"
  fi
}

echo "===== MCI AUTOMATIC HEALTH CHECK ====="
echo "Time: $(date '+%Y-%m-%d %H:%M:%S %z')"
echo "HEAD: $(git rev-parse --short HEAD 2>/dev/null || echo unknown)"

# Core Laravel checks
if "$PHP" artisan migrate:status >/dev/null 2>&1; then
  ok "Database connection and migrations"
else
  bad "Database connection or migrations"
fi

if [ -L public/storage ] && [ -e public/storage ]; then
  ok "Public storage link"
else
  bad "Public storage link"
fi

if "$PHP" artisan route:list >/dev/null 2>&1; then
  ok "Laravel routes load"
else
  bad "Laravel routes load"
fi

# Essential database content can be read without changing live data.
DB_CHECK=$($PHP artisan tinker --execute="
try {
    echo App\\Models\\Institution::count().'|'.App\\Models\\Setting::count();
} catch (Throwable \$e) {
    echo 'ERROR';
}
" 2>/dev/null | tail -1)

if echo "$DB_CHECK" | grep -Eq '^[0-9]+\|[0-9]+$'; then
  institutions=${DB_CHECK%%|*}
  settings=${DB_CHECK##*|}
  if [ "$institutions" -ge 1 ] && [ "$settings" -ge 1 ]; then
    ok "Database content readable (institutions=$institutions settings=$settings)"
  else
    bad "Required database content missing (institutions=$institutions settings=$settings)"
  fi
else
  bad "Database content model check"
fi

# Public website smoke checks
for path in / /about /institutions /programs /news-events /gallery /downloads /career /contact /admin/login; do
  check_code "$path" 200
done

# Protected admin pages must redirect guests to the admin login page.
for path in /admin /admin/institutions /admin/news /admin/gallery /admin/downloads /admin/enquiries /admin/attendance /admin/settings; do
  code=$(curl --retry 2 --retry-delay 1 --connect-timeout 10 --max-time 25 -sS -o /dev/null -w "%{http_code}" "$BASE_URL$path" 2>/dev/null || true)
  location=$(curl --retry 2 --retry-delay 1 --connect-timeout 10 --max-time 25 -sSI "$BASE_URL$path" 2>/dev/null | tr -d '\r' | awk 'BEGIN{IGNORECASE=1} /^Location:/{print $2; exit}')
  if [ "$code" = "302" ] && [ "$location" = "$BASE_URL/admin/login" ]; then
    ok "$path guest protection"
  else
    bad "$path guest protection (HTTP ${code:-000}, Location ${location:-none})"
  fi
done

echo "===== RESULT ====="
echo "PASS=$PASS FAIL=$FAIL"

if [ "$FAIL" -eq 0 ]; then
  echo "MCI_HEALTH=PASS"
  exit 0
fi

echo "MCI_HEALTH=FAIL"
exit 1
