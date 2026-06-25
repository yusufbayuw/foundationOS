# 15 — Aturan Bisnis (Business Rules)

Dokumen ini mengekstrak aturan bisnis **berbasis bukti kode** dari service, validasi, logika domain, workflow, policy, observer, dan middleware. Kebijakan organisasi manual (SOP) di luar kode tidak disertakan.

---

## 1. Metodologi Ekstraksi

| Sumber | Lokasi investigasi | Kriteria masuk dokumen |
|--------|-------------------|------------------------|
| **Service** | `Modules/*/app/Services/` | Guard clause, throw exception, perhitungan status, transaksi bisnis |
| **Validation** | Controller API (`Validator::make`), `LaravelWorkflowFormSchemaValidator`, Filament `Schemas/*Form.php` | Rule Laravel eksplisit (`required`, `in:`, `after_or_equal`, dll.) |
| **Domain Logic** | Model method (`isLockedForMutation`), listener/observer, enum | Constraint status, side-effect otomatis, idempotensi domain |
| **Workflow** | `Modules/Workflow/app/Services/*` | Resolver, engine, quorum, SLA, evidence, lifecycle definisi |
| **Policy / Security** | `app/Providers/AppServiceProvider.php`, `ModuleResource`, middleware API/tenant | Akses panel, isolasi tenant, Shield permission, rate limit |

**Penomoran:** `BR-001` … `BR-048` — nomor global lintas kategori.

**Format bukti wajib:** setiap BR memuat `Lokasi kode:` (path + class/method).

---

## 2. Validation Rules

### BR-001
**Aturan:** Pembuatan applicant via API wajib `admission_period_id`, `registration_number`, dan `full_name`; field opsional divalidasi tipe/format (email, date, gender `male|female`).  
**Deskripsi:** Request POST `/api/v1/applicants` ditolak 422 jika field wajib kosong atau format tidak valid.  
**Sumber:** Validation  
**Lokasi kode:** `app/Http/Controllers/Api/v1/ApplicantController.php` — `store()`

### BR-002
**Aturan:** `registration_number` applicant API maksimal 50 karakter; `full_name` maksimal 255 karakter.  
**Deskripsi:** Batas panjang string ditegakkan di validator sebelum persist.  
**Sumber:** Validation  
**Lokasi kode:** `app/Http/Controllers/Api/v1/ApplicantController.php` — `store()`

### BR-003
**Aturan:** Pembuatan payment via API wajib `student_invoice_id`, `chart_of_account_id`, `payment_number`, `payment_date`, `amount` (numeric ≥ 0).  
**Deskripsi:** Payment eksternal tidak boleh dibuat tanpa referensi invoice dan akun kas/bank.  
**Sumber:** Validation  
**Lokasi kode:** `app/Http/Controllers/Api/v1/PaymentController.php` — `store()`

### BR-004
**Aturan:** Status payment API (jika dikirim) hanya boleh `pending`, `verified`, atau `rejected`.  
**Deskripsi:** Enum status dibatasi; default `pending` bila tidak dikirim.  
**Sumber:** Validation  
**Lokasi kode:** `app/Http/Controllers/Api/v1/PaymentController.php` — `store()`

### BR-005
**Aturan:** Leave request API: `end_date` harus `after_or_equal:start_date`; `total_days` minimal 1; `reason` wajib.  
**Deskripsi:** Rentang cuti tidak boleh mundur; durasi dan alasan wajib diisi.  
**Sumber:** Validation  
**Lokasi kode:** `app/Http/Controllers/Api/v1/LeaveRequestController.php` — `store()`

### BR-006
**Aturan:** Setiap field pada `form_schema` workflow step wajib punya `name` non-kosong.  
**Deskripsi:** Konfigurasi step tanpa nama field memicu `WorkflowConfigurationException`.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Workflow/app/Services/LaravelWorkflowFormSchemaValidator.php` — `validate()`

### BR-007
**Aturan:** Field workflow yang `_visible=false` atau `_disabled=true` dikecualikan dari validasi dan tidak didehidrasi.  
**Deskripsi:** Visibility/disabled dievaluasi JsonLogic sebelum rule Laravel diterapkan.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Workflow/app/Services/LaravelWorkflowFormSchemaValidator.php` — `validate()`

### BR-008
**Aturan:** Tipe field workflow memetakan rule dasar: `number`→numeric, `date`/`datetime`→date, `checkbox`→boolean, `file`→file, default→string.  
**Deskripsi:** Validasi tipe otomatis berdasarkan metadata `form_schema`.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Workflow/app/Services/LaravelWorkflowFormSchemaValidator.php` — `validate()`

### BR-009
**Aturan:** Field file workflow mendukung `mimes` dari `accepted_types` dan `max` KB dari `max_size_kb`.  
**Deskripsi:** Upload evidence/form dibatasi ekstensi dan ukuran per definisi step.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Workflow/app/Services/LaravelWorkflowFormSchemaValidator.php` — `validate()`

### BR-010
**Aturan:** `options_source.kind` workflow hanya `eloquent` atau `enum`; model eloquent harus ada di whitelist `config('workflow-dynamic-sources.models')`.  
**Deskripsi:** Dynamic select tidak boleh merujuk model sembarangan.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Workflow/app/Services/LaravelWorkflowFormSchemaValidator.php` — `validateOptionsSourceShape()`

### BR-011
**Aturan:** Form Filament Applicant: `admission_period_id`, `registration_number`, `status`, `full_name` wajib diisi.  
**Deskripsi:** UI admin enrollment menegakkan field inti pendaftaran.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Enrollment/app/Filament/Resources/Applicants/Schemas/ApplicantForm.php` — `configure()`

### BR-012
**Aturan:** Three-way match procurement: GR harus milik PO yang sama; baris bill harus merujuk PO item yang valid.  
**Deskripsi:** Validator mengembalikan error jika `gr.purchase_order_id !== po.id` atau PO item tidak dikenal.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Procurement/app/Services/ThreeWayMatchValidator.php` — `validate()`

### BR-013
**Aturan:** Three-way match: `billed_qty ≤ received_qty` dan `billed_qty ≤ ordered_qty` per baris PO.  
**Deskripsi:** Tidak boleh menagih lebih dari yang diterima atau dipesan.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Procurement/app/Services/ThreeWayMatchValidator.php` — `validate()`

### BR-014
**Aturan:** Three-way match: harga unit bill harus dalam toleransi ±5% dari harga PO (default `priceTolerance=0.05`).  
**Deskripsi:** Deviasi harga di luar toleransi menghasilkan error validasi.  
**Sumber:** Validation  
**Lokasi kode:** `Modules/Procurement/app/Services/ThreeWayMatchValidator.php` — `validate()`

---

## 3. Domain Rules

### BR-015
**Aturan:** Invoice siswa berstatus `paid`, `void`, atau `cancelled` terkunci (`isLockedForMutation`).  
**Deskripsi:** Invoice final tidak dapat diedit melalui jalur mutasi normal.  
**Sumber:** Domain Logic  
**Lokasi kode:** `Modules/Finance/app/Models/StudentInvoice.php` — `isLockedForMutation()`

### BR-016
**Aturan:** Payment berstatus `verified`, `rejected`, atau `reversed` terkunci; verifikasi ulang ditolak.  
**Deskripsi:** `FinanceControlService::verifyPayment()` throw jika payment sudah final.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Finance/app/Services/FinanceControlService.php` — `verifyPayment()`; `Modules/Finance/app/Models/Payment.php` — `isLockedForMutation()`

### BR-017
**Aturan:** Jurnal hanya boleh diposting jika total debit = total credit (dibulatkan 2 desimal).  
**Deskripsi:** `postJournalEntry()` throw `Journal entry is not balanced` bila tidak seimbang.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Finance/app/Services/FinanceControlService.php` — `postJournalEntry()`

### BR-018
**Aturan:** Hanya jurnal yang sudah `is_posted` dan belum `is_reversed` yang boleh dibalik (`reverseJournalEntry`).  
**Deskripsi:** Reversal dilindungi dari jurnal draft atau yang sudah dibalik.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Finance/app/Services/FinanceControlService.php` — `reverseJournalEntry()`

### BR-019
**Aturan:** Status invoice dihitung ulang hanya dari payment berstatus `verified`; mapping: tanpa verified→`issued`/`draft`, partial jika sisa > 0, `paid` jika lunas.  
**Deskripsi:** Payment pending/rejected tidak menambah `paid_amount`. Event `StudentInvoicePaid` dipicu saat transisi ke `paid`.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Finance/app/Services/FinanceControlService.php` — `recalculateInvoice()`

### BR-020
**Aturan:** Verifikasi payment otomatis membuat jurnal `PAY-{payment_number}` (idempoten jika entry_number sudah ada) debit kas, kredit piutang.  
**Deskripsi:** Akun piutang default wajib dikonfigurasi di `tenant_settings` (`group=finance`, `key=default_receivable_account_id`).  
**Sumber:** Service  
**Lokasi kode:** `Modules/Finance/app/Services/FinanceControlService.php` — `verifyPayment()`, `createPaymentJournalEntry()`, `resolveReceivableAccount()`

### BR-021
**Aturan:** Budget terkunci setelah status `submitted`, `in_review`, `approved`, `closed`, `cancelled`, atau `rejected`.  
**Deskripsi:** Mutasi anggaran pasca-submit diblok di model layer.  
**Sumber:** Domain Logic  
**Lokasi kode:** `Modules/Finance/app/Models/Budget.php` — `isLockedForMutation()`

### BR-022
**Aturan:** Purchase requisition terkunci setelah `submitted` dan status workflow berikutnya (`in_review`, `approved`, `rejected`, `cancelled`).  
**Deskripsi:** PR yang sudah masuk alur approval tidak bisa diubah sembarangan.  
**Sumber:** Domain Logic  
**Lokasi kode:** `Modules/Procurement/app/Models/PurchaseRequisition.php` — `isLockedForMutation()`

### BR-023
**Aturan:** Vendor bill dari goods receipt wajib lulus three-way match; gagal → `ThreeWayMatchException`.  
**Deskripsi:** `VendorBillAutoCreationService` memanggil validator sebelum create bill.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Procurement/app/Services/VendorBillAutoCreationService.php` — `createFromGoodsReceipt()`

### BR-024
**Aturan:** GR tanpa parent PO tidak boleh dijadikan sumber vendor bill.  
**Deskripsi:** Exception eksplisit jika `purchaseOrder` null pada receipt.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Procurement/app/Services/VendorBillAutoCreationService.php` — `createFromGoodsReceipt()`

### BR-025
**Aturan:** Promosi applicant ke student hanya jika setting tenant `enrollment.auto_promote_accepted_applicant` aktif (default `true`).  
**Deskripsi:** `promote()` return `null` bila setting nonaktif.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Enrollment/app/Services/ApplicantPromotionService.php` — `isEnabledFor()`, `promote()`

### BR-026
**Aturan:** Promosi applicant idempoten: jika `converted_to_student_id` sudah terisi, return student existing tanpa duplikasi.  
**Deskripsi:** Mencegah double enrollment dari event yang sama.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Enrollment/app/Services/ApplicantPromotionService.php` — `promote()`

### BR-027
**Aturan:** NIS student baru digenerate `ADM-{registration_number}` dengan suffix numerik jika bentrok unik per tenant.  
**Deskripsi:** Loop uniqueness memakai `Student::withoutTenantScope()`.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Enrollment/app/Services/ApplicantPromotionService.php` — `generateNis()`

### BR-028
**Aturan:** Event `ApplicantAccepted` memicu listener queue `CreateStudentFromAcceptedApplicant` yang memanggil `ApplicantPromotionService::promote()`.  
**Deskripsi:** Konversi applicant→student otomatis setelah acceptance (bukan manual wajib).  
**Sumber:** Observer (Listener)  
**Lokasi kode:** `Modules/School/app/Listeners/CreateStudentFromAcceptedApplicant.php` — `handle()`

### BR-029
**Aturan:** Booking ruangan ditolak jika overlap waktu dengan booking lain (kecuali `rejected`/`cancelled`) pada `room_id` sama.  
**Deskripsi:** Kondisi overlap: `start_at < endAt` AND `end_at > startAt`.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Facility/app/Services/RoomBookingConflictChecker.php` — `conflictingQuery()`

### BR-030
**Aturan:** Jurnal stock move tidak diposting jika `total_cost ≤ 0`.  
**Deskripsi:** `StockJournalService::postForMove()` throw pada nilai nol.  
**Sumber:** Service  
**Lokasi kode:** `Modules/Inventory/app/Services/StockJournalService.php` — `postForMove()`

### BR-031
**Aturan:** Model `BelongsToTenant` auto-set `tenant_id` pada event `creating` jika kosong dan konteks tenant tersedia.  
**Deskripsi:** Mencegah record operasional tanpa `tenant_id` saat konteks aktif.  
**Sumber:** Domain Logic  
**Lokasi kode:** `Modules/Core/app/Models/Concerns/BelongsToTenant.php` — `bootBelongsToTenant()`

### BR-032
**Aturan:** Tenant dianggap terkunci (`isLocked`) jika `status=suspended` atau `past_due` dengan grace period habis.  
**Deskripsi:** Dasar bisnis pembatasan akses langganan.  
**Sumber:** Domain Logic  
**Lokasi kode:** `Modules/Core/app/Models/Tenant.php` — `isLocked()`

---

## 4. Security Rules

### BR-033
**Aturan:** Global scope `TenantScope` memfilter query ke `tenant_id` konteks aktif (`CurrentTenant`).  
**Deskripsi:** Isolasi data multi-tenant di layer Eloquent.  
**Sumber:** Domain Logic  
**Lokasi kode:** `app/Scopes/TenantScope.php` — `apply()`

### BR-034
**Aturan:** Jika `tenancy.scope_fail_closed=true`, HTTP request dan PHPUnit tanpa konteks tenant throw `MissingTenantContextException`.  
**Deskripsi:** Mode fail-closed mencegah query lintas-tenant tidak disengaja.  
**Sumber:** Domain Logic  
**Lokasi kode:** `app/Scopes/TenantScope.php` — `shouldFailClosed()`, `apply()`

### BR-035
**Aturan:** Akses panel `admin`: global super admin **atau** memiliki baris `user_tenant_roles`.  
**Deskripsi:** Membership tenant wajib untuk user non-super-admin.  
**Sumber:** Policy (gate panel)  
**Lokasi kode:** `Modules/Core/app/Models/User.php` — `canAccessPanel()`

### BR-036
**Aturan:** Akses panel `platform` memerlukan role Spatie `platform_owner` dengan `team tenant_id=0`.  
**Deskripsi:** Panel platform terpisah dari tenant operasional.  
**Sumber:** Policy (gate panel)  
**Lokasi kode:** `Modules/Core/app/Models/User.php` — `canAccessPanel()`

### BR-037
**Aturan:** Akses panel `parent` memerlukan relasi `parent_students.parent_user_id`.  
**Deskripsi:** Orang tua hanya masuk jika terhubung ke siswa.  
**Sumber:** Policy (gate panel)  
**Lokasi kode:** `Modules/Core/app/Models/User.php` — `canAccessPanel()`

### BR-038
**Aturan:** `Gate::before` mengembalikan `true` untuk semua ability jika `users.is_super_admin`.  
**Deskripsi:** Bypass authorization Shield/Spatie untuk super admin global.  
**Sumber:** Policy  
**Lokasi kode:** `app/Providers/AppServiceProvider.php` — `Gate::before()`

### BR-039
**Aturan:** Resource Filament global (tidak scoped tenant) membatasi create/edit/delete hanya untuk global super admin.  
**Deskripsi:** `GlobalResourceGuard` + override `canCreate`/`canEdit`/… di `ModuleResource`.  
**Sumber:** Domain Logic  
**Lokasi kode:** `Modules/Core/app/Filament/Support/ModuleResource.php` — `canCreate()`; `Modules/Core/app/Filament/Support/Guards/GlobalResourceGuard.php`

### BR-040
**Aturan:** Tenant terkunci (`isLocked`) diarahkan ke halaman billing kecuali path mengandung `billing`.  
**Deskripsi:** Middleware subscription memblokir operasi admin saat langganan habis.  
**Sumber:** Domain Logic (middleware)  
**Lokasi kode:** `app/Http/Middleware/EnsureTenantSubscriptionActive.php` — `handle()`

### BR-041
**Aturan:** API Sanctum: token tanpa `tenant_id` ditolak 403 jika `tenancy.api_require_tenant=true` (default).  
**Deskripsi:** Write/read API tenant-scoped memerlukan token terikat tenant.  
**Sumber:** Domain Logic (middleware)  
**Lokasi kode:** `app/Http/Middleware/ResolveApiTenant.php` — `handle()`

### BR-042
**Aturan:** Rate limit API: 60 request/menit per access token ID atau IP anonim.  
**Deskripsi:** Throttle `throttle:api` pada route v1/v2.  
**Sumber:** Validation (middleware)  
**Lokasi kode:** `routes/api.php` — `RateLimiter::for('api')`

### BR-043
**Aturan:** Idempotency-Key pada POST/PUT/PATCH: replay response identik 24 jam; body berbeda dengan key sama → HTTP 409.  
**Deskripsi:** Berlaku untuk applicants, payments, leave-requests (v1 & v2).  
**Sumber:** Domain Logic (middleware)  
**Lokasi kode:** `app/Http/Middleware/IdempotencyKey.php` — `handle()`

### BR-044
**Aturan:** CRUD WorkflowInstance di Filament memetakan ke permission Shield (`View:WorkflowInstance`, `Create:WorkflowInstance`, dll.).  
**Deskripsi:** Policy tidak hardcode logic bisnis; mengandalkan Spatie permission generated.  
**Sumber:** Policy  
**Lokasi kode:** `Modules/Workflow/app/Policies/WorkflowInstancePolicy.php` — `view()`, `create()`, `update()`

### BR-045
**Aturan:** Spatie Permission teams mode: `team_foreign_key = tenant_id` (konfigurasi Shield).  
**Deskripsi:** Role/permission dievaluasi per tenant, bukan global aplikasi.  
**Sumber:** Policy (konfigurasi)  
**Lokasi kode:** `docs/catalogs/authorization-matrix.json` — `shield.teams_enabled`, `team_foreign_key`

---

## 5. Workflow Rules

### BR-046
**Aturan:** Resolver workflow memilih definisi aktif paling spesifik: match `workflowCode` subject > `organization_id` > `subject_type` > `version` > `published_at`.  
**Deskripsi:** Tanpa match → `WorkflowConfigurationException`.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/DatabaseWorkflowResolver.php` — `resolveForSubject()`

### BR-047
**Aturan:** Hanya assignee dengan assignment `Pending` pada instance yang boleh advance/return/cancel/reassign; global super admin bypass.  
**Deskripsi:** `WorkflowAuthorizationException` jika actor tidak punya assignment aktif.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` — `authorizeActor()`

### BR-048
**Aturan:** Step dengan `required_evidence.file_count > 0` wajib upload evidence sebelum advance; kurang → `WorkflowEvidenceRequiredException`.  
**Deskripsi:** Evidence dihitung per `workflow_step_id` pada instance.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` — `advance()`; `Modules/Workflow/app/Models/WorkflowStep.php` — `requiresEvidence()`

### BR-049
**Aturan:** Transisi workflow: kandidat diurutkan `priority` desc lalu `is_default` desc; `condition_rules` kosong = unconditional; non-kosong dievaluasi JsonLogic.  
**Deskripsi:** Rule engine kosong (`[]`) selalu match (`JsonLogicRuleEngine::matches`).  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/JsonLogicWorkflowTransitionResolver.php` — `resolveFromLiveDefinition()`; `Modules/Workflow/app/Services/JsonLogicRuleEngine.php` — `matches()`

### BR-050
**Aturan:** Action `reject` → status instance `Rejected`; `cancel` → `Cancelled`; step terminal atau tanpa next step → `Completed`; selain itu `Running`.  
**Deskripsi:** Mapping status deterministik di engine.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` — `determineStatus()`

### BR-051
**Aturan:** Step parallel/quorum: advance menunggu `WorkflowParallelCoordinator::evaluate()`; threshold `All`/`Majority`/`Count`/`Percentage`; reject memblokir jika sisa voter tidak cukup capai threshold.  
**Deskripsi:** Setelah quorum, assignment pending lain di-cancel; transisi memakai aggregate outcome.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/WorkflowParallelCoordinator.php` — `isParallel()`, `evaluate()`; `DatabaseWorkflowEngine.php` — `advanceParallel()`

### BR-052
**Aturan:** SLA due date = `now + sla_hours` per step; breach dicatat sekali per instance (`SlaBreached` log).  
**Deskripsi:** `QueuedWorkflowSlaService` menjadwalkan `CheckWorkflowSlaJob` pada `due_at`.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/QueuedWorkflowSlaService.php` — `computeDueAt()`, `markBreached()`

### BR-053
**Aturan:** Start instance wajib punya tepat satu step `is_initial`; workflow di-snapshot ke `workflow_snapshot` agar definisi tidak berubah mid-flight.  
**Deskripsi:** Tanpa initial step → `WorkflowConfigurationException`.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/DatabaseWorkflowInstanceStarter.php` — `start()`; `snapshotWorkflow()`

### BR-054
**Aturan:** Publish workflow: versi lain dengan `code` + scope org sama di-archive; workflow baru harus punya 1 initial step, ≥1 terminal, tidak ada orphan step.  
**Deskripsi:** `assertPublishable()` validasi struktur graf sebelum aktivasi.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/WorkflowDefinitionLifecycleService.php` — `publish()`, `assertPublishable()`

### BR-055
**Aturan:** Assignee tipe `Role` hanya user dengan role tersebut **dan** membership `user_tenant_roles` pada tenant instance (scoped org bila ada).  
**Deskripsi:** Resolver role tidak mengembalikan user lintas tenant.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/DatabaseWorkflowAssigneeResolver.php` — `resolveRoleUsers()`

### BR-056
**Aturan:** `advance()` mem-lock row instance (`lockForUpdate`) untuk serialisasi concurrent approval paralel.  
**Deskripsi:** Mencegah race condition pada quorum/multi-approver.  
**Sumber:** Workflow  
**Lokasi kode:** `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` — `advance()`

---

## 6. Ringkasan Jumlah per Kategori

| Kategori | Jumlah BR | ID |
|----------|-----------|-----|
| **Validation Rules** | 14 | BR-001 – BR-014 |
| **Domain Rules** | 18 | BR-015 – BR-032 |
| **Security Rules** | 13 | BR-033 – BR-045 |
| **Workflow Rules** | 11 | BR-046 – BR-056 |
| **Total terverifikasi** | **56** | BR-001 – BR-056 |

---

## 7. TIDAK TERDETEKSI / TERBATAS

| Topik | Status | Catatan |
|-------|--------|---------|
| Formula bobot penilaian K-12 global | **TIDAK TERDETEKSI** | Per model assessment; tidak ada service terpusat |
| Perhitungan gaji bulanan terpusat | **TIDAK TERDETEKSI DI KODE** | Payroll ada sebagai model/resource; engine kalkulasi tidak ditemukan di service utama |
| Validasi per-field 257+ Filament resource | **TERINDIKASI, tidak diaudit per file** | Pola umum: `->required()` di `Schemas/*Form.php`; audit penuh di luar scope dokumen ini |
| `Modules/*/app/Http/Requests/` FormRequest | **TIDAK TERDETEKSI** | Tidak ada FormRequest di modul; validasi API inline di controller |
| Business rule unik per 3100+ permission Shield | **TERINDIKASI** | Permission di-generate; policy umumnya delegasi ke `user->can('Action:Resource')` |
| Aturan Moodle sync (idnumber mapping) | **TERINDIKASI** | Ada di `app/Integrations/Moodle/` — tidak diekstrak penuh di dokumen ini |

---

## 8. Referensi Silang

| Dokumen | Relevansi |
|---------|-----------|
| `WORKFLOW.md` | Konteks operasional workflow V2 |
| `docs/catalogs/authorization-matrix.json` | Matriks permission Shield |
| `FINANCE.md` / `PROCUREMENT.md` | Golden path domain |
| `14_api_dan_integrasi.md` | Endpoint API & middleware |
