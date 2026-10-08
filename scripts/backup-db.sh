#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="$(pwd)"
cd "$APP_DIR"

test -f .env || { echo "ERROR: .env not found"; exit 1; }
if command -v mariadb-dump >/dev/null 2>&1; then
  DUMP_BIN="mariadb-dump"
elif command -v mysqldump >/dev/null 2>&1; then
  DUMP_BIN="mysqldump"
else
  echo "ERROR: mariadb-dump/mysqldump is not installed"
  exit 1
fi

BACKUP_DIR="$APP_DIR/storage/backups/database"
mkdir -p "$BACKUP_DIR"

STAMP="$(date '+%Y-%m-%d_%H-%M-%S')"
BACKUP_FILE="$BACKUP_DIR/redbag_$STAMP.sql.gz"
CNF="$(mktemp)"
trap 'rm -f "$CNF"' EXIT
chmod 600 "$CNF"

MYSQL_CNF="$CNF" php -r '
$env = parse_ini_file(".env", false, INI_SCANNER_RAW);
foreach (["DB_HOST","DB_PORT","DB_DATABASE","DB_USERNAME","DB_PASSWORD"] as $key) {
    if (!array_key_exists($key, $env)) { fwrite(STDERR, "Missing $key in .env\n"); exit(1); }
}
file_put_contents(getenv("MYSQL_CNF"),
    "[client]\n".
    "host=".addcslashes($env["DB_HOST"] ?: "127.0.0.1","\\\"")."\n".
    "port=".($env["DB_PORT"] ?: "3306")."\n".
    "user=".addcslashes($env["DB_USERNAME"],"\\\"")."\n".
    "password=".addcslashes($env["DB_PASSWORD"],"\\\"")."\n"
);
'

DB_NAME="$(php -r '$e=parse_ini_file(".env",false,INI_SCANNER_RAW); echo $e["DB_DATABASE"];')"

$DUMP_BIN --defaults-extra-file="$CNF" \
  --single-transaction \
  --quick \
  --routines \
  --triggers \
  --no-tablespaces \
  "$DB_NAME" | gzip > "$BACKUP_FILE"

test -s "$BACKUP_FILE"
find "$BACKUP_DIR" -type f -name 'redbag_*.sql.gz' -mtime +14 -delete

echo "Database backup created: $BACKUP_FILE"
