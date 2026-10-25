# Issue #115 — CI draft/ready tiering, duplicate removal (25 Oct 2026)

## Baseline (real GitHub Actions data)

- Commit: `5e4a2830aff0a83986d8f636530f31a72432d871` (`main`).
- Workflow: [Tests run 40154226149](https://github.com/yusufbayuw/foundationOS/actions/runs/40154226149).
- Wall clock: **31m 41s**; PHPUnit with coverage: **22m 07s**; duplicate named **Feature** suite ran after PHPUnit for **9m 30s**.
- PostgreSQL smoke: **10m 18s** (in parallel); MariaDB migration smoke: **53s**.
- Independent comparison samples: [PR #123](https://github.com/yusufbayuw/foundationOS/actions/runs/40156563334) ~36m54s total, 27m10s PHPUnit, 9m39s repeated Feature; [PR #124](https://github.com/yusufbayuw/foundationOS/actions/runs/40156627366) ~30m51s, 20m PHPUnit, 9m27s repeated Feature.
- These are **sample observations, not statistical medians**; elapsed time is not billed runner-minutes. A/B measures must use consistent sampling and note external runner congestion.

## Changes

1. **No duplicate full-suite run:** remove ten SQLite named suite jobs which used `needs: phpunit` and repeated cases already covered by complete `phpunit.coverage.xml` selection. Preserve `PHPUnit (PHP 8.4)`, `Coverage (80% minimum)`, PostgreSQL priority suites, MariaDB schema migration smoke, and external security/static/lint/backup workflows.
2. **Strict discovery contract:** extend `scripts/verify-phpunit-suite-parity.php` so it fails when any of the original ten mandatory suite names disappears, even when *both* XML configs omit it. Keep existing source configuration/environment parity test and intentionally failing Regression assertion fixture. Add a second negative fixture omitting Regression from both XML files.
3. **Fast feedback on draft:** a draft PR runs an isolated short list of Unit, Tenant, Policy, Permission, Queue, and Regression suites with PHP 8.4 and SQLite. Existing quality, security, static analysis, dependency audit, and other workflows continue on draft PRs; a fast smoke PASS is **never** a release gate.
4. **Full integration on ready:** `pull_request.ready_for_review`, later `synchronize` and `reopened` on a non-draft PR, plus every `push` to `main`, run the complete PHPUnit with PCOV and coverage 80% threshold, PostgreSQL priority integration and MariaDB migration checks. `converted_to_draft` removes full-run scheduling, and `ready_for_review` explicitly schedules it anew.
5. **No security bypass:** do not disable composer/npm advisory audits, secret scan, static analysis, database migration checks, or tenant/auth/regression test code. This work does not fix the critical advisory on `main` (#121), historical key issue (#122), or workflow failures inside domain PRs.

## CI acceptance (fail closed)

- [ ] Latest-head PR is marked **ready for review**, and full PHPUnit, >=80% scoped coverage, PostgreSQL/MariaDB, security audit, secret scanning, static, lint, and backup contract are all successful.
- [ ] Workflow shows **no** `Named suites smoke` matrix, but full suite still includes Unit, Feature, Workflow, Tenant, Policy, Permission, Regression, Queue, ParallelWorkflow, ImportExport.
- [ ] Both missing-suite negative fixture and intentionally failing PHPUnit assertion are detected.
- [ ] A separate draft PR runs the new fast smoke, and expensive tests do not run until `ready_for_review`. A draft fast check alone is not acceptable merge evidence.
- [ ] Check event `ready_for_review` triggers full tests and a new latest-commit set of statuses. If any trigger is missed or check is missing, block merge.
- [ ] Measure before/after full-run wall clock, summed job elapsed, and median draft feedback over multiple attempts. Target draft fast <8min; benchmark, not assertion.
- [ ] Rollback: revert this PR's changes to `.github/workflows/tests.yml` and `scripts/verify-phpunit-suite-parity.php`.

## Required rules and governance

As of 25 Oct 2026 `main` reports `protected=false`. **Before relying on automated merge gating**, the owner must enable a branch protection rule or repository ruleset requiring PR reviews and successful up-to-date integration checks including `PHPUnit (PHP 8.4)`, `Coverage (80% minimum)`, `PostgreSQL integration and migration smoke`, `MariaDB migration smoke`, security, secret, static and other existing critical checks. Do not set `Fast PHP smoke (draft PR)` as the sole required status. Protect against direct pushes to `main`; use a consistent ruleset and require the checks to run on each ready head. Where settings cannot be changed through GitHub connection, owner/admin must configure and verify them separately. Until that rule exists, **PM manually enforces full green ready-to-merge checks** and must not merge on draft smoke or skipped tests.

Do not alter deployment or live databases. All MySQL/MariaDB `migrate:fresh` commands in workflow operate only on disposable GitHub Actions database services. Never run on staging/production.

## Not in scope

- Eliminating the full PCOV pass on ready PRs: requires evidence-backed risk-tier strategy or stronger pre-merge verification.
- Pruning PostgreSQL integration priority tests: no reduction approved.
- Modifying backup/restore fixture workflows: investigate separately as Issue #115 phase 3.
- Changing production secrets or rotating `APP_KEY`: Security #122 only.
