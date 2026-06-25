# Refactor Stages 0–6 — Changelog & Handoff

> Ringkasan pekerjaan refactor bertahap (Juni 2026). Branch stack: `stage1` → `stage2` → … → `stage6`.  
> PR: [#14](https://github.com/yusufbayuw/foundationOS/pull/14) … [#19](https://github.com/yusufbayuw/foundationOS/pull/19).

---

## Executive Summary

| Stage | Fokus | Hasil utama |
|-------|--------|-------------|
| **0** | Baseline & safety net | PHPStan level 0→1, CI static gate, characterization tests sebelum refactor |
| **1** | Safety net | Characterization tests modul kritis; perbaikan setup test Moodle outbox |
| **2** | Architecture | Library OPAC diekstrak ke services + Form Requests |
| **3** | Database layer | Eager loading `ModuleResource`, workflow relation manager shared, Library enum/scopes |
| **4** | Filament UX | Komponen table/action reusable; pilot `PurchaseRequisition`; workflow page service |
| **5** | Filament tests | Livewire CRUD/auth/action tests; `BootstrapsFilamentAdmin` |
| **6** | Perf & security | Queue notifikasi/broadcast, cache resolver, rate limits, hardening action Filament |

**Test suite:** ~891 → **911 tests** (+20), **4133 assertions** (+62). Larastan **level 1, 0 errors** (baseline ~913 entri).

---

## Stage 0 — Baseline & Safety Net

### Apa yang diubah
- PHPStan/Larastan dipromosikan ke **level 1** dengan `phpstan-baseline.neon` (~152–913 entri aktif seiring cleanup).
- CI job `static.yml`: Pint + PHPStan (level 1 + baseline) + `phpstan-app.neon` untuk API resources.
- Dokumentasi analisis (`ANALISIS_REKOMENDASI.md`) memetakan risiko arsitektur.

### Kenapa
Tanpa baseline statis dan CI, refactor multi-modul (~380 resource Filament) berisiko regressions silent.

### Risiko tersisa
- Baseline masih besar; **level 2** menambah 1000+ finding baru — belum siap dinaikkan.
- ~209 resource tanpa policy eksplisit (Shield) — authorization bergantung regenerasi permission.

### Follow-up
- Kurangi baseline 10–20 entri per sprint; target level 2 dalam 2–3 gelombang cleanup.
- Aktifkan branch protection GitHub (require `tests` + `static`).

---

## Stage 1 — Safety Net (Characterization Tests)

### Apa yang diubah
- `tests/Feature/Characterization/` — snapshot perilaku sebelum refactor (Library, Procurement, Workflow, dll.).
- Perbaikan `MoodleOutboxAtomicClaimTest` / tenant FK setup.

### Kenapa
Characterization tests memberi jaring pengaman saat mengekstrak controller/service (Stage 2+).

### Risiko tersisa
- Characterization ≠ spec lengkap; edge case baru tetap perlu feature test dedicated.

### Follow-up
- Tambah characterization untuk modul berikutnya sebelum refactor besar (Finance reports, Enrollment pipeline).

---

## Stage 2 — Architecture (Library OPAC)

### Apa yang diubah
- `Modules/Library/app/Http/Controllers/OpacController.php` ditipis.
- Services baru: `OpacSearchService`, `CirculationAuthorizationService`, `OpacReservationService`, dll.
- Form Requests untuk validasi input OPAC.

### Kenapa
Controller monolit sulit di-test dan rawan authorization bypass; service layer memisahkan auth, query, dan mutasi.

### Risiko tersisa
- Modul lain (~40 GA) masih pola controller/resource langsung tanpa service extraction.

### Follow-up
- Terapkan pola yang sama ke endpoint publik/API lain (Enrollment inquiry, document PDF controllers).

---

## Stage 3 — Database Layer

### Apa yang diubah

| Area | Perubahan |
|------|-----------|
| **Core** | `FilamentResourceEagerLoads` — auto `with(['tenant','organization'])` di `ModuleResource::getEloquentQuery()` |
| **Workflow** | Base `WorkflowInstancesRelationManager`; 6 module RMs extend; `WorkflowInstance::scopeWithTableRelations()` |
| **Library** | Enum casts (`LoanStatus`, `MemberStatus`); trait `ScopesOrganizationVisibility`; query helpers `Book::scopeActive()`, `Loan::scopeActive()` |
| **Tests** | `FilamentResourceEagerLoadingTest` — bukti query count turun (11→3, 10→4) |

### Kenapa
N+1 di list Filament (~380 resource) dan relation manager workflow adalah bottleneck operasional.

### Risiko tersisa
- **Index komposit** untuk `tenant_id + status` diusulkan di PR #16 — belum di-apply (menunggu konfirmasi DBA).
- Morph map: `purchase_requisition` ditambahkan Stage 5; subject workflow lain mungkin belum terdaftar.

### Follow-up
- Apply index migrations setelah review beban query production.
- Audit semua `StartsWorkflow` models → daftarkan alias di `AppServiceProvider::enforceMorphMap()`.

---

## Stage 4 — Filament Reusable Layer

### Apa yang diubah

**Core — komponen reusable**

| Class | Path | Fungsi |
|-------|------|--------|
| `CommonTableColumns` | `Modules/Core/.../Tables/` | Kolom tenant, user, status badge, timestamps |
| `StatusSelectFilter` | idem | Filter status bilingual via `FilamentUi` |
| `StandardSoftDeleteTable` | idem | Record actions + bulk soft-delete defaults |
| `PanelNotification` | `Modules/Core/.../Notifications/` | Wrapper notifikasi panel success/danger |

**Workflow — action + service layer**

| Class | Fungsi |
|-------|--------|
| `WorkflowSubjectPageService` | `hasActiveInstance`, `startApprovalWorkflow` |
| `StartSubjectWorkflowAction` | Action header ViewRecord — start workflow |
| `OpenActiveSubjectWorkflowAction` | Action header — buka instance aktif |

**Procurement pilot — `PurchaseRequisition`**

- `ViewPurchaseRequisition`: 106 → ~32 baris (actions diekstrak).
- `PurchaseRequisitionsTable`: pakai shared columns/filters.
- `DownloadPurchaseRequisitionPdfAction` — action PDF terpisah.
- `PurchaseRequisitionStatusOptions` — label status bilingual.

**Secondary:** `ViewBudget` pakai workflow actions; `LibraryStatsOverview` tenant-scoped + cache 5 menit.

### Kenapa
Duplikasi table column/filter di ratusan resource; logic workflow di page class sulit di-reuse dan di-test.

### Pola yang harus diikuti tim (→ lihat `CLAUDE.md`)

1. **Schema/Table class** — jangan inline form/table di Resource.
2. **Action class** (`Modules/*/Filament/Actions/`) — custom header/table actions, bukan closure 50 baris di Page.
3. **Service class** — business logic (workflow start, PDF assembly) di `Services/`, Page hanya wiring.
4. **Support class** — enum/options/filter labels (`PurchaseRequisitionStatusOptions`).

### Risiko tersisa
- Hanya **1 resource** (`PurchaseRequisition`) di-refactor penuh; ~379 resource lain masih pola lama.
- Rollout `CommonTableColumns` ke semua module = effort besar; prioritaskan modul high-traffic.

### Follow-up
- Batch rollout 5–10 resource/modul sprint (Finance invoices, Employee leave, School students).
- Generators/skills Cursor untuk scaffold Action + Schema split otomatis.

---

## Stage 5 — Filament Testing

### Apa yang diubah

| File | Cakupan |
|------|---------|
| `BootstrapsFilamentAdmin` | Super-admin + tenant member + `actAsFilamentTenantMember()` |
| `ProcurementPurchaseRequisitionFilamentTest` | List/create/edit/delete/bulk; custom actions visibility |
| `ProcurementPurchaseRequisitionAuthorizationTest` | `assertForbidden()` tanpa Shield permission |
| Bugfix | `purchase_requisition` morph map |

### Kenapa
Stage 4 actions belum teruji Livewire; morph map missing menyebabkan `ClassMorphViolationException` di View page.

### Risiko tersisa
- `StartSubjectWorkflowAction` + redirect — `callAction()` Livewire tidak reliable untuk assert DB; visibility + service test dipisah.
- C coverage tool (pcov/xdebug) tidak ada di semua environment CI.

### Follow-up
- Template test Filament per resource (copy dari Procurement pilot).
- Browser test opsional untuk redirect workflow (Playwright/Dusk).

---

## Stage 6 — Performance & Security

### Apa yang diubah

**Queue**

| Job | Trigger |
|-----|---------|
| `DeliverNotificationJob` | Channel eksternal (whatsapp, mail, …) via `NotificationDispatcher` |
| `SendBroadcastNotificationsJob` | Mass broadcast via `BroadcastService` |
| Filament export | Sudah queued oleh Filament (`PrepareCsvExport`, dll.) |

**Cache**

| Target | TTL |
|--------|-----|
| `DatabaseWorkflowResolver` | 5 menit |
| `NotificationDispatcher::channelEnabled()` | 10 menit |
| `LibraryStatsOverview` widget | 5 menit (Stage 4) |
| `ModuleVisibility` navigation | 5 menit (existing) |

**Rate limiting**

| Limiter | Scope | Limit |
|---------|-------|-------|
| `documents` | PDF Procurement + Finance | 30/min |
| `api-write` | POST API v1/v2 writes | 30/min |
| `api` | Semua API (existing) | 60/min |

**Security — Stage 4 actions**

- `WorkflowSubjectPageService`: tenant scope + `Gate::authorize('update')` + reject locked records.
- `StartSubjectWorkflowAction`: `Filament` import fix, `->authorize()`, visibility guard.
- `ViewPurchaseRequisition`: start workflow hanya status `draft`.

### Kenapa
Broadcast/notifikasi massal blocking request; workflow resolver hot path; PDF endpoints tanpa throttle; action Filament tanpa authorization eksplisit.

### Risiko tersisa
- Queue `database`/`sync` di dev — production wajib `redis`/`database` worker.
- Cache workflow resolver stale 5 menit setelah publish definition (acceptable untuk definisi jarang berubah).
- Rate limit PDF belum di-rollout ke semua modul (Exam, Property, MerchOrder, …).

### Follow-up
- Rollout `throttle:documents` ke semua route `*/pdf`.
- Monitor queue depth + failed jobs (`DeliverNotificationJob`, `SendBroadcastNotificationsJob`).
- PHPStan level 2 setelah baseline < 400 entri.

---

## Konvensi Baru (Quick Reference)

```
Modules/<Module>/app/
  Filament/
    Actions/           ← custom Action::make wrappers (static make())
    Resources/<Model>/
      Schemas/         ← *Form.php, *Infolist.php
      Tables/          ← *Table.php
      Pages/           ← thin; delegate to Actions + Services
  Services/            ← domain logic, authorization-adjacent rules
  Support/             ← enums, status options, filter label maps

Modules/Core/app/Filament/Support/
  Tables/CommonTableColumns.php
  Tables/StatusSelectFilter.php
  Tables/StandardSoftDeleteTable.php
  Notifications/PanelNotification.php

tests/Concerns/BootstrapsFilamentAdmin.php   ← Filament Livewire tests
tests/Feature/Filament/                      ← resource/action tests
tests/Feature/Security/                      ← authorization tests
tests/Feature/Performance/                   ← cache/query proofs
```

**Filament v5 namespaces:** Actions = `Filament\Actions\` (bukan `Filament\Tables\Actions\`).

**Morph map:** Setiap model dengan `morphMany(WorkflowInstance)` wajib terdaftar di `AppServiceProvider::enforceMorphMap()`.

---

## Remaining Risks (Cross-Cutting)

| Risiko | Severity | Mitigasi disarankan |
|--------|----------|---------------------|
| PHPStan baseline besar | Medium | Incremental cleanup; jangan naik level tanpa budget |
| Resource rollout partial | Medium | Prioritas modul operasional + template Stage 4 |
| Shield permissions drift | Medium | `shield:generate` setelah resource baru; test authorization |
| Tenancy fail-open default | High (prod) | `TENANCY_SCOPE_FAIL_CLOSED=true` di production |
| N+1 di resource belum eager-load | Medium | Extend `FilamentResourceEagerLoads` per domain |
| Missing DB indexes | Medium | Apply Stage 3 proposals; monitor slow query log |
| Queue worker tidak jalan | High (prod) | Supervisor + alerting failed_jobs |

---

## Recommended Next Epics

1. **Stage 4 rollout** — batch 10 resource Filament ke pola Action/Schema/Service.
2. **Index migration pack** — apply composite indexes dari Stage 3 proposal.
3. **PHPStan level 2** — reduce baseline, enable stricter property checks module-by-module.
4. **Public API hardening** — rate limit read endpoints, OpenAPI contract tests.
5. **Workflow V3** — lihat `ROADMAP.md` (parallel gateway UX, SLA dashboard).

---

## Verification Commands

```bash
# Full suite (expect 911+ tests)
php artisan test --compact

# Static analysis (level 1, 0 errors target)
vendor/bin/phpstan analyse --memory-limit=1G

# Translation lint
composer run lint:translations

# Format
vendor/bin/pint --dirty --format agent
```

---

*Terakhir diperbarui: Stage 7 final review — Juni 2026.*
