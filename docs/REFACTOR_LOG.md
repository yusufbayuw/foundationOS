# Refactor Log

Chronological record of structural refactors applied to FoundationOS.

**Last updated:** 2026-06-27

---

## 2026-06-27 — Final cleanup & testing audit

### Testing architecture

- Reorganized PHPUnit into **10 named suites**: Unit, Feature, Workflow, Tenant, Policy, Permission, Regression, Queue, ParallelWorkflow, ImportExport.
- Added `phpunit.coverage.xml` with **80% minimum** on critical integration paths (Workflow support, Moodle sync, queue middleware, tenant context).
- Added `tests/Regression/KnownBugRegistry.php` — every production bug fix must register a regression test ID.
- Ported workflow security hardening: `WorkflowActionGuard`, automation job allowlist, JSON Logic depth cap.

### Core model decoupling

- Removed **22 deprecated cross-module relations** from `Tenant` (3) and `User` (19) that had zero callers.
- Reduces inverted dependencies (Core → feature modules) documented in `docs/refactor/foundationos-refactor-audit.md`.

### Dead code removal

- Deleted nwidart scaffold: `CoreController`, hello-world views, `api/v1/cores` and web `cores` routes.
- Wired `UserPasswordPolicy` into `UserForm` password validation (was unused).

### Quality gates

- PHPStan level 10 — 0 errors (no baseline).
- Laravel Pint enforced on dirty files.
- CI: PHPUnit + named-suite matrix + coverage threshold job.

---

## 2026-06-19 — Tenancy security hardening

- `scope_fail_closed` production default.
- `MissingTenantContextException` for HTTP requests without tenant when fail-closed enabled.
- Reverse-engineering documentation under `docs-reverse-engineering/`.

---

## 2026-05-24 — Module boundary audit

- Documented Core model coupling problem (`Tenant`/`User` importing 76+ feature models).
- Identified `ModuleResource` as central Filament coupling point.
- See `docs/refactor/foundationos-refactor-audit.md` for full analysis and target architecture.

---

## Conventions for future entries

Each entry should include:

1. **What changed** (files/modules)
2. **Why** (problem solved)
3. **Risk** (breaking changes, migration needed)
4. **Verification** (tests/commands run)
