# Encrypted local backup utility (operator-assisted)

**Scope:** non-destructive CLI snapshot for **local** `storage/app` plus **one** MySQL/MariaDB or PostgreSQL database. **Status: candidate only, NOT an activated production backup service.** Complements [issue #74](https://github.com/yusufbayuw/foundationOS/issues/74); the full recovery protocol is in PR #105.

## Guarantees and limits

- Requires a human-confirmed `FOS_BACKUP_WRITES_QUIESCED=yes`; refuses to capture if this is absent or different. This flag is an **attestation only**: the script cannot pause PHP, cron, queues, external webhooks, or database writers itself. Operator must actually quiesce all writes first.
- Streams dumps and tar to `age` recipients, without intentionally staging plaintext dump/archive on disk. Writes into a restrictive temporary directory and atomically publishes ciphertext plus SHA-256 checksums on success; fails with nonzero exit status.
- Requires externally managed credentials (MySQL client option file or PostgreSQL `pgpass`) and an age public recipient. Never put credentials in command-line arguments, cron contents or GitHub. Protect recipient identity/decryption keys separately.
- Covers `storage/app` **only**. Configured S3/external disks, paths outside storage/app, `.env`/`APP_KEY`, Redis data, external Moodle records, and transaction logs/PITR need **separate verified backup mechanisms**. A snapshot here alone cannot satisfy production DR.
- Encrypted backup is not automatically copied off-host/off-account. An independent immutable/offsite replication system and checks are required.
- MariaDB transactional consistency assumes reviewed InnoDB tables and operational quiescence; PostgreSQL produces `pg_dump -Fc`. Extension/version/multi-database consistency is outside this script.
- `verify-encrypted-snapshot.sh` checks checksums, decryptability, and tar structure but **does not import a database**. Real isolated restore remains mandatory.

## Prerequisites

Bash, `realpath`, `tar`, `sha256sum`, `age`, and the relevant database dump binary (`mariadb-dump`/`mysqldump` or `pg_dump`). Test binaries/permissions against the **actual** aaPanel/Debian/Ubuntu host first. Destination must already exist, be writable and not under the Laravel application tree.

Before operating on real data, compare all filesystem disks with `config/filesystems.php`, module-specific storage code and environment inventory. Arrange a consistent snapshot window, encrypted offsite replication, age identity escrow, and a documented isolated restore test.

## Operator-only environment (examples; no credentials)

```bash
export FOS_BACKUP_APP_DIR=/verified/path/to/foundationos
export FOS_BACKUP_DESTINATION=/verified/private/encrypted-snapshots
export FOS_BACKUP_DB_DRIVER=pgsql        # or mysql/mariadb
export FOS_BACKUP_DB_NAME=verified_database_name
export FOS_BACKUP_DB_HOST=verified_database_host
export FOS_BACKUP_DB_PORT=5432
export FOS_BACKUP_DB_USER=restricted_backup_user
export FOS_BACKUP_AGE_RECIPIENT=age1REPLACE_WITH_PUBLIC_RECIPIENT
export FOS_BACKUP_PGPASSFILE=/protected/path/to/pgpass
# For mysql/mariadb instead use FOS_BACKUP_MYSQL_DEFAULTS_FILE=/protected/path/to/my.cnf

# Set this to yes ONLY after actual web/scheduler/queue/integration writes are quiesced.
export FOS_BACKUP_WRITES_QUIESCED=yes
bash scripts/ops/snapshot-local-encrypted.sh
```

`FOS_BACKUP_AGE_RECIPIENT` is the **public** encryption recipient, never the private identity. Maintain the private age identity file in the approved secret store/offline escrow and restrict access (0600). The snapshot path is printed by the utility; it contains `database.dump.age`, `storage.tar.age`, `metadata.txt` and `SHA256SUMS`.

## Verify, replicate, drill

```bash
bash scripts/ops/verify-encrypted-snapshot.sh /private/snapshots/snapshot-UTC-ID /protected/age-identity.txt
```

An SRE must then copy the whole snapshot directory (ciphertext and integrity manifest) to a separately controlled, immutable/offsite destination, confirm hash match, and simulate loss of the origin. For the actual restore drill: build a **new** isolated database and storage target, disable outbound integrations and workers, decrypt and import the database with the matching engine/version, restore file data, verify tenant isolation/private uploads, and measure RPO/RTO. Never point restored staging toward live Moodle, payment endpoints, mail or production DB. This is deliberately a **manual reviewed** database restore, not an automated overwrite script.

## CI verification

`.github/workflows/backup-contract.yml` tests bash syntax, refuses backup without quiescence attestation, creates a ciphertext snapshot of fake files and a **mock** PostgreSQL dump, verifies decryption/manifest, and detects tampering. CI **cannot** prove a real PostgreSQL/MySQL restore, offsite durability, or production write quiescence.

No automatic cron or production activation is provided until the server topology, key management, backup frequency, privileges, storage inventory, integrity drill and Project Owner approval are established.
