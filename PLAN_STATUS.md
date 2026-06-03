# PLAN_STATUS — Audit Implementasi vs Semua Rencana

> **Tanggal audit:** 3 Juni 2026  
> **Metode:** Penelusuran statis kode + perbandingan seluruh dokumen roadmap/plan di root repo.  
> **Legenda:** `[x]` selesai · `[~]` sebagian / scaffold GA · `[ ]` belum / backlog eksplisit

Dokumen ini adalah **sumber kebenaran terpusat** untuk status rencana. Roadmap individual (`ROADMAP*.md`) mungkin belum disinkronkan; lihat tabel di bawah untuk status aktual.

---

## Ringkasan Eksekutif

| Sumber rencana | Selesai | Sebagian | Belum | Catatan |
|----------------|--------:|---------:|------:|---------|
| [ROADMAP.md](ROADMAP.md) (Epic 1–6) | 19 fase | 0 fase | 0 fase | Epic 1.2 designer selesai 2026-06-03 |
| [ROADMAPv01.md](ROADMAPv01.md) Fase 1 SaaS | 8 | 2 | 7 | Banyak item sudah di kode tapi checkbox dokumen belum diupdate |
| [ROADMAPv02.md](ROADMAPv02.md) Operasional | 22 | 2 | 0 | Hampir lengkap |
| [ROADMAPv03.md](ROADMAPv03.md) BI & polish | 14 | 2 | 2 | HR→Finance sebenarnya sudah ada |
| [ROADMAPv2.md](ROADMAPv2.md) Translasi UI | 6 | 2 | 14 | Fondasi locale selesai; cleanup massal belum |
| [ROADMAPv04–v09](ROADMAPv04-overview.md) | — | 45 modul GA | Sprint v05–v09 | Modul scaffold aktif; fitur sprint belum penuh |
| [FASE_1_2_FULL.md](FASE_1_2_FULL.md) | 3 | 5 | 10 AC | Blueprint vs implementasi |
| [MOODLE_HARDENING_CHECKLIST.md](MOODLE_HARDENING_CHECKLIST.md) | 0 | — | 12 bagian | Checklist operasional go-live, bukan kode |
| [ANALISIS_REKOMENDASI.md](ANALISIS_REKOMENDASI.md) Gelombang 1–5 | 28 | 0 | 1 | Gelombang 1–5 hampir seluruhnya `[x]` |

**Kesimpulan singkat:** Domain inti (tenancy, workflow V2+V3 parsial, procurement automation, Moodle campus, API v1/v2) **sudah diimplementasi dan tertutup test**. Yang belum terutama: **SaaS monetization penuh**, **cleanup translasi massal**, **sprint v05–v09 (fitur mendalam)**, dan **checklist operasional Moodle go-live**.

---

## 1. ROADMAP.md — Epic Pengembangan Utama

| Fase | Status | Verifikasi kode |
|------|--------|-----------------|
| **Epic 1.1** Parallel Gateway & Quorum | `[x]` | `WorkflowParallelCoordinator`, `WorkflowParallelGatewayTest`, gateway columns |
| **Epic 1.2** Visual Workflow Designer | `[x]` | Designer + import JSON + publish archives; tests AC5–AC8, parallel gateway, Vite bundle separation |
| **Epic 1.3** Dynamic Form Data Source | `[x]` | `DynamicOptionsResolver`, `workflow-dynamic-sources.php`, `DynamicOptionsResolverTest`; **endpoint kind belum** |
| **Epic 2.1** Auto RFQ dari PR | `[x]` | `RfqAutoCreationService`, `CreateRfqFromApprovedPurchaseRequisition`, tests |
| **Epic 2.2** RFQ → PO | `[x]` | `PurchaseOrderAutoCreationService`, award flow, tests |
| **Epic 2.3** PO → GR → Vendor Bill | `[x]` | `ThreeWayMatchValidator`, pipeline tests |
| **Epic 3.1** Enrollment drift detection | `[x]` | `MoodleEnrollmentReconciler`, `MoodleEnrollmentDrift`, command, Filament resource |
| **Epic 3.2** Auto-fix enrollment | `[x]` | `--fix` pada reconcile command, tenant settings |
| **Epic 4.1** Global tenant scope | `[x]` | `BelongsToTenant`, `TenantScope`, widespread models |
| **Epic 4.2** Queue tenant context | `[x]` | `InteractsWithTenant`, job tests |
| **Epic 4.3** Tenant switch audit | `[x]` | `TenantSwitched`, `fos:tenancy:audit-leaks`, badge UI |
| **Epic 4.4** Package tenancy | `[x]` deferred | ADR `docs/adr/0001-tenancy-package-evaluation.md` — tidak adopt package |
| **Epic 5.1–5.4** API publik v1 | `[x]` | Sanctum tenant tokens, v1 routes, webhooks, mobile endpoints |
| **Epic 6.1** Course catalog & prerequisite | `[x]` | `CourseOfferingObserver`, offering idnumber, prerequisite hint |
| **Epic 6.2** KRS / Study plan sync | `[x]` | `StudyPlanItemObserver`, bulk-enroll command |
| **Epic 6.3** Lecturer assignment | `[x]` | offering lecturers sync tests |
| **Epic 6.4** Detailed grade pull | `[x]` | `MoodleDetailedGradePullService`, GPA calculator |
| **Epic 6.5** Thesis workflow | `[x]` | thesis lifecycle + workflow template |
| **Epic 6.6** Academic calendar sync | `[x]` | calendar events, offering dates |

**Belum / sisa epic ROADMAP.md:**
- `[ ]` `options_source` kind **endpoint** (hanya eloqent + enum)
- `[ ]` Study plan **grace period** timer terjadwal (unenroll langsung saat status berubah)

---

## 2. ROADMAPv01 — Fase 1 Multi-Tenant SaaS

| Item | Status dokumen lama | **Status aktual (kode)** |
|------|---------------------|--------------------------|
| Central subdomain routing | `[ ]` | `[ ]` — path-based `/admin` tetap; backlog eksplisit v01 |
| Tenant scope manual | `[x]` | `[x]` |
| Single DB multi-tenancy | `[x]` | `[x]` |
| Module registry + TenantModule | `[x]` | `[x]` |
| Navigation gating `tenant_modules` | `[ ]` | `[x]` — `ModuleVisibility::shouldRegisterNavigation()` + `ModuleMarketplace` |
| MFA Filament v5 | `[ ]` | `[x]` — `AdminPanelProvider::multiFactorAuthentication()` + kolom MFA migration |
| RBAC Shield | `[x]` | `[x]` |
| Spatie ActivityLog | `[ ]` | `[ ]` — masih `Monitoring\AuditLog` custom |
| Payment gateway | `[ ]` | `[~]` — `BillingService`, Midtrans webhook, `BillingPage`; engine subscription penuh belum |
| Pay-per-module / seat calculator | `[ ]` | `[ ]` — model ada, kalkulator belum |
| Grace period auto-lock | `[ ]` | `[~]` — `EnsureTenantSubscriptionActive` middleware ada; perlu verifikasi lengkap |
| White labeling | `[ ]` | `[~]` — `brand_logo`, `primary_color` via `TenantSetting` + panel closures |
| Localization id/en | `[x]` | `[x]` |
| Currency per tenant | `[ ]` | `[ ]` |
| Landing page publik | `[ ]` | `[~]` — `welcome` view di `/`, bukan marketing lengkap |
| Self-registration tenant | `[ ]` | `[x]` — `->registration()`, `RegisterTenant`, email verification |
| Super Admin platform panel | `[ ]` | `[x]` — `PlatformPanelProvider`, `/platform`, tests |
| Tenant dashboard | `[x]` | `[x]` — `TabbedDashboard` |
| App Store modul | `[ ]` | `[x]` — `ModuleMarketplace` page |

---

## 3. ROADMAPv02 — Operasional Inti (HRM, Finance, Inventory, Procurement)

| Area | Status |
|------|--------|
| Employee, payroll, slip PDF, BPJS/PPh21 | `[x]` |
| Finance COA, GL, AP/AR, laporan standar | `[x]` |
| Inventory WMS, valuation, procurement chain | `[x]` |
| Workflow gates (procurement, finance, HRM) + evidence | `[x]` |
| Audit trail HasAuditTrail | `[x]` |
| Event listener PR→RFQ | `[x]` |
| dompdf pinned | `[x]` |
| Approval limit JsonLogic seeders | `[x]` |
| Cross-module event bus luas | `[~]` — terbatas pada procurement; bukan bus umum |

**Belum:** tidak ada item operasional inti yang masih `[ ]` di v02.

---

## 4. ROADMAPv03 — BI, Reporting, Edutech

| Item | Status dokumen | **Status aktual** |
|------|----------------|-------------------|
| Executive dashboard & analytics | `[x]` | `[x]` |
| Custom widget builder | `[ ]` backlog | `[ ]` |
| Cross-module reporting, export, scheduled reports | `[x]` | `[x]` |
| Deep search | `[x]` | `[x]` |
| Keyboard shortcuts kustom | `[~]` | `[~]` |
| Audit explorer, security logs | `[x]` | `[x]` |
| Database archiving | `[ ]` backlog | `[ ]` |
| HR → Finance auto-journal payroll | `[ ]` | `[x]` — `PayrollJournalService` + integrasi salary slip |
| Sales/POS → Finance | `[~]` | `[~]` — modul Sales GA scaffold; POS/journal listener belum lengkap |
| School SPP → Finance | `[x]` | `[x]` |
| Pulse, indexing | `[x]` | `[x]` |
| SIAKAD K-12 & PT | `[x]` | `[x]` |
| LMS native | `[~]` Moodle | `[~]` |
| Raport workflow | `[x]` | `[x]` |

---

## 5. ROADMAPv2 — Standardisasi Translasi UI

| Epic | Status dokumen | **Status aktual** |
|------|----------------|-------------------|
| 1.1 Locale switching | `[ ]` | `[x]` — `SetUserLocale`, `preferred_locale`, `LocaleSwitchController`, tests |
| 1.2 Kamus FilamentUi | `[ ]` | `[~]` — kamus besar ada; cleanup massal belum |
| 1.3 Konvensi & linter | `[ ]` | `[~]` — `scripts/lint-translations.php`, `composer lint:translations`; **belum** required di CI |
| 2.x Workflow refactor label | `[ ]` | `[~]` — struktur `Schemas/` sebagian; hardcode masih dominan di Workflow |
| 3.x Section/layout titles | `[ ]` | `[ ]` — ratusan `Section::make('...')` hardcode |
| 4.x Table/infolist labels | `[ ]` | `[ ]` |
| 5.x Placeholder/helper | `[ ]` | `[ ]` |
| 6.x Per-module field cleanup | `[ ]` | `[ ]` |
| 7.1 User menu language | `[ ]` | `[x]` — user menu + profile + `/locale/{locale}` |
| 7.2 Tenant default locale | `[ ]` | `[x]` — `TenantSetting` `default_locale` |
| 8.1 CI linter wajib | `[ ]` | `[ ]` — tidak ada step di `.github/workflows/tests.yml` |
| 8.2 Snapshot bilingual | `[ ]` | `[~]` — `BilingualResourceSnapshotTest` ada; cakupan terbatas |
| 8.3 Update CLAUDE/Onboarding | `[ ]` | `[x]` — CLAUDE.md section translasi lengkap |

---

## 6. ROADMAPv04–v09 — 64 Area & Sprint Modul Baru

### 6.1 Modul aktif (`modules_statuses.json`)

**45 modul enabled**, semua tier **`ga`** di `config/fos_module_maturity.php` (`experimental` kosong).

Ini berarti: **scaffold GA** (resource, factory, importer, test tipis H2/H3) — **bukan** semua deliverable sprint v05–v09 selesai.

### 6.2 Pemetaan sprint vs implementasi (high level)

| Roadmap | Sprint / area | Status implementasi |
|---------|---------------|---------------------|
| **v05** | FoundationProfile, Stakeholder, Legal, Asset, Dms, Helpdesk, Facility, EOffice, ItOps, Transport, Boarding, Cafeteria, PhysicalSecurity | `[~]` — modul + CRUD + sebagian domain service registrasi; **belum** semua scheduled command, QR label, SLA helpdesk penuh, org chart |
| **v06** | Parent Portal, Counseling, Clinic, Event, MerchOrder, Alumni, Messaging | `[~]` — modul ada kecuali **ParentPortal** (`Modules/ParentPortal` tidak ada); BK/UKS/Event sebagai modul terpisah |
| **v07** | PPDB CRM, Donation, Sales/POS, CMS, Marketplace, unit usaha | `[~]` — modul GA; pipeline CRM/POS mendalam belum |
| **v08** | Risk, InternalAudit, IsoCompliance, EducationQa, KpiEnterprise, Capacity, Ai | `[~]` — modul + service pilot; BI/DWH/AI advisor terbatas |
| **v09** | WhatsApp, Mobile, Workflow expansion, External integrations, Public API, Super admin SaaS | `[~]` — Messaging/WhatsApp abstraction (`LogWhatsAppProvider`); API v2 `[x]`; mobile PWA `[ ]` |

### 6.3 Area v04 yang masih `NEW` atau `partial` (belum fitur penuh)

| § | Area | Status |
|---|------|--------|
| 9 | Parent Portal | `[ ]` modul belum dibuat |
| 48 | Mobile App | `[ ]` |
| 56 | Research & Incubator | `[ ]` backlog ≥18 bln |
| 7 | LMS in-house | `[ ]` skip — delegasi Moodle `[x]` |
| 2 | SSO/OAuth/LDAP | `[ ]` — di v09 backlog |
| 61–62 | SaaS premium (reseller, migration) | `[ ]` |

---

## 7. FASE_1_2_FULL.md — Visual Workflow Designer (detail AC)

| Deliverable / AC | Status |
|------------------|--------|
| `WorkflowDesignerPage` + `WorkflowCanvas` | `[x]` |
| `WorkflowDefinitionPorter` import/export JSON | `[x]` |
| `WorkflowVersionSnapshotService` / publish dari designer | `[x]` — via lifecycle service |
| AC1: 5 step + 2 parallel gateway tanpa kode | `[~]` — manual/UI belum diverifikasi test |
| AC2: draft tidak mempengaruhi instance running | `[~]` — perlu `WorkflowDesignerVersioningTest` |
| AC3–AC4: publish + export/import parity | `[~]` — `WorkflowDefinitionPorterTest` ada |
| AC5–AC8: test suite designer lengkap | `[x]` |
| AC9: bundle size gate | `[ ]` |
| AC10: WORKFLOW.md visual section | `[ ]` |
| Definition of Done (pint, shield, npm build) | `[~]` |

---

## 8. MOODLE_HARDENING_CHECKLIST.md — Operasional Go-Live

Semua **12 section** masih `[ ]` — ini **checklist deployment/ops**, bukan fitur aplikasi. Kode reconcile/outbox/health-check **sudah ada**; pemenuhan checklist bergantung environment produksi (HTTPS, cron Moodle, monitoring, UAT manual).

---

## 9. ANALISIS_REKOMENDASI.md — Gelombang Perbaikan

| Gelombang | Status |
|-----------|--------|
| Gelombang 1 — Stabilisasi CI, bug observer, dompdf, super_admin guard | `[x]` |
| Gelombang 2 — Outbox atomik, API tenant, workflow lock, SLA, library tenant, lint tenant fields | `[x]` |
| Gelombang 3 — Larastan/Pint CI, LazilyRefreshDatabase, eager load, Livewire CRUD sample | `[x]` |
| Gelombang 4 — GA modul, organizationSelect, domain services pilot, snapshot transition | `[x]` |
| Gelombang 5 — fail-closed tenancy, PHPStan L1, API v2, OpenAPI, workflow notif, Moodle G1/G5, thin GA tests H2/H3 | `[x]` |
| Sisa eksplisit | — Gelombang 5 ANALISIS selesai (2026-06-03) |

---

## 10. Dokumen Plan Lainnya

| Dokumen | Peran | Status |
|---------|-------|--------|
| [WORKFLOW.md](WORKFLOW.md) | Runbook workflow V2/V3 | `[x]` termasuk § Visual Designer & Parallel Gateway |
| [PROCUREMENT.md](PROCUREMENT.md) | Pipeline PR→bill | `[x]` selaras kode |
| [MOODLE.md](MOODLE.md) | Integrasi Moodle | `[x]`; enrollment reconcile ditambahkan |
| [FINANCE.md](FINANCE.md) | Golden path finance | `[x]` |
| [LIBRARY.md](LIBRARY.md) | SLIMS/library | `[x]` modul ada |
| [EXAM.md](EXAM.md) | Modul Exam | `[x]` modul GA |
| [EVENTS.md](EVENTS.md) | Event bus registry | `[~]` — dokumentasi; subscriber lintas modul terbatas |
| [SETUP.md](SETUP.md) / [ONBOARDING.md](ONBOARDING.md) | Dev onboarding | `[x]` |
| [FASE_1_2_FULL.md](FASE_1_2_FULL.md) | Blueprint designer | lihat §7 |

---

## 11. Prioritas Berikutnya (disarankan)

2. **`[ ]` ROADMAPv2 cleanup** — jalankan `composer run lint:translations` hingga exit 0; tambahkan ke CI.
3. **`[ ]` ROADMAPv01 monetization** — billing engine + grace lock produksi.
4. **`[ ]` v06 Parent Portal** — modul belum ada di `Modules/`.
5. **`[ ]` MOODLE_HARDENING** — jalankan checklist di staging sebelum go-live.
6. **`[ ]` Sprint v05–v09 mendalam** — dari scaffold GA ke fitur bisnis penuh per `ROADMAPv05`–`v09`.

---

## 12. Cara Memperbarui Dokumen Ini

Setelah menyelesaikan fase roadmap:

1. Update checkbox di file roadmap terkait (`ROADMAP.md`, `ROADMAPv01`, dll.).
2. Update baris yang bersangkutan di **PLAN_STATUS.md** (dokumen ini).
3. Commit dengan pesan: `docs: sync PLAN_STATUS for <fase>`.

---

*Generated by cloud agent audit — commit pada branch `main`.*
