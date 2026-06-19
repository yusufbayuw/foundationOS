# Sequence Diagrams

Evidence-based sequence diagrams for verified FoundationOS flows. Every message maps to a concrete class, method, middleware, or route handler in the repository.

**Verification date:** 2026-06-19  
**Related:** `USE_CASES.md`, `EVENTS.md`

---

## Diagram List

| ID | Title | Category | Entry point |
|----|-------|----------|-------------|
| SD-01 | Filament admin session authentication | Login / auth | `GET /admin/login` |
| SD-02 | API bearer token authentication | Login / auth | `GET /api/v1/me` |
| SD-03 | API create payment (write transaction) | Create transaction | `POST /api/v1/payments` |
| SD-04 | API create applicant (write transaction) | Create transaction | `POST /api/v1/applicants` |
| SD-05 | Purchase requisition workflow approval | Approval | `ViewWorkflowInstance` advance action |
| SD-06 | Leave request manual approval | Approval | `ViewLeaveRequest` approve action |
| SD-07 | Applicant acceptance finalization pipeline | Posting / finalization | `Applicant` status → `accepted` |
| SD-08 | Midtrans subscription webhook finalization | Posting / finalization | `POST /billing/webhook` |
| SD-09 | Moodle course sync (outbound integration) | Integration call | `CourseObserver` → outbox |
| SD-10 | Tenant outbound webhook delivery | Integration call | `WebhookDispatcher::dispatch()` |
| SD-11 | Moodle outbox background job processing | Background job | `ProcessMoodleSyncOutboxJob` |
| SD-12 | PR-approved RFQ auto-creation (queued listener) | Background job | `PurchaseRequisitionApproved` |
| SD-13 | Leave request PDF report generation | Report generation | `GET …/leave-requests/{id}/pdf` |

---

## Actors and Lifelines

| Lifeline | Role | Source |
|----------|------|--------|
| **AdminUser** | Human operator | Filament admin panel |
| **ApiClient** | External/mobile integrator | Sanctum bearer token |
| **Midtrans** | Payment gateway | `POST /billing/webhook` |
| **Moodle** | LMS REST API | `MoodleClient::call()` |
| **Subscriber** | Tenant webhook endpoint | `DeliverWebhookJob` HTTP POST |
| **Browser** | HTTP client | All web routes |

| Lifeline | Layer | Primary files |
|----------|-------|---------------|
| **FilamentPanel** | UI / routing | `app/Providers/Filament/AdminPanelProvider.php` |
| **SessionGuard** | Auth | Laravel `web` guard (Filament `->login()`) |
| **UserModel** | Domain | `Modules/Core/app/Models/User.php` |
| **SanctumMiddleware** | Auth | `auth:sanctum` (`routes/api.php`) |
| **ResolveApiTenant** | Tenancy | `app/Http/Middleware/ResolveApiTenant.php` |
| **CurrentTenant** | Tenancy | `app/Support/CurrentTenant.php` |
| **IdempotencyKey** | API safety | `app/Http/Middleware/IdempotencyKey.php` |
| **WorkflowEngine** | Approval | `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` |
| **EventBus** | Events | Laravel `Event::dispatch` / listeners |
| **QueueWorker** | Async | Laravel queue (`ShouldQueue` jobs/listeners) |
| **DB** | Persistence | Eloquent models / `DB::transaction` |

---

## SD-01 — Filament Admin Session Authentication

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor AdminUser as Admin User
    participant Browser
    participant FilamentPanel as AdminPanelProvider<br/>/admin
    participant SessionGuard as Laravel web guard
    participant UserModel as User::canAccessPanel
    participant TenantMW as BindTenantToContainer
    participant SubMW as EnsureTenantSubscriptionActive

    AdminUser->>Browser: Open /admin
    Browser->>FilamentPanel: GET /admin
    FilamentPanel->>SessionGuard: Authenticate middleware (unauthenticated)
    SessionGuard-->>Browser: Redirect /admin/login

    AdminUser->>Browser: Submit email + password
    Browser->>FilamentPanel: POST /admin/login
    FilamentPanel->>SessionGuard: Attempt credentials
    alt Invalid credentials
        SessionGuard-->>Browser: Validation error (stay on login)
    else Valid credentials
        SessionGuard->>UserModel: canAccessPanel('admin')
        alt No tenant role and not super admin
            UserModel-->>Browser: 403 / denied
        else Access granted
            UserModel-->>FilamentPanel: true
            FilamentPanel-->>Browser: Session cookie + redirect /admin
            Browser->>FilamentPanel: GET /admin/{tenant}/...
            FilamentPanel->>TenantMW: Bind Filament tenant → CurrentTenant
            FilamentPanel->>SubMW: Check tenant.isLocked()
            alt Subscription locked
                SubMW-->>Browser: Redirect …/billing
            else Active
                SubMW-->>Browser: Render panel
            end
        end
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Panel login enabled | `->login()` | `AdminPanelProvider.php:47` |
| Auth middleware stack | `Authenticate::class` | `AdminPanelProvider.php:115-118` |
| Panel access gate | `User::canAccessPanel()` | `Modules/Core/app/Models/User.php:463-484` |
| Tenant bind | `CurrentTenant::set()` | `BindTenantToContainer.php:31` |
| Subscription lock | `Tenant::isLocked()` | `EnsureTenantSubscriptionActive.php:20-26` |

### Database Interaction

| Step | Table / model | Operation |
|------|---------------|-----------|
| Login | `users` | Read by email; session auth |
| Access check | `user_tenant_roles` | `userTenantRoles()->exists()` |
| Super admin | `users.is_super_admin` | Boolean bypass |
| Tenant context | `tenants` | Filament tenant switcher |

### External Call

None.

### Error Branches

| Branch | Trigger | Evidence |
|--------|---------|----------|
| Invalid credentials | Failed guard attempt | Filament login form |
| Panel denied | `canAccessPanel` false | `User.php:476` |
| Subscription locked | `tenant->isLocked()` | `EnsureTenantSubscriptionActive.php:20` |

---

## SD-02 — API Bearer Token Authentication

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor ApiClient as API Client
    participant Browser as HTTP
    participant Sanctum as auth:sanctum
    participant PAT as PersonalAccessToken
    participant Resolve as ResolveApiTenant
    participant CT as CurrentTenant
    participant AuthCtrl as AuthController::me

    Note over ApiClient,PAT: Token created via User::createToken()<br/>no /login route in routes/api.php

    ApiClient->>Browser: GET /api/v1/me<br/>Authorization: Bearer {token}
    Browser->>Sanctum: Authenticate token
    alt Token invalid / expired
        Sanctum-->>ApiClient: 401 Unauthenticated
    else Token valid
        Sanctum->>PAT: Load personal_access_tokens row
        Sanctum->>Resolve: handle()
        alt tenant_id null and api_require_tenant=true
            Resolve-->>ApiClient: 403 API token must be scoped to a tenant
        else tenant_id set
            Resolve->>CT: set(tenant_id)
            Resolve->>AuthCtrl: me()
            AuthCtrl-->>ApiClient: 200 UserResource JSON
        end
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Route middleware | `auth:sanctum`, `resolve.api.tenant` | `routes/api.php:44-46` |
| Token model | `PersonalAccessToken` (+ `tenant_id`) | `app/Models/PersonalAccessToken.php` |
| Tenant resolution | `ResolveApiTenant::handle()` | `ResolveApiTenant.php:15-34` |
| Profile | `AuthController::me()` | `AuthController.php:12-14` |

### Database Interaction

| Step | Table | Operation |
|------|-------|-----------|
| Auth | `personal_access_tokens` | Lookup by token hash |
| Tenant scope | `personal_access_tokens.tenant_id` | Read |
| User | `users` | Load authenticated user |

### External Call

None.

### Error Branches

| Branch | HTTP | Evidence |
|--------|------|----------|
| Unauthenticated | 401 | Sanctum |
| Token without tenant | 403 | `ResolveApiTenant.php:24-25` |
| No tenant on token endpoint | 404 `no_tenant` | `AuthController.php:21-22` (tenants/current only) |

---

## SD-03 — API Create Payment (Write Transaction)

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor ApiClient as API Client
    participant HTTP
    participant Sanctum as auth:sanctum
    participant Resolve as ResolveApiTenant
    participant Idem as IdempotencyKey
    participant Ctrl as PaymentController::store
    participant DB as payments table
    participant WH as WebhookDispatcher

    ApiClient->>HTTP: POST /api/v1/payments<br/>Idempotency-Key: {key}
    HTTP->>Sanctum: Authenticate
    Sanctum->>Resolve: Set tenant context
    HTTP->>Idem: Check cache replay
    alt Idempotency conflict (same key, different body)
        Idem-->>ApiClient: 409 idempotency_conflict
    else Replay hit
        Idem-->>ApiClient: Cached 201/4xx (X-Idempotency-Replayed)
    else New request
        Idem->>Ctrl: store()
        alt Validation fails
            Ctrl-->>ApiClient: 422 validation_failed
        else Valid
            Ctrl->>DB: Payment::create(tenant_id, status default pending)
            alt status === verified
                Ctrl->>WH: dispatch(tenant, payment.verified, payload)
            end
            Ctrl-->>ApiClient: 201 success JSON
            Idem->>Idem: Cache response 24h
        end
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Controller | `PaymentController::store()` | `PaymentController.php:19-62` |
| Idempotency | `IdempotencyKey::handle()` | `IdempotencyKey.php:14-52` |
| Outbound event | `WebhookDispatcher::dispatch()` | `WebhookDispatcher.php:16-37` |

### Database Interaction

| Step | Model | Operation |
|------|-------|-----------|
| Create | `Modules\Finance\Models\Payment` | INSERT with `tenant_id` |
| Webhook prep | `webhook_deliveries` | INSERT (via dispatcher) |

### External Call

Deferred to SD-10 (`DeliverWebhookJob`).

### Error Branches

| Branch | Response | Evidence |
|--------|----------|----------|
| Validation | 422 | `PaymentController.php:33-35` |
| Idempotency conflict | 409 | `IdempotencyKey.php:29-34` |
| Unauthenticated | 401 | Sanctum |

---

## SD-04 — API Create Applicant (Write Transaction)

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor ApiClient as API Client
    participant HTTP
    participant Sanctum as auth:sanctum
    participant Resolve as ResolveApiTenant
    participant Idem as IdempotencyKey
    participant Ctrl as ApplicantController::store
    participant DB as applicants table
    participant WH as WebhookDispatcher

    ApiClient->>HTTP: POST /api/v1/applicants
    HTTP->>Sanctum: Authenticate + tenant resolve
    HTTP->>Idem: Idempotency check
    Idem->>Ctrl: store()
    alt Validation fails
        Ctrl-->>ApiClient: 422
    else Valid
        Ctrl->>DB: Applicant::create(status registered)
        Ctrl->>WH: dispatch(enrollment.created)
        Ctrl-->>ApiClient: 201 {id, registration_number, ...}
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Controller | `ApplicantController::store()` | `ApplicantController.php:19-64` |
| Webhook | `enrollment.created` event | `ApplicantController.php:47-54` |

### Database Interaction

| Step | Model | Operation |
|------|-------|-----------|
| Create | `Modules\Enrollment\Models\Applicant` | INSERT |

### External Call

Outbound webhook via SD-10.

### Error Branches

| Branch | Response | Evidence |
|--------|----------|----------|
| Validation | 422 | `ApplicantController.php:35-37` |

---

## SD-05 — Purchase Requisition Workflow Approval

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor AdminUser as Assignee (Tenant Admin)
    participant UI as ViewWorkflowInstance
    participant Engine as DatabaseWorkflowEngine
    participant DB as DB::transaction
    participant EventBus as WorkflowAdvanced event
    participant Sync as SyncWorkflowSubjectState
    participant PR as PurchaseRequisition

    AdminUser->>UI: Click step action (e.g. approve)
    UI->>Engine: advance(instance, actionName, formData, actor)
    Engine->>DB: lockForUpdate workflow_instances
    Engine->>Engine: authorizeActor(pending assignment)
    alt Not authorized
        Engine-->>UI: WorkflowAuthorizationException
    else Evidence required but missing
        Engine-->>UI: WorkflowEvidenceRequiredException
    else Authorized
        Engine->>Engine: validate form_schema
        Engine->>DB: Update instance step/status, assignments, logs
        DB-->>Engine: commit
        Engine->>EventBus: WorkflowAdvanced::dispatch()
        EventBus->>Sync: handle()
        alt Instance status Completed
            Sync->>PR: status=approved, ready_for_sourcing=true
            Sync->>EventBus: PurchaseRequisitionApproved::dispatch()
        else Instance status Rejected
            Sync->>PR: status=rejected
        else In progress
            Sync->>PR: status=in_review
        end
        Engine-->>UI: Fresh WorkflowInstance
        UI-->>AdminUser: Success notification
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| UI action | `ViewWorkflowInstance` header action | `ViewWorkflowInstance.php:86-109` |
| Engine | `DatabaseWorkflowEngine::advance()` | `DatabaseWorkflowEngine.php:37-92` |
| Subject sync | `SyncWorkflowSubjectState::handle()` | `SyncWorkflowSubjectState.php:15-72` |
| PR approved event | `PurchaseRequisitionApproved::dispatch()` | `SyncWorkflowSubjectState.php:54` |

### Database Interaction

| Step | Tables | Operation |
|------|--------|-----------|
| Lock | `workflow_instances` | `lockForUpdate` |
| Advance | `workflow_assignments`, `workflow_instance_logs` | UPDATE/INSERT |
| Final approve | `purchase_requisitions` | `status`, `approved_by`, `approved_at` |

### External Call

None directly; downstream SD-12 (RFQ automation).

### Error Branches

| Branch | Exception / behavior | Evidence |
|--------|---------------------|----------|
| No pending assignment | Actions hidden | `ViewWorkflowInstance.php:47-48` |
| Unauthorized actor | `WorkflowAuthorizationException` | `DatabaseWorkflowEngine.php:48` |
| Missing evidence | `WorkflowEvidenceRequiredException` | `DatabaseWorkflowEngine.php:52-62` |

---

## SD-06 — Leave Request Manual Approval

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor AdminUser as Approver
    participant UI as ViewLeaveRequest
    participant LR as LeaveRequest model
    participant DB as leave_requests

    AdminUser->>UI: Submit for Approval (draft)
    UI->>LR: update(status pending)
    LR->>DB: UPDATE

    AdminUser->>UI: Supervisor Approve (pending)
    UI->>LR: update(supervisor_approved_at)
    LR->>DB: UPDATE

    AdminUser->>UI: Approve (pending)
    UI->>LR: update(status approved, approver_id, approved_at)
    LR->>DB: UPDATE
    UI-->>AdminUser: Notification success

    opt Reject path
        AdminUser->>UI: Reject + reason
        UI->>LR: update(status rejected, rejection_reason)
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Submit | `ViewLeaveRequest` submitForApproval | `ViewLeaveRequest.php:31-41` |
| Supervisor | supervisorApprove action | `ViewLeaveRequest.php:43-55` |
| Approve | approve action | `ViewLeaveRequest.php:57-71` |
| Reject | reject action | `ViewLeaveRequest.php:73-91` |

### Database Interaction

| Field | Transition |
|-------|------------|
| `status` | `draft` → `pending` → `approved` / `rejected` |
| `supervisor_approved_at` | Set on supervisor step |
| `approver_id`, `approved_at` | Set on final approve |

### External Call

None.

### Error Branches

| Branch | Behavior | Evidence |
|--------|----------|----------|
| Wrong status | Action not visible | `->visible(fn (): bool => $record->status === …)` |

---

## SD-07 — Applicant Acceptance Finalization Pipeline

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor AdminUser as Tenant Admin
    participant UI as Applicant Filament edit
    participant Model as Applicant model
    participant Bus as Event ApplicantAccepted
    participant Q as Queue worker
    participant L1 as CreateStudentFromAcceptedApplicant
    participant L2 as CreateInitialInvoiceFromAcceptedApplicant
    participant L3 as CreateLibraryMemberFromAcceptedApplicant
    participant Promo as ApplicantPromotionService
    participant Inv as ApplicantOnboardingInvoiceService
    participant DB as DB

    AdminUser->>UI: Set status = accepted
    UI->>Model: save()
    Model->>Model: wasChanged(status) && status===accepted
    Model->>Bus: ApplicantAccepted::dispatch(applicant, actor)

  par Queued listeners (ShouldQueue)
        Bus->>Q: CreateStudentFromAcceptedApplicant
        Q->>L1: handle()
        L1->>Promo: promote(applicant)
        Promo->>DB: transaction: Student::create + applicant.converted_to_student_id

        Bus->>Q: CreateInitialInvoiceFromAcceptedApplicant
        Q->>L2: handle()
        L2->>Inv: createDraftFor(applicant)

        Bus->>Q: CreateLibraryMemberFromAcceptedApplicant
        Q->>L3: handle()
        L3->>DB: library member (if setting enabled)
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Trigger | `Applicant::updated` booted callback | `Applicant.php:68-78` |
| Student | `ApplicantPromotionService::promote()` | `ApplicantPromotionService.php:26-70` |
| Invoice listener | `CreateInitialInvoiceFromAcceptedApplicant` | `CreateInitialInvoiceFromAcceptedApplicant.php:21-28` |
| Student listener | `CreateStudentFromAcceptedApplicant` | `CreateStudentFromAcceptedApplicant.php:21-28` |

### Database Interaction

| Step | Models | Operation |
|------|--------|-----------|
| Accept | `applicants` | UPDATE `status` |
| Promote | `students`, `applicants.converted_to_student_id` | INSERT + UPDATE |
| Invoice | `student_invoices` | INSERT draft (service-dependent) |
| Library | `library_members` | INSERT (setting-gated) |

### External Call

None.

### Error Branches

| Branch | Behavior | Evidence |
|--------|----------|----------|
| Auto-promote disabled | `promote()` returns null | `ApplicantPromotionService.php:28-30` |
| Already converted | Idempotent return existing student | `ApplicantPromotionService.php:34-36` |
| Setting off per listener | Listener skips via setting / AutomationRunLogger | `EVENTS.md`, listener services |

---

## SD-08 — Midtrans Subscription Webhook Finalization

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor Midtrans
    participant HTTP
    participant Ctrl as BillingController::webhook
    participant Svc as BillingService
    participant Ver as MidtransWebhookVerifier
    participant DB as DB::transaction
    participant SubLog as subscription_logs
    participant Tenant as tenants

    Midtrans->>HTTP: POST /billing/webhook (notification JSON)
    HTTP->>Ctrl: webhook()
    Ctrl->>Svc: handleWebhookNotification(payload)
    Svc->>Ver: verifySignature(notification)
    alt Invalid signature
        Ver-->>Ctrl: MidtransWebhookException
        Ctrl-->>Midtrans: 400 Invalid webhook notification
    else Valid
        Svc->>DB: lock subscription_logs by invoice_number
        alt Invoice not found
            DB-->>Svc: return (no-op)
        else Duplicate webhook key
            Svc-->>Ctrl: return (idempotent skip)
        else Amount/currency mismatch
            Svc->>SubLog: metadata midtrans_validation_failure
        else Status mapped
            Svc->>SubLog: update payment_status
            alt Newly paid
                Svc->>Tenant: activateTenantSubscription(status active, expires_at)
            end
        end
        Ctrl-->>Midtrans: 200 {status: ok}
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Controller | `BillingController::webhook()` | `BillingController.php:16-36` |
| Handler | `BillingService::handleWebhookNotification()` | `BillingService.php:117-195` |
| Activate | `activateTenantSubscription()` | `BillingService.php:197-205` |

### Database Interaction

| Table | Operation |
|-------|-----------|
| `subscription_logs` | `lockForUpdate`, UPDATE `payment_status`, metadata |
| `tenants` | UPDATE `status`, `subscribed_at`, `subscription_expires_at` on first paid |

### External Call

| Direction | Target | Evidence |
|-----------|--------|----------|
| Inbound | Midtrans notification POST | `routes/web.php:12` |
| Outbound (user flow) | Midtrans Snap token | `BillingService::createSnapPayment()` (SD related, `BillingPage.php:75-98`) |

### Error Branches

| Branch | HTTP | Evidence |
|--------|------|----------|
| Invalid signature | 400 | `BillingController.php:20-26` |
| Processing error | 500 | `BillingController.php:27-33` |
| Amount mismatch | No status change; metadata flag | `BillingService.php:143-150` |
| Duplicate webhook | Early return | `BillingService.php:139-141` |

---

## SD-09 — Moodle Course Sync (Outbound Integration)

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor AdminUser as Tenant Admin
    participant UI as Course Filament save
    participant Obs as CourseObserver
    participant Outbox as MoodleOutboxService
    participant DB as moodle_sync_outbox
    participant Job as ProcessMoodleSyncOutboxJob
    participant Sync as MoodleSyncService
    participant Client as MoodleClient
    participant Moodle as Moodle REST API

    AdminUser->>UI: Create/update Course
    UI->>Obs: created/updated(Course)
    Obs->>Outbox: enqueue(ENTITY_COURSE, action, dedupe_key)
    alt moodle.enabled false
        Outbox-->>Obs: null (skip)
    else Enabled
        Outbox->>DB: INSERT outbox status=pending
        Outbox->>Job: dispatch(outboxId)->afterCommit()
    end

    Note over Job,Moodle: Async (SD-11 details)

    Job->>Sync: syncOutboxItem(outbox)
    Sync->>Sync: syncCourseOutbox()
    Sync->>Client: call(wsfunction, params)
    Client->>Moodle: POST /webservice/rest/server.php
    Moodle-->>Client: JSON response
    Client-->>Sync: parsed array
    Sync->>DB: upsert moodle_entity_mappings
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Observer | `CourseObserver::enqueue()` | `CourseObserver.php:55-76` |
| Outbox | `MoodleOutboxService::enqueue()` | `MoodleOutboxService.php:35-72` |
| Sync | `MoodleSyncService::syncOutboxItem()` | `MoodleSyncService.php:33-44` |
| HTTP | `MoodleClient::call()` | `MoodleClient.php:12-60` |

### Database Interaction

| Table | Operation |
|-------|-----------|
| `courses` | Source entity (Campus module) |
| `moodle_sync_outbox` | INSERT pending row |
| `moodle_entity_mappings` | UPSERT after successful sync |

### External Call

| Target | Protocol | Evidence |
|--------|----------|----------|
| Moodle | REST `server.php` form POST | `MoodleClient.php:25-38` |

### Error Branches

| Branch | Behavior | Evidence |
|--------|----------|----------|
| Moodle disabled | No outbox row | `MoodleOutboxService.php:43-45` |
| Duplicate dedupe_key | Swallow `QueryException` 23000 | `MoodleOutboxService.php:65-67` |
| Sync failure | Job retry / terminal failed | `ProcessMoodleSyncOutboxJob.php:55-69` |

---

## SD-10 — Tenant Outbound Webhook Delivery

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    participant Orig as PaymentController /<br/>ApplicantController
    participant Disp as WebhookDispatcher
    participant DB as webhook_deliveries
    participant Job as DeliverWebhookJob
    participant HTTP as Illuminate Http client
    participant Sub as Subscriber URL

    Orig->>Disp: dispatch(tenantId, event, payload)
    Disp->>DB: Load active WebhookSubscription for event
    loop Each matching subscription
        Disp->>DB: WebhookDelivery::create(status pending)
        Disp->>Job: dispatch(deliveryId)
    end

    Job->>DB: Load delivery + subscription
    alt Already delivered or inactive subscription
        Job-->>Job: return
    else Deliver
        Job->>Job: HMAC sha256 signature
        Job->>HTTP: POST url with X-Hub-Signature-256
        HTTP->>Sub: JSON payload
        Sub-->>HTTP: response
        alt HTTP successful
            Job->>DB: status=delivered
        else Failed
            Job->>DB: status=failed, maybeRelease backoff
        end
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Dispatch | `WebhookDispatcher::dispatch()` | `WebhookDispatcher.php:16-37` |
| Deliver | `DeliverWebhookJob::handle()` | `DeliverWebhookJob.php:23-71` |
| Backoff | `backoff()` 1m→8h | `DeliverWebhookJob.php:74-77` |

### Database Interaction

| Table | Operation |
|-------|-----------|
| `webhook_subscriptions` | READ active, event filter |
| `webhook_deliveries` | INSERT; UPDATE status, attempt_count |

### External Call

| Target | Headers | Evidence |
|--------|---------|----------|
| Tenant-configured URL | `X-Hub-Signature-256`, `X-Webhook-Event`, `X-Delivery-Id` | `DeliverWebhookJob.php:45-52` |

### Error Branches

| Branch | Behavior | Evidence |
|--------|----------|----------|
| Inactive subscription | `failed` + message | `DeliverWebhookJob.php:33-36` |
| HTTP non-2xx | `failed` + retry release | `DeliverWebhookJob.php:54-63` |
| Exception | `failed` + error_message | `DeliverWebhookJob.php:64-70` |

---

## SD-11 — Moodle Outbox Background Job Processing

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    participant Queue as Queue worker
    participant Job as ProcessMoodleSyncOutboxJob
    participant Outbox as MoodleSyncOutbox
    participant Sync as MoodleSyncService
    participant Moodle as Moodle API

    Queue->>Job: handle(outboxId)
    alt config moodle.enabled false
        Job-->>Queue: return
    else
        Job->>Outbox: tryClaim(outboxId)
        alt Already claimed / not pending
            Outbox-->>Job: null → return
        else Claimed
            Job->>Sync: syncOutboxItem(outbox)
            Sync->>Moodle: REST call(s)
            alt Success
                Job->>Outbox: status=SYNCED, synced_at=now
            else MoodleReadonlySkipException
                Job->>Outbox: status=SKIPPED
            else Throwable
                Job->>Outbox: increment attempts, schedule retry or FAILED
            end
        end
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Job | `ProcessMoodleSyncOutboxJob::handle()` | `ProcessMoodleSyncOutboxJob.php:27-71` |
| Claim | `MoodleSyncOutbox::tryClaim()` | Referenced in job line 33 |
| Sync | `MoodleSyncService::syncOutboxItem()` | `MoodleSyncService.php:33-44` |

### Database Interaction

| Table | Operation |
|-------|-----------|
| `moodle_sync_outbox` | Atomic claim; UPDATE status, attempts, `next_retry_at`, `last_error` |

### External Call

Moodle REST via `MoodleClient` (see SD-09).

### Error Branches

| Branch | Outbox status | Evidence |
|--------|---------------|----------|
| Readonly mode | `SKIPPED` | `ProcessMoodleSyncOutboxJob.php:48-54` |
| Retries exhausted | `FAILED` + report | `ProcessMoodleSyncOutboxJob.php:58-68` |
| Transient error | `PENDING` + `next_retry_at` | `ProcessMoodleSyncOutboxJob.php:60-65` |

---

## SD-12 — PR-Approved RFQ Auto-Creation (Queued Listener)

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    participant Sync as SyncWorkflowSubjectState
    participant Bus as PurchaseRequisitionApproved
    participant Q as Queue
    participant L as CreateRfqFromApprovedPurchaseRequisition
    participant Svc as RfqAutoCreationService
    participant DB as request_for_quotations
    participant Notify as Filament Notification

    Sync->>Bus: dispatch(PR, approver)
    Bus->>Q: enqueue listener
    Q->>L: handle(event)
    L->>Svc: createDraftFor(requisition)
    alt RFQ not created (setting/off/idempotent)
        Svc-->>L: null → return
    else Created
        Svc->>DB: INSERT draft RFQ + items
        L->>Notify: sendToDatabase(approver/requester)
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| Event | `PurchaseRequisitionApproved` | `SyncWorkflowSubjectState.php:54` |
| Listener | `CreateRfqFromApprovedPurchaseRequisition::handle()` | `CreateRfqFromApprovedPurchaseRequisition.php:17-31` |
| Service | `RfqAutoCreationService::createDraftFor()` | Referenced line 25 |

### Database Interaction

| Model | Operation |
|-------|-----------|
| `PurchaseRequisition` | Source (approved) |
| `RequestForQuotation` | INSERT draft |

### External Call

None.

### Error Branches

| Branch | Behavior | Evidence |
|--------|----------|----------|
| No requisition | Early return | `CreateRfqFromApprovedPurchaseRequisition.php:21-23` |
| RFQ skipped | `createDraftFor` null | `CreateRfqFromApprovedPurchaseRequisition.php:27-29` |
| Notification failure | Swallowed | `CreateRfqFromApprovedPurchaseRequisition.php:46-48` |

---

## SD-13 — Leave Request PDF Report Generation

### Step-by-step Message Flow

```mermaid
sequenceDiagram
    autonumber
    actor AdminUser as Authenticated User
    participant Browser
    participant Route as employee.leave-requests.pdf
    participant Ctrl as LeaveRequestPdfController
    participant Trait as RendersTenantPdf
    participant Policy as authorize(print)
    participant Svc as LeaveRequestDocumentService
    participant Pdf as PdfDocumentRenderer
    participant DomPDF as Barryvdh DomPDF

    AdminUser->>Browser: Click Download PDF (ViewLeaveRequest)
    Browser->>Route: GET /employee/leave-requests/{id}/pdf
    Route->>Ctrl: __invoke(leaveRequest)
    Ctrl->>Trait: downloadTenantPdf(record, view, data, filename)
    Trait->>Policy: authorizePrint → authorize('print', record)
    alt Not printable status
        Trait-->>Browser: AuthorizationException
    else Authorized
        Trait->>Svc: assemble(leaveRequest)
        Trait->>Pdf: download(view, data, TenantDocumentContext)
        Pdf->>DomPDF: loadView + setPaper
        DomPDF-->>Browser: application/pdf attachment
    end
```

### Service Calls

| Step | Call | File |
|------|------|------|
| UI link | `ViewLeaveRequest` downloadPdf | `ViewLeaveRequest.php:24-30` |
| Controller | `LeaveRequestPdfController::__invoke()` | `LeaveRequestPdfController.php:14-22` |
| PDF trait | `RendersTenantPdf::downloadTenantPdf()` | `RendersTenantPdf.php:22-48` |
| Assembler | `LeaveRequestDocumentService::assemble()` | Referenced in controller |
| Renderer | `PdfDocumentRenderer::download()` | `PdfDocumentRenderer.php:14-24` |

### Database Interaction

| Step | Operation |
|------|-----------|
| Route model binding | READ `leave_requests` |
| Tenant context | READ `tenants` (+ optional `organizations`) |

### External Call

None (local DomPDF render).

### Error Branches

| Branch | Exception | Evidence |
|--------|-----------|----------|
| Policy denied | AuthorizationException | `RendersTenantPdf.php:32` |
| Not printable | AuthorizationException | `RendersTenantPdf.php:34-36` |
| Action hidden in UI | `isPrintable()` false | `ViewLeaveRequest.php:28` |

---

## Cross-Diagram Service Index

| Service / component | Diagrams | File |
|-------------------|----------|------|
| `BillingService` | SD-08 | `app/Services/BillingService.php` |
| `WebhookDispatcher` | SD-03, SD-04, SD-10 | `app/Services/WebhookDispatcher.php` |
| `DatabaseWorkflowEngine` | SD-05 | `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` |
| `SyncWorkflowSubjectState` | SD-05, SD-12 | `Modules/Workflow/app/Listeners/SyncWorkflowSubjectState.php` |
| `ApplicantPromotionService` | SD-07 | `Modules/Enrollment/app/Services/ApplicantPromotionService.php` |
| `MoodleOutboxService` | SD-09, SD-11 | `app/Integrations/Moodle/MoodleOutboxService.php` |
| `MoodleSyncService` | SD-09, SD-11 | `app/Integrations/Moodle/MoodleSyncService.php` |
| `PdfDocumentRenderer` | SD-13 | `Modules/Core/app/Support/Pdf/PdfDocumentRenderer.php` |

---

## Coverage Notes

| Requested category | Diagram(s) | Gap |
|--------------------|------------|-----|
| Login / auth | SD-01, SD-02 | No `POST /api/login` route — API uses pre-issued Sanctum PAT |
| Create transaction | SD-03, SD-04 | Billing invoice creation (`BillingPage::generateInvoice`) follows same BillingService path as SD-08 |
| Approval | SD-05, SD-06 | Budget workflow mirrors SD-05 via `SyncBudgetWorkflowState` |
| Posting / finalization | SD-07, SD-08 | Finance payment verify in admin may use separate `FinanceControlService` (not expanded here) |
| Integration call | SD-09, SD-10 | Exam runtime ingest (`ExamRuntimeWebhookController`) is inbound-only, no outbound |
| Background job | SD-11, SD-12 | Other `ShouldQueue` listeners follow same Event → Queue pattern |
| Report generation | SD-13 | 20+ module `*PdfController` classes share `RendersTenantPdf` pattern |
