# 09 — Sequence Diagram

## Ringkasan Singkat

Diagram sequence untuk interaksi antar komponen yang paling kritis dan terbukti di kode.

---

## SD-01: API — Buat Applicant (dengan Idempotency)

**Aktor:** Klien API (A5)  
**Bukti:** `routes/api.php:68-69`, `ApplicantController`, `IdempotencyKey`

```mermaid
sequenceDiagram
    participant C as API Client
    participant MW as Middleware Stack
    participant I as IdempotencyKey
    participant CTL as ApplicantController
    participant DB as Database

    C->>MW: POST /api/v1/applicants + Bearer token
    MW->>MW: auth:sanctum
    MW->>MW: resolve.api.tenant
    MW->>I: idempotency middleware
    I->>CTL: store(request)
    CTL->>DB: INSERT applicant
    DB-->>CTL: record
    CTL-->>C: 201 JSON
```

---

## SD-02: Workflow — Advance Instance

**Bukti:** `DatabaseWorkflowEngine.php`, `ViewWorkflowInstance.php`

```mermaid
sequenceDiagram
    participant U as Admin User
    participant UI as ViewWorkflowInstance
    participant ENG as DatabaseWorkflowEngine
    participant RES as JsonLogicTransitionResolver
    participant EVT as Event Bus
    participant ASN as CreateAssignmentsForCurrentStep

    U->>UI: Click approve action
    UI->>ENG: advance(instance, action, actor)
    ENG->>ENG: authorizeActor()
    ENG->>RES: resolveTransition(snapshot, action)
    RES-->>ENG: transition
    ENG->>ENG: update instance step/status
    ENG->>EVT: dispatch WorkflowAdvanced
    EVT->>ASN: handle event
    ASN->>ASN: create workflow_assignments
```

**File:** `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php:37-149`

---

## SD-03: Moodle — Observer ke Outbox

**Bukti:** `StudentObserver.php`, `MoodleOutboxService.php`

```mermaid
sequenceDiagram
    participant ADM as Admin Filament
    participant M as Student Model
    participant OBS as StudentObserver
    participant OUT as MoodleOutboxService
    participant Q as Queue
    participant JOB as ProcessMoodleSyncOutboxJob
    participant API as Moodle REST

    ADM->>M: save()
    M->>OBS: updated event
    OBS->>OUT: enqueue(tenant, user, upsert)
    OUT->>Q: dispatch job
    Q->>JOB: handle()
    JOB->>API: core_user_create_users
    API-->>JOB: response
```

---

## SD-04: Billing Midtrans Webhook

**Bukti:** `routes/web.php`, `BillingService`, `MidtransWebhookVerifier`

```mermaid
sequenceDiagram
    participant MT as Midtrans
    participant WEB as BillingController
    participant VER as MidtransWebhookVerifier
    participant SVC as BillingService
    participant T as Tenant Model

    MT->>WEB: POST /billing/webhook
    WEB->>VER: verify signature
    VER-->>WEB: valid
    WEB->>SVC: processNotification(payload)
    SVC->>T: update subscription status
    SVC-->>WEB: done
    WEB-->>MT: 200 OK
```

---

## SD-05: Resolve Tenant pada Request API

**Bukti:** `ResolveApiTenant.php`, `CurrentTenant.php`

```mermaid
sequenceDiagram
    participant C as API Client
    participant SAN as Sanctum
    participant R as ResolveApiTenant
    participant CT as CurrentTenant
    participant CTL as Controller

    C->>SAN: Bearer personal_access_token
    SAN-->>R: authenticated user + token
    R->>CT: set(token.tenant_id)
    CT-->>CTL: scoped queries
```

**File:** `app/Http/Middleware/ResolveApiTenant.php:23-31`

---

## SD-06: Notifikasi Workflow Assignee

**Bukti:** `EventServiceProvider.php`, `NotifyWorkflowAssignees.php`

```mermaid
sequenceDiagram
    participant EVT as WorkflowAssignmentCreated
    participant L as NotifyWorkflowAssignees
    participant N as NotificationDispatcher
    participant WA as WhatsAppProvider
    participant DB as notification_deliveries

    EVT->>L: handle(assignment)
    L->>N: dispatch channels
    N->>WA: sendWhatsApp (if configured)
    N->>DB: log delivery
```

---

## SD-07: Public Enrollment Inquiry

**Bukti:** `InquiryController`, `LeadInquiryService`

```mermaid
sequenceDiagram
    participant V as Visitor
    participant CTL as InquiryController
    participant SVC as LeadInquiryService
    participant DB as leads table

    V->>CTL: POST /api/inquiry
    CTL->>SVC: createFromInquiry(data)
    SVC->>DB: INSERT lead stage=new
    DB-->>SVC: lead
    SVC-->>CTL: lead
    CTL-->>V: 201 JSON
```

---

## Diagram TIDAK Dibuat

| Interaksi | Alasan |
|-----------|--------|
| Semua 257 CRUD Filament | Pola identik Livewire — redundant |
| Internal payroll calculation | **TIDAK TERDETEKSI** service engine terpusat |
| Full exam proctoring flow | **PARSIAL** — hanya webhook runtime |

---

## Catatan Ketidakpastian

- `SEQUENCE_DIAGRAMS.md` di root repo berisi SD-01–SD-13; beberapa referensi baris **Draft/stale** per `REAP_AUDIT.md`.
- Urutan listener concurrent: **INFERENSI** — Laravel synchronous default kecuali `ShouldQueue`.
