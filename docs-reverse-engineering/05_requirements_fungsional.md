# 05 — Requirements Fungsional (Diturunkan dari Source Code)

**Commit analisis:** `d3be06aa` · **Metode:** reverse engineering route → controller → service → UI Filament → policy/validasi

**Legenda bukti:** setiap FR wajib punya minimal satu bukti route/controller/service. Jika layer tidak ada: **TIDAK TERDETEKSI DI KODE**.

---

## Dimensi Analisis

### Route

| Sumber | Jumlah / pola | Bukti |
|--------|---------------|-------|
| API global | 101 route terkatalog | `docs/catalogs/api-routes-catalog.json` (`route_count`: 101) |
| API v1/v2 app | Read + write + mobile | `routes/api.php` |
| API modul | Exam runtime, inquiry, module scaffolds | `Modules/Exam/routes/api.php`, `Modules/Enrollment/routes/api.php` |
| Web modul | PDF, OPAC, CMS, webhook | `Modules/Library/routes/web.php`, `Modules/Cms/routes/web.php` |
| Filament panel | `/admin`, `/platform`, `/parent` | `AdminPanelProvider.php`, `PlatformPanelProvider.php`, `ParentPanelProvider.php` |
| Scheduler | 28+ `Schedule::command` | `routes/console.php` |

### Controller

| Pola | Lokasi | Bukti |
|------|--------|-------|
| API v1/v2 terpusat | `app/Http/Controllers/Api/v1/*`, `v2/*` | `routes/api.php` |
| API modul | `Modules/*/Http/Controllers/Api/*` | `ExamRuntimeWebhookController.php` |
| Web / PDF | `Modules/*/Http/Controllers/*PdfController.php` | `Modules/Procurement/routes/web.php` |
| Filament pages | `Modules/*/Filament/Pages/*` | Auto-discover via `ModulesPlugin` |

### Service

| Pola | Lokasi | Bukti |
|------|--------|-------|
| Domain service | `Modules/*/app/Services/*.php` | ~130+ file (scan `Modules/*/app/Services/`) |
| Integrasi | `app/Integrations/Moodle/*` | `MoodleSyncService.php` |
| App service | `app/Services/WebhookDispatcher.php` | Dipanggil `PaymentController` |

### UI (Filament)

| Pola | Bukti |
|------|-------|
| Resource CRUD per entitas | 375 resource (257 `ModuleResource` + 118 alias) — `03-module-dependency-map.md` |
| Form/Table/Infolist terpisah | `Modules/School/app/Filament/Resources/Students/Schemas/StudentForm.php` |
| Relation managers | `StudentResource::getRelations()` — absensi, nilai, dll. |
| Import CSV | `Modules/Core/app/Filament/Support/ImportTableActions.php` |

### API

| Karakteristik | Bukti |
|---------------|-------|
| Sanctum Bearer | `auth:sanctum` di `routes/api.php` |
| Tenant dari token | `ResolveApiTenant` middleware |
| Idempotency write | `IdempotencyKey` middleware |
| Throttle 60/menit | `RateLimiter::for('api')` di `routes/api.php` |
| OpenAPI | `GET api/openapi.json` → `OpenApiController@v1` |

### Validasi

| Layer | Pola | Bukti |
|-------|------|-------|
| API inline | `$request->validate()` / `Validator::make()` | `InquiryController::store()`, `PaymentController::store()` |
| Filament form | `->required()`, rules field | `StudentForm.php` baris `->required()` |
| Service guard | `RuntimeException` / custom exception | `FinanceControlService::verifyPayment()`, `ThreeWayMatchValidator::validate()` |
| Workflow form | `WorkflowFormSchemaValidator` | `DatabaseWorkflowEngine::advance()` |
| FormRequest dedicated | **TIDAK TERDETEKSI DI KODE** sebagai pola dominan | Tidak ada namespace `Http/Requests` luas di modul |

### Permission

| Layer | Bukti |
|-------|-------|
| Filament Shield | `FilamentShieldPlugin` di `AdminPanelProvider.php` |
| Policy per model | `StudentPolicy::viewAny()` → `can('ViewAny:Student')` |
| Spatie teams | `config/permission.php` `teams` => true |
| Super admin bypass | `AppServiceProvider.php` `Gate::before` |
| Panel access | `User::canAccessPanel()` |

---

## Daftar Functional Requirement

---

### FR-001
**Autentikasi Admin Panel**

Pengguna dapat login, registrasi, verifikasi email, reset password, dan MFA (app/email) pada panel admin tenant.

**Deskripsi:** Panel `/admin` menyediakan alur auth lengkap Filament plus MFA recoverable.

**Bukti:**
- **Route:** Filament route auto (`/admin/login`, `/admin/register`) — `AdminPanelProvider.php` `->login()`, `->registration()`
- **Controller:** **TIDAK TERDETEKSI DI KODE** (handled oleh Filament auth stack)
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** `app/Providers/Filament/AdminPanelProvider.php` — `multiFactorAuthentication([AppAuthentication, EmailAuthentication])`
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** Filament built-in auth validation
- **Permission:** `Authenticate::class` di `authMiddleware`

---

### FR-002
**Registrasi Tenant Baru**

Tenant SaaS baru dapat didaftarkan melalui wizard registrasi tenant di panel admin.

**Deskripsi:** User membuat tenant dan menjadi anggota tenant tersebut.

**Bukti:**
- **Route:** Filament tenant registration route — `->tenantRegistration(RegisterTenant::class)`
- **Controller:** **TIDAK TERDETEKSI DI KODE** (Filament page)
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** `app/Filament/Pages/Tenancy/RegisterTenant.php`
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** Form Filament `RegisterTenant`
- **Permission:** Guest/unauthenticated pada flow registrasi

---

### FR-003
**Isolasi Data Multi-Tenant**

Semua operasi data operasional di-scope `tenant_id`; query tanpa konteks tenant dapat fail-closed di produksi.

**Deskripsi:** Trait `BelongsToTenant` + global scope + `CurrentTenant` container.

**Bukti:**
- **Route:** Middleware `BindTenantToContainer` pada panel Filament
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** `ModuleResource` tenant safety checks
- **API:** `ResolveApiTenant` — token wajib `tenant_id` jika `tenancy.api_require_tenant`
- **Validasi:** `config/tenancy.php` `scope_fail_closed`
- **Permission:** Spatie `team_foreign_key = tenant_id`

---

### FR-004
**Langganan Tenant Aktif**

Akses panel admin diblokir jika langganan tenant tidak aktif (grace period).

**Deskripsi:** Middleware memeriksa status subscription sebelum request panel.

**Bukti:**
- **Route:** Middleware stack `EnsureTenantSubscriptionActive` — `AdminPanelProvider.php` `authMiddleware`
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** Redirect/block dari middleware
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** `subscription.active` alias — `bootstrap/app.php`

---

### FR-005
**RBAC per Resource (Shield)**

Akses CRUD Filament per entitas dikontrol permission Shield (`ViewAny:Student`, dll.).

**Deskripsi:** Policy memetakan ability Shield ke aksi Eloquent.

**Bukti:**
- **Route:** Filament resource routes (auto)
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** `StudentResource` + policy binding
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** `Modules/School/app/Policies/StudentPolicy.php` — `viewAny()` → `can('ViewAny:Student')`

---

### FR-006
**Super Admin Global Bypass**

User dengan flag super admin melewati semua Gate ability.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE**
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** Semua panel/resource
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** `app/Providers/AppServiceProvider.php` — `Gate::before()` + `User::isGlobalSuperAdmin()`

---

### FR-007
**UI Bilingual (id/en)**

Label UI admin melalui `FilamentUi`; user dapat ganti locale dari menu.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE**
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** `Modules/Core/app/Support/FilamentUi.php` — `text()`, `field()`
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** `scripts/lint-translations.php`
- **Permission:** **TIDAK TERDETEKSI DI KODE**

---

### FR-008
**CRUD Siswa K-12**

Admin tenant dapat mengelola data siswa (create/read/update/delete) beserta relasi absensi, nilai, pelanggaran.

**Bukti:**
- **Route:** Filament `/admin/{tenant}/students/*` (auto dari resource)
- **Controller:** **TIDAK TERDETEKSI DI KODE** (Filament pages)
- **Service:** `Modules/School/app/Services/AttendanceRecapService.php` (domain terkait)
- **UI:** `Modules/School/app/Filament/Resources/Students/StudentResource.php`
- **API:** `GET api/v1/students`, `GET api/v1/students/{id}` — `routes/api.php`
- **Validasi:** `StudentForm.php` — `->required()` pada field wajib
- **Permission:** `StudentPolicy`

---

### FR-009
**Rekap Absensi Sekolah**

Sistem menghitung rekap kehadiran siswa per periode.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE** (dipanggil dari UI/action)
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** `Modules/School/app/Services/AttendanceRecapService.php`
- **UI:** Resource `Attendance`, relation manager di `StudentResource`
- **API:** Data absensi di `StudentDashboardController::buildDashboard()`
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** `Modules/School/app/Policies/AttendancePolicy.php`

---

### FR-010
**Generate SPP Bulanan**

Scheduler membuat tagihan SPP siswa setiap awal bulan.

**Deskripsi:** Artisan command dijadwalkan `monthlyOn(1, '03:00')` dengan `withoutOverlapping`.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE** (Artisan `school:generate-monthly-tuition`)
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE** (logic di command class)
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** **TIDAK TERDETEKSI DI KODE**

**Bukti scheduler:** `routes/console.php` — `Schedule::command('school:generate-monthly-tuition')->monthlyOn(1, '03:00')`

---

### FR-011
**Inquiry Publik Admisi**

Calon siswa/orang tua dapat mengirim inquiry tanpa login; lead dibuat di tenant target.

**Bukti:**
- **Route:** `POST api/inquiry` — `docs/catalogs/api-routes-catalog.json`, middleware `throttle:10,1`
- **Controller:** `Modules/Enrollment/app/Http/Controllers/InquiryController.php` — `store()`
- **Service:** `Modules/Enrollment/app/Services/LeadInquiryService.php` — `createFromInquiry()`
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** `InquiryController::store()` — validasi `tenant_code`, `full_name`, UTM
- **Validasi:** `$request->validate([...])` baris 19–28 `InquiryController.php`
- **Permission:** Public (tanpa `auth:sanctum`)

---

### FR-012
**Promosi Applicant ke Student**

Applicant yang diterima dapat dipromosikan menjadi record `Student` dengan NIS otomatis.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE** (event/listener internal)
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** `Modules/Enrollment/app/Services/ApplicantPromotionService.php` — `promote()`, `isEnabledFor()`
- **UI:** Filament `ApplicantResource` (aksi accept)
- **API:** `POST api/v1/applicants` — `ApplicantController@store`
- **Validasi:** Setting tenant `enrollment.auto_promote_accepted_applicant`
- **Permission:** Policy `ApplicantPolicy`

**Bukti test:** `tests/Feature/ApplicantAcceptedPipelineTest.php`

---

### FR-013
**Invoice Siswa — Terbit & Terkunci**

Invoice yang sudah `issued` tidak dapat diubah (immutable).

**Bukti:**
- **Route:** Filament Finance resources
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** `Modules/Finance/app/Services/FinanceControlService.php` — `markInvoiceIssued()`, `assertInvoiceMutable()` via `isLockedForMutation()`
- **UI:** `StudentInvoiceResource`
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** `RuntimeException` jika invoice locked
- **Permission:** `StudentInvoicePolicy`

---

### FR-014
**Verifikasi Pembayaran & Jurnal**

Pembayaran terverifikasi memicu rekalkulasi invoice, jurnal akuntansi, notifikasi, dan audit.

**Bukti:**
- **Route:** `POST api/v1/payments` — `routes/api.php`
- **Controller:** `app/Http/Controllers/Api/v1/PaymentController.php` — `store()`
- **Service:** `FinanceControlService::verifyPayment()` — DB transaction + `createPaymentJournalEntry()`
- **UI:** Filament action verify payment
- **API:** `PaymentController` — rules `student_invoice_id`, `amount`, `status in:pending,verified,rejected`
- **Validasi:** `Validator::make()` baris 21–31; service-level lock check
- **Permission:** `PaymentPolicy`

**Bukti event:** `Modules/Finance/app/Events/StudentInvoicePaid.php`

---

### FR-015
**Three-Way Match Procurement**

Vendor bill divalidasi terhadap PO dan goods receipt (qty & toleransi harga).

**Bukti:**
- **Route:** Filament procurement vendor bill flow
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** `Modules/Procurement/app/Services/ThreeWayMatchValidator.php` — `validate()`
- **UI:** `VendorBillResource`
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** Array error human-readable dari `validate()`
- **Permission:** `VendorBillPolicy`

---

### FR-016
**PDF Dokumen Procurement**

User terautentikasi dapat mengunduh PDF PR, PO, GR, RFQ, vendor bill.

**Bukti:**
- **Route:** `GET /procurement/purchase-orders/{purchaseOrder}/pdf` — `Modules/Procurement/routes/web.php`
- **Controller:** `PurchaseOrderPdfController`, `PurchaseRequisitionPdfController`, dll.
- **Service:** `RequestForQuotationDocumentService` (RFQ)
- **UI:** Tombol print/PDF di Filament
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** Middleware `auth`, `verified`

---

### FR-017
**Workflow Approval — Advance**

Actor yang berwenang dapat memajukan instance workflow dengan validasi evidence dan form schema.

**Bukti:**
- **Route:** Filament `WorkflowInstanceResource` actions
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` — `advance()`, `authorizeActor()`
- **UI:** `WorkflowInstanceResource`, `WorkflowCanvas` Livewire
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** `WorkflowEvidenceRequiredException`, `WorkflowFormSchemaValidator`
- **Permission:** Assignment-based di `authorizeActor()`

---

### FR-018
**Workflow — Return, Cancel, Reassign**

Instance workflow dapat dikembalikan ke step sebelumnya, dibatalkan, atau assignment di-reassign.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE**
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** `DatabaseWorkflowEngine` — `returnToStep()`, `cancel()`, `reassign()`
- **UI:** Actions di Filament workflow instance
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** `WorkflowAuthorizationException`
- **Permission:** Per assignment + role step

**Bukti test:** `tests/Feature/WorkflowBudgetApprovalTest.php`

---

### FR-019
**OPAC Perpustakaan Publik**

Pengunjung dapat menelusuri katalog buku per tenant; anggota terauth dapat reservasi dan sirkulasi.

**Bukti:**
- **Route:** `GET opac/{tenant}/` — `Modules/Library/routes/web.php`
- **Controller:** `Modules/Library/app/Http/Controllers/PublicOpacController.php` — `index()`, `reserve()`, `checkout()`
- **Service:** **TIDAK TERDETEKSI DI KODE** (logic di controller/model)
- **UI:** Blade views modul Library
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** Public index; `auth, verified` untuk reserve/circulation

---

### FR-020
**Hitung Ulang Denda Perpustakaan**

Scheduler menghitung denda keterlambatan pinjaman setiap jam.

**Deskripsi:** Command `fos:library:recalc-fines` dijadwalkan hourly.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE**
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** **TIDAK TERDETEKSI DI KODE**

**Bukti scheduler:** `routes/console.php` — `Schedule::command('fos:library:recalc-fines')->hourly()`

---

### FR-021
**Sinkronisasi Moodle (Outbox)**

Perubahan master data (course, user, cohort) di-queue ke Moodle via outbox pattern.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE** (queue job)
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** `app/Integrations/Moodle/MoodleSyncService.php` — `syncOutboxItem()`
- **UI:** `MoodleSyncOutboxResource` (Monitoring)
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** `config/moodle.enabled`
- **Permission:** Monitoring resource policy

**Bukti job:** `app/Jobs/ProcessMoodleSyncOutboxJob.php` — `handle()`  
**Bukti observer:** `app/Observers/StudentObserver.php`, `CourseObserver.php`  
**Bukti scheduler:** `fos:moodle:drain-outbox` setiap menit

---

### FR-022
**Ingest Hasil Ujian Runtime (CBT)**

Runtime ujian eksternal mengirim attempt via webhook API; skor disinkronkan ke FOS.

**Bukti:**
- **Route:** `POST api/exam/runtime/attempts` — `Modules/Exam/routes/api.php`
- **Controller:** `Modules/Exam/app/Http/Controllers/Api/ExamRuntimeWebhookController.php` — `storeAttempt()`
- **Service:** `Modules/Exam/app/Services/ExamRuntimeSyncService.php` — `ingestAttempt()`
- **UI:** Filament Exam resources (definisi, peserta)
- **API:** `auth:sanctum` — `api-routes-catalog.json`
- **Validasi:** `$request->validate()` UUID `exam_definition_id`, `exam_participant_id`
- **Permission:** Token Sanctum

---

### FR-023
**API Master Data Read-Only**

Klien mobile/API dapat membaca organizations, students, classes, courses, employees.

**Bukti:**
- **Route:** `GET api/v1/students`, `GET api/v1/organizations`, dll. — `routes/api.php`
- **Controller:** `StudentController`, `OrganizationController`, `CourseController`, dll.
- **Service:** **TIDAK TERDETEKSI DI KODE** (Eloquent langsung di controller)
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** Middleware `auth:sanctum`, `resolve.api.tenant`, `throttle:api`
- **Validasi:** **TIDAK TERDETEKSI DI KODE** (read-only)
- **Permission:** Sanctum token + tenant scope

---

### FR-024
**API Write — Applicant, Payment, Leave Request**

Klien dapat membuat applicant, payment, dan leave request via API dengan idempotency.

**Bukti:**
- **Route:** `POST api/v1/applicants|payments|leave-requests` — middleware `idempotency`
- **Controller:** `ApplicantController`, `PaymentController`, `LeaveRequestController`
- **Service:** **TIDAK TERDETEKSI DI KODE** (create langsung / FinanceControl di UI)
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** v1 dan v2 mirror — `routes/api.php`
- **Validasi:** Per-controller `Validator::make()` / `validate()`
- **Permission:** `auth:sanctum` + tenant

---

### FR-025
**Idempotency API Write**

Request POST/PUT/PATCH dengan header `Idempotency-Key` di-replay jika key sama; conflict jika body berbeda.

**Bukti:**
- **Route:** Middleware group `idempotency` — `routes/api.php`
- **Controller:** Semua write endpoint dalam group
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** `app/Http/Middleware/IdempotencyKey.php` — `handle()`, TTL 24 jam
- **Validasi:** HTTP 409 `idempotency_conflict`
- **Permission:** **TIDAK TERDETEKSI DI KODE**

---

### FR-026
**Dashboard Siswa (Mobile API)**

API mengagregasi absensi 30 hari, ringkasan nilai, dan invoice untuk satu siswa.

**Bukti:**
- **Route:** `GET api/v1/students/{id}/dashboard` — `routes/api.php`
- **Controller:** `app/Http/Controllers/Api/v1/StudentDashboardController.php` — `show()`, `buildDashboard()`
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** Response JSON `{ data: ... }`
- **Validasi:** `Student::findOrFail($id)`
- **Permission:** `auth:sanctum`

---

### FR-027
**Registrasi Device Push**

Klien mobile mendaftarkan device token untuk notifikasi.

**Bukti:**
- **Route:** `POST api/v1/devices`, `DELETE api/v1/devices/{token}`
- **Controller:** `app/Http/Controllers/Api/v1/DeviceController.php`
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** v1/v2
- **Validasi:** **TIDAK TERDETEKSI DI KODE** (perlu baca controller penuh)
- **Permission:** `auth:sanctum`

---

### FR-028
**Verifikasi Surat E-Office (Publik)**

Pihak eksternal memverifikasi keaslian surat via token tanpa login.

**Bukti:**
- **Route:** `GET api/letters/verify/{token}` — `routes/api.php`
- **Controller:** `Modules/EOffice/app/Http/Controllers/LetterVerificationController.php` — `show()`
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** Public, middleware `api` only
- **Validasi:** Token lookup
- **Permission:** Public

---

### FR-029
**Webhook WhatsApp**

Provider WhatsApp mengirim callback ke endpoint webhook aplikasi.

**Bukti:**
- **Route:** `POST api/webhooks/whatsapp/{provider}` — `routes/api.php`
- **Controller:** `Modules/Messaging/app/Http/Controllers/WhatsAppWebhookController.php` — `handle()`
- **Service:** `Modules/Messaging/app/Services/NotificationDispatcher.php` — `sendWhatsApp()`
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** Public webhook
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** **TIDAK TERDETEKSI DI KODE**

---

### FR-030
**CMS Situs Publik**

Institusi menyajikan halaman web publik, sitemap, dan form kontak throttled.

**Bukti:**
- **Route:** `GET cms/{site}/pages/{slug}`, `POST cms/{site}/contact` — `Modules/Cms/routes/web.php`
- **Controller:** `Modules/Cms/app/Http/Controllers/PublicSiteController.php` — `page()`, `contact()`
- **Service:** `Modules/Cms/app/Services/CmsPublishService.php`
- **UI:** Blade public templates
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** `throttle:10,1` pada contact
- **Permission:** Public

---

### FR-031
**Webhook Donasi**

Gateway pembayaran donasi memanggil webhook aplikasi (CSRF dikecualikan).

**Bukti:**
- **Route:** `donation/webhook` — CSRF except `bootstrap/app.php`
- **Controller:** `Modules/Donation/app/Http/Controllers/DonationWebhookController.php`
- **Service:** `Modules/Donation/app/Services/DonationPaymentService.php`
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** Web POST
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** **TIDAK TERDETEKSI DI KODE**

---

### FR-032
**Import CSV per Resource**

Admin dapat mengimpor data bulk dari template CSV per entitas Filament.

**Bukti:**
- **Route:** Filament import action (Livewire)
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** `ImportTableActions` pada table Filament
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** `app/Filament/Imports/BaseModelImporter.php`
- **Permission:** Shield import permission per resource

---

### FR-033
**Panel Platform Owner**

User `platform_owner` mengakses panel `/platform` terpisah dari tenant admin.

**Bukti:**
- **Route:** Filament `/platform/*` — `PlatformPanelProvider.php`
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** Platform panel pages/resources
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** `Modules/Core/app/Models/User.php` — `canAccessPanel()` case platform

---

### FR-034
**Panel Orang Tua**

User terhubung `parent_students` mengakses panel `/parent` terbatas.

**Bukti:**
- **Route:** Filament `/parent/*` — `ParentPanelProvider.php`
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** Parent panel resources
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** `User::canAccessPanel()` case parent

---

### FR-035
**AI Prompt Registry & Governance**

Modul Ai mengelola template prompt dan audit penggunaan AI advisor.

**Bukti:**
- **Route:** Filament resource auto `/admin/.../ai-prompt-templates`
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** `Modules/Ai/app/Services/AiAdvisorService.php`
- **UI:** `AiPromptTemplateResource`
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** `AiPromptTemplatePolicy`

**Bukti modul:** `Modules/Ai/module.json` description

---

### FR-036
**Mutasi Stok Inventory**

Pergerakan stok gudang dicatat dan dapat memicu event workflow.

**Bukti:**
- **Route:** Filament + `Modules/Inventory/routes/api.php` scaffold
- **Controller:** `Modules/Inventory/app/Http/Controllers/InventoryController.php`
- **Service:** `Modules/Inventory/app/Services/StockMoveService.php`
- **UI:** `StockMoveResource`
- **API:** Module API scaffold
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** `StockMovePolicy`

**Bukti event:** `Modules/Inventory/app/Events/StockMoveCommitted.php`

---

### FR-037
**OpenAPI Specification**

Klien dapat mengunduh spesifikasi OpenAPI v1/v2.

**Bukti:**
- **Route:** `GET api/openapi.json`, `GET api/v2/openapi.json`
- **Controller:** `app/Http/Controllers/Api/OpenApiController.php` — `v1()`, `v2()`
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** **TIDAK TERDETEKSI DI KODE**
- **API:** Public read
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** Public

---

### FR-038
**Pengajuan Cuti via API**

Karyawan/HR membuat draft leave request melalui API mobile.

**Bukti:**
- **Route:** `POST api/v1/leave-requests`
- **Controller:** `LeaveRequestController::store()`
- **Service:** `LeaveRequestDocumentService` (PDF/UI terpisah)
- **UI:** `LeaveRequestResource` Filament
- **API:** Validasi `start_date`, `end_date after_or_equal`, `total_days min:1`
- **Validasi:** `Validator::make()` baris 19–27
- **Permission:** `LeaveRequestPolicy` (UI); Sanctum (API)

---

### FR-039
**Branding Tenant Dinamis**

Panel admin menampilkan warna primary dan logo dari `TenantSetting`.

**Bukti:**
- **Route:** **TIDAK TERDETEKSI DI KODE**
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** **TIDAK TERDETEKSI DI KODE**
- **UI:** `AdminPanelProvider.php` — `->colors()`, `->brandLogo()` query `TenantSetting`
- **API:** **TIDAK TERDETEKSI DI KODE**
- **Validasi:** **TIDAK TERDETEKSI DI KODE**
- **Permission:** **TIDAK TERDETEKSI DI KODE**

---

### FR-040
**CRUD Generik 375 Entitas Domain**

Setiap modul domain menyediakan CRUD Filament standar (list/create/edit/view) untuk model bisnis.

**Deskripsi:** Pola `ModuleResource` + `Schemas/*Form` + `Tables/*Table` — aturan bisnis per form **belum diaudit semua**.

**Bukti:**
- **Route:** Filament auto-discovery via `ModulesPlugin`
- **Controller:** **TIDAK TERDETEKSI DI KODE**
- **Service:** Per-domain (tidak seragam)
- **UI:** `Modules/Core/app/Filament/Support/ModuleResource.php`
- **API:** Scaffold `apiResource` beberapa modul — **TERINDIKASI boilerplate**
- **Validasi:** Per `*Form.php` — **TERINDIKASI**
- **Permission:** Shield auto-generated per resource

---

## TIDAK TERDETEKSI DI KODE

| Requirement | Status |
|-------------|--------|
| Portal login self-service siswa dedicated | **TIDAK TERDETEKSI DI KODE** |
| Product backlog / prioritas bisnis FR | **TIDAK TERDETEKSI DI KODE** |
| FormRequest class terpusat per endpoint API | **TIDAK TERDETEKSI DI KODE** (dominan inline validation) |
| SRS / user story formal | **TIDAK TERDETEKSI DI KODE** |

---

## Regenerasi

```bash
php scripts/extract-api-routes.php   # → docs/catalogs/api-routes-catalog.json
php artisan route:list --except-vendor
php artisan test --compact tests/Feature/ApplicantAcceptedPipelineTest.php
php artisan test --compact tests/Feature/WorkflowBudgetApprovalTest.php
```

**Referensi:** `03_arsitektur_aplikasi.md`, `04_analisis_modul.md`, `14_api_dan_integrasi.md`
