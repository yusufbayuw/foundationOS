# Issue #74 — Application, queue, scheduler, backup and recovery validation

**Assessment:** 25 October 2026 · **Source:** [foundationOS](https://github.com/yusufbayuw/foundationOS), `main` `5e4a2830aff0a83986d8f636530f31a72432d871` · **Classification:** repository/CI evidence only; not staging certification.

**Overall release recommendation: NO-GO.** No production/staging SSH, PHP-FPM, Supervisor, database, Cloudflare or offsite-backup access was exercised in this assessment.

## 1. Verified on exact repository SHA

| Control | Outcome and evidence | Scope |
|---|---|---|
| Laravel route/config/scheduler bootstrap | **PASS**: [Release Contract Smoke #40154226187](https://github.com/yusufbayuw/foundationOS/actions/runs/40154226187), validates `config:cache`, GET `/up`, `schedule:list` registrations | Registration in isolated CI only |
| PHPUnit, 80% coverage, tenant/queue/policy/regression/feature, PostgreSQL | **PASS**: [Tests #40154226149](https://github.com/yusufbayuw/foundationOS/actions/runs/40154226149) | Test data, not live workers |
| MariaDB migration smoke | **PASS**, within same Tests run | Empty disposable CI DB, not live schema upgrade |
| Encrypted archive, decrypt/integrity and isolated real PostgreSQL 16 + MariaDB 11.4 restores | **PASS**: [Backup Tool Contract #40154226237](https://github.com/yusufbayuw/foundationOS/actions/runs/40154226237) | Isolated **synthetic fixture** DBs and private/public files only |
| Static Analysis / Pint / Larastan | **PASS**: [Static Analysis #40154226208](https://github.com/yusufbayuw/foundationOS/actions/runs/40154226208) | Source quality |
| Supply chain / security dependencies | **FAILED**: [Quality and Supply Chain #40154226239](https://github.com/yusufbayuw/foundationOS/actions/runs/40154226239), job `Dependency security (High and Critical)`; [CVE-2026-114509, Laravel TokenGuard](https://github.com/yusufbayuw/foundationOS/issues/121) | **P0**: patch dependency lock and confirm full CI green; never suppress Composer audit |

A green `/up` route registration does not prove HTTP requests succeed through the deployed Nginx/aaPanel/Cloudflare ingress.

## 2. Source configuration audit

- `bootstrap/app.php` registers GET `/up`, but **still invokes `trustProxies('*')`** on `main`. [Draft PR #80](https://github.com/yusufbayuw/foundationOS/pull/80) must remain held until actual ingress route and forged-forwarding-header staging tests pass. A green CI cannot establish the trusted proxy IP chain.
- `config/queue.php` defaults to **database** when `QUEUE_CONNECTION` is unspecified; Redis is supported. Database/Redis `retry_after` defaults to **90 seconds**. Actual configured backend, queue names, worker `--timeout`, process count, Supervisor definitions, failed jobs, oldest queue age, duplicate delivery, and backpressure are **not verified** on the server. The required invariant is `worker timeout < retry_after` and exclusive worker ownership for explicitly named queues.
- `routes/console.php` registers **28 scheduled command chains** spanning Moodle, library, billing/invoices, reporting, tuition, legal, helpdesk, assets, DMS, IT ops, cafeteria, safety, counseling, communications, alumni, enrollment, donations, training, printing, property and workflow escalation. CI checks **registration**, not scheduled execution, single-runner ownership, backlogged event age or protected integrations.
- Snapshot utility exports **one MySQL/MariaDB or PostgreSQL database and local `storage/app`** to age-encrypted archives. It requires an operator-supplied `FOS_BACKUP_WRITES_QUIESCED=yes`; that variable **does not actually pause writers**. It does not include external S3 disks, separate APP_KEY escrow, offsite/immutable retention, transaction logs/PITR, or Moodle/payment systems. Database/file consistency is only guaranteed if operators actually quiesce application and external writes.
- [Issue #122](https://github.com/yusufbayuw/foundationOS/issues/122) requires private MATCH / NO MATCH classification of historic public CI `APP_KEY` versus each deployed environment, **without exposing any key or fingerprint**. Do not rotate blindly.
- There is no demonstrated real site restore, operational recovery metrics or signed RPO/RTO targets in the repository.

## 3. Safe operator-side runtime validation

From an **authorized staging shell**, verify the actual Laravel directory and PHP 8.4 executable first. The path must not be a guess; aaPanel installations often use a nonstandard PHP path. Execute:

```bash
export FOS_PREFLIGHT_APP_DIR=/verified/staging/release/path
export FOS_PREFLIGHT_PHP_BIN=/verified/php84/bin/php
bash "$FOS_PREFLIGHT_APP_DIR/scripts/ops/preflight-readonly.sh"
```

The script does **not** execute `schedule:run`, `queue:work`, `queue:retry`, migrations, seeds, restore or any backup. It checks app configuration policy, PHP 8.4, GET `/up` registration, scheduler task definitions, and queue command registration **without printing secrets or raw exception traces**. **PASS is not a claim about queue liveness or recovery**.

Record results only in an access-controlled operator ticket; in public GitHub report only test outcome, exact deployed commit SHA (or documented release artifact identity), UTC observation time and sanitized status. Never paste `.env`, DB URL, secrets, cookies, raw queue payloads, IP topology or identities.

### Mandatory extra observations (read-only)

| Domain | Evidence for PASS | Must be executed on staging |
|---|---|---|
| Application | GET `/up` returns expected status through real ingress; authenticated login/tenant A/B access and private upload denial tests pass; HTTPS/Secure/SameSite cookie, real client IP/proxy and rate limiting pass | Yes |
| Queue | Verified Supervisor worker state and process ownership; configured connection and named queue processing (non-sensitive designated fixture); confirmed no job stuck/failed; worker timeout and `retry_after` ordering | Yes; use approved disposable fixture, not live business transactions |
| Scheduler | Exactly one legitimate tick source; observe consecutive ticks, overlapping-task prevention and due command outcomes, including Moodle outbox, billing and SLA | Yes; **do not** run `schedule:run` manually on production |
| Backup | Encrypt snapshot of staging database + all configured local storage, encrypted offsite transfer with restricted permissions and immutable retention; key escrow verified by authorized operator | Yes, scheduled/tested and independently observed |
| Recovery | Restore staging snapshot to **different** isolated database and storage; disable outbound payment/Moodle/mail/webhooks; verify record counts, per-tenant privacy, private files, schema compatibility and queue data consistency | Yes, NOT production; controlled change |
| RPO/RTO | Owner approves numerical targets; recoverer logs backup point, interruption/restart timestamps, data gap, app acceptance and measured RPO/RTO | Yes; no target is inferred |
| Rollback | Verify previous deploy artifact and compatibility across migrated schema; app/worker restart, cache and external reconciliation runbook rehearsal | Yes, non-destructive staging |

See [Staging Evidence Capture](./STAGING_EVIDENCE_CAPTURE.md) and [Release & DR Runbook](./RELEASE_AND_DR_RUNBOOK.md). For live restoration do not replay unsafely queued jobs or resume external integrations without module-owner review.

## 4. Release blockers and gate owners

| Severity | Blocker | Owner / disposition |
|---|---|---|
| **P0** | Critical Composer advisory [#121](https://github.com/yusufbayuw/foundationOS/issues/121); mandatory audit RED | Security; approve patched lock and auth negative tests |
| **P0** | Historical APP_KEY live reuse [#122](https://github.com/yusufbayuw/foundationOS/issues/122) not classified | Authorized server operator + Security; privately classify, safe rotation only on MATCH |
| **P0** | Wildcard trusted proxies in deployed code [#72](https://github.com/yusufbayuw/foundationOS/issues/72)/[PR #80](https://github.com/yusufbayuw/foundationOS/pull/80); unknown ingress behavior | Security + SRE; staging evidence before changing |
| **P0** | No representative staging encrypted offsite backup and **actual isolated restore** | SRE + operator; RPO/RTO measured |
| **P0** | No integrated staging app/tenant permissions, migrations and rollback evidence | QA + domain agents; sign-off |
| **P1** | Worker, scheduler, outbox and alert-delivery operational evidence absent | SRE + operator; establish validated alerts |
| **P1** | Actual branch protection required checks not available to connector (403) | GitHub repository administrator; verify enforced rules |

**Decision: NO-GO** until P0 gates close and the Project Owner explicitly approves a candidate SHA and production window. No production deployment, database write, main merge or credential exposure is authorized by this document.

## 5. New CI artifact from this issue

An added *safe preflight integration* verifies that the operator script can boot Laravel and examine routes/schedules in isolated GitHub Actions CI, with an explicit-target negative test. This **does not** prove production runtime health; check current PR run status before claiming the new validation passed.
