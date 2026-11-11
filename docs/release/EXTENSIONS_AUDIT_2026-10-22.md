# FoundationOS Wave 4 — Extension Modules: Preliminary Readiness Audit

**Date:** 2026-10-22  
**Repository:** `yusufbayuw/foundationOS`  
**Issue:** [#84](https://github.com/yusufbayuw/foundationOS/issues/84)  
**Scope:** Ai, Alumni, Capacity, Cms, Consulting, Donation, Event, InternalAudit, IsoCompliance, KpiEnterprise, Legal, Marketplace, Member, MerchOrder, Messaging, Printing, Property, Risk, Training, Voucher.

> **RELEASE STATUS: BLOCKED for unrestricted production use.** This is a source-level *triage*, not a successful staging/production audit or UAT sign-off. The `modules_statuses.json` switch is not a production certification.

## Classification and evidence basis

- **Complete:** required business lifecycle, tenant isolation, authorization, negative-path tests, rollback and staging UAT are evidenced. **None verified.**
- **Partial:** some actionable domain logic, public endpoints, integrations or reports exist, but end-to-end lifecycle and release gates are unproven.
- **Scaffold:** models, migrations, policies and administrative resources exist, but a primary operational lifecycle has not been demonstrated.
- **Backlog:** no identifiable implementation. **None** among the 20 candidate module directories.
- **Not verified** is an evidence boundary, not a claim that the implementation is absent or defective.

This pass reviewed the #84 acceptance criteria, recursive repository tree, models, services, controllers, test filenames, the module enablement list and targeted Voucher/Messaging source and Voucher migrations. Other-module classifications require code-path review, test execution and UAT before promotion.

| Module | Provisional | Business golden path to prove | Next blocking evidence / acceptance test |
|---|---|---|---|
| Ai | Partial | Prompt catalog → advisory request → authorized recommendation and audit | Guardrail, provider availability, failure/fallback, privacy and cost caps |
| Alumni | Scaffold | Alumni profile → verification → opportunity/mentorship → closure | Owner decisions, approval flow, cross-tenant profile and application permissions |
| Capacity | Scaffold | Define resource → forecast → allocation/utilization → reconciliation | Quantity/period constraint and reconciliation service/tests |
| Cms | Partial | Draft → reviewer approval → publish → public rendering → unpublish | Tenant/site isolation, preview and content/file sanitization |
| Consulting | Scaffold | Client → proposal approval → engagement → deliverable acceptance → invoice closure | Approval and receivables integration, reversals and tax review |
| Donation | Partial | Campaign → checkout → verified payment webhook → reconciliation → receipt | Signature, idempotency, settlement reversal and sensitive donor data |
| Event | Partial | Proposal → approval → registration → check-in → certificate → closure | Deduplicated registration/check-in, capacity and tenant boundaries |
| InternalAudit | Scaffold | Audit plan → evidence → finding → corrective action → closure verification | Independent approver, evidence immutability, overdue tracking |
| IsoCompliance | Partial | Control → owner → evidence → assessment → exception/remediation | Evidence retention, access controls, formal sign-off |
| KpiEnterprise | Partial | Metric → target → actual → rollup → approval/locking | Versioned periods, weighting and recalculation consistency |
| Legal | Partial | Document → contract review/approval → signature evidence → expiry/renewal | Valid authorization, private attachments, electronic-signature limitations |
| Marketplace | Partial | Product → order → payment → fulfillment → return/settlement | Oversell, concurrent payment, returns and seller isolation |
| Member | Partial | Registration/proof → reviewer decision → activate/reject → notification | Proof storage, duplicate registration, reviewer permissions and isolation |
| MerchOrder | Scaffold | Package → order → payment → pickup/return → reconciliation | Stock reservation, delivery evidence, invoice/payment integration |
| Messaging | Partial | Template → user preferences → provider dispatch → retry → final delivery status | **P0:** tenant context and truthfulness of `sent` status; PII-safe logging |
| Printing | Partial | Template/request → approve → production → delivery → completed invoice | Cost/material allocation, output access control and acceptance evidence |
| Property | Scaffold | Unit → lease approval → deposit → invoice → payment → exit | Proration, deposit accounting, double booking and collections |
| Risk | Scaffold | Risk → assessment → treatment → mitigation evidence → acceptance/close | Review/approval rights, scoring and residual risk audit |
| Training | Partial | Program/batch → enrollment → attendance/payment → certificate → verify | Entitlement, completion threshold and anti-duplicate certificate tests |
| Voucher | Partial | Create → activate → claim → redeem/expire → reporting | Duplicate/quota concurrency, tenant A/B, expiration persistence, CSV semantics |

## Confirmed source-level findings and dependency boundaries

1. **Voucher expiration rollback**: `VoucherClaimService::redeem()` previously updated an expired claim, then threw within the same transaction; the status update was rolled back. Patch in draft PR [#102](https://github.com/yusufbayuw/foundationOS/pull/102). API contract retained; regression test added; tests have not been run in this audit.
2. **Voucher claim title mismatch**: claims table and CSV exported `voucher.name` although the Voucher domain model uses `title`. Corrected in draft PR #102.
3. **Voucher overlapping schemas**: voucher/voucher_claims create definitions coexist in root migrations, Sales and Voucher, plus a reconciliation migration. Confirm migration order and actual deployed schema before any database change. No schema changes in #102.
4. **Messaging tenant ambiguity**: `NotificationDispatcher::tenantIdFor(User)` selects the first user membership rather than an explicitly proven current tenant. Cross-tenant notification template/delivery selection needs a focused security design review with tenancy owner (#76).
5. **Messaging false-positive delivery**: `sendMail()` is a no-op but `dispatch()` can set status `sent`. Other disabled/unavailable channels can also result in `sent`. Implement honest channel outcomes, outbox and retry semantics before enabling external delivery.
6. **Enablement is not release readiness**: all 20 candidates are set to `true` in `modules_statuses.json`; keep activation policy/release controls separate from the source manifest and evaluate real tenant settings.

## Implementation order (dependency-aware)

**P0 — Stop unsafe releases:** Messaging tenant scope and delivery status; Donation payment-webhook and replay/chargeback checks; schema reconciliation and cross-tenant isolation for Voucher. Coordinate shared Tenancy/Core/Auth and Finance contracts with owners; do not modify those modules unilaterally.

**P1 — Small, demonstrable lifecycle completions:** Voucher expiry/CSV (PR #102); Member registration → review; Cms draft/publication; Event registration → check-in; Training completion → certificate. For each, add tenant A/B, unauthorized actor, duplicate/retry and state-transition regressions.

**P2 — Back-office and governance workflows:** Capacity, Alumni, Risk, InternalAudit, IsoCompliance, KpiEnterprise, Printing, Property, Consulting, MerchOrder. Build one complete golden path per module, rather than adding more CRUD fields.

**P3 — Integration/financial dependencies:** Marketplace settlement, Donation gateways, Legal e-sign and AI/external messaging providers need confirmed business requirements, contract/credential approval and rollback strategy. Prefer local or no-cost adapters until approved.

## Release requirements per candidate

1. Named owner/approver and agreed golden path with state transitions and negative paths.
2. Tenant and role permission checks, file privacy, transaction integrity, queue behavior, idempotency and audit evidence.
3. Automated suite: happy path, cross-tenant read/write, unauthorized actor, duplicate, retry, reversal and historical-data compatibility.
4. Documented workflow for operator, setup, notifications, imports/exports, mobile/Filament UX and dependency credentials.
5. Database migration/restore rehearsal and deployment preflight/smoke/rollback; tested on staging for tenant A and tenant B.
6. Recorded UAT sign-off, deployment SHA and explicit pilot/release decision.

## Coordination

- **#72–#76:** release gating, security, tenancy, CI/deployment.
- **#81:** financial journal/receivables/payroll integration.
- **#82:** student/alumni/training user data definitions and campus/school integrations.
- **#83:** stock, transport, procurement, facilities and operational dependencies.
- **#84:** application lifecycle ownership, acceptance criteria and opt-in decisions.

**Verification:** source-only assessment. No automated test-suite output, staging walkthrough, deployment or UAT evidence is claimed. Do not merge this classification into a claim of 20 production-ready modules.
