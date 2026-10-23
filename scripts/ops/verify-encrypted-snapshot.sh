#!/usr/bin/env bash
# Non-destructive integrity check for a FoundationOS encrypted local snapshot.
# Does NOT connect to or restore into any database.
set -euo pipefail
umask 077

if [[ $# -ne 2 ]]; then
  echo "Usage: $0 SNAPSHOT_DIRECTORY AGE_IDENTITY_FILE" >&2
  exit 2
fi
command -v age >/dev/null || { echo "Missing age" >&2; exit 2; }
command -v tar >/dev/null || { echo "Missing tar" >&2; exit 2; }
DIR=$(realpath -e -- "$1")
IDENTITY=$(realpath -e -- "$2")
[[ -f "$DIR/SHA256SUMS" && -f "$DIR/metadata.txt" &&
   -f "$DIR/database.dump.age" && -f "$DIR/storage.tar.age" ]] ||
  { echo "Incomplete snapshot" >&2; exit 3; }
grep -qx 'schema=foundationos-local-snapshot-v1' "$DIR/metadata.txt" ||
  { echo "Unsupported snapshot format" >&2; exit 3; }

(cd "$DIR" && sha256sum -c SHA256SUMS)
age -d -i "$IDENTITY" "$DIR/database.dump.age" > /dev/null
age -d -i "$IDENTITY" "$DIR/storage.tar.age" | tar -tf - > /dev/null
echo "Checksum, age decryption, and tar archive integrity passed."
echo "A real database import + private file authorization drill on isolated staging remains REQUIRED."
