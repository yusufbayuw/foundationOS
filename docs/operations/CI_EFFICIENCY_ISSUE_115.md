# Issue #115 — CI Efficiency: baseline, controlled optimization and rollback

**Owner:** DevOps & QA · **Issue:** [#115](https://github.com/yusufbayuw/foundationOS/issues/115) · **Coordination:** [#74](https://github.com/yusufbayuw/foundationOS/issues/74)

## Baseline (GitHub Actions; no estimates)

Workflow `Tests` on `main` commit `b6db91a7d4d8f5731f2ff087380bab5161ef13f7`:

| Workflow run | Wall elapsed | Sum of individual test-job elapsed times | Full SQLite job | Coverage job | 10 named suites | PostgreSQL job |
|---|---:|---:|---:|---:|---:|---:|
| [39722221185](https://github.com/yusufbayuw/foundationOS/actions/runs/39722221185), 2026-10-22 | 38m41s | 64.3 min | 10.6 min | 28.0 min | 13.3 min | 12.4 min |
| [39641246366](https://github.com/yusufbayuw/foundationOS/actions/runs/39641246366), 2026-10-21 | 45m14s | 69.5 min | 17.3 min | 27.9 min | 11.8 min | 12.6 min |

Job time is elapsed `started_at` to `completed_at` via GitHub REST Jobs API, **not** provider-invoiced billable minutes (rounding, private runner costs, waiting and queue effects may differ). Wall time is workflow `created_at` to `updated_at`. Two historical runs are evidence, **not sufficient for a statistical median**.

A standard PR executes: one SQLite PHPUnit run, another full suite pass with scoped source coverage, ten named SQLite suites, plus PostgreSQL priority suites. `phpunit.xml` and `phpunit.coverage.xml` have matching ten test suites and PHPUnit environment values; their `<source>` coverage scopes differ intentionally.

## Change delivered in this PR (safest de-duplication, no removed checks)

- Reuse the full PHPUnit **test run with coverage enabled** as the single execution for both test success and scoped coverage collection. PHP 8.4, PCOV, test suite selection, PHPUnit env, coverage scope and existing threshold are retained.
- Existing `PHPUnit (PHP 8.4)` check is still an enforced full SQLite suite. The distinct `Coverage (80% minimum)` check **still validates the unchanged 80% threshold**, consuming only a GitHub Actions clover artifact from PHPUnit.
- All ten `Named suites smoke (SUITE)` jobs, PostgreSQL priority suites and empty database migration smoke, and separate supply chain/static/security checks remain unchanged.
- `scripts/verify-phpunit-suite-parity.php` refuses to proceed if test suite paths/bootstrap/testing env disagree between the two configs. A negative config fixture intentionally omitting `Regression` must be rejected.
- A short temporary PHPUnit test in the Regression directory **intentionally fails**; CI verifies that the coverage-config runner reports a genuine failed PHPUnit assertion, then removes the fixture before running the mandatory full suite. It must not be committed or executed in production.
- Neither `composer.lock`, actual tests, business logic, database migrations, nor production configuration are changed. Workflow names and required job names are preserved.

This **does not yet remove** the ten duplicate named jobs. Phase A removal requires branch-protection verification and Pimpro authorization as required by #115.

## Acceptance and A/B measurement

| Metric | Before | After on optimized PR head |
|---|---|---|
| Full test suite check | Pass | **Pending CI** |
| Scoped coverage >=80% | Pass | **Pending CI** |
| PHPunit / PostgreSQL / named-suite required checks | All pass on main | **Pending CI** |
| A real intentionally failing Regression fixture is detected | Implicit in full suite | **Must explicitly pass guard step** |
| SQLite full executions per PR | 2 | **Proposed 1; CI evidence required** |
| Total test workflow wall elapsed | 38m41s / 45m14s | **Not yet measured** |
| Test workflow total job elapsed | 64.3m / 69.5m | **Not yet measured** |
| PostgreSQL full migrations and critical suites | Included | **Preserved** |
| Retained ten named-suite jobs | 10 | **10 unchanged** |

Once GitHub Actions finish on the **exact PR head SHA**, record all job start/completion timestamps and suite assertions. Compare at least two successful optimized runs for useful runtime distribution if possible, because runner time can vary. Failed CI is a blocker and should **not** be hidden by reclassifying a job as optional. An integration check showing green means only that it passed on its head.

## Future phases (separate explicitly approved PRs)

1. **Phase A — remove redundant 10-job matrix only after verifying enforced branch protection.** GitHub branch-protection read currently returns **403** via connector; repository administrator must inspect Settings > Rules > Rulesets and Settings > Branches, record exact check names and enable replacement enforcement before job removal. Never remove a required job name silently.
2. **Phase B — coverage cadence**, optional proposal only. Do not downgrade/delete `Coverage (80% minimum)` for PRs unless an equally enforced pre-merge requirement is demonstrated, with specific coverage gating, rollback and Pimpro signoff.
3. **Phase C — PostgreSQL risk-tiered paths**, optional proposal only. Retain PostgreSQL for all tenancy/auth/security/finance/workflow/grade/inventory/queue/schema cases; a full PostgreSQL integration gate must remain enforced and bypass-proof. No paths-only exception by default.
4. **Cache and scheduling improvements**, only with reproducible data. Composer caching is already active in full and PostgreSQL jobs; coverage and suite jobs previously repeated installs. This PR removes the coverage job install entirely.

## Rollback

Revert this PR as a unit to restore the original `php artisan test --compact` job and the independent `phpunit.coverage.xml` second runner. No application schema, data or deployment changes. Since the PR retains all GitHub check names, rollback does not require branch protection edits.

**Approval:** Do not merge without current-head green CI and Project Manager review. No production changes are involved.
