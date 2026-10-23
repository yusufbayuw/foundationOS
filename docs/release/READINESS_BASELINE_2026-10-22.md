# FoundationOS — Release Readiness Baseline

**Audit date:** 22 October 2026 (WIB)  
**Reference:** `main` after [PR #77](https://github.com/yusufbayuw/foundationOS/pull/77); snapshot from repository tree and `modules_statuses.json`.  
**Release decision:** **NO-GO for an unrestricted production rollout of all modules** until the gates below have evidence.

## What is and is not verified

- There are **47 enabled module directories** in the manifest. Counts below describe code structure only, **not functional completeness**.
- GitHub Actions cover PHPUnit (PHP 8.4), named suites, a PostgreSQL migration smoke test, lint/static analysis, frontend build and dependency scanning. Passing CI is necessary, **not sufficient** for release.
- CI's 80% coverage threshold is scoped by `phpunit.coverage.xml`; do **not** advertise it as 80% line coverage across the entire application. The older `docs/TECHNICAL_DEBT_LOG.md` explicitly reports much lower full-repository coverage; remeasure before publishing a new percentage.
- `PLAN_STATUS.md` is from June 2026 and distinguishes scaffold GA from completed business processes. Do not treat its old checkbox data as October 2026 acceptance evidence.
- This matrix has not been browser-tested or connected to an actual aaPanel/staging installation. Operational gates require environment access.

## Module inventory — structural evidence, *not* GA certification

Count rules: direct model files under `app/Models`, resource classes under Filament `Resources/*/*Resource.php`, service files directly under `app/Services`, and migration files in module `database/migrations`. Counts exclude nested services and resources outside the standard pattern. **Missing structural files does not prove missing functionality**, and positive counts never prove a usable flow.

| Enabled module | Models | Filament resources | Services | Migrations | End-to-end acceptance |
|---|---:|---:|---:|---:|---|
| `Ai` | 1 | 1 | 2 | 1 | Belum ada bukti UAT |
| `Alumni` | 10 | 10 | 1 | 3 | Belum ada bukti UAT |
| `Asset` | 7 | 7 | 2 | 2 | Belum ada bukti UAT |
| `Boarding` | 7 | 7 | 2 | 2 | Belum ada bukti UAT |
| `Cafeteria` | 8 | 8 | 1 | 2 | Belum ada bukti UAT |
| `Campus` | 17 | 17 | 8 | 16 | Belum ada bukti UAT |
| `Capacity` | 3 | 3 | 1 | 2 | Belum ada bukti UAT |
| `Clinic` | 9 | 9 | 1 | 2 | Belum ada bukti UAT |
| `Cms` | 11 | 11 | 3 | 4 | Belum ada bukti UAT |
| `Consulting` | 6 | 6 | 2 | 2 | Belum ada bukti UAT |
| `Core` | 24 | 24 | 9 | 18 | Belum ada bukti UAT |
| `Counseling` | 7 | 7 | 2 | 2 | Belum ada bukti UAT |
| `Dms` | 4 | 4 | 1 | 2 | Belum ada bukti UAT |
| `Donation` | 7 | 7 | 5 | 2 | Belum ada bukti UAT |
| `EducationQa` | 8 | 8 | 2 | 2 | Belum ada bukti UAT |
| `Employee` | 13 | 13 | 5 | 12 | Belum ada bukti UAT |
| `Enrollment` | 10 | 10 | 6 | 7 | Belum ada bukti UAT |
| `EOffice` | 5 | 5 | 3 | 3 | Belum ada bukti UAT |
| `Event` | 10 | 10 | 2 | 2 | Belum ada bukti UAT |
| `Exam` | 19 | 5 | 25 | 11 | Belum ada bukti UAT |
| `Facility` | 8 | 8 | 3 | 3 | Belum ada bukti UAT |
| `Finance` | 10 | 10 | 6 | 8 | Belum ada bukti UAT |
| `Global` | 6 | 6 | 0 | 6 | Belum ada bukti UAT |
| `Helpdesk` | 6 | 6 | 1 | 2 | Belum ada bukti UAT |
| `InternalAudit` | 9 | 9 | 1 | 2 | Belum ada bukti UAT |
| `Inventory` | 7 | 7 | 7 | 1 | Belum ada bukti UAT |
| `IsoCompliance` | 7 | 7 | 2 | 2 | Belum ada bukti UAT |
| `ItOps` | 4 | 4 | 3 | 2 | Belum ada bukti UAT |
| `KpiEnterprise` | 6 | 6 | 2 | 2 | Belum ada bukti UAT |
| `Legal` | 4 | 4 | 2 | 3 | Belum ada bukti UAT |
| `Library` | 21 | 21 | 3 | 12 | Belum ada bukti UAT |
| `Marketplace` | 8 | 8 | 2 | 3 | Belum ada bukti UAT |
| `Member` | 4 | 2 | 0 | 1 | Belum ada bukti UAT |
| `MerchOrder` | 6 | 6 | 2 | 2 | Belum ada bukti UAT |
| `Messaging` | 3 | 3 | 4 | 3 | Belum ada bukti UAT |
| `Monitoring` | 6 | 5 | 3 | 5 | Belum ada bukti UAT |
| `PhysicalSecurity` | 8 | 8 | 1 | 2 | Belum ada bukti UAT |
| `Printing` | 8 | 8 | 3 | 2 | Belum ada bukti UAT |
| `Procurement` | 14 | 14 | 10 | 16 | Belum ada bukti UAT |
| `Property` | 6 | 6 | 2 | 2 | Belum ada bukti UAT |
| `Risk` | 7 | 7 | 1 | 2 | Belum ada bukti UAT |
| `Sales` | 6 | 5 | 3 | 4 | Belum ada bukti UAT |
| `School` | 21 | 21 | 12 | 17 | Belum ada bukti UAT |
| `Training` | 9 | 9 | 3 | 3 | Belum ada bukti UAT |
| `Transport` | 9 | 9 | 1 | 2 | Belum ada bukti UAT |
| `Voucher` | 2 | 2 | 1 | 1 | Belum ada bukti UAT |
| `Workflow` | 10 | 10 | 17 | 11 | Belum ada bukti UAT |

## Critical blockers already tracked

| Issue | Go-live impact | Exit evidence |
|---|---|---|
| [#75 Passkey/WebAuthn](https://github.com/yusufbayuw/foundationOS/issues/75) | **P0**: custom signed-message flow is not compliant WebAuthn | Disable unsafe flow immediately; later verify replacement WebAuthn RP stack and real authenticator tests |
| [#76 API tenant and membership policy](https://github.com/yusufbayuw/foundationOS/issues/76) | **P0**: token lifecycle/membership isolation | PR #77 covers subscription locks; define/restrict user vs integration token policies and regression tests |
| [#72 Trusted proxies](https://github.com/yusufbayuw/foundationOS/issues/72) | **P1**: ingress-dependent client-IP spoofing / throttling | Staging test with actual aaPanel/Nginx/Cloudflare topology and explicit trusted proxy allowlist |
| [#73 Business workflow readiness](https://github.com/yusufbayuw/foundationOS/issues/73) | **P1**: CRUD ≠ complete service | UAT evidence per module and golden paths with permission and tenant isolation tests |
| [#74 Disaster recovery/ops](https://github.com/yusufbayuw/foundationOS/issues/74) | **P1**: recovery/queues/jobs uncertain | Restore drill, backups, alerts, scheduled tasks, rollback rehearsal and operational sign-off |

## Release strategy

### Wave 0 — P0 stabilization

1. Make insecure passkey APIs unusable until correct WebAuthn is implemented; keep credentials for controlled migration.
2. Complete tenant and actor authorization, service-token revocation, cross-tenant negative cases and subscription lock behavior.
3. Verify that deployment secrets, webhooks and trusted ingress cannot bypass authentication or throttles.
4. Run all CI workflows on the exact release SHA.

### Wave 1 — institution foundation

Candidate domains: `Core`, `Global`, `Workflow`, `Monitoring`, `Employee`, `Finance`. Complete institutional setup, users/roles, permission segregation, organization, approval, payroll-to-ledger and audit trail with anonymized sample data.

### Wave 2 — education and enrollment

Candidate domains: `School`, `Campus`, `Enrollment`, `Exam`, `Library`, `EducationQa`, `Counseling`, `Clinic`. Prove admissions → student record → billing → academic process → report/grade, plus Moodle if enabled.

### Wave 3 — business and operations

Candidate domains: `Procurement`, `Inventory`, `Asset`, `Facility`, `Helpdesk`, `ItOps`, `Transport`, `EOffice`, `Dms`, `Sales`, `Cafeteria`, `Boarding`, `PhysicalSecurity`. Prove requisition → approval → order → receipt → invoice → ledger and support ticket → SLA → resolution.

### Wave 4 — optional/extension modules

All remaining enabled modules are opt-in until their own workflow UAT and security checks are documented; 'enabled in manifest' is not an authorization to sell or deploy them without validation.

## Mandatory evidence per module

Record a module-specific owner, version/commit SHA, deployment environment, data fixture, relevant routes, permissions, observed UAT result and rollback plan. Gate items:

- [ ] Core happy-path business lifecycle completed in a real browser/Livewire UI
- [ ] Role/policy checks for super admin, tenant admin and read-only/end user
- [ ] Tenant A cannot read/write tenant B data, including exports, uploads, jobs and APIs
- [ ] Duplicate submission, validation failure, retry/idempotency, concurrent transition and audit events tested
- [ ] Database migrations on anonymized representative data; rollback/restore rehearsal completed
- [ ] Mobile layout/accessibility smoke, notifications/mail/queue/scheduler, time zone and localization validated
- [ ] UAT signed by process owner with screenshots/test transcript
- [ ] Deploy / monitor / rollback instructions reviewed by operator

Use `Blocked → In development → CI passed → Staging UAT passed → Pilot passed → Approved for production`. Never substitute module file counts, test names or a README claim for signed evidence.

## Production cutover gate

Before announcing general availability, confirm HTTPS/APP_DEBUG=false, trusted proxy/IP configuration, secrets rotation, backups with tested restoration, queues/scheduler, mail delivery, external integrations, DB lock/deadlock behavior, per-tenant logging and alerting, and a rollback plan. Require **zero open P0** issues relevant to enabled functions. Record who signed each gate and the SHA deployed.

**This document is a factual inventory and delivery plan, not a declaration that FoundationOS is production-ready.**
