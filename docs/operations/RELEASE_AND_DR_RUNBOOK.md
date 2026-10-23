# FoundationOS — Release, Monitoring, Backup & Recovery Runbook

**Tracking:** #74 and #72 · **Status:** operations procedure, **not** proof of production readiness. Server assumptions must be verified by the operator. **No commands below authorize production deployment or destructive migrations.**

## 1. Ownership and release authorization

- Project Owner authorizes the release window, target environment, migration plan, and any rollback decision.
- DevOps/SRE collects CI status, release SHA, backup/restore evidence, infrastructure inventory, and on-call coverage.
- Module owners sign off tenant isolation, financial consistency, enrollment, uploads, Moodle integration and other critical journeys.
- Preserve a change ticket containing release SHA, timestamp/timezone, who approved, migration diff, maintenance window, test results, rollback artifact and incident contacts.
- Do not combine security ingress changes (PR #80) with an unrelated business-release cutover.

## 2. Baseline evidence (must be filled from **the real server**)

| Item | Staging | Production |
|---|---|---|
| FQDN, TLS termination point and certificate expiry | TODO | TODO |
| Nginx/aaPanel virtual host, document root, PHP-FPM socket, effective PHP 8.4 executable | TODO | TODO |
| Network path including Cloudflare (if used), actual REMOTE_ADDR, forwarded-header sanitization | TODO | TODO |
| Database type/version, replication and transactional backup method | TODO | TODO |
| Cache/session backend and shared cache/Redis isolation | TODO | TODO |
| Queue backend, named queues, process supervisor definition, worker count and retry_after | TODO | TODO |
| One scheduler runner/cron and timezone | TODO | TODO |
| Public/private upload disks, object store, lifecycle/versioning, encryption keys | TODO | TODO |
| Outbound integrations (Moodle, mail, payment, exam runtime, webhooks) | TODO | TODO |
| Existing alerts, backup repository, recovery owners and escalation contact | TODO | TODO |

Never paste private addresses, credentials, cookies, tokens or personal user records into issues/PRs. Store the protected server inventory in an approved secret/document system.

## 3. Release preflight (read-only until owner approval)

1. Pin the candidate commit SHA; require the existing **Tests**, **Static Analysis**, **Quality and Supply Chain**, **Translation Lint**, **Tenant Field Lint**, and new **Release Contract Smoke** checks to pass at **that SHA**. A green unrelated branch or older commit is not sufficient.
2. Review Composer/npm security audits, migrations, permissions, new scheduler jobs and external side effects. Build assets from the committed `package-lock.json` using Node 22 and `npm ci && npm run build` in a separate build/release directory; `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader` there.
3. Check the resolved PHP binary and extensions, Node, Composer, Nginx syntax, SSL certificate, disk/inodes, database reachability, Redis when used, file permissions and writable storage. Do **not** assume aaPanel binaries or service names; inspect actual paths.
4. Confirm the production `.env` is external to Git, permissions restricted, and `APP_KEY` is **preserved** across updates. `APP_ENV=production`, `APP_DEBUG=false`, HTTPS `APP_URL`, effective session cookie flags, tenant isolation settings, disk/queue/cache and outbound services must be reviewed without printing secret values.
5. Before any migration, **read migration sources** and capture `php artisan migrate:status`. Classify schema changes (additive, locking, data backfill, destructive). Never run `migrate:fresh`, `db:wipe`, automatic seeds, or blanket `migrate:rollback` on live data.
6. Require a recent encrypted backup with a **successful isolated restore drill** and checksum manifest (see §6). Back up files and database as a coordinated recovery point.
7. Run a staging rehearsal on sanitized production-shaped data, including tenant isolation, login/HTTPS/session, public and private uploads, document export, Mail, Moodle outbox/reconciliation, billing, queue workers and scheduler.
8. Validate trusted proxies using [INGRESS_STAGING_VALIDATION.md](./INGRESS_STAGING_VALIDATION.md) before making PR #80 eligible for merge.
9. Record operator/owner GO or NO-GO decision. If the server evidence or rollback plan is missing, **NO-GO**.

### Operational read-only commands (examples)

Execute in the verified release directory with the verified PHP executable; do **not** include env contents or credentials in output.

```bash
php -v
composer --version
node --version
php artisan about
php artisan route:list --path=up
php artisan schedule:list
php artisan migrate:status
php artisan queue:failed
df -h
df -i
# sudo nginx -t            # only after confirming the real Nginx instance
# sudo supervisorctl status # only after confirming Supervisor is used
# curl -fsS -o /dev/null -w '%{http_code}\n' https://YOUR_VERIFIED_HOST/up
```

For `queue:failed` do not replay failures indiscriminately; retries may duplicate outbound effects. Confirm each job's idempotency.

## 4. Controlled deployment procedure (only with production approval)

1. Announce maintenance window and freeze writes/integrations as required; capture release SHA, database schema state, active worker identities, backup timestamp and previous release artifact. Verify the backup offsite.
2. Ensure scheduler runs from **exactly one** approved instance. Pause schedules and prevent processing of non-idempotent jobs before schema transitions. Drain/stop actual workers under the identified process manager; do not kill in-flight jobs unless incident response requires it.
3. Prepare dependencies/assets outside the serving directory. Share preserved `.env` and `storage` (or verified object store) with the new release. Never rebuild `APP_KEY` on update.
4. Apply only approved, reviewed **non-destructive** schema changes with `php artisan migrate --force` after explicit owner authorization. When zero downtime is required, use an expand/contract migration across compatible releases.
5. Switch web application to the approved artifact using the server's verified method (atomic release symlink is preferred only where supported). Build/refresh Laravel config cache (`php artisan config:cache`). Reload PHP-FPM/OPcache only via the actual service management plan.
6. Run `/up`, TLS, login/session, upload/download access-control and tenant smoke tests. Resume scheduler and workers with the approved queue list; `php artisan queue:restart` politely recycles running workers but **does not start them**.
7. Closely observe 5xx, latency, outboxes, failed jobs, queue age, tenant access denials and storage. Close the incident/change record only after post-release checks and stakeholder approval.

**Rollback:** return the application artifact and cached configuration to the last compatible SHA, recover the previous queue-worker configuration and re-run smoke tests. Database migrations are **not** automatically reversible; irreversible changes require a forward fix or an approved point-in-time data recovery plan. Rolling back data to an older point can discard new transactions and must be authorized and coordinated with external systems. Preserve forensic logs and avoid repeated retries during incident triage.

## 5. Monitoring and alerting contract

Set up **external** alert delivery and verify that a test alert reaches on-call. An HTTP 200 from `/up` is a liveness signal, not comprehensive readiness.

| Signal / source | Initial actionable threshold (proposal; calibrate in staging) | Action |
|---|---|---|
| External TLS + GET `/up` | 2 consecutive failures | Check ingress, FPM, deploy SHA, origin |
| Web 5xx & latency | Error rate >1% for 5 min or sudden p95 regression | Check application logs and last release |
| Queue backlog, oldest waiting job | Age >5 min for critical queues | Inspect supervisor, queue connection, failed jobs |
| `php artisan queue:failed` | Any unexpected increase | Triage type, duplicate-processing risk and owner |
| Scheduler heartbeat per environment | Not observed for >2 min for every-minute jobs | Check single cron/scheduler runner, locks |
| `fos:moodle:ops-report --json` | Failed/stuck outbox or sustained growth | Check connectivity/retry/reconciliation safely |
| Invoice/billing/webhook outbox, overdue approvals | Beyond module SLA, threshold set by owner | Inspect idempotency and workflow ownership |
| Backup freshness & off-site replication | Missed agreed cadence, checksum mismatch | Escalate and suspend risky changes |
| Disk free and DB capacity | Sustained >80% utilization | Capacity/retention response, no blind deletions |
| TLS certificate, integrations/secret expiry | 30/14/7 days before expiry | Rotate and verify without exposing keys |

Every tenant-sensitive metric/log must avoid personal data and cross-tenant visibility; aggregate tenant identifiers only in authorized views. Implement alert routing, quiet hours, acknowledgement and runbook links. Record the telemetry source and retention policy. **These alerts are specifications, not currently verified implementations.**

## 6. Backup, restore and disaster recovery

### Define RPO/RTO before go-live

Project Owner must approve **explicit numerical RPO and RTO targets** per data category and institution (student records, finance, private documents, Moodle mappings, outboxes) **before any production go-live**. No numerical target is assumed by this runbook. Only a successful measured recovery drill and an appropriate backup/replication interval may support a claim that targets can be achieved. Establish retention, legal holds and data residency rules with the data owner; daily full backups alone cannot satisfy a tighter RPO than their restorable recovery points.

### Backup standard

- Database: transactional consistent snapshot using `mariadb-dump`/`mysqldump --single-transaction` for verified InnoDB setups, or PostgreSQL `pg_dump -Fc`/WAL or provider snapshots as applicable; include triggers/routines/events where relevant. Verify engine, lock behavior, size and point-in-time compatibility. Prefer database-native encrypted snapshot/PITR for large datasets.
- Files: include `storage/app/private`, `storage/app/public`, all non-default disks and object-store versions; avoid assuming `public/storage` symlink is the original data. Coordinate DB snapshot and file/object versions for a consistent recovery point. Back up `APP_KEY` and external config **separately** with tightly controlled, encrypted secret management, as loss of APP_KEY may make encrypted content unrecoverable.
- Encrypt at rest **and** in transit, use credentials inaccessible to the application user, and a second off-host/off-account immutable or write-protected copy. Never put DB passwords on a cron command line or in a repository. Do not use `aws s3 sync --delete` on the sole recovery destination.
- Record timestamp (UTC), SHA/release, database engine/version, schema migration state, artifact/checksum hashes, storage snapshot identifier, object count, encryption key reference, backup outcome, operator and expiry. Monitor backup completion and test checksum/restore integrity.
- A backup is **not certified** until an operator restores it to a **distinct isolated staging instance** and verifies data and private files.

### Restore drill — isolated environment, never in-place production

1. Create a separate isolated DB and storage namespace, block internet/outbound integration access, disable cron/workers/webhooks/mail and set test-only credentials. Never reuse production API/payment/Moodle secrets on restored staging.
2. Confirm source backup checksum, decrypt using access-controlled key, restore into a **new** empty database with matching engine version and required extensions. Preserve production DB and storage untouched.
3. Restore the matching file/object snapshot to a **separate** path/bucket; verify both public and private downloads with correct authorization. Validate DB referential integrity, sample tenant boundaries, migration status, queue/outbox counts and reconciliation records.
4. Switch restored staging app to the matching release SHA and its environment-specific configuration. Run read-only migrations status and smoke tests; do not replay queued financial/payment messages or trigger Moodle mutations.
5. Time restore from incident start to verified service; calculate RPO based on last restorable transaction and RTO from observed drill. Capture evidence with redacted output, date, operator, failures and remediation actions.
6. Before **any** production restore, obtain Project Owner approval and decide transaction cut-off, external payment/email/Moodle reconciliation, user notification and legal/data retention requirements.

**Drill cadence proposal:** at least quarterly, after changes to backup format/database/storage and before initial production release. Alert on any failed backup, backup with unknown decryption key, or failed quarterly restore drill.

## 7. Exit checklist (must have evidence, not assumptions)

- [ ] Release candidate SHA and all required green check results recorded
- [ ] Staging effective ingress/client IP and HTTPS/session/rate limits verified
- [ ] PHP 8.4, aaPanel/Nginx, database and worker topology recorded
- [ ] Real cron scheduler and named queue processing verified, including failure/recovery
- [ ] Private/public upload restore and cross-tenant authorization tested
- [ ] Backup encrypted/offsite with checksum, successful isolated restore, measured RPO/RTO
- [ ] Outbox/reconciliation and external side-effect test cases passed
- [ ] Alert delivery tested and on-call acknowledged; security logs redacted
- [ ] Migration and previous artifact rollback compatibility approved
- [ ] Project Owner explicitly approved production deployment

Unmet boxes mean **NO-GO**. Do not equate passing GitHub Actions with completed production UAT.
