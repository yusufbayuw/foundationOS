#!/usr/bin/env bash
# Capture only explicitly scoped *local* Laravel files + DB in encrypted archives.
# IMPORTANT: Quiesced application writes and separate offsite copying are operator responsibilities.
set -euo pipefail
umask 077

required=(FOS_BACKUP_APP_DIR FOS_BACKUP_DESTINATION FOS_BACKUP_DB_DRIVER
          FOS_BACKUP_DB_NAME FOS_BACKUP_DB_HOST FOS_BACKUP_DB_PORT
          FOS_BACKUP_DB_USER FOS_BACKUP_AGE_RECIPIENT FOS_BACKUP_WRITES_QUIESCED)
for name in "${required[@]}"; do
  if [[ -z "${!name:-}" ]]; then
    echo "Missing required configuration: $name" >&2
    exit 2
  fi
done

if [[ "$FOS_BACKUP_WRITES_QUIESCED" != "yes" ]]; then
  echo "Refusing backup: confirm scheduler, workers, web writes and external integrations are quiesced." >&2
  exit 3
fi

command -v age >/dev/null || { echo "Missing age encryption executable" >&2; exit 2; }
command -v tar >/dev/null || { echo "Missing tar executable" >&2; exit 2; }
command -v sha256sum >/dev/null || { echo "Missing sha256sum" >&2; exit 2; }

APP_DIR=$(realpath -e -- "$FOS_BACKUP_APP_DIR")
DEST=$(realpath -e -- "$FOS_BACKUP_DESTINATION")
if [[ ! -d "$APP_DIR/storage/app" || -L "$APP_DIR/storage/app" ]]; then
  echo "Local storage/app must exist as a directory, not a symlink." >&2
  exit 3
fi
case "$DEST/" in
  "$APP_DIR/"*) echo "Destination cannot be inside the Laravel application tree." >&2; exit 3 ;;
esac
if [[ ! -w "$DEST" || ! -d "$DEST" || -L "$DEST" ]]; then
  echo "Destination is not an existing writable non-symlink directory." >&2
  exit 3
fi

# Prevent option injection through database identifiers; advanced names need a reviewed adapter.
if [[ ! "$FOS_BACKUP_DB_NAME" =~ ^[a-zA-Z0-9_]+$ ]]; then
  echo "Unsupported database identifier for safe command invocation." >&2
  exit 2
fi
case "$FOS_BACKUP_DB_DRIVER" in
  mysql|mariadb)
    : "${FOS_BACKUP_MYSQL_DEFAULTS_FILE:?Provide path to private mysql client option file}"
    [[ -f "$FOS_BACKUP_MYSQL_DEFAULTS_FILE" && -r "$FOS_BACKUP_MYSQL_DEFAULTS_FILE" ]] || exit 2
    MYSQL_DUMP=$(command -v mariadb-dump || command -v mysqldump || true)
    [[ -n "$MYSQL_DUMP" ]] || { echo "Missing mariadb-dump/mysqldump" >&2; exit 2; }
    ;;
  pgsql)
    : "${FOS_BACKUP_PGPASSFILE:?Provide path to private pgpass file}"
    [[ -f "$FOS_BACKUP_PGPASSFILE" && -r "$FOS_BACKUP_PGPASSFILE" ]] || exit 2
    command -v pg_dump >/dev/null || { echo "Missing pg_dump" >&2; exit 2; }
    ;;
  *)
    echo "Unsupported DB driver (mysql, mariadb, pgsql only)." >&2
    exit 2
    ;;
esac

STAGE=$(mktemp -d "$DEST/.fos-backup-staging.XXXXXXXX")
cleanup() {
  if [[ -n "$STAGE" && -d "$STAGE" ]]; then rm -rf -- "$STAGE"; fi
}
trap cleanup EXIT

case "$FOS_BACKUP_DB_DRIVER" in
  mysql|mariadb)
    "$MYSQL_DUMP" --defaults-extra-file="$FOS_BACKUP_MYSQL_DEFAULTS_FILE" \
      --single-transaction --quick --routines --triggers --events \
      --default-character-set=utf8mb4 --skip-lock-tables \
      --host="$FOS_BACKUP_DB_HOST" --port="$FOS_BACKUP_DB_PORT" \
      --user="$FOS_BACKUP_DB_USER" "$FOS_BACKUP_DB_NAME" |
      age -r "$FOS_BACKUP_AGE_RECIPIENT" -o "$STAGE/database.dump.age"
    ;;
  pgsql)
    PGPASSFILE="$FOS_BACKUP_PGPASSFILE" pg_dump --no-password --format=custom \
      --host="$FOS_BACKUP_DB_HOST" --port="$FOS_BACKUP_DB_PORT" \
      --username="$FOS_BACKUP_DB_USER" --dbname="$FOS_BACKUP_DB_NAME" |
      age -r "$FOS_BACKUP_AGE_RECIPIENT" -o "$STAGE/database.dump.age"
    ;;
esac

# tar does not follow symlinks by default. Review custom filesystems/S3 separately.
tar -C "$APP_DIR" -cf - storage/app |
  age -r "$FOS_BACKUP_AGE_RECIPIENT" -o "$STAGE/storage.tar.age"

printf 'schema=foundationos-local-snapshot-v1\nengine=%s\ncreated_utc=%s\nscope=storage/app-only\n' \
  "$FOS_BACKUP_DB_DRIVER" "$(date -u +%Y-%m-%dT%H:%M:%SZ)" > "$STAGE/metadata.txt"
(cd "$STAGE" && sha256sum database.dump.age storage.tar.age metadata.txt > SHA256SUMS)
(cd "$STAGE" && sha256sum -c SHA256SUMS >/dev/null)

# Avoid overwriting backups; atomic rename inside the same destination filesystem.
SNAPSHOT="snapshot-$(date -u +%Y%m%dT%H%M%SZ)-$$"
[[ ! -e "$DEST/$SNAPSHOT" ]] || { echo "Snapshot name collision" >&2; exit 3; }
mv -- "$STAGE" "$DEST/$SNAPSHOT"
STAGE=""
echo "Encrypted local snapshot created: $DEST/$SNAPSHOT"
echo "NOT production-certified until independent offsite copy and isolated DB+file restore."
