# FoundationOS Development Roadmap

Dokumen ini merangkum fase pengembangan FoundationOS yang **belum dijalankan** setelah MVP saat ini. Roadmap dibagi per inisiatif besar (epic), bukan per kalender, karena prioritas bergantung pada kebutuhan tenant.

Setiap epic punya:
- **Tujuan** — apa yang ingin dicapai
- **Fase** — pemecahan kerja menjadi langkah inkremental yang bisa di-release
- **Deliverable** — artefak konkret (file, command, migration, dst.)
- **Acceptance Criteria** — bagaimana cara tahu fase selesai
- **Dependensi** — pekerjaan lain yang harus selesai dulu
- **Risiko** — hal yang bisa membuat fase tergelincir

Status legend: `[ ]` belum mulai · `[~]` sedang berjalan · `[x]` selesai

---

## Epic 1 — Workflow V3

**Tujuan.** Mengangkat Workflow Engine dari approval linear sederhana menjadi engine BPMN-lite yang mendukung percabangan paralel, perancangan visual, dan sumber data dinamis untuk form runtime.

### Fase 1.1 — Parallel Gateway & Quorum Approval `[x]`

**Konteks.** V2 saat ini hanya mendukung transisi linear (`current_step -> next_step`) dengan satu assignee aktif per langkah. Untuk approval di mana, misalnya, "minimal 2 dari 3 manajer harus approve" atau "Finance dan Legal harus paralel sebelum CEO" — engine belum bisa.

**Deliverable.**
- Migration: tambah kolom `gateway_type` (`none`, `parallel_split`, `parallel_join`, `inclusive`, `exclusive`) ke `workflow_steps`.
- Migration: tambah kolom `quorum_strategy` (`all`, `majority`, `count`, `percentage`) dan `quorum_value` ke `workflow_steps`.
- Tabel baru: `workflow_step_branches` untuk menyimpan cabang aktif per instance.
- Enum baru: `Modules\Workflow\Enums\WorkflowGatewayType`, `WorkflowQuorumStrategy`.
- Service baru: `Modules\Workflow\Services\WorkflowParallelCoordinator` — bertanggung jawab atas fork/join.
- Refaktor `DatabaseWorkflowEngine::advance()` untuk konsultasi ke coordinator saat step berupa gateway.
- Update `WorkflowAssigneeResolver` agar bisa mengembalikan multi-assignee paralel.
- Update relasi `AssignmentsRelationManager` di Filament agar menampilkan multiple cabang aktif.

**Acceptance Criteria.**
- Test: `WorkflowParallelGatewayTest` mensimulasikan 3 approver paralel, 2 approve, 1 reject → outcome ditentukan strategi quorum.
- Test: `WorkflowParallelJoinTest` memastikan join hanya advance setelah semua/kuorum cabang selesai.
- Dokumentasi di `WORKFLOW.md` section baru "Parallel Gateway".
- Workflow lama (linear) tetap jalan tanpa perubahan kode di consumer.

**Dependensi.** Tidak ada (bisa langsung mulai).

**Risiko.** Race condition saat dua approver paralel call `advance()` bersamaan — wajib pakai DB transaction + row lock pada `workflow_instances`.

---

### Fase 1.2 — Visual Workflow Designer

**Konteks.** Saat ini definisi workflow dibuat manual via seeder/command (`SetupBudgetWorkflowCommand`, `SetupProcurementWorkflowPilotCommand`). Tenant non-teknis tidak bisa mendesain workflow sendiri.

**Deliverable.**
- Halaman Filament baru: `Modules\Workflow\Filament\Pages\WorkflowDesignerPage`.
- Livewire component: `WorkflowCanvas` — drag-drop step + transition pakai library (kandidat: `dagre` + `cytoscape.js` atau `react-flow` via Inertia island).
- Endpoint Livewire untuk: tambah step, hapus step, tambah transition, set rule, set gateway type, set form schema.
- Service baru: `WorkflowVersionSnapshotService` — auto-create draft version saat designer disimpan, tanpa overwrite versi aktif.
- Integrasi `WorkflowDefinitionLifecycleService::publish()` dari tombol "Publish" di designer.
- Import/export JSON: definisi workflow exportable agar tenant bisa share template.

**Acceptance Criteria.**
- Tenant admin bisa membuat workflow 5 step + 2 paralel gateway tanpa menyentuh kode.
- Versi draft tidak mempengaruhi instance running.
- Test: `WorkflowDesignerSavesValidDefinitionTest`.
- Dokumentasi visual designer di `WORKFLOW.md`.

**Dependensi.** Fase 1.1 (designer perlu UI untuk gateway type).

**Risiko.** Library JS canvas berat → split bundle, lazy-load hanya di halaman designer. Pastikan `npm run build` tidak meledak.

---

### Fase 1.3 — Dynamic Form Data Source

**Konteks.** `form_schema` V2 hanya mendukung static options (`["draft", "approved"]`). Untuk dropdown approver, vendor, atau cost center, options harus dihardcode atau di-seed.

**Deliverable.**
- Spec baru di `form_schema`: `{ "type": "select", "options_source": { "kind": "eloquent", "model": "Modules\\Procurement\\Models\\Vendor", "scope": "active", "tenant_aware": true, "label": "name", "value": "id" } }`.
- Service baru: `Modules\Workflow\Services\DynamicOptionsResolver` — resolve options saat form di-render.
- Whitelist model boleh diquery via `config/workflow-dynamic-sources.php` (security: jangan biarkan tenant query model arbitrary).
- Cache per tenant + per query signature, TTL 5 menit, invalidasi via observer.
- Support source non-eloquent: `{"kind": "endpoint", "url": "..."}` (internal API) dan `{"kind": "enum", "class": "..."}`.
- Update `LaravelWorkflowFormSchemaValidator` untuk validate `options_source` shape.

**Acceptance Criteria.**
- Tenant bisa bikin form workflow dengan dropdown "Pilih Vendor" yang menampilkan vendor aktif tenant tersebut.
- Test: `DynamicOptionsResolverFiltersByTenantTest` memastikan tenant A tidak melihat opsi tenant B.
- Test security: model di luar whitelist menolak query.

**Dependensi.** Tenancy hardening (Epic 4) — agar tenant scoping otomatis bisa dipakai resolver tanpa kebocoran data.

**Risiko.** Performance — dropdown dengan 10k+ options. Tambah opsi `searchable` + lazy pagination via Filament `Select::getSearchResultsUsing()`.

---

## Epic 2 — Procurement Automation

**Tujuan.** Setelah workflow approval PR selesai, sistem secara otomatis (dengan persetujuan eksplisit di tiap gate) menggerakkan PR → RFQ → PO → Goods Receipt → Vendor Bill, sehingga tenant tidak perlu input ulang data.

### Fase 2.1 — Auto-create RFQ dari PR Approved `[x]`

**Konteks.** `PROCUREMENT.md:47` menyebut PR approved **belum** otomatis menghasilkan RFQ. Saat ini procurement officer harus input ulang nominal, item, dan vendor candidate.

**Deliverable.**
- Event baru: `Modules\Procurement\Events\PurchaseRequisitionApproved` — dispatched saat workflow instance PR finish dengan outcome `approved`.
- Listener: `Modules\Procurement\Listeners\CreateRfqFromApprovedPurchaseRequisition`.
- Service: `Modules\Procurement\Services\RfqAutoCreationService` — copy line items PR → RFQ Items, set status `draft`.
- Setting tenant: `procurement.auto_create_rfq_from_pr` (boolean) via `TenantSetting`.
- Filament notification: "RFQ #ABC123 otomatis dibuat dari PR #XYZ" ke procurement officer.
- Tombol "Skip RFQ" pada PR detail untuk kasus single-source procurement → langsung buat PO.

**Acceptance Criteria.**
- PR approved → RFQ draft tersedia dalam <2 detik via queue.
- Test: `RfqAutoCreatedAfterPrApprovedTest`.
- Test: setting off → tidak ada RFQ auto-created.
- Audit log mencatat siapa & kapan PR memicu RFQ.

**Dependensi.** Workflow V2 trigger event saat instance selesai (mungkin perlu small refactor di `DatabaseWorkflowEngine` untuk emit event per outcome).

**Risiko.** Idempotency — jika listener gagal dan retry, jangan buat RFQ duplikat. Pakai unique constraint `(pr_id, status='draft')`.

---

### Fase 2.2 — RFQ → PO Pipeline `[x]`

**Konteks.** Setelah vendor terpilih di RFQ, PO masih dibuat manual.

**Deliverable.**
- Action di RFQ detail: `Award to Vendor` → membuat PO draft dengan vendor terpilih, item, harga RFQ-winner.
- Service: `PurchaseOrderAutoCreationService`.
- Workflow PO Approval (template baru) — opsional, bisa pakai threshold seperti PR.
- Command setup: `php artisan fos:procurement:setup-po-workflow <tenant>`.
- Locking: setelah PO dibuat, RFQ status → `awarded`, item RFQ tidak bisa diedit.

**Acceptance Criteria.**
- Test: `PoCreatedFromAwardedRfqTest`.
- PO mewarisi `tenant_id`, `organization_id`, dan `currency` dari RFQ.
- UI menampilkan trail PR → RFQ → PO di breadcrumb / related records.

**Dependensi.** Fase 2.1.

**Risiko.** Multi-currency RFQ — pastikan exchange rate di-snapshot saat award.

---

### Fase 2.3 — PO → Goods Receipt → Vendor Bill `[x]`

**Deliverable.**
- Action di PO: `Receive Goods` → membuat Goods Receipt draft.
- Service: `GoodsReceiptAutoCreationService` dengan partial receipt support (`received_qty <= ordered_qty`).
- Auto-update PO status: `partial`, `received`, `closed`.
- Action di GR confirmed: `Create Vendor Bill` → bikin VendorBill matching 3-way (PO + GR + Invoice qty).
- Three-way matching validator: `Modules\Procurement\Services\ThreeWayMatchValidator`.
- Auto-journal: VendorBill confirmed → JournalEntry draft via Finance (cross-module integration).

**Acceptance Criteria.**
- Test full pipeline: `ProcurementEndToEndPipelineTest` — PR draft → ... → Journal posted.
- Three-way mismatch ditolak dengan pesan jelas.
- `PROCUREMENT.md` updated dengan diagram pipeline.

**Dependensi.** Fase 2.2, koordinasi dengan Finance module untuk auto-journal contract.

**Risiko.** Journal posting harus reversible — pastikan VendorBill voided menghasilkan reverse journal.

---

## Epic 3 — Moodle Enrollment Reconciliation Dua-Arah

**Tujuan.** Saat ini reconcile hanya level user/course (`MOODLE.md:172`). Enrollment drift (mahasiswa di Moodle tapi tidak di FOS, atau sebaliknya) belum terdeteksi otomatis.

### Fase 3.1 — Enrollment Drift Detection `[x]`

**Deliverable.**
- Tabel baru: `moodle_enrollment_drifts` — `tenant_id`, `class_id`, `course_moodle_id`, `user_moodle_id`, `drift_type` (`missing_in_fos`, `missing_in_moodle`, `mismatched_role`), `detected_at`, `resolved_at`.
- Command: `php artisan fos:moodle:reconcile-enrollment --tenant=X [--dry-run] [--fix]`.
- Service: `Modules\Integrations\Moodle\MoodleEnrollmentReconciler` (perpanjang `MoodleSyncService` atau pisah).
- Strategi:
  1. Pull enrollment per course dari Moodle (`core_enrol_get_enrolled_users`).
  2. Bandingkan dengan `class_students` × `moodle_class_course_mappings`.
  3. Insert drift records.

**Acceptance Criteria.**
- Test: `EnrollmentDriftDetectorTest` — seed 3 mismatch → 3 drift records.
- Filament resource: `MoodleEnrollmentDriftResource` (read-only di Monitoring) dengan filter per drift_type.
- Dry-run mode tidak menyentuh data.

**Dependensi.** Web service Moodle perlu izin `core_enrol_get_enrolled_users` — update di `MOODLE.md` section "Fungsi Web Service yang Wajib".

**Risiko.** Performance untuk course besar (1000+ enrolled). Pakai chunking + queue per course.

---

### Fase 3.2 — Auto-fix dengan Persetujuan

**Deliverable.**
- Action `--fix` pada command reconcile-enrollment:
  - `missing_in_moodle` → enqueue `enrol_manual_enrol_users`.
  - `missing_in_fos` → buat `ClassStudent` record OR mark for review (configurable per tenant).
  - `mismatched_role` → re-enroll dengan role benar.
- Approval flow opsional via Workflow module untuk fix yang men-create FOS-side record.
- Setting tenant: `moodle.enrollment_drift_auto_fix_mode` (`disabled`, `outbound_only`, `bidirectional`).

**Acceptance Criteria.**
- Drift `missing_in_moodle` ter-resolve via outbox + audit log.
- Drift `missing_in_fos` (default) hanya logged, tidak auto-create — kecuali tenant explicitly opt-in.
- Cron entry tersedia: `php artisan fos:moodle:reconcile-enrollment --all-tenants --dry-run` per hari.

**Dependensi.** Fase 3.1.

**Risiko.** Auto-create FOS user dari Moodle bisa bocorkan boundary tenancy — wajib `outbound_only` default.

---

## Epic 4 — Tenancy Hardening

**Tujuan.** Mengurangi ketergantungan pada disiplin developer (manual `tenant_id` di setiap query) menjadi enforcement otomatis di level framework.

### Fase 4.1 — Global Scope Otomatis (Pendekatan In-House) `[x]`

**Konteks.** Bukan adopsi package eksternal dulu — minimize blast radius. Pakai trait sendiri.

**Deliverable.**
- Trait: `App\Concerns\BelongsToTenant` — auto-apply global scope `TenantScope` dan auto-set `tenant_id` saat create.
- Class: `App\Scopes\TenantScope` — resolve `tenant_id` dari `Filament::getTenant()` atau `app('current_tenant')`.
- Singleton resolver: `App\Support\CurrentTenant` — diisi oleh Filament middleware + manual setter untuk queue/console.
- Middleware: `App\Http\Middleware\BindTenantToContainer`.
- Migrasi bertahap: tambah trait pada model satu modul sekaligus, mulai dari `Modules\Procurement\Models\*` (paling rapat dengan workflow & finance).
- Override `withoutTenantScope()` untuk command/seeder cross-tenant.

**Acceptance Criteria.**
- Query `Vendor::all()` di context tenant=5 hanya return vendor tenant 5 — tanpa `where()` manual.
- Test: `TenantScopeIsolationTest` — seed 2 tenant × 5 vendor, verify isolation.
- Tidak break test existing (semua test feature lulus).
- Audit semua model dengan `tenant_id`: list di `ROADMAP.md` (atau dokumen migrasi terpisah) dengan status migration trait per model.

**Dependensi.** Tidak ada.

**Risiko.** Queue job: `Filament::getTenant()` tidak available. Setiap dispatch wajib `->onTenant($id)` pattern — sediakan custom dispatcher helper.

---

### Fase 4.2 — Queue & Console Tenant Context `[x]`

**Deliverable.**
- Trait: `App\Concerns\InteractsWithTenant` untuk Job classes — serialize `tenant_id`, restore ke `CurrentTenant` saat `handle()` jalan.
- Refaktor `ProcessMoodleSyncOutboxJob` + semua workflow automated action job untuk pakai trait.
- Artisan macro: `php artisan tenant:run <tenant_id> "<command>"` — wrapper yang set tenant context lalu jalankan command.
- Console kit untuk seeder cross-tenant.

**Acceptance Criteria.**
- Test: `JobRestoresTenantContextTest`.
- Tidak ada lagi query manual `->where('tenant_id', ...)` di jobs.

**Dependensi.** Fase 4.1.

**Risiko.** Job serialization size bertambah — minor.

---

### Fase 4.3 — Tenant Switching UX & Audit

**Deliverable.**
- Audit log entry: setiap tenant switch dicatat di `audit_logs`.
- Filament middleware sudah ada (`SyncShieldTenant`), pastikan trigger event `TenantSwitched` yang kita listen.
- Indikator UI: badge tenant aktif di topbar (warna berbeda untuk super-admin yang lihat tenant lain).
- Command: `php artisan fos:tenancy:audit-leaks` — scan semua tabel dengan `tenant_id`, cari record dengan FK ke tabel beda `tenant_id` (data leak).

**Acceptance Criteria.**
- Command audit-leaks return zero rows pada database test.
- Setiap tenant switch traceable di audit log.

**Dependensi.** Fase 4.1.

**Risiko.** False positive di tabel cross-tenant by design (Country, Province, dst.). Whitelist global tables.

---

### Fase 4.4 — (Opsional) Adopsi Package Tenancy

**Konteks.** Kalau Fase 4.1–4.3 sudah stabil dan tetap dirasa kurang, evaluasi `stancl/tenancy` (single-database multi-tenant mode) atau `spatie/laravel-multitenancy`. Decision deferred — keputusan pakai package hanya jika ada use case nyata yang tidak bisa kita penuhi (mis. tenant-specific database connection).

**Deliverable saat keputusan diambil.**
- ADR (Architecture Decision Record): `docs/adr/0001-tenancy-package-evaluation.md`.
- POC branch dengan 1 modul migrated.

---

## Epic 5 — API Layer Publik Berversi

**Tujuan.** `routes/api.php` setiap modul saat ini hampir kosong (hanya Campus punya 1 resource). Bangun API publik berversi, ter-otentikasi, dengan rate limiting, untuk integrasi pihak ketiga (mobile app, parent portal, BI tools).

### Fase 5.1 — Foundation API (v1)

**Deliverable.**
- Standar API: REST, JSON:API-ish, versioned via path (`/api/v1/...`).
- Auth: Laravel Sanctum (token-based) + tenant-scoped tokens (`token->tenant_id`).
- Migration: tambah `tenant_id` dan `scopes` ke `personal_access_tokens`.
- Middleware: `App\Http\Middleware\ResolveApiTenant` — set `CurrentTenant` dari token.
- Rate limiting per token via `RateLimiter::for('api', ...)` dengan tier configurable.
- Base controller: `App\Http\Controllers\Api\v1\ApiController` — pakai Eloquent API Resources.
- Standard error response: `{ "error": { "code": "...", "message": "...", "details": [...] } }`.
- OpenAPI spec auto-generation via `darkaonline/l5-swagger` atau handwritten YAML di `docs/api/v1/openapi.yaml`.
- Endpoint pertama: `GET /api/v1/me`, `GET /api/v1/tenants/current`.

**Acceptance Criteria.**
- Test: `ApiTokenScopedToTenantTest`.
- Rate limit kena saat exceed.
- OpenAPI spec validasi via `spectral lint`.

**Dependensi.** Fase 4.1 (tenant scope otomatis).

**Risiko.** Sanctum default tidak tenant-aware — perlu custom guard atau middleware.

---

### Fase 5.2 — Core Resources Read API

**Deliverable.**
- Endpoint readonly: students, classes, courses, employees, tenants, organizations.
- Pagination cursor-based untuk list.
- Filtering: `?filter[name]=...&filter[is_active]=true`.
- Sparse fieldsets: `?fields[student]=name,email`.
- Includes: `?include=class,teacher`.
- Caching per request signature dengan invalidasi via observer.

**Acceptance Criteria.**
- Test setiap resource: list, show, filter, paginate, include.
- Postman collection di `docs/api/v1/postman.json`.

**Dependensi.** Fase 5.1.

**Risiko.** N+1 queries pada includes — wajib pakai eager loading dinamis.

---

### Fase 5.3 — Write API & Webhooks

**Deliverable.**
- Endpoint write untuk: applicant registration, payment recording, leave request submission.
- Idempotency-key header untuk POST.
- Webhook outbound: tenant bisa subscribe ke event (`payment.verified`, `enrollment.created`, `workflow.completed`).
- Tabel: `webhook_subscriptions`, `webhook_deliveries`.
- HMAC signing untuk security.
- Filament resource: `WebhookSubscriptionResource` di Monitoring module.

**Acceptance Criteria.**
- Test: `WebhookDeliveryRetriesOnFailureTest` dengan exponential backoff.
- Idempotency-key dengan body sama return original response, beda body return 409.

**Dependensi.** Fase 5.2.

**Risiko.** Webhook delivery loop / amplification — rate-limit per subscription.

---

### Fase 5.4 — Mobile-First Endpoints

**Deliverable.**
- Endpoint khusus mobile: bundle data untuk dashboard parent/student (1 call, multiple resources).
- Push notification token registration: `POST /api/v1/devices`.
- Endpoint: `/api/v1/student/{id}/dashboard` returns attendance, grades, fees outstanding, announcements.
- Versioning policy: minor changes additive, breaking changes bump ke `/api/v2/`.

**Acceptance Criteria.**
- Single dashboard call < 200ms p95 dengan cache.
- Backward compatibility test antara v1 minor versions.

**Dependensi.** Fase 5.3.

---

## Epic 6 — Moodle Complete untuk Campus/Universitas

**Tujuan.** Integrasi Moodle saat ini lebih cocok untuk K-12. Untuk universitas (modul Campus), banyak konsep yang belum ditangani: course catalog dengan prerequisite, study plan (KRS) sync, lecturer assignment, gradebook level-mata-kuliah dengan komponen, thesis tracking, semester academic calendar.

### Fase 6.1 — Course Catalog & Prerequisite Sync

**Deliverable.**
- Mapping `Modules\Campus\Models\Course` → Moodle Course **Template** (category `fos_template_{tenant}`).
- Mapping `Modules\Campus\Models\CourseOffering` (= mata kuliah ditawarkan semester X) → Moodle Course instance per semester.
- Idnumber convention baru: `fos_offering_{offering_id}`.
- Sync prerequisite via Moodle "Restrict access" — POC dulu karena Moodle restriction API limited; mungkin perlu plugin atau manual hint via course description.
- Migration: tambah `moodle_template_id`, `moodle_offering_id` mappings.

**Acceptance Criteria.**
- Test: `CourseOfferingSyncsToMoodleTest`.
- Course offering baru di FOS → Moodle course muncul di category semester yang benar.

**Dependensi.** Tidak ada.

**Risiko.** Moodle restriction API tidak lengkap via web service — mungkin perlu kompromi (manual setup atau plugin).

---

### Fase 6.2 — Study Plan (KRS) Sync ke Moodle

**Konteks.** Mahasiswa di Indonesia memilih mata kuliah per semester (KRS). Setelah KRS approved, mahasiswa harus terenroll otomatis di Moodle course offering yang dipilih.

**Deliverable.**
- Observer: `StudyPlanItemObserver` — saat status `approved`, enqueue enrollment ke Moodle course offering.
- Service: `MoodleStudyPlanSyncService`.
- Workflow integration: approval KRS bisa pakai modul Workflow (template per tenant).
- Bulk enrollment: semester rollover command `php artisan fos:moodle:bulk-enroll-semester <semester_id>`.

**Acceptance Criteria.**
- Test: `ApprovedStudyPlanEnrollsStudentInMoodleTest`.
- KRS dibatalkan → un-enroll otomatis (dengan grace period configurable).

**Dependensi.** Fase 6.1, Fase 3 (enrollment reconcile) untuk safety net.

**Risiko.** Mahasiswa pindah KRS tengah semester — perlu audit trail.

---

### Fase 6.3 — Lecturer Assignment & Multi-Teacher Courses

**Deliverable.**
- Sync `Lecturer` → Moodle teacher role enrollment di course offering yang ditugaskan.
- Tabel: `course_offering_lecturers` (mungkin sudah ada — verifikasi schema).
- Support multi-teacher (utama + asisten) dengan role berbeda di Moodle (`editingteacher`, `teacher`).
- Filament action di CourseOffering: "Assign Lecturer" → memicu sync.

**Acceptance Criteria.**
- Lecturer assigned di FOS muncul sebagai teacher di Moodle course dalam < 1 menit (queue).
- Test: `LecturerAssignmentSyncsAsMoodleTeacherTest`.

**Dependensi.** Fase 6.1.

**Risiko.** Lecturer global vs per-tenant — beberapa universitas pakai dosen tamu lintas fakultas; pastikan mapping benar.

---

### Fase 6.4 — Gradebook Granular Pull

**Konteks.** Saat ini pull grade pakai `gradereport_overview_get_course_grades` (overview saja). Untuk universitas perlu pull per komponen (UTS, UAS, tugas, kuis) untuk masuk ke `StudyResult`.

**Deliverable.**
- Pakai `core_grades_get_grades` + `gradereport_user_get_grade_items` untuk per-item detail.
- Mapping komponen Moodle ↔ `StudyResult` schema (bobot UTS/UAS/tugas).
- Setting tenant: `campus.gradebook_components` — define komponen + bobot default.
- Command: `php artisan fos:moodle:pull-detailed-grades --tenant=X --semester=Y`.
- Conversion grade Moodle (0-100) → nilai huruf (A, B+, C, dst.) sesuai skala universitas.

**Acceptance Criteria.**
- Pull grade Moodle → `StudyResult` dengan breakdown komponen tersedia.
- Test: `DetailedGradePullPopulatesStudyResultTest`.
- Recalculation IPK setelah pull.

**Dependensi.** Fase 6.1.

**Risiko.** Skala nilai tiap universitas berbeda — pastikan konfigurable per tenant.

---

### Fase 6.5 — Thesis / Tugas Akhir Workflow

**Konteks.** Modul `Thesis` di Campus belum punya alur lengkap. Universitas butuh: proposal → seminar → bimbingan → sidang → revisi → final.

**Deliverable.**
- Workflow template thesis lifecycle (pakai Workflow V3 dengan parallel gateway untuk multi-supervisor).
- Document upload milestone per fase (proposal PDF, draft, final).
- Optional Moodle assignment integration untuk submit-review cycle.
- Filament page: `ThesisProgressTrackerPage` per mahasiswa.

**Acceptance Criteria.**
- End-to-end test: mahasiswa submit proposal → supervisor approve → mahasiswa upload draft → 2 supervisor review paralel → final approval.
- Linked dengan `StudyResult` setelah final.

**Dependensi.** Epic 1 Fase 1.1 (parallel gateway), Fase 6.1.

**Risiko.** Banyak edge case (ganti supervisor, perpanjangan, gagal sidang) — perlu workflow yang flexible.

---

### Fase 6.6 — Academic Calendar Sync

**Deliverable.**
- Sync `AcademicPeriod` → Moodle course start/end dates per offering.
- Calendar event di Moodle: UTS, UAS, libur akademik (via `core_calendar_create_calendar_events`).
- Cohort semester auto-archive setelah semester ended.

**Acceptance Criteria.**
- Semester baru di FOS → Moodle courses untuk offering baru ter-set tanggal benar.
- Calendar event muncul di Moodle calendar mahasiswa.

**Dependensi.** Fase 6.1.

**Risiko.** Timezone — semua tenant Indonesia (WIB/WITA/WIT). Pastikan konsisten.

---

## Prioritisasi & Saran Urutan Eksekusi

Urutan yang direkomendasikan (bukan kalender, tapi dependensi & nilai):

1. **Epic 4 Fase 4.1–4.2** (Tenancy hardening) — fondasi semua epic lain.
2. **Epic 1 Fase 1.1** (Parallel gateway) — bebas dependensi, dipakai Epic 2 & 6.5.
3. **Epic 2 Fase 2.1–2.3** (Procurement automation) — high business value, dependensi minimal.
4. **Epic 3** (Moodle enrollment reconcile) — defensive, mengurangi support burden.
5. **Epic 6 Fase 6.1–6.4** (Moodle campus complete) — unlock segmen pasar universitas.
6. **Epic 1 Fase 1.2–1.3** (Designer + dynamic source) — UX polish, bisa paralel.
7. **Epic 5** (API publik) — setelah core stabil, ini multiplier integrasi.
8. **Epic 4 Fase 4.4** (Package adopsi) — kalau memang dibutuhkan.

---

## Definition of Done (Per Fase, Universal)

Setiap fase dianggap selesai jika:

- [ ] Semua acceptance criteria check.
- [ ] Feature test PHPUnit hijau (`php artisan test --compact --filter=<EpicName>`).
- [ ] `vendor/bin/pint --dirty --format agent` clean.
- [ ] Migration reversible (`down()` works) — kecuali destructive change yang didokumentasikan.
- [ ] Dokumentasi terkait diupdate (`WORKFLOW.md`, `PROCUREMENT.md`, `MOODLE.md`, atau bikin `<DOMAIN>.md` baru).
- [ ] `php artisan shield:generate --all --panel=admin --option=permissions --no-interaction` dijalankan kalau ada resource/page baru.
- [ ] Setting/feature toggle didokumentasikan di `README.md` section konfigurasi.
- [ ] Tidak ada permintaan ke production data dari test (RefreshDatabase + factories).

---

## Catatan Operasional

- Setiap epic boleh dipecah menjadi beberapa PR; fase adalah unit deploy minimum.
- Breaking change ke API atau workflow definition wajib bump version + migration strategy + deprecation period.
- Sebelum memulai fase baru, update bagian "Status" di awal fase (`[ ]` → `[~]` → `[x]`).
- Setelah selesai, tambahkan tanggal selesai + commit SHA terakhir di akhir bagian fase tersebut.
