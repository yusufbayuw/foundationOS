# 09 — Sequence Diagram

**Commit analisis:** `d3be06aa` · **Use case sumber:** `07_use_case_diagram.md`, `08_activity_diagram.md`

## Konvensi Layer (ABCE)

| Layer | Simbol diagram | Pemetaan kode | Bukti |
|-------|----------------|---------------|-------|
| **Actor** | `actor` | User, gateway eksternal, runtime CBT | `User`, Midtrans POST, dll. |
| **Boundary** | `participant BND` | Filament page, route HTTP, middleware stack | `ViewWorkflowInstance`, `routes/api.php` |
| **Control** | `participant CTR` | Controller, Service, Middleware, Job | `*Controller`, `*Service`, `*Middleware` |
| **Entity** | `participant ENT` | Eloquent Model (bukan Repository terpisah) | `Applicant`, `Payment`, `WorkflowInstance` |
| **Database** | `participant DB` | MySQL via Eloquent | migrasi, `DB::transaction` |
| **External API** | `participant EXT` | Moodle REST, Midtrans | `MoodleClient`, webhook Midtrans |

> **Repository:** **TIDAK TERDETEKSI DI KODE** — tidak ada class `*Repository`; persistensi langsung via Eloquent Model (Entity) → Database.

---

## Indeks Diagram

| ID | Proses utama | Actor |
|----|--------------|-------|
| SD-01 | Login panel admin + cek langganan | Staf tenant |
| SD-02 | Registrasi tenant baru | User terdaftar |
| SD-03 | Verifikasi pembayaran siswa (UI) | Staf keuangan |
| SD-04 | Workflow advance | Approver |
| SD-05 | Inquiry admisi publik | Pengunjung |
| SD-06 | Applicant diterima → student | Staf + sistem event |
| SD-07 | POST applicant API | Klien API |
| SD-08 | OPAC checkout pinjaman | Staf perpustakaan |
| SD-09 | Exam runtime ingest | Runtime CBT |
| SD-10 | Webhook billing Midtrans | Midtrans |
| SD-11 | Dashboard siswa API | Klien API |
| SD-12 | Moodle outbox sync (queue) | Scheduler / queue worker |
| SD-13 | Portal orang tua (baca nilai) | Orang tua |
| SD-14 | Verifikasi surat e-office | Pengunjung |
| SD-15 | Webhook WhatsApp | Provider WhatsApp |

---

## SD-01 — Login Panel Admin + Cek Langganan

**Catatan:** Validasi kredensial login ditangani Filament/Laravel Auth — **TIDAK TERDETEKSI DI KODE** (handler credential di repo ini).

```mermaid
sequenceDiagram
    actor User as ACT Staf Tenant
    participant BND as BND Filament /admin<br/>AdminPanelProvider
    participant CTR as CTR EnsureTenantSubscriptionActive
    participant ENT as ENT Tenant (Eloquent)
    participant DB as Database

    User->>BND: GET /admin/{tenant}/...
    BND->>BND: Authenticate + SetUserLocale<br/>authMiddleware baris 115-118
    BND->>CTR: handle(request)
    CTR->>ENT: Filament::getTenant()
    ENT->>DB: SELECT tenants
  alt tenant null
        CTR-->>BND: pass through (baris 16-17)
    else tenant isLocked()
        ENT->>ENT: isLocked() Tenant.php:165
        CTR->>CTR: request is *billing*? (baris 23)
        CTR-->>User: redirect billing + warning (baris 24-25)
    else subscription OK
        CTR-->>BND: next(request)
        BND-->>User: render page
    end
```

## Bukti

| Layer | File | Method / baris |
|-------|------|----------------|
| Boundary | `app/Providers/Filament/AdminPanelProvider.php` | `authMiddleware`, `->login()` baris 47, 115–118 |
| Control | `app/Http/Middleware/EnsureTenantSubscriptionActive.php` | `handle()` baris 12–30 |
| Entity | `Modules/Core/app/Models/Tenant.php` | `isLocked()` baris 165–169 |
| Actor gate | `Modules/Core/app/Models/User.php` | `canAccessPanel('admin')` baris 471–476 |

---

## SD-02 — Registrasi Tenant Baru

```mermaid
sequenceDiagram
    actor User as ACT User Terautentikasi
    participant BND as BND RegisterTenant<br/>(Filament Page)
    participant CTR as CTR TenantAdminProvisioner<br/>TenantModuleProvisioner
    participant ENT as ENT Tenant (Eloquent)
    participant DB as Database

    User->>BND: submit form tenant
    BND->>BND: validate unique code<br/>RegisterTenant.php:39-41
    BND->>ENT: Tenant::create([...])
    ENT->>DB: INSERT tenants
    BND->>CTR: ensureTenantOwnerRole(user, tenant)
    CTR->>DB: INSERT user_tenant_roles / roles
    BND->>CTR: assignShieldSuperAdmin(user, tenant)
    BND->>CTR: enableForTenant(core, global)
    CTR->>DB: INSERT tenant_modules
    BND-->>User: redirect ke tenant baru
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `app/Filament/Pages/Tenancy/RegisterTenant.php` | `form()`, `handleRegistration()` baris 67–88 |
| Control | `Modules/Core/app/Services/TenantAdminProvisioner.php` | `ensureTenantOwnerRole()`, `assignShieldSuperAdmin()` |
| Control | `Modules/Core/app/Services/TenantModuleProvisioner.php` | `enableForTenant()` baris 85 |
| Entity | `Modules/Core/app/Models/Tenant.php` | `create()` |

---

## SD-03 — Verifikasi Pembayaran Siswa (Filament)

```mermaid
sequenceDiagram
    actor User as ACT Staf Keuangan
    participant BND as BND ViewPayment<br/>(Filament Page)
    participant CTR as CTR FinanceControlService
    participant ENT as ENT Payment, StudentInvoice,<br/>JournalEntry (Eloquent)
    participant DB as Database

    User->>BND: klik Verify Payment
    Note over BND: visible jika status=pending<br/>ViewPayment.php:38
    BND->>CTR: verifyPayment(payment, user, notes)
    CTR->>DB: BEGIN TRANSACTION
    CTR->>ENT: payment->fresh()
    alt isLockedForMutation()
        CTR-->>BND: RuntimeException baris 48-50
        BND-->>User: Notification danger baris 49-52
    else pending
        CTR->>ENT: forceFill status=verified
        ENT->>DB: UPDATE payments
        CTR->>ENT: recalculateInvoice()
        ENT->>DB: UPDATE student_invoices
        CTR->>ENT: createPaymentJournalEntry()
        ENT->>DB: INSERT journal_entries, lines
        CTR->>ENT: audit(AuditLog)
        ENT->>DB: INSERT audit_logs
        CTR->>DB: COMMIT
        BND-->>User: Notification success baris 47
    end
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `Modules/Finance/app/Filament/Resources/Payments/Pages/ViewPayment.php` | action `verifyPayment` baris 34–53 |
| Control | `Modules/Finance/app/Services/FinanceControlService.php` | `verifyPayment()` baris 43–71 |
| Entity | `Modules/Finance/app/Models/Payment.php` | `isLockedForMutation()` |
| Entity | `Modules/Monitoring/app/Models/AuditLog.php` | via `audit()` |

**Repository:** TIDAK TERDETEKSI DI KODE

---

## SD-04 — Workflow Advance

```mermaid
sequenceDiagram
    actor User as ACT Approver / Super Admin
    participant BND as BND ViewWorkflowInstance<br/>(Filament)
    participant CTR as CTR DatabaseWorkflowEngine
    participant CTR2 as CTR JsonLogicWorkflowTransitionResolver<br/>WorkflowFormSchemaValidator
    participant ENT as ENT WorkflowInstance,<br/>WorkflowAssignment (Eloquent)
    participant DB as Database
    participant EVT as Event Bus (Laravel)

    User->>BND: klik action approve
    BND->>BND: hasPendingAssignment() baris 213-231
    BND->>CTR: advance(instance, action, data, actor)
    CTR->>DB: BEGIN + lockForUpdate instance
    CTR->>CTR: authorizeActor() baris 416-431
    alt tidak berwenang
        CTR-->>BND: WorkflowAuthorizationException
    else OK
        CTR->>CTR2: validator.validate() baris 68
        CTR->>CTR2: transitionResolver.resolve() baris 110
        CTR->>ENT: update assignments Completed
        ENT->>DB: UPDATE workflow_assignments
        CTR->>ENT: forceFill instance step/status
        ENT->>DB: UPDATE workflow_instances
        CTR->>DB: COMMIT
        CTR->>EVT: WorkflowAdvanced::dispatch baris 87-88
        EVT->>EVT: CreateAssignmentsForCurrentStep<br/>EventServiceProvider.php:31-37
        BND-->>User: Notification success baris 105-108
    end
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `Modules/Workflow/app/Filament/Resources/WorkflowInstances/Pages/ViewWorkflowInstance.php` | `getHeaderActions()` baris 86–109 |
| Control | `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` | `advance()`, `authorizeActor()` |
| Control | `Modules/Workflow/app/Services/JsonLogicWorkflowTransitionResolver.php` | `resolve()` |
| Control | `Modules/Workflow/app/Providers/EventServiceProvider.php` | listener map baris 31–37 |
| Entity | `Modules/Workflow/app/Models/WorkflowInstance.php` | — |

---

## SD-05 — Inquiry Admisi Publik

```mermaid
sequenceDiagram
    actor User as ACT Pengunjung Web
    participant BND as BND POST api/inquiry<br/>throttle:10,1
    participant CTR as CTR InquiryController
    participant CTR2 as CTR LeadInquiryService
    participant ENT as ENT Tenant, Lead,<br/>LeadSource, LeadActivity
    participant DB as Database

    User->>BND: POST JSON inquiry
    BND->>CTR: store(request)
    CTR->>CTR: validate() baris 19-28
    alt validasi gagal
        CTR-->>User: 422
    end
    CTR->>ENT: Tenant::where(code)->firstOrFail()
    ENT->>DB: SELECT tenants
    CTR->>CTR2: createFromInquiry(...)
    CTR2->>ENT: LeadSource::firstOrCreate
    ENT->>DB: INSERT/SELECT lead_sources
    CTR2->>ENT: Lead::create
    ENT->>DB: INSERT leads
    CTR2->>ENT: LeadActivity::create
    ENT->>DB: INSERT lead_activities
    CTR-->>User: 201 {lead_id}
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `Modules/Enrollment/routes/api.php` | `POST /inquiry` baris 6–7 |
| Control | `Modules/Enrollment/app/Http/Controllers/InquiryController.php` | `store()` |
| Control | `Modules/Enrollment/app/Services/LeadInquiryService.php` | `createFromInquiry()` baris 15–49 |
| Entity | `Modules/Enrollment/app/Models/Lead.php` | — |

---

## SD-06 — Applicant Diterima → Student (Event Pipeline)

```mermaid
sequenceDiagram
    actor User as ACT Staf Admisi
    participant BND as BND ApplicantResource<br/>(Filament Edit)
    participant ENT as ENT Applicant (Eloquent)
    participant DB as Database
    participant EVT as Event Bus
    participant CTR as CTR ApplicantPromotionService
    participant ENT2 as ENT Student (Eloquent)

    User->>BND: ubah status → accepted
    BND->>ENT: save()
    ENT->>DB: UPDATE applicants
    ENT->>ENT: booted updated hook<br/>Applicant.php:68-77
    ENT->>EVT: ApplicantAccepted::dispatch
    EVT->>CTR: CreateStudentFromAcceptedApplicant<br/>→ promote() baris 26
    CTR->>CTR: isEnabledFor(tenant) baris 28-30
    alt auto_promote false
        CTR-->>EVT: return null
    else enabled
        CTR->>DB: BEGIN TRANSACTION
        CTR->>ENT2: Student::create baris 49-62
        ENT2->>DB: INSERT students
        loop generateNis unik
            CTR->>DB: SELECT EXISTS nis
        end
        CTR->>ENT: update converted_to_student_id
        ENT->>DB: UPDATE applicants
        CTR->>DB: COMMIT
    end
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | Filament `ApplicantResource` edit page | save via Livewire |
| Entity | `Modules/Enrollment/app/Models/Applicant.php` | `booted()` baris 62–89 |
| Control | `Modules/School/app/Listeners/CreateStudentFromAcceptedApplicant.php` | `handle()` baris 21–28 |
| Control | `Modules/Enrollment/app/Services/ApplicantPromotionService.php` | `promote()` baris 26–70 |
| Test | `tests/Feature/ApplicantAcceptedPipelineTest.php` | — |

---

## SD-07 — POST Applicant API (Sanctum + Idempotency)

```mermaid
sequenceDiagram
    actor User as ACT Klien API
    participant BND as BND routes/api.php v1<br/>middleware stack
    participant CTR as CTR ResolveApiTenant<br/>IdempotencyKey
    participant CTR2 as CTR ApplicantController
    participant CTR3 as CTR WebhookDispatcher
    participant ENT as ENT Applicant (Eloquent)
    participant DB as Database
    participant EXT as EXT Tenant Webhook URL

    User->>BND: POST /api/v1/applicants<br/>Bearer + Idempotency-Key
    BND->>CTR: sanctum authenticate
    BND->>CTR: ResolveApiTenant::handle
    CTR->>CTR: CurrentTenant::set(token.tenant_id)
    BND->>CTR: IdempotencyKey::handle
    alt cache hit same body
        CTR-->>User: cached 201 + X-Idempotency-Replayed
    end
    BND->>CTR2: store(request)
    CTR2->>CTR2: Validator::make baris 21-33
    CTR2->>ENT: Applicant::create
    ENT->>DB: INSERT applicants
    CTR2->>CTR3: dispatch enrollment.created
    CTR3->>EXT: HTTP POST webhook (jika dikonfigurasi)
    CTR2-->>User: 201 JSON
    CTR->>CTR: Cache::put response
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `routes/api.php` | baris 43–72 |
| Control | `app/Http/Middleware/ResolveApiTenant.php` | `handle()` |
| Control | `app/Http/Middleware/IdempotencyKey.php` | `handle()` |
| Control | `app/Http/Controllers/Api/v1/ApplicantController.php` | `store()` |
| Control | `app/Services/WebhookDispatcher.php` | `dispatch()` |
| Entity | `Modules/Enrollment/app/Models/Applicant.php` | `create()` |

---

## SD-08 — OPAC Checkout Pinjaman

```mermaid
sequenceDiagram
    actor User as ACT Staf Perpustakaan
    participant BND as BND POST opac/{tenant}/circulation/checkout<br/>auth, verified
    participant CTR as CTR PublicOpacController
    participant CTR2 as CTR CirculationPolicyResolver<br/>LibraryCirculationService
    participant ENT as ENT Member, BookCopy, Loan
    participant DB as Database

    User->>BND: POST checkout form
    BND->>CTR: checkout()
    CTR->>CTR: authorizeCirculation() baris 705-708
    alt 403 tidak berwenang
        CTR-->>User: abort 403
    end
    CTR->>CTR: handleCheckout validate baris 494-498
    CTR->>ENT: Member::firstOrFail
    CTR->>ENT: BookCopy::firstOrFail
    ENT->>DB: SELECT
    CTR->>CTR: abort_if business rules baris 523-528
    CTR->>DB: BEGIN TRANSACTION
    CTR->>ENT: Loan::create baris 531-546
    ENT->>DB: INSERT loans
    CTR->>ENT: copy status borrowed baris 548
    ENT->>DB: UPDATE book_copies
    CTR->>DB: COMMIT
    CTR->>CTR2: refreshMemberCounters<br/>refreshBookAvailability
    CTR2->>DB: UPDATE aggregates
    CTR-->>User: redirect back + flash
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `Modules/Library/routes/web.php` | baris 26–28 |
| Control | `Modules/Library/app/Http/Controllers/PublicOpacController.php` | `checkout()`, `handleCheckout()` baris 492–559 |
| Control | `Modules/Library/app/Support/LibraryCirculationService.php` | `refreshMemberCounters()` |
| Entity | `Modules/Library/app/Models/Loan.php` | — |

---

## SD-09 — Exam Runtime Ingest Attempt

```mermaid
sequenceDiagram
    actor User as ACT Runtime CBT / Klien API
    participant BND as BND POST api/exam/runtime/attempts<br/>auth:sanctum
    participant CTR as CTR ExamRuntimeWebhookController
    participant CTR2 as CTR ExamRuntimeSyncService
    participant ENT as ENT ExamDefinition,<br/>ExamParticipant, ExamAttemptSync
    participant DB as Database

    User->>BND: POST attempt payload
    BND->>CTR: storeAttempt(request, syncService)
    CTR->>CTR: validate() baris 16-24
    CTR->>ENT: ExamDefinition::findOrFail
    CTR->>ENT: ExamParticipant::findOrFail
    ENT->>DB: SELECT
    CTR->>CTR2: ingestAttempt(def, participant, payload)
    CTR2->>DB: BEGIN TRANSACTION
    CTR2->>ENT: ExamAttemptSync::updateOrCreate
    ENT->>DB: UPSERT exam_attempt_syncs
    loop gradeBridges
        CTR2->>CTR2: bridge.syncAttempt() jika supports
    end
    CTR2->>DB: COMMIT
    CTR-->>User: JSON {id, sync_status}
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `Modules/Exam/routes/api.php` | baris 6–8 |
| Control | `Modules/Exam/app/Http/Controllers/Api/ExamRuntimeWebhookController.php` | `storeAttempt()` |
| Control | `Modules/Exam/app/Services/ExamRuntimeSyncService.php` | `ingestAttempt()` baris 24–45 |
| Entity | `Modules/Exam/app/Models/ExamAttemptSync.php` | `updateOrCreate` |

---

## SD-10 — Webhook Billing Midtrans

```mermaid
sequenceDiagram
    actor User as ACT Midtrans Gateway
    participant BND as BND POST /billing/webhook<br/>CSRF except
    participant CTR as CTR BillingController
    participant CTR2 as CTR BillingService
    participant CTR3 as CTR MidtransWebhookVerifier
    participant ENT as ENT SubscriptionLog, Tenant
    participant DB as Database

    User->>BND: POST notification JSON
    BND->>CTR: webhook(request)
    CTR->>CTR2: handleWebhookNotification(all)
    CTR2->>CTR3: verifySignature() baris 119
    alt signature invalid
        CTR3-->>CTR: MidtransWebhookException
        CTR-->>User: 400 JSON
    end
    CTR2->>DB: BEGIN TRANSACTION
    CTR2->>ENT: SubscriptionLog lockForUpdate
    ENT->>DB: SELECT ... FOR UPDATE
    alt invoice tidak ada / duplicate key / amount mismatch
        CTR2->>DB: ROLLBACK or early return
        CTR-->>User: 200 ok (silent skip)
    else paid baru
        CTR2->>ENT: invoice update payment_status
        CTR2->>ENT: tenant activateTenantSubscription
        ENT->>DB: UPDATE tenants, subscription_logs
        CTR2->>DB: COMMIT
        CTR-->>User: 200 {status: ok}
    end
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `routes/web.php` | `billing.webhook` baris 12 |
| Boundary | `bootstrap/app.php` | CSRF except baris 30–33 |
| Control | `app/Http/Controllers/BillingController.php` | `webhook()` baris 16–37 |
| Control | `app/Services/BillingService.php` | `handleWebhookNotification()` baris 117–195 |
| Control | `app/Services/Billing/MidtransWebhookVerifier.php` | `verifySignature()` |
| External | Midtrans | POST notification |
| Entity | `Modules/Core/app/Models/SubscriptionLog.php` | — |

---

## SD-11 — Dashboard Siswa API

```mermaid
sequenceDiagram
    actor User as ACT Klien API Mobile
    participant BND as BND GET api/v1/students/{id}/dashboard
    participant CTR as CTR ResolveApiTenant
    participant CTR2 as CTR StudentDashboardController
    participant ENT as ENT Student, Attendance,<br/>StudentGrade, StudentInvoice
    participant DB as Database
    participant CACHE as Cache Store

    User->>BND: GET + Bearer token
    BND->>CTR: set CurrentTenant
    BND->>CTR2: show(request, id)
    CTR2->>ENT: Student::findOrFail(id)
    ENT->>DB: SELECT students
    CTR2->>CACHE: Cache::remember 300s
    alt cache miss
        CTR2->>CTR2: buildDashboard(student)
        CTR2->>ENT: Attendance query 30 hari
        ENT->>DB: SELECT attendances
        CTR2->>ENT: StudentGrade, StudentInvoice
        ENT->>DB: SELECT
        CTR2->>CACHE: store result
    end
    CTR2-->>User: JSON {data: ...}
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `routes/api.php` | baris 77 |
| Control | `app/Http/Controllers/Api/v1/StudentDashboardController.php` | `show()`, `buildDashboard()` baris 17–40 |
| Entity | `Modules/School/app/Models/Student.php`, `Attendance.php` | — |
| Cache | `config/cache.php` | default store |

---

## SD-12 — Moodle Outbox Sync (Queue Worker)

```mermaid
sequenceDiagram
    actor User as ACT Queue Worker<br/>fos:moodle:drain-outbox
    participant BND as BND ProcessMoodleSyncOutboxJob
    participant CTR as CTR MoodleSyncService
    participant CTR2 as CTR MoodleMapper
    participant ENT as ENT MoodleSyncOutbox, User, Course
    participant DB as Database
    participant EXT as EXT Moodle REST API<br/>MoodleClient

    User->>BND: job handle(outboxId)
    alt moodle.enabled false
        BND-->>User: return baris 29-30
    end
    BND->>ENT: MoodleSyncOutbox::tryClaim
    ENT->>DB: UPDATE status processing
    BND->>CTR: syncOutboxItem(outbox)
    CTR->>ENT: load entity by type
    ENT->>DB: SELECT users/courses/...
    CTR->>CTR2: map payload
    CTR2->>EXT: MoodleClient REST call
    EXT-->>CTR2: response
    CTR->>ENT: update outbox status
    ENT->>DB: UPDATE moodle_sync_outbox
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Trigger | `app/Observers/StudentObserver.php` | → `MoodleOutboxService::enqueue()` |
| Control | `app/Integrations/Moodle/MoodleOutboxService.php` | `enqueue()` baris 35+ |
| Control | `app/Jobs/ProcessMoodleSyncOutboxJob.php` | `handle()` baris 27+ |
| Control | `app/Integrations/Moodle/MoodleSyncService.php` | `syncOutboxItem()` baris 33–44 |
| External | `app/Integrations/Moodle/MoodleClient.php` | REST |
| Scheduler | `routes/console.php` | `fos:moodle:drain-outbox` |

---

## SD-13 — Portal Orang Tua (Baca Nilai Anak)

```mermaid
sequenceDiagram
    actor User as ACT Orang Tua
    participant BND as BND /parent panel<br/>ChildGradeResource
    participant CTR as CTR ScopesToParentChildren<br/>(trait query)
    participant ENT as ENT ParentStudent, StudentGrade
    participant DB as Database

    User->>BND: GET /parent/child-grades
    BND->>BND: Authenticate ParentPanelProvider
    BND->>BND: canAccessPanel parent<br/>User.php:479-481
    BND->>CTR: getEloquentQuery()
    CTR->>ENT: ParentStudent::where parent_user_id
    ENT->>DB: SELECT parent_students
    CTR->>ENT: StudentGrade::whereIn student_id
    ENT->>DB: SELECT student_grades
    BND-->>User: table read-only<br/>canCreate false baris 19-22
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `app/Providers/Filament/ParentPanelProvider.php` | panel `parent` |
| Boundary | `app/Filament/Parent/Resources/ChildGrades/ChildGradeResource.php` | uses trait |
| Control | `app/Filament/Parent/Support/Concerns/ScopesToParentChildren.php` | `getEloquentQuery()` baris 10–17 |
| Entity | `Modules/Core/app/Models/ParentStudent.php` | — |

---

## SD-14 — Verifikasi Surat E-Office

```mermaid
sequenceDiagram
    actor User as ACT Pengunjung
    participant BND as BND GET api/letters/verify/{token}
    participant CTR as CTR LetterVerificationController
    participant ENT as ENT Letter (Eloquent)
    participant DB as Database

    User->>BND: GET token
    BND->>CTR: show(token)
    CTR->>ENT: Letter::where verification_token
    ENT->>DB: SELECT letters
    alt letter null
        CTR-->>User: 404 {valid: false}
    else found
        CTR-->>User: 200 {valid: true, letter_number, status}
    end
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `routes/api.php` | baris 40–41 |
| Control | `Modules/EOffice/app/Http/Controllers/LetterVerificationController.php` | `show()` baris 11–24 |
| Entity | `Modules/EOffice/app/Models/Letter.php` | `verification_token` |

---

## SD-15 — Webhook WhatsApp

```mermaid
sequenceDiagram
    actor User as ACT WhatsApp Provider
    participant BND as BND POST api/webhooks/whatsapp/{provider}
    participant CTR as CTR WhatsAppWebhookController
    participant DB as Database

    User->>BND: POST payload + optional signature
    BND->>CTR: handle(request, provider)
    alt whatsapp_secret configured
        CTR->>CTR: hash_hmac sha256 vs X-Hub-Signature-256
        alt mismatch
            CTR-->>User: abort 403 baris 19
        end
    end
    CTR-->>User: JSON {received: true, provider, verified}
```

## Bukti

| Layer | File | Method |
|-------|------|--------|
| Boundary | `routes/api.php` | baris 31–32 |
| Control | `Modules/Messaging/app/Http/Controllers/WhatsAppWebhookController.php` | `handle()` baris 11–27 |

**Catatan:** Handler **tidak** mempersist payload ke DB — **TIDAK TERDETEKSI DI KODE** penyimpanan inbound message.

---

## Diagram Layer Reference (ABCE → Komponen)

```mermaid
flowchart TB
    subgraph Actor
        A1[Staf / Parent / Public / Gateway]
    end
    subgraph Boundary
        B1[Filament Pages]
        B2[routes api/web]
    end
    subgraph Control
        C1[Controllers]
        C2[Services]
        C3[Middleware / Jobs]
    end
    subgraph Entity
        E1[Eloquent Models]
    end
    subgraph Persistence
        D1[(Database)]
    end
    subgraph External
        X1[Moodle / Midtrans / Webhooks]
    end

    A1 --> B1
    A1 --> B2
    B1 --> C1
    B2 --> C3
    C3 --> C1
    C1 --> C2
    C2 --> E1
    E1 --> D1
    C2 --> X1
```

---

## TIDAK TERDETEKSI DI KODE

| Item | Status |
|------|--------|
| Layer Repository (`*Repository` class) | **TIDAK TERDETEKSI DI KODE** — Eloquent langsung |
| Sequence detail credential Filament login | Framework internal |
| Meta sequence 257 Filament CRUD | Pola identik SD-03 (Policy → Filament → Eloquent → DB) |
| WhatsApp webhook persist inbound | **TIDAK TERDETEKSI DI KODE** |

---

## Regenerasi

```bash
php artisan test --compact tests/Feature/ApplicantAcceptedPipelineTest.php
php artisan test --compact tests/Feature/WorkflowBudgetApprovalTest.php
php artisan test --compact tests/Feature/UserIdentityAccessTest.php
grep -r "class.*Repository" Modules/ app/   # expect empty
```

**Referensi:** `07_use_case_diagram.md`, `08_activity_diagram.md`, `05_requirements_fungsional.md`
