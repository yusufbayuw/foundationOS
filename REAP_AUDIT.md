# REAP v1.0 — Reverse Engineering Auditor Protocol

**Audit run:** 2026-06-19  
**Repository:** FoundationOS (`cursor/use-case-model-0a94`)  
**Auditor role:** AI Reverse Engineering Auditor  
**Stage covered:** Technical documentation discovery (artifacts produced on branch)

---

## 1. Scope Analisis

| Item | Boundary |
|------|----------|
| In scope | Root `.md` discovery artifacts, `docs/catalogs/*.json`, extraction scripts under `scripts/`, cross-check against PHP routes/migrations/config |
| Out of scope | Runtime DB dumps (`permissions` table rows), production deploy state, OpenAPI byte-for-byte diff, line-by-line audit of all 13 sequence diagrams |
| Method | Static code analysis, `php artisan route:list --json`, scripted scans, spot-check line references |

---

## 2. Artifact Registry

| Artifact Name | Artifact Purpose | Location | Artifact Status |
|---------------|------------------|----------|-----------------|
| Technical Documentation (master) | Consolidated index & catalogs | `TECHNICAL_DOCUMENTATION.md` | **Verified** (metrics re-checked); partial gaps remain |
| Use Case Model | Actors & use cases | `USE_CASES.md` | **Verified** (spot-check); no REAP EV-IDs yet |
| Sequence Diagrams | Message-level flows SD-01–SD-13 | `SEQUENCE_DIAGRAMS.md` | **Draft** — stale line refs found |
| Activity Diagrams | Processes AD-01–AD-07 | `ACTIVITY_DIAGRAMS.md` | **Verified** (workflow line refs spot-check) |
| State Diagrams | State machines ST-01–ST-13 | `STATE_DIAGRAMS.md` | **Draft** — not fully re-audited |
| Architecture | Layer & integration map | `ARCHITECTURE.md` | **Verified** (structure); shares gaps with master |
| Authorization Matrix | Shield permission catalog | `AUTHORIZATION_MATRIX.md` + `docs/catalogs/authorization-matrix.json` | **Verified** (257 resources); permissions **INFERRED** from config |
| API Routes Catalog | Full `api/*` routes | `API_ROUTES.md` + `docs/catalogs/api-routes-catalog.json` | **Verified** (101 routes, JSON bootstrap) |
| Event catalog | Cross-module events | `EVENTS.md` | **Verified** (pre-existing; listener files confirmed) |
| Entity catalog | 434 tables / 402 models | `storage/app/entity-catalog.json` | **UNVERIFIED in git** — file not committed |
| FK catalog | Foreign keys | `storage/app/verified-fks.json` | **UNVERIFIED in git** — file not committed |
| REAP Audit (this file) | Protocol compliance & validation | `REAP_AUDIT.md` | **Final** for discovery stage |
| Repository Manifest | Stage 01 scope boundary | `00-repository-manifest.md` | **Verified** |
| Evidence Registry | Stage 02 full evidence index | `01-evidence-registry.json` | **Verified** |
| Structure Catalog | Stage 03 folders, modules, entrypoints | `02-structure-catalog.md` | **Verified** |
| Module Dependency Map | Stage 04 inter-module graph, hub/leaf, Filament recount | `03-module-dependency-map.md` | **Verified** |

---

## 3. Temuan (with Evidence & Confidence)

### 3.1 Repository metrics

| Finding | Confidence | Evidence ID | Proof |
|---------|------------|-------------|-------|
| 45 feature modules under `Modules/` | **VERIFIED** | EV-00001 | `ls Modules/ \| wc -l` → 45 |
| 257 classes extend `ModuleResource` | **VERIFIED** | EV-00002 | `find Modules -name '*Resource.php' -exec grep -l 'extends ModuleResource'` → 257; `docs/catalogs/authorization-matrix.json` `resource_count` |
| 1778 application routes (except vendor) | **VERIFIED** | EV-00003 | `php artisan route:list --except-vendor --json` array count → 1778 |
| 101 API routes (`api/*`) | **VERIFIED** | EV-00004 | `docs/catalogs/api-routes-catalog.json` `route_count`; `scripts/extract-api-routes.php` |
| 39 `*PdfController.php` files | **VERIFIED** | EV-00005 | `find Modules -name '*PdfController.php' \| wc -l` → 39 |
| 28 scheduled commands | **VERIFIED** | EV-00006 | `grep -c 'Schedule::command' routes/console.php` → 28 |
| 132 CSV importers | **VERIFIED** | EV-00007 | `find app/Filament/Imports -name '*Importer.php' ! -name BaseModelImporter \| wc -l` → 132 |
| ~3100 Shield permission keys | **INFERRED** | EV-00008 | 257×12+16=3100; assumes all resources get all 12 policy methods from `config/filament-shield.php` |
| 434 DB tables / 402 models | **UNVERIFIED in git** | EV-00009 | Cited from `storage/app/entity-catalog.json` — file exists locally but **not** in `git ls-files` |
| ~5472 FK entries | **UNVERIFIED in git** | EV-00010 | `storage/app/verified-fks.json` count locally; not committed |

### 3.2 Tenancy & auth

| Finding | Confidence | Evidence ID | Proof |
|---------|------------|-------------|-------|
| Shared DB; `tenant_id` scoping | **VERIFIED** | EV-00011 | `Modules/Core/app/Models/Concerns/BelongsToTenant.php`; migrations `tenant_id()->constrained('tenants')` |
| Admin panel access via `user_tenant_roles` or super admin | **VERIFIED** | EV-00012 | `Modules/Core/app/Models/User.php` `canAccessPanel()` lines 471–476 |
| Platform panel requires `platform_owner` role, team 0 | **VERIFIED** | EV-00013 | `User.php` lines 465–468; `database/migrations/2026_05_22_145853_create_platform_owner_role.php` |
| Parent panel requires `parent_students` link | **VERIFIED** | EV-00014 | `User.php` lines 479–481 |
| Global super admin `Gate::before` bypass | **VERIFIED** | EV-00015 | `app/Providers/AppServiceProvider.php` lines 93–98 |
| Spatie teams; `team_foreign_key = tenant_id` | **VERIFIED** | EV-00016 | `config/permission.php` lines 96, 134 |

### 3.3 API surface

| Finding | Confidence | Evidence ID | Proof |
|---------|------------|-------------|-------|
| Core mobile writes: applicants, payments, leave-requests with idempotency | **VERIFIED** | EV-00017 | `routes/api.php` POST routes + `idempotency` middleware group |
| Exam runtime: `POST api/exam/runtime/attempts`, Sanctum, no `resolve.api.tenant` on group | **VERIFIED** | EV-00018 | `Modules/Exam/routes/api.php`; `docs/catalogs/api-routes-catalog.json` |
| Public enrollment inquiry `POST api/inquiry` | **VERIFIED** | EV-00019 | `docs/catalogs/api-routes-catalog.json` → `InquiryController@store` |
| Module REST scaffolds (e.g. `api/v1/campuses` CRUD) | **VERIFIED** | EV-00020 | `API_ROUTES.md` Campus 5 routes; module route service providers |

### 3.4 ERD hub (corrected)

| Finding | Confidence | Evidence ID | Proof |
|---------|------------|-------------|-------|
| Table `students` (not `school_students`) | **VERIFIED** | EV-00021 | `Modules/School/database/migrations/2026_03_24_152528_create_students_table.php` |
| Table `classes` (not `school_classes`) | **VERIFIED** | EV-00022 | `class_students.class_id` → `classes` in `2026_03_24_152531_create_class_students_table.php` |
| `applicants.converted_to_student_id` → `students` | **VERIFIED** | EV-00023 | `Modules/Enrollment/database/migrations/2026_03_24_152536_create_applicants_table.php` |
| `workflow_instances.subject_type/id` polymorphic (no direct FK to PR) | **VERIFIED** | EV-00024 | `Modules/Workflow/database/migrations/2026_03_26_210300_create_workflow_instances_table.php` lines 21–22 |

### 3.5 Explicit non-claims

| Finding | Confidence | Evidence ID | Proof |
|---------|------------|-------------|-------|
| No Dockerfile / docker-compose in repo | **VERIFIED** | EV-00025 | `test ! -f Dockerfile && test ! -f docker-compose.yml` |
| No student Filament panel | **VERIFIED** | EV-00026 | Only 3 panel providers: `AdminPanelProvider`, `PlatformPanelProvider`, `ParentPanelProvider` |
| Moodle outbound only (no inbound webhook actor) | **VERIFIED** | EV-00027 | `app/Integrations/Moodle/`, `ProcessMoodleSyncOutboxJob`; no inbound Moodle route in route list |

---

## 4. Conflict Section

**Rule:** Do not overwrite silently. Report and resolve explicitly.

| Conflict ID | Artifact A | Artifact B | Nature | Resolution |
|-------------|------------|------------|--------|------------|
| **CF-001** | `SEQUENCE_DIAGRAMS.md` SD-05 error table cites `DatabaseWorkflowEngine.php:48` for `WorkflowAuthorizationException` | `ACTIVITY_DIAGRAMS.md` cites `416-430` for same concern | Line reference precision | Line **48** is `authorizeActor()` **call site** inside `advance()`; exception **thrown** at **429**. SD-05 should cite **416-430** (or 48 call + 429 throw). **ACTIVITY_DIAGRAMS is more accurate for the exception.** |
| **CF-002** | `TECHNICAL_DOCUMENTATION.md` cites `storage/app/entity-catalog.json` | Git tree | Traceability | JSON not committed (`storage/app/.gitignore`). Either commit extract under `docs/catalogs/` or mark table/model counts **UNVERIFIED in repo**. |
| **CF-003** | `USE_CASES.md` / older text uses **belum terverifikasi** | REAP requires **UNVERIFIED** | Terminology | Semantically equivalent; REAP audit standardizes on VERIFIED / INFERRED / UNVERIFIED. |
| **CF-004** | Prior assistant messages cited route counts **1782/105/1781/104** | Current `route:list --json` | Historical drift | **Authoritative:** 1778 total, 101 API (`EV-00003`, `EV-00004`). Master doc updated; chat history may be stale. |
| **CF-S04-01** | `02-structure-catalog.md` GAP-S03-03 (0 ModuleResource for Global/Procurement/Exam) | Source uses `ModuleResource as LocalizedResource` alias | False gap | 25 resources verified; grep methodology was incomplete. **Resolved in Stage 04.** |
| **CF-S04-02** | `authorization-matrix.json` `resource_count: 257` | Stage 04 scan: 375 Filament resources | Undercount | `extract-authorization-matrix.php:67` regex misses `extends LocalizedResource`. **Open** — fix script. |

---

## 5. Gap

| Gap ID | Description | Confidence impact | Recommended action |
|--------|-------------|-------------------|-------------------|
| **GAP-001** | `entity-catalog.json`, `verified-fks.json` not versioned | Table/model/FK counts **UNVERIFIED in git** | Run `scripts/extract-entity-catalog.php`; commit output to `docs/catalogs/` |
| **GAP-002** | Permission matrix derived from config, not DB | Runtime role assignments **UNVERIFIED** | Optional: export `permissions`/`roles` per tenant in seed environment |
| **GAP-003** | OpenAPI vs 101 API routes not diffed | API doc completeness **UNVERIFIED** | Script: compare `api/openapi.json` paths to `api-routes-catalog.json` |
| **GAP-004** | SD-01–SD-13 line-by-line audit incomplete | Sequence doc **Draft** | REAP pass per diagram with EV-IDs |
| **GAP-005** | ST-01–ST-13 full state transition audit incomplete | State doc **Draft** | Cross-check each transition against enum/guard in code |
| **GAP-006** | `TX-PUB-003` CMS contact form controller | **UNVERIFIED** | Locate route + controller or remove use case |
| **GAP-007** | Mobile push provider abstraction | **UNVERIFIED** | Trace `NotificationService` / device delivery driver |
| **GAP-008** | Discovery artifacts lack REAP EV-IDs inline | Traceability incomplete | Backfill EV-IDs into companion docs or link to this registry |

---

## 6. Evidence Index (abbreviated)

| EV-ID | File | Symbol / route | Notes |
|-------|------|----------------|-------|
| EV-00001 | `Modules/` | directory count | 45 modules |
| EV-00002 | `Modules/*/app/Filament/Resources/**` | `extends ModuleResource` | 257 files |
| EV-00003 | `php artisan route:list --except-vendor --json` | route count | 1778 |
| EV-00004 | `docs/catalogs/api-routes-catalog.json` | `route_count` | 101 |
| EV-00011 | `Modules/Core/app/Models/Concerns/BelongsToTenant.php` | trait | tenancy |
| EV-00012 | `Modules/Core/app/Models/User.php:471-476` | `canAccessPanel('admin')` | |
| EV-00015 | `app/Providers/AppServiceProvider.php:93-98` | `Gate::before` | |
| EV-00017 | `routes/api.php` | POST applicants/payments/leave-requests | |
| EV-00018 | `Modules/Exam/routes/api.php` | `exam/runtime/attempts` | |
| EV-00021 | `Modules/School/database/migrations/2026_03_24_152528_create_students_table.php` | `Schema::create('students')` | |
| EV-00024 | `Modules/Workflow/database/migrations/2026_03_26_210300_create_workflow_instances_table.php:21-22` | `subject_type`, `subject_id` | morph |

*Full EV list: sections 3.1–3.5 above. Expand on demand per REAP stage.*

---

## 7. End of Stage Validation Checklist

### Coverage

| Area | Covered? | Artifact |
|------|----------|----------|
| Use cases & actors | Yes | `USE_CASES.md` |
| Sequence flows | Partial | `SEQUENCE_DIAGRAMS.md` (Draft) |
| Activity processes | Yes | `ACTIVITY_DIAGRAMS.md` |
| State machines | Partial | `STATE_DIAGRAMS.md` (Draft) |
| Architecture | Yes | `ARCHITECTURE.md` |
| Master consolidation | Yes | `TECHNICAL_DOCUMENTATION.md` |
| Authorization | Yes (config-level) | `AUTHORIZATION_MATRIX.md` |
| API routes | Yes | `API_ROUTES.md` |
| ERD hub | Yes (corrected) | `TECHNICAL_DOCUMENTATION.md` §16 |
| REAP compliance | Yes | `REAP_AUDIT.md` (this file) |

### Missing Evidence

- [ ] Committed entity/FK catalogs (`GAP-001`)
- [ ] DB permission export (`GAP-002`)
- [ ] OpenAPI parity check (`GAP-003`)
- [ ] Full sequence/state line audits (`GAP-004`, `GAP-005`)

### Ambiguous Findings

- [ ] `SEQUENCE_DIAGRAMS.md:48` vs `416-430` for workflow auth (`CF-001`)
- [ ] Whether all 257 resources expose all 12 Shield methods at runtime (`EV-00008` INFERRED)

### Risk Assessment

| Risk | Severity | Mitigation |
|------|----------|------------|
| Undocumented API routes outside `api/*` (webhooks, Filament) | Medium | Separate REAP stage for web routes |
| Stale line numbers in diagrams | Medium | CI script: validate cited files exist |
| Uncommitted machine catalogs | High | Move to `docs/catalogs/` |
| INFERRED permission count used as fact | Low | Label **INFERRED** wherever ~3100 cited |

---

## 8. Stage Gate Decision

| Question | Answer |
|----------|--------|
| May next REAP stage begin? | **Conditional yes** — documentation discovery artifacts exist, but **SD/STATE diagrams remain Draft** and **entity catalog not in git**. |
| Recommended next stage | **REAP Stage 05:** Fix `extract-authorization-matrix.php` (CF-S04-02); commit entity/FK catalogs; FK dependency graph; fix `CF-001`. |
| Forbidden until resolved | Treating all companion `.md` files as **Final** without line-reference audit. |

---

## 9. Regeneration Commands (traceability)

```bash
php scripts/extract-authorization-matrix.php
php scripts/extract-api-routes.php
php scripts/generate-docs-appendices.php
php artisan route:list --except-vendor --json   # EV-00003
# entity catalog (local only until committed):
php scripts/extract-entity-catalog.php          # GAP-001
```

---

*REAP v1.0 — Evidence First, Traceability, Confidence Level mandatory on all future findings.*
