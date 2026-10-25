# Issue #115 — Risk-aware CI tiering experiment

**Status 25 Oct 2026:** proposed in stacked PR targeting #126. No production or main changes.

## Verified size and time

[PR #126 Tests](https://github.com/yusufbayuw/foundationOS/actions/runs/40159643715): PHPUnit PASS **7,897 tests / 24,176 assertions**, ~26m44s executing the full suite with PCOV, Tests workflow ~27m21s after removing 10 duplicated named-suite jobs. Full tests remain in the repository; number was read from actual PHPUnit output, not estimated.

PR #127 four-way sharding experiment has all four test shards PASS but comparison FAIL at manifest equality; its exact coverage parity is **NOT** proven. Do not replace full regression based on that experiment.

## Targeted tiers

- **Draft PR:** fast Unit, Tenant, Policy, Permission, Queue, Regression suites plus the independent security/quality/static/backup workflows. Full PHPUnit and cross-DB suites skipped while draft.
- **Ready PR, narrow non-sensitive changes:** same fast core plus explicitly mapped module feature tests. Eligible only when <=15 paths change across <=2 allowlisted modules and known documentation/assets. Mappings: Ai, Cms, Capacity, Consulting, KpiEnterprise, Printing, Property. Any missing mapped file -> full.
- **Ready PR, high-risk/unknown:** existing complete PHPUnit+PCOV coverage with threshold >=80%, PostgreSQL priority suites and MariaDB migration smoke. This includes all edits under Core, Auth, SaaS/tenant, routes, config, migrations, payment, accounting, academic, queue/ jobs, API and all unknown/unmapped files. If diff cannot be computed, classifier fails and integration gate FAILS.
- **Main push, daily scheduled and manual dispatch:** full PHPUnit+PCOV and cross-DB smoke, retain 80% threshold.
- **Every classifiable PR:** external Composer/npm audit, secret scanning, static checks, lint and backup/restore workflows remain unmodified.

The explicit **CI integration gate (risk-aware)** fails when any result required for the selected tier fails or disappears. It must not replace independent Quality and Supply Chain, secret scanning, static analysis or deployment gates.

## Conservative defaults and irreversible gates

- Do NOT use PR labels or descriptions to lower risk tier. The classifier reads the exact git merge base/head changed paths with `git diff --name-only -z` and exits fail-closed on checkout/diff failure.
- No database reset or seeder action was added. MariaDB `migrate:fresh` remains strictly in disposable CI when full is required.
- **Main is currently unprotected.** Owner/admin must configure mandatory branch rules and up-to-date successful checks before allowing routine low-risk merges. Until that is done, PM must manually review, require full premerge evidence where uncertain, and never automate merge based solely on an ordinary fast check.
- Critical Laravel Composer advisory Issue #121 is independent and must stay blocking on Quality and Supply Chain until PR #123 is reviewed and merged.
- The `on.pull_request.branches` includes temporary stacked test branch `agent/devops-ci-duplicate-suite-removal-20261025`; remove that value before final merge to main.
- Changes in tests or in CI files trigger FULL by classifier (including changes to the classifier itself).
- Baseline target fast <=5min, ordinary ready <=10min, but **no duration claim until benchmark measured**.

## Acceptance gates

- [ ] Exact-head Actions run completes on this branch with all appropriate mandatory checks passing.
- [ ] Classifier negative fixtures prove auth/finance/db/config/tests/unknown/cross-module changes require full.
- [ ] A separate representative narrow PR demonstrates limited smoke plus mapped tests and valid risk gate.
- [ ] A sensitive sample (tenant/auth/finance/migration) demonstrates that complete PHPUnit+coverage>=80% + PostgreSQL/MariaDB are non-skippable.
- [ ] A faulty or empty diff fails closed (no green result through missing prerequisites).
- [ ] Docs-only branch shows no heavy PHPUnit job and still passes independent security checks.
- [ ] Measure median and tail wall-clock times AND runner-minute costs over >=3 comparable runs, avoiding comparison across unrelated dependency loads.
- [ ] QA/Security review validates whether the allowlist is appropriately conservative.
- [ ] Branch protection/ruleset verified (or manual PM gate maintained) before merging any PR on fast-only evidence.

**Rollback:** revert this PR; restores #126 behavior (full on every ready PR and main), independent of business data.

Full suite code is not deleted or weakened: the change adjusts trigger frequency and risk classification only.
