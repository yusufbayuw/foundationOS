# FoundationOS Production Readiness — Evidence Register

**Assessment date:** 2026-10-22 (Asia/Jakarta)  
**Assessment owner:** Principal DevOps/SRE/QA  
**Requested issues:** [#74](https://github.com/yusufbayuw/foundationOS/issues/74), [#72](https://github.com/yusufbayuw/foundationOS/issues/72)  
**Recommendation:** **NO-GO for production**. This is a reproducible evidence register, not a declaration that infrastructure has been accessed or certified.

## 1. Scope and evidence rules

- **Verified** means a named GitHub Actions job completed successfully on the stated exact head SHA, or source was inspected in GitHub. An in-progress CI run is not green.
- **Pending** means a test/run is queued, incomplete or has not yet been independently validated.
- **Not verified** means the required data cannot be established through this repository/GitHub integration. Never fill a gap with an assumption about aaPanel, Nginx, Cloudflare, database, object storage or service topology.
- **No action taken on production**; no migration, restore, branch merge or secret changes have been made.
- Branch protection read request returned GitHub **403** (integration lacks the required administration permission). Repository-level rulesets query returned no rulesets, which **does not prove** no classic protection exists.

## 2. PR-specific release evidence

| PR | Branch / head SHA at last observed verification | Evidence | Release gate |
|---|---|---|---|
| [#80](https://github.com/yusufbayuw/foundationOS/pull/80) | `fix/security-explicit-trusted-proxies-20261022` / `3cf062debbe9197b49a4a570fb2f9216db0ed1bc` | Five workflows succeeded (see [PR CI](https://github.com/yusufbayuw/foundationOS/actions/runs/39723257394)); source review identifies incomplete trusted-ingress/rate-limit test coverage | **HOLD DRAFT**, staging topology evidence mandatory |
| [#101](https://github.com/yusufbayuw/foundationOS/pull/101) | `agent/devops-release-contract-ci-20261022` / `dd6a8e8edad5b9e82ac0fec230adfa5dbfc12f0f` | Six workflows succeeded, including [Laravel release contract smoke](https://github.com/yusufbayuw/foundationOS/actions/runs/39756078348) | **Reviewable**, does not replace staging |
| [#105](https://github.com/yusufbayuw/foundationOS/pull/105) | `agent/devops-safe-production-runbooks-20261022`; head evolves as documentation is refined | Five workflows succeeded at prior head `215f82bf9df3078ff0695485f59ed4c7f343876e`; **re-check newest head CI before approval** | **Reviewable only after current-head checks** |
| [#108](https://github.com/yusufbayuw/foundationOS/pull/108) | `agent/devops-encrypted-backup-contract-20261022`; head evolves as restore tests are added | Original [encrypted archive contract smoke](https://github.com/yusufbayuw/foundationOS/actions/runs/39757535326) passed; latest CI fixture jobs include genuine ephemeral PostgreSQL 16 and MariaDB 11.4 restore; **must inspect final results at exact new head** | **Hold until real fixture restore CI green**, still no production restore certification |

### Existing CI inventory (main SHA `b6db91a7d4d8f5731f2ff087380bab5161ef13f7`)

The [main Tests run](https://github.com/yusufbayuw/foundationOS/actions/runs/39722221185) passed PHPUnit, 80% coverage, ten named suites and PostgreSQL integration/migration smoke. [Static Analysis](https://github.com/yusufbayuw/foundationOS/actions/runs/39722221127) passed Pint and both Larastan jobs. [Supply Chain](https://github.com/yusufbayuw/foundationOS/actions/runs/39722221109) passed Composer manifest/optimized PSR-4, frontend production build, Composer/npm High/Critical audit, and secret scan; dependency review was skipped on the push event (it is PR-scoped). Tenant and translation lint passed on the same main SHA. These checks test **source and CI services**, not production ingress, queue/cron or backup recovery.

The earlier [branch-protection instructions](../../.github/BRANCH_PROTECTION.md) used generic `Named suites smoke`; GitHub emitted ten actual `Named suites smoke (Suite)` checks. The updated branch-protection document enumerates these explicitly. An admin must verify the configured protection in GitHub Settings.

## 3. Verified preflight vs outstanding staging evidence

| Control | Repository/CI evidence | Actual staging/production evidence | Gate |
|---|---|---|---|
| PHP 8.4 Laravel boot, configuration compilation, GET `/up` registration, scheduler registration | New #101 smoke job succeeded | No server `/up` or PHP-FPM/aaPanel path measured | Conditional |
| Unit/feature, tenant/policy/permission, queue, regression, PostgreSQL CI | Main CI passed, latest PR heads must be checked individually | No production-sized, cross-service UAT yet | Conditional |
| HTTPS, secure cookies, CSRF, real client IP/throttle | #80 changes allowlist and has limited spoofing unit tests | **Missing real Nginx/Cloudflare ingress evidence** | **P0 blocker** |
| DB and upload recovery | #108 encrypted-archive fixture smoke passed; database restore integration CI being added | **No documented isolated restore from actual staging backup; no measured RPO/RTO** | **P0 blocker** |
| Off-site immutable backup, secrets escrow and retention | Required by #105/#108 runbooks | **Not installed or verified** | **P0 blocker** |
| Safe schema migrations/rollback | PostgreSQL empty-DB smoke in CI; runbook describes expand/contract | Migration compatibility & anonymized full-size rehearsal absent | **P0 blocker** |
| Scheduler, Supervisor, named queues, failed jobs, outboxes | Scheduler definition inspected; task registration CI passed | One cron runner, workers, queue age and alerts **not measured** | P1 blocker |
| Monitoring uptime, disk, backup, email, Moodle, webhook and billing | Thresholds and ownership defined in #105 | External alert route/delivery and failure drill **not proven** | P1 blocker |
| Required GitHub branch protection | Documented job names from Actions API | **403 on protected branch settings** | P1 administrative gate |

## 4. Stop/go decision

**NO-GO.** Existing CI is healthy but core production security/recoverability evidence is absent. Do not merge PR #80 until real staging ingress UAT, and do not represent CI archive tests as complete disaster recovery. Do not merge any PR into `main` from this DevOps workstream; the Project Manager/Owner reviews PRs and controls approval and release authorization.

Conditions to revisit this decision:

1. GitHub exact current-head required checks green for all release PRs; branch protection confirmed by admin with the correct job names and no bypass.
2. Real staging proxy measurements of `REMOTE_ADDR`, canonical HTTPS, effective client IP, Secure cookie and rate limiting, including forged headers, approved and signed.
3. Protected database/storage snapshot created on approved staging and **restored into separate DB and storage** with verified records, private file access, external integrations disabled, and recorded measured RPO/RTO. Retention/offsite/keys escrow audited.
4. Scheduler/worker and alerting test with queue backlog/failed jobs, unexpected Moodle outbox, billing and operational escalation; one scheduler runner.
5. Production-shaped staging rehearsal for non-destructive migrations, feature smoke, tenant isolation, uploads and rollback; independently observed.
6. Explicit written Project Owner GO decision and on-call ownership, with reviewable evidence in release ticket.

## 5. Server evidence required (read-only collection)

See [release & DR runbook](./RELEASE_AND_DR_RUNBOOK.md), [ingress staging matrix](./INGRESS_STAGING_VALIDATION.md), and [staging evidence capture sheet](./STAGING_EVIDENCE_CAPTURE.md). No production access has been assumed. Never paste private IPs, auth headers, credentials, raw config files, full access logs, user records, or database dumps into public GitHub issues.

## 6. Rollback and operational risks

- **Before release:** if any CI, backup integrity or staging UAT fails, cancel the release; do not weaken assertions to force green.
- **Ingress:** rollback to previously tested proxy config and application release as a coordinated operation; unknown proxy chain can cause HTTPS/login failure and spoofable IP throttles.
- **Database:** rollback code only if compatible with the migrated schema. Never perform blind `migrate:rollback`; forward-fix or restore at an approved incident cut-off with external payment/Moodle/mail reconciliation.
- **Backups:** local encrypted snapshot without offsite retention, separate key escrow and periodic actual restore is **not** disaster recovery.
- **Operations:** no monitoring evidence = unknown detection time; request the on-call alert receipt and actual queue/scheduler logs.
