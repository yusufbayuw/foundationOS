# FoundationOS testing architecture — risk-tiered redesign

**Date:** 25 October 2026  
**Owner:** QA / DevOps (#115), with Security Engineer implementation and Project Manager review.  
**Review status:** DRAFT; the existing merge/release checks must not be weakened without verified branch rules.

## Grounded baseline

- At `main` commit `5e4a2830`, the repository contains **226 `*Test.php` files** across the root and Workflow module.
- Successful full PHP 8.4 PHPUnit run [#40154226149](https://github.com/yusufbayuw/foundationOS/actions/runs/40154226149): **7,897 tests / 24,176 assertions / 21m44s** in the full/coverage run. End-to-end Tests workflow was **31m41s**.
- `.github/workflows/tests.yml` performs one full SQLite+coverage PHPUnit pass, ten separate named-suite re-executions, PostgreSQL six priority suites with fresh migration, and a disposable MariaDB migration smoke.
- Issue #115 / merged PR #116 already removed a duplicated complete **second** coverage run, retained 80% threshold and added PHPUnit config parity/negative regression fixtures. Do not reverse that work.
- Branch protection/ruleset read is 403 under this connection. An administrator must confirm required checks before changing their names or removing them. Retaining the same display name does **not** alone prove the new check has equivalent semantics; independent review is still required.

## Proposed test layers

| Layer | Goal | Trigger | Must include | Merge policy |
| --- | --- | --- | --- | --- |
| L0 | Syntax, static analysis, supply-chain/security checks | Every PR; main | Pint, Larastan, migration identifier lint, translations, secret-scan, Composer High/Critical audit, npm audit, dependency review | Blocking on every PR |
| L1 | High-value functional smoke | Every PR | App bootstrap; auth, OTP/TOTP, Laravel TokenGuard, session revocation, tenant access, permission bypass, private file access, finance posting, migrations, queue context | Blocking; target single-digit minutes |
| L2 | Impacted domain tests | PR changes touching modules | Changed module plus shared dependencies and cross-module integration; security/tenancy/core/config/schema changes must fall back to expanded set | Blocking; unknown diff defaults to full |
| L3 | Full regression on supported databases | Before merge; nightly; release branch | All PHPUnit, coverage >=80%, PostgreSQL integration, MariaDB migration and cross-driver critical cases | **Required before merge**; nightly alone does not authorize merge |
| L4 | Production-oriented acceptance | Before production rollout | Real browser authenticator, tenant boundaries, concurrent writes, queued jobs, restore/rollback, off-host backup, end-to-end academic/financial workflows | Release approval with evidence |

Important: the L1/L2/L3 gating redesign is **not activated by this PR**. Move L3 away from per-PR only if QA/PM can enforce it as a required pre-merge check with an unbypassable gate. GitHub native PRs do not have a built-in universally reliable "run only when merge is about to occur" event; a merge queue, enforced required full workflow, or other audited automation is necessary.

## Implemented now — non-destructive deduplication

**Current stage:** Remove duplicate test **execution**, not test cases.

1. Full required PHPUnit still runs **all 10 configured suites once**, with scoped Clover coverage and the unchanged 80% coverage gate.
2. Full PHPUnit also emits `coverage/junit.xml`, alongside `coverage/clover.xml`, in the same artifact. The existing full job is still the authority for pass/fail.
3. Each of ten existing **`Named suites smoke (SUITE)`** checks retains its exact displayed name and waits for full PHPUnit to succeed, then asserts from JUnit that its configured file selectors have actual successful, nonskipped executed test cases with no failures. It does **not** re-run those cases.
4. If artifact is missing, XML invalid, suite missing or empty, all cases skipped, or any case failed, the attestation fails closed. Regression tests prove these cases, plus acceptance of a valid record.
5. PHPunit config test selection parity and deliberately failing regression fixture from #116 remain mandatory and untouched; PostgreSQL/MariaDB and static/supply-chain jobs are unchanged.
6. No business/controller, database migration, actual security test, encryption key, or production deployment configuration changed.

**Residual risk:** JUnit file attribution must be verified on the actual PHPUnit 12 output. Attesting a suite via `>=1` passed test case is not a replacement for independently executing every test; it is acceptable here only because all cases already ran in the required full job and its suite/config/negative-fixture contract is enforced. If JUnit metadata is absent, this change intentionally fails rather than silently passing.

## Future restructuring after this PR

- **Catalog actual coverage by business capability**, not implementation directory alone: Auth, Tenant, Education, Finance, Workflow, Operations, Extensions, Integrations/Queues, Data/Import, Database, Security/Release.
- **Profile hotspots from actual JUnit**: number of data-provider invocations and elapsed time per test file/class. Audit huge generated matrices for distinct assertions, duplicates and side effects before reducing them; **never delete a regression just for runtime savings**.
- **Enforce test ownership:** each bug fixed gets one focused negative regression; each high-risk domain gets both authorization and cross-tenant rejection tests; integration is tested at API and boundary, not repeated CRUD permutations.
- **Introduce L1 smoke manifest** and conservative changed-path dependency map with "unknown/shared means expanded/full" fallback; prohibit paths-only rules for `app/`, `Modules/Core/`, `Modules/Global/`, tenant context, auth, migrations, shared routes, config, `composer.lock`, policy, workflow infrastructure, and jobs.
- **Database realism:** SQLite fast feedback; PostgreSQL for selected full contracts; MariaDB for production-compatible migrations plus targeted auth/JSON/locking/FK/concurrency. SQLite pass is not database equivalence.
- **Merge governance:** verify required status-check/ruleset names (currently 403 to agent), compare old/new same-head results, time via GitHub Actions jobs API, and test intentionally failing/omitted suite fixtures.

## Acceptance checklist for this deduplication PR

- [ ] All ten named-suite attestations pass on real JUnit output, not just handcrafted fixture.
- [ ] Existing `PHPUnit (PHP 8.4)`, `Coverage (80% minimum)`, PostgreSQL, MariaDB and security/static workflows pass on PR latest head.
- [ ] Missing/failed/skipped/other-suite negative fixtures produce expected failure, not silent success.
- [ ] Report exact job count, runner-minute sum and workflow wall duration before/after at comparable CI heads; distinguish queue effects and random runner variance.
- [ ] QA verifies GitHub required status checks/rulesets, explicitly approves transformed named-suite semantics, and signs off with Pimpro.
- [ ] No tests deleted; rollback reverts only `.github/workflows/tests.yml`, the attestation script and its unit regressions.

## Release blockers independent of test optimization

- #121 / PR #123 Laravel TokenGuard vulnerable lockfile must be resolved; the optimization PR is **stacked on #123**, not a new security-audit exception.
- #122 / PR #125 historical APP_KEY exposure and deployed-runtime classification require independent operator signoff.
- No CI reduction, green suite, or coverage percentage alone constitutes security acceptance or production deployment approval.
