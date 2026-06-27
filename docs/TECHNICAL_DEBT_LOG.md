# Technical Debt Log

Tracked technical debt items for FoundationOS. Update when debt is introduced or resolved.

**Last updated:** 2026-06-27

---

## Open

| ID | Area | Description | Priority | Notes |
|----|------|-------------|----------|-------|
| TD-001 | Core models | `Tenant` still has ~70 `hasMany` relations to feature modules | High | Deprecated relations removed 2026-06-27; full decoupling needs module-owned query APIs |
| TD-002 | Workflow | `WorkflowStepBranch` Filament CRUD exists but engine ignores branch rows | Medium | `WorkflowParallelCoordinator` comment — implement or hide admin UI |
| TD-003 | Workflow | `DynamicOptionsResolver` tested but not wired to runtime form rendering | Medium | `endpoint` kind also unimplemented (see `ROADMAP.md`) |
| TD-004 | Coverage | Full `app/` + `Modules/` line coverage ~6%; threshold applies to critical path scope only | Medium | Expand scope incrementally per module |
| TD-005 | Executive | `ExecutiveWarningService` only consumed by roadmap test, not dashboard | Low | Integrate into GRC widgets or defer |
| TD-006 | Auth | `UserPasswordPolicy::mustChangeAfterDays()` not enforced on login | Low | Config `auth.password_change_days` exists; needs middleware |
| TD-007 | API | Core scaffold `api/v1/cores` removed; no public Core REST surface | Low | Domain modules own API routes |
| TD-008 | PHPStan | Filament/Livewire dynamic properties rely on stubs (`phpstan-stubs/`) | Low | Acceptable until upstream generics improve |
| TD-009 | Tenancy | `LibraryScopeResolver` uses `Filament::getTenant()` instead of `CurrentTenant` | Low | Consolidation opportunity |
| TD-010 | Modules | ~40 experimental GA modules with thin factory/CRUD coverage only | Medium | See `ThinGaModuleTestH2/H3` |

---

## Resolved

| ID | Resolved | Description |
|----|----------|-------------|
| TD-R001 | 2026-06-27 | Moodle outbox tests used hardcoded `tenant_id=1` — fixed with `CreatesTenantForTests` |
| TD-R002 | 2026-06-27 | 22 deprecated `Tenant`/`User` cross-module relations removed |
| TD-R003 | 2026-06-27 | `UserPasswordPolicy` unused — wired to Filament `UserForm` |
| TD-R004 | 2026-06-27 | Core nwidart scaffold (`CoreController`, hello-world views) deleted |
| TD-R005 | 2026-06-27 | Workflow advance actions not allowlisted — `WorkflowActionGuard` added |
| TD-R006 | 2026-06-27 | Workflow automation could dispatch arbitrary jobs — allowlist enforced |
| TD-R007 | 2026-06-27 | JSON Logic unbounded recursion — `MAX_DEPTH = 32` |
| TD-R008 | 2026-06-27 | PHPStan baseline (152 entries) eliminated — level 10 clean |
| TD-R009 | 2026-06-27 | No named test suites or coverage gate in CI |

---

## Debt intake process

1. Add row to **Open** with unique `TD-###` ID.
2. Link to issue/PR when work starts.
3. Move to **Resolved** with date when fixed; reference regression test if applicable.
