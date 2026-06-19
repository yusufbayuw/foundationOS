# Use Case Model

Evidence-based catalog of actors and use cases in FoundationOS. Only flows with identifiable routes, controllers, Filament pages/actions, or model/event handlers are listed. **257** Filament `ModuleResource` subclasses provide standard CRUD as a single meta-use-case rather than 257 separate specifications.

**Verification date:** 2026-06-19  
**Related catalogs:** `EVENTS.md`, `storage/app/entity-catalog.json`

---

## Actor List

| ID | Actor | Type | Access evidence |
|----|-------|------|-----------------|
| A1 | **Tenant Admin User** | Human | `User::canAccessPanel('admin')` requires `user_tenant_roles` membership (`Modules/Core/app/Models/User.php:471-476`) |
| A2 | **Global Super Admin** | Human | `User::isGlobalSuperAdmin()` bypasses tenant membership for admin panel (`User.php:472-473`) |
| A3 | **Platform Owner** | Human | Spatie role `platform_owner` (team `tenant_id = 0`); `canAccessPanel('platform')` (`User.php:465-468`, migration `2026_05_22_145853_create_platform_owner_role.php`) |
| A4 | **Parent User** | Human | `canAccessPanel('parent')` when `parent_students.parent_user_id` link exists (`User.php:479-481`, `ParentPanelProvider.php`) |
| A5 | **API Client** | System (token) | Sanctum bearer token + `resolve.api.tenant` middleware on `/api/v1` and `/api/v2` (`routes/api.php`) |
| A6 | **Public Visitor** | Human / anonymous | Unauthenticated routes (enrollment inquiry, CMS, letter verify, OPAC browse) |
| A7 | **Authenticated Web User** | Human | `auth` + `verified` middleware on OPAC circulation, locale switch, donation PDF (`Modules/Library/routes/web.php`, `routes/web.php`) |
| A8 | **Midtrans** | External system | `POST /billing/webhook` (`routes/web.php`, `BillingController`) |
| A9 | **Donation Payment Provider** | External system | `POST /donation/webhook` (`Modules/Donation/routes/web.php`) |
| A10 | **Exam Runtime** | External system | `POST /api/exam/runtime/attempts` with Sanctum (`Modules/Exam/routes/api.php`) |
| A11 | **WhatsApp Provider** | External system | `POST /api/webhooks/whatsapp/{provider}` (`routes/api.php`, `WhatsAppWebhookController`) |
| A12 | **IT Monitoring Sender** | External system | `POST /itops/monitoring/webhook` (`Modules/ItOps/routes/web.php`) |

### Notes on actors not listed

| Excluded | Reason |
|----------|--------|
| Generic **Student** portal user | No dedicated Filament panel or student-login routes; mobile data via API Client (`StudentDashboardController`) |
| **Moodle** | Outbound sync only (`MoodleOutboxService`, observers); no inbound webhook actor in repo |
| **Workflow Assignee** | Same human login as A1/A2; distinguished only by pending `workflow_assignments` on `ViewWorkflowInstance` |

---

## Use Case List

| ID | Use case | Primary actors | Boundary |
|----|----------|----------------|----------|
| UC-ADM-000 | Manage domain records via Filament CRUD | A1, A2 | Admin `/admin` |
| UC-ADM-001 | Register new tenant (organization) | A1 | Admin `/admin` |
| UC-ADM-002 | Manage SaaS billing and pay via Midtrans Snap | A1, A2 | Admin `/admin` |
| UC-ADM-003 | Switch UI locale | A1, A2, A4, A7 | Web (authenticated) |
| UC-ADM-004 | Start purchase requisition approval workflow | A1, A2 | Admin `/admin` |
| UC-ADM-005 | Start budget approval workflow | A1, A2 | Admin `/admin` |
| UC-ADM-006 | Process workflow instance (advance / return / cancel) | A1, A2 | Admin `/admin` |
| UC-ADM-007 | Manage leave request approval lifecycle | A1, A2 | Admin `/admin` |
| UC-ADM-008 | Accept applicant (trigger acceptance pipeline) | A1, A2 | Admin `/admin` |
| UC-PLT-001 | Manage SaaS tenants | A3 | Platform `/platform` |
| UC-PLT-002 | Manage platform users | A3 | Platform `/platform` |
| UC-PAR-001 | View linked children's school data | A4 | Parent `/parent` |
| UC-PAR-002 | Submit parent satisfaction survey | A4 | Parent `/parent` |
| UC-API-001 | Read authenticated user and tenant context | A5 | API `/api/v1`, `/api/v2` |
| UC-API-002 | Read core master data (organizations, students, …) | A5 | API |
| UC-API-003 | Create applicant | A5 | API |
| UC-API-004 | Record payment | A5 | API |
| UC-API-005 | Create leave request (draft) | A5 | API |
| UC-API-006 | Register / deactivate mobile push device | A5 | API |
| UC-API-007 | View student dashboard aggregates | A5 | API |
| UC-PUB-001 | Submit enrollment lead inquiry | A6 | Public API |
| UC-PUB-002 | Browse library OPAC catalog | A6 | Public web |
| UC-PUB-003 | Reserve library book | A7 | Public web (auth) |
| UC-PUB-004 | Perform library circulation (checkout / return / extend / issue) | A7 | Public web (auth) |
| UC-PUB-005 | View CMS public page or sitemap | A6 | Public web |
| UC-PUB-006 | Submit CMS contact form | A6 | Public web |
| UC-PUB-007 | Verify official letter by token | A6 | Public API |
| UC-EXT-001 | Process Midtrans subscription webhook | A8 | Webhook |
| UC-EXT-002 | Process donation payment webhook | A9 | Webhook |
| UC-EXT-003 | Ingest exam runtime attempt result | A10 | Webhook API |
| UC-EXT-004 | Receive WhatsApp provider webhook | A11 | Webhook API |
| UC-EXT-005 | Receive IT monitoring alert webhook | A12 | Webhook |

---

## Use Case Diagrams

### Admin panel

```mermaid
usecaseDiagram
    actor "Tenant Admin User" as TA
    actor "Global Super Admin" as SA

    package "Admin Panel (/admin)" {
        usecase "UC-ADM-000\nManage Filament CRUD" as CRUD
        usecase "UC-ADM-001\nRegister Tenant" as REG
        usecase "UC-ADM-002\nBilling / Midtrans" as BILL
        usecase "UC-ADM-004\nStart PR Workflow" as PRW
        usecase "UC-ADM-005\nStart Budget Workflow" as BDW
        usecase "UC-ADM-006\nProcess Workflow" as WFP
        usecase "UC-ADM-007\nLeave Request Lifecycle" as LEAVE
        usecase "UC-ADM-008\nAccept Applicant" as ACC
    }

    TA --> CRUD
    TA --> REG
    TA --> BILL
    TA --> PRW
    TA --> BDW
    TA --> WFP
    TA --> LEAVE
    TA --> ACC
    SA --> CRUD
    SA --> BILL
    SA --> WFP
```

### Platform and parent panels

```mermaid
usecaseDiagram
    actor "Platform Owner" as PO
    actor "Parent User" as PU

    package "Platform (/platform)" {
        usecase "UC-PLT-001\nManage Tenants" as MT
        usecase "UC-PLT-002\nManage Users" as MU
    }

    package "Parent (/parent)" {
        usecase "UC-PAR-001\nView Children Data" as CHILD
        usecase "UC-PAR-002\nParent Survey" as SURV
    }

    PO --> MT
    PO --> MU
    PU --> CHILD
    PU --> SURV
```

### REST API

```mermaid
usecaseDiagram
    actor "API Client" as API

    package "API v1 / v2" {
        usecase "UC-API-001\nMe / Current Tenant" as ME
        usecase "UC-API-002\nRead Master Data" as READ
        usecase "UC-API-003\nCreate Applicant" as CAPP
        usecase "UC-API-004\nRecord Payment" as CPAY
        usecase "UC-API-005\nCreate Leave Request" as CLV
        usecase "UC-API-006\nDevice Registration" as DEV
        usecase "UC-API-007\nStudent Dashboard" as DASH
    }

    API --> ME
    API --> READ
    API --> CAPP
    API --> CPAY
    API --> CLV
    API --> DEV
    API --> DASH
```

### Public web and external systems

```mermaid
usecaseDiagram
    actor "Public Visitor" as PV
    actor "Authenticated Web User" as AW
    actor "Midtrans" as MID
    actor "Donation Provider" as DON
    actor "Exam Runtime" as EXM
    actor "WhatsApp Provider" as WA
    actor "IT Monitoring" as IT

    package "Public" {
        usecase "UC-PUB-001\nEnrollment Inquiry" as INQ
        usecase "UC-PUB-002\nOPAC Browse" as OPAC
        usecase "UC-PUB-003\nReserve Book" as RSV
        usecase "UC-PUB-004\nCirculation" as CIRC
        usecase "UC-PUB-005\nCMS Page" as CMS
        usecase "UC-PUB-006\nCMS Contact" as CNT
        usecase "UC-PUB-007\nVerify Letter" as LTR
        usecase "UC-ADM-003\nSwitch Locale" as LOC
    }

    package "Webhooks" {
        usecase "UC-EXT-001\nBilling Webhook" as WH1
        usecase "UC-EXT-002\nDonation Webhook" as WH2
        usecase "UC-EXT-003\nExam Attempt" as WH3
        usecase "UC-EXT-004\nWhatsApp Webhook" as WH4
        usecase "UC-EXT-005\nMonitoring Webhook" as WH5
    }

    PV --> INQ
    PV --> OPAC
    PV --> CMS
    PV --> CNT
    PV --> LTR
    AW --> RSV
    AW --> CIRC
    AW --> LOC
    MID --> WH1
    DON --> WH2
    EXM --> WH3
    WA --> WH4
    IT --> WH5
```

---

## Actor–Use Case Matrix

| Use case | A1 | A2 | A3 | A4 | A5 | A6 | A7 | A8 | A9 | A10 | A11 | A12 |
|----------|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:---:|:---:|:---:|
| UC-ADM-000 | ✓ | ✓ | | | | | | | | | | |
| UC-ADM-001 | ✓ | | | | | | | | | | | |
| UC-ADM-002 | ✓ | ✓ | | | | | | | | | | |
| UC-ADM-003 | ✓ | ✓ | | ✓ | | | ✓ | | | | | |
| UC-ADM-004 | ✓ | ✓ | | | | | | | | | | |
| UC-ADM-005 | ✓ | ✓ | | | | | | | | | | |
| UC-ADM-006 | ✓ | ✓ | | | | | | | | | | |
| UC-ADM-007 | ✓ | ✓ | | | | | | | | | | |
| UC-ADM-008 | ✓ | ✓ | | | | | | | | | | |
| UC-PLT-001 | | | ✓ | | | | | | | | | |
| UC-PLT-002 | | | ✓ | | | | | | | | | |
| UC-PAR-001 | | | | ✓ | | | | | | | | |
| UC-PAR-002 | | | | ✓ | | | | | | | | |
| UC-API-001 | | | | | ✓ | | | | | | | |
| UC-API-002 | | | | | ✓ | | | | | | | |
| UC-API-003 | | | | | ✓ | | | | | | | |
| UC-API-004 | | | | | ✓ | | | | | | | |
| UC-API-005 | | | | | ✓ | | | | | | | |
| UC-API-006 | | | | | ✓ | | | | | | | |
| UC-API-007 | | | | | ✓ | | | | | | | |
| UC-PUB-001 | | | | | | ✓ | | | | | | |
| UC-PUB-002 | | | | | | ✓ | | | | | | |
| UC-PUB-003 | | | | | | | ✓ | | | | | |
| UC-PUB-004 | | | | | | | ✓ | | | | | |
| UC-PUB-005 | | | | | | ✓ | | | | | | |
| UC-PUB-006 | | | | | | ✓ | | | | | | |
| UC-PUB-007 | | | | | | ✓ | | | | | | |
| UC-EXT-001 | | | | | | | | ✓ | | | | |
| UC-EXT-002 | | | | | | | | | ✓ | | | |
| UC-EXT-003 | | | | | | | | | | ✓ | | |
| UC-EXT-004 | | | | | | | | | | | ✓ | |
| UC-EXT-005 | | | | | | | | | | | | ✓ |

---

## Use Case Specifications

### UC-ADM-000 — Manage domain records via Filament CRUD

| Field | Detail |
|-------|--------|
| **Actors** | Tenant Admin User (A1), Global Super Admin (A2) |
| **Evidence** | `Modules/Core/app/Filament/Support/ModuleResource.php`; 257 resources extending `ModuleResource`; `AdminPanelProvider` + `ModulesPlugin`; Filament Shield permissions |
| **Preconditions** | User authenticated to admin panel; tenant selected (except global super admin); Spatie permission for target resource |
| **Main flow** | 1. User navigates to module resource list. 2. User creates, views, edits, or deletes record (or imports CSV via `ImportTableActions`). 3. `ModuleResource` enforces tenant scoping and global-mutation guard. |
| **Alternative flows** | A1: Super admin mutates global reference resources blocked for non-super-admins (`GlobalResourceGuard`). A2: Soft-deleted records visible (`withoutGlobalScopes(SoftDeletingScope)`). |
| **Postconditions** | Record persisted with `tenant_id` where applicable; audit/activity per model configuration |
| **Exceptions** | Authorization denied by Shield policy; subscription inactive (`EnsureTenantSubscriptionActive` middleware) |

---

### UC-ADM-001 — Register new tenant (organization)

| Field | Detail |
|-------|--------|
| **Actors** | Tenant Admin User (A1) |
| **Evidence** | `app/Filament/Pages/Tenancy/RegisterTenant.php`; `AdminPanelProvider::tenantRegistration()` |
| **Preconditions** | User authenticated; user may register tenant per Filament tenancy rules |
| **Main flow** | 1. User opens tenant registration form. 2. User submits name, code, timezone, locale, currency. 3. `handleRegistration()` creates `Tenant`, provisions admin role and modules via `TenantAdminProvisioner` / `TenantModuleProvisioner`. |
| **Alternative flows** | Code auto-slugged from name on blur |
| **Postconditions** | New tenant exists; registering user linked as tenant admin |
| **Exceptions** | Duplicate `code` (unique validation); provisioning failure |

---

### UC-ADM-002 — Manage SaaS billing and pay via Midtrans Snap

| Field | Detail |
|-------|--------|
| **Actors** | Tenant Admin User (A1), Global Super Admin (A2) |
| **Evidence** | `app/Filament/Pages/BillingPage.php`; `app/Services/BillingService.php`; `resources/views/billing/page.blade.php` |
| **Preconditions** | User in admin panel with tenant context |
| **Main flow** | 1. User opens Billing page; monthly amount calculated. 2. User generates monthly invoice (`generateInvoice`) if not already billed. 3. User pays pending invoice (`payInvoice` → `createSnapPayment` → Midtrans Snap UI). 4. Midtrans notifies via UC-EXT-001. |
| **Alternative flows** | Invoice already exists for current month → warning notification |
| **Postconditions** | `SubscriptionLog` records invoice/payment status |
| **Exceptions** | Payment error surfaced as Filament notification |

---

### UC-ADM-003 — Switch UI locale

| Field | Detail |
|-------|--------|
| **Actors** | Authenticated users (A1, A2, A4, A7) |
| **Evidence** | `routes/web.php` → `LocaleSwitchController`; `SetUserLocale` middleware on panels |
| **Preconditions** | User authenticated |
| **Main flow** | 1. User requests `GET /locale/{id\|en}`. 2. Controller stores preference on `users.preferred_locale`. 3. Subsequent requests use locale via middleware. |
| **Postconditions** | UI language preference persisted |
| **Exceptions** | Invalid locale rejected by route constraint |

---

### UC-ADM-004 — Start purchase requisition approval workflow

| Field | Detail |
|-------|--------|
| **Actors** | Tenant Admin User (A1), Global Super Admin (A2) |
| **Evidence** | `Modules/Procurement/app/Filament/Resources/PurchaseRequisitions/Pages/ViewPurchaseRequisition.php` (`startWorkflow` action) |
| **Preconditions** | PR record exists; no running workflow instance for subject |
| **Main flow** | 1. User opens PR view. 2. User clicks Start Approval Workflow. 3. `WorkflowResolver::resolveForSubject()` picks workflow. 4. `WorkflowInstanceStarter::start()` creates running instance. |
| **Alternative flows** | Open Active Workflow when instance already running |
| **Postconditions** | `WorkflowInstance` status `running`; assignments created per definition |
| **Exceptions** | Resolver/start failure → error notification |

---

### UC-ADM-005 — Start budget approval workflow

| Field | Detail |
|-------|--------|
| **Actors** | Tenant Admin User (A1), Global Super Admin (A2) |
| **Evidence** | `Modules/Finance/app/Filament/Resources/Budgets/Pages/ViewBudget.php` (`startWorkflow` action) |
| **Preconditions** | Budget status `draft` or `revision_required`; no running workflow for subject |
| **Main flow** | Same pattern as UC-ADM-004 with `Budget` subject and organization context |
| **Postconditions** | Running workflow instance linked to budget |
| **Exceptions** | Same as UC-ADM-004 |

---

### UC-ADM-006 — Process workflow instance (advance / return / cancel)

| Field | Detail |
|-------|--------|
| **Actors** | Tenant Admin User (A1) with pending assignment, Global Super Admin (A2) |
| **Evidence** | `Modules/Workflow/app/Filament/Resources/WorkflowInstances/Pages/ViewWorkflowInstance.php`; `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` |
| **Preconditions** | Instance `running`; actor has pending `workflow_assignments` on current step |
| **Main flow** | 1. Assignee opens workflow instance view. 2. Available step actions rendered from definition. 3. Assignee submits action form. 4. Engine `advance()`, `returnToStep()`, or `cancel()` updates instance, logs, and subject state. |
| **Alternative flows** | Return to earlier step when `getReturnTargetOptions()` non-empty; Open Subject links to PR/Budget |
| **Postconditions** | Instance advanced, returned, or cancelled; automated actions may run (`WorkflowAutomatedActionRunner`) |
| **Exceptions** | No pending assignment → action buttons hidden |

---

### UC-ADM-007 — Manage leave request approval lifecycle

| Field | Detail |
|-------|--------|
| **Actors** | Tenant Admin User (A1), Global Super Admin (A2) |
| **Evidence** | `Modules/Employee/app/Filament/Resources/LeaveRequests/Pages/ViewLeaveRequest.php` |
| **Preconditions** | Leave request record exists |
| **Main flow** | 1. Draft → Submit for Approval (`status` → `pending`). 2. Supervisor Approve sets `supervisor_approved_at`. 3. Approve sets `status` approved with `approver_id`. 4. Reject sets `status` rejected with reason. |
| **Alternative flows** | Download PDF when `isPrintable()` |
| **Postconditions** | Status transitions persisted on `leave_requests` |
| **Exceptions** | Actions visible only for matching status |

---

### UC-ADM-008 — Accept applicant (trigger acceptance pipeline)

| Field | Detail |
|-------|--------|
| **Actors** | Tenant Admin User (A1), Global Super Admin (A2) |
| **Evidence** | `Modules/Enrollment/app/Models/Applicant.php` (`updated` → `ApplicantAccepted`); listeners in School/Finance/Library `EventServiceProvider`; `tests/Feature/ApplicantAcceptedPipelineTest.php` |
| **Preconditions** | Applicant exists; user can edit applicant (Filament resource) |
| **Main flow** | 1. User updates applicant `status` to `accepted`. 2. Model dispatches `ApplicantAccepted`. 3. Listeners (when settings enabled): create student, initial invoice, library member. |
| **Alternative flows** | Revert from accepted dispatches `ApplicantAcceptanceReverted` → compensation listener |
| **Postconditions** | Downstream records idempotently linked (`converted_to_student_id`, invoice morph, member) |
| **Exceptions** | Listener failures depend on queue/retry configuration |

---

### UC-PLT-001 — Manage SaaS tenants

| Field | Detail |
|-------|--------|
| **Actors** | Platform Owner (A3) |
| **Evidence** | `app/Filament/Platform/Resources/Tenants/TenantResource.php`; `TenantResource::canAccess()`; `PlatformPanelProvider` |
| **Preconditions** | User has `platform_owner` role |
| **Main flow** | CRUD on `Tenant` via List/Create/Edit/View pages |
| **Postconditions** | Tenant records updated at platform scope |
| **Exceptions** | Non-platform users cannot access resource |

---

### UC-PLT-002 — Manage platform users

| Field | Detail |
|-------|--------|
| **Actors** | Platform Owner (A3) |
| **Evidence** | `app/Filament/Platform/Resources/Users/UserResource.php` |
| **Preconditions** | Platform panel access |
| **Main flow** | List/manage users at SaaS operator level |
| **Postconditions** | User records updated |
| **Exceptions** | Shield/platform policies |

---

### UC-PAR-001 — View linked children's school data

| Field | Detail |
|-------|--------|
| **Actors** | Parent User (A4) |
| **Evidence** | `app/Filament/Parent/Resources/MyChildren/`, `ChildAttendances/`, `ChildGrades/`, `ChildAnnouncements/`; `ScopesToParentChildren` trait |
| **Preconditions** | `parent_students` links parent to student(s) |
| **Main flow** | 1. Parent logs into `/parent`. 2. Resources query scoped to linked `student_id`s. 3. Read-only lists (create/edit/delete disabled). |
| **Postconditions** | No data mutation from parent panel |
| **Exceptions** | Empty scope if no linked children |

---

### UC-PAR-002 — Submit parent satisfaction survey

| Field | Detail |
|-------|--------|
| **Actors** | Parent User (A4) |
| **Evidence** | `app/Filament/Parent/Pages/ParentSurveyPage.php` |
| **Preconditions** | Active `ParentSurvey` exists (`is_active`, not past `closes_at`) |
| **Main flow** | 1. Parent opens survey page. 2. Submits feedback. 3. `ParentSurveyResponse` created. |
| **Postconditions** | Response stored |
| **Exceptions** | No active survey → handled in page logic |

---

### UC-API-001 — Read authenticated user and tenant context

| Field | Detail |
|-------|--------|
| **Actors** | API Client (A5) |
| **Evidence** | `routes/api.php`; `app/Http/Controllers/Api/v1/AuthController.php` |
| **Preconditions** | Valid Sanctum token; tenant resolved on token |
| **Main flow** | `GET /api/v1/me` or `GET /api/v1/tenants/current` returns user/tenant JSON resources |
| **Alternative flows** | v2 mirrors v1 endpoints |
| **Postconditions** | Read-only response |
| **Exceptions** | `tenants/current` → 404 `no_tenant` if token lacks tenant |

---

### UC-API-002 — Read core master data

| Field | Detail |
|-------|--------|
| **Actors** | API Client (A5) |
| **Evidence** | `routes/api.php`; controllers under `app/Http/Controllers/Api/v1/*` (Organization, Student, CollegeStudent, SchoolClass, Course, Employee) |
| **Preconditions** | Authenticated API client |
| **Main flow** | Index/show GET endpoints return tenant-scoped collections or single records |
| **Postconditions** | JSON API resources |
| **Exceptions** | 404 for missing IDs; rate limit 60/min per token/IP |

---

### UC-API-003 — Create applicant

| Field | Detail |
|-------|--------|
| **Actors** | API Client (A5) |
| **Evidence** | `app/Http/Controllers/Api/v1/ApplicantController.php`; idempotency middleware |
| **Preconditions** | Valid token; admission period exists |
| **Main flow** | 1. `POST /api/v1/applicants` with required fields. 2. Applicant created with `tenant_id`. 3. Outbound webhook `enrollment.created` dispatched. |
| **Postconditions** | Applicant row; optional webhook delivery record |
| **Exceptions** | 422 validation errors |

---

### UC-API-004 — Record payment

| Field | Detail |
|-------|--------|
| **Actors** | API Client (A5) |
| **Evidence** | `app/Http/Controllers/Api/v1/PaymentController.php` |
| **Preconditions** | Valid invoice and chart of account IDs |
| **Main flow** | 1. `POST /api/v1/payments`. 2. Payment created (default `pending`). 3. If `status=verified`, webhook `payment.verified` dispatched. |
| **Postconditions** | Payment row in Finance module |
| **Exceptions** | 422 validation |

---

### UC-API-005 — Create leave request (draft)

| Field | Detail |
|-------|--------|
| **Actors** | API Client (A5) |
| **Evidence** | `app/Http/Controllers/Api/v1/LeaveRequestController.php` |
| **Preconditions** | Valid employee in tenant |
| **Main flow** | `POST /api/v1/leave-requests` creates record with `status=draft` |
| **Postconditions** | Leave request row; approval continues via UC-ADM-007 in admin |
| **Exceptions** | 422 validation |

---

### UC-API-006 — Register / deactivate mobile push device

| Field | Detail |
|-------|--------|
| **Actors** | API Client (A5) |
| **Evidence** | `app/Http/Controllers/Api/v1/DeviceController.php`; `app/Models/Device.php` |
| **Preconditions** | Authenticated user |
| **Main flow** | POST registers/updates device token; DELETE sets `is_active=false` |
| **Postconditions** | Device row tied to `user_id` |
| **Exceptions** | 422 on invalid platform |

---

### UC-API-007 — View student dashboard aggregates

| Field | Detail |
|-------|--------|
| **Actors** | API Client (A5) |
| **Evidence** | `app/Http/Controllers/Api/v1/StudentDashboardController.php` |
| **Preconditions** | Student exists (tenant scope via query) |
| **Main flow** | `GET /api/v1/students/{id}/dashboard` returns cached attendance, grades, invoices summary |
| **Postconditions** | JSON aggregate (5-minute cache) |
| **Exceptions** | 404 if student not found |

---

### UC-PUB-001 — Submit enrollment lead inquiry

| Field | Detail |
|-------|--------|
| **Actors** | Public Visitor (A6) |
| **Evidence** | `Modules/Enrollment/routes/api.php`; `Modules/Enrollment/app/Http/Controllers/InquiryController.php`; `LeadInquiryService` |
| **Preconditions** | Valid `tenant_code` |
| **Main flow** | 1. `POST /api/inquiry` (throttle 10/min). 2. Tenant resolved by code. 3. Lead created with UTM metadata. |
| **Postconditions** | Lead record; 201 with `lead_id` |
| **Exceptions** | 404 unknown tenant; validation errors |

---

### UC-PUB-002 — Browse library OPAC catalog

| Field | Detail |
|-------|--------|
| **Actors** | Public Visitor (A6) |
| **Evidence** | `Modules/Library/routes/web.php`; `PublicOpacController::index`, `show`, organization variants |
| **Preconditions** | Valid tenant (and optional organization) in URL |
| **Main flow** | User browses catalog and book detail without authentication |
| **Postconditions** | HTML/response from OPAC views |
| **Exceptions** | Unknown tenant slug |

---

### UC-PUB-003 — Reserve library book

| Field | Detail |
|-------|--------|
| **Actors** | Authenticated Web User (A7) |
| **Evidence** | `PublicOpacController::reserve`, `organizationReserve`; `auth, verified` middleware |
| **Preconditions** | User authenticated; book exists |
| **Main flow** | POST reserve action on book |
| **Postconditions** | Reservation recorded (controller logic) |
| **Exceptions** | Auth redirect if unauthenticated |

---

### UC-PUB-004 — Perform library circulation

| Field | Detail |
|-------|--------|
| **Actors** | Authenticated Web User (A7) |
| **Evidence** | `PublicOpacController` checkout, quickReturn, extendLoan, markIssue (+ organization variants) |
| **Preconditions** | Authenticated staff/user with circulation access (controller-level checks) |
| **Main flow** | Staff performs checkout, return, extend, or issue reporting via circulation UI |
| **Postconditions** | Loan state updated per action |
| **Exceptions** | Auth required |

---

### UC-PUB-005 — View CMS public page or sitemap

| Field | Detail |
|-------|--------|
| **Actors** | Public Visitor (A6) |
| **Evidence** | `Modules/Cms/routes/web.php`; `PublicSiteController` |
| **Preconditions** | CMS site and page slug exist |
| **Main flow** | GET page or sitemap.xml for site |
| **Postconditions** | Public HTML/XML |
| **Exceptions** | 404 missing page |

---

### UC-PUB-006 — Submit CMS contact form

| Field | Detail |
|-------|--------|
| **Actors** | Public Visitor (A6) |
| **Evidence** | `Modules/Cms/routes/web.php` → `PublicSiteController::contact`; throttle 10/min |
| **Preconditions** | Site accepts contact submissions |
| **Main flow** | POST contact payload |
| **Postconditions** | Contact handled per controller/service |
| **Exceptions** | Throttle exceeded |

---

### UC-PUB-007 — Verify official letter by token

| Field | Detail |
|-------|--------|
| **Actors** | Public Visitor (A6) |
| **Evidence** | `routes/api.php`; `Modules/EOffice/app/Http/Controllers/LetterVerificationController.php` |
| **Preconditions** | Letter issued with `verification_token` |
| **Main flow** | `GET /api/letters/verify/{token}` returns validity and letter metadata |
| **Postconditions** | JSON `{valid: true, ...}` or 404 |
| **Exceptions** | Invalid token → 404 |

---

### UC-EXT-001 — Process Midtrans subscription webhook

| Field | Detail |
|-------|--------|
| **Actors** | Midtrans (A8) |
| **Evidence** | `routes/web.php`; `app/Http/Controllers/BillingController.php`; `BillingService::handleWebhookNotification()` |
| **Preconditions** | Valid Midtrans notification payload |
| **Main flow** | 1. POST `/billing/webhook`. 2. Service validates and updates subscription payment state. 3. Returns `{status: ok}`. |
| **Postconditions** | `SubscriptionLog` payment status updated |
| **Exceptions** | 400 invalid notification; 500 processing error |

---

### UC-EXT-002 — Process donation payment webhook

| Field | Detail |
|-------|--------|
| **Actors** | Donation Payment Provider (A9) |
| **Evidence** | `Modules/Donation/routes/web.php`; `DonationWebhookController`; `DonationPaymentService` |
| **Preconditions** | Provider sends expected webhook shape |
| **Main flow** | POST `/donation/webhook` → payment status updated on donation |
| **Postconditions** | JSON with `donation_id`, `status` |
| **Exceptions** | Service-level validation failures |

---

### UC-EXT-003 — Ingest exam runtime attempt result

| Field | Detail |
|-------|--------|
| **Actors** | Exam Runtime (A10) |
| **Evidence** | `Modules/Exam/routes/api.php`; `ExamRuntimeWebhookController`; `ExamRuntimeSyncService` |
| **Preconditions** | Sanctum-authenticated runtime client; valid definition/participant UUIDs |
| **Main flow** | POST attempt payload → `ingestAttempt()` persists sync record |
| **Postconditions** | Attempt sync row with `sync_status` |
| **Exceptions** | 404 missing definition/participant; validation errors |

---

### UC-EXT-004 — Receive WhatsApp provider webhook

| Field | Detail |
|-------|--------|
| **Actors** | WhatsApp Provider (A11) |
| **Evidence** | `routes/api.php`; `Modules/Messaging/app/Http/Controllers/WhatsAppWebhookController.php` |
| **Preconditions** | Optional `messaging.webhooks.whatsapp_secret` HMAC configured |
| **Main flow** | 1. Verify `X-Hub-Signature-256` when secret set. 2. Return acknowledgment JSON. |
| **Postconditions** | HTTP 200 with `received: true` (no message processing in controller) |
| **Exceptions** | 403 signature mismatch |

---

### UC-EXT-005 — Receive IT monitoring alert webhook

| Field | Detail |
|-------|--------|
| **Actors** | IT Monitoring Sender (A12) |
| **Evidence** | `Modules/ItOps/routes/web.php`; `MonitoringWebhookController`; `ItMonitoringWebhookReceiver` |
| **Preconditions** | Payload accepted by receiver implementation |
| **Main flow** | POST `/itops/monitoring/webhook` → `receive()` processes alert |
| **Postconditions** | `{accepted: true}` |
| **Exceptions** | Receiver-specific failures |

---

## Evidence Index (by use case ID)

| Use case | Primary evidence files |
|----------|------------------------|
| UC-ADM-000 | `Modules/Core/app/Filament/Support/ModuleResource.php`, `app/Providers/Filament/AdminPanelProvider.php` |
| UC-ADM-001 | `app/Filament/Pages/Tenancy/RegisterTenant.php` |
| UC-ADM-002 | `app/Filament/Pages/BillingPage.php`, `app/Services/BillingService.php` |
| UC-ADM-003 | `routes/web.php`, `app/Http/Controllers/LocaleSwitchController.php` |
| UC-ADM-004 | `Modules/Procurement/.../ViewPurchaseRequisition.php` |
| UC-ADM-005 | `Modules/Finance/.../ViewBudget.php` |
| UC-ADM-006 | `Modules/Workflow/.../ViewWorkflowInstance.php`, `DatabaseWorkflowEngine.php` |
| UC-ADM-007 | `Modules/Employee/.../ViewLeaveRequest.php` |
| UC-ADM-008 | `Modules/Enrollment/app/Models/Applicant.php`, `tests/Feature/ApplicantAcceptedPipelineTest.php` |
| UC-PLT-001 | `app/Filament/Platform/Resources/Tenants/TenantResource.php` |
| UC-PLT-002 | `app/Filament/Platform/Resources/Users/UserResource.php` |
| UC-PAR-001 | `app/Filament/Parent/Resources/*`, `ScopesToParentChildren.php` |
| UC-PAR-002 | `app/Filament/Parent/Pages/ParentSurveyPage.php` |
| UC-API-001 | `routes/api.php`, `AuthController.php` |
| UC-API-002 | `routes/api.php`, `app/Http/Controllers/Api/v1/*Controller.php` |
| UC-API-003 | `ApplicantController.php` |
| UC-API-004 | `PaymentController.php` |
| UC-API-005 | `LeaveRequestController.php` |
| UC-API-006 | `DeviceController.php` |
| UC-API-007 | `StudentDashboardController.php` |
| UC-PUB-001 | `Modules/Enrollment/routes/api.php`, `InquiryController.php` |
| UC-PUB-002 | `Modules/Library/routes/web.php`, `PublicOpacController.php` |
| UC-PUB-003 | `PublicOpacController.php` (reserve actions) |
| UC-PUB-004 | `PublicOpacController.php` (circulation actions) |
| UC-PUB-005 | `Modules/Cms/routes/web.php`, `PublicSiteController.php` |
| UC-PUB-006 | `Modules/Cms/routes/web.php` (contact route) |
| UC-PUB-007 | `LetterVerificationController.php` |
| UC-EXT-001 | `BillingController.php` |
| UC-EXT-002 | `DonationWebhookController.php` |
| UC-EXT-003 | `ExamRuntimeWebhookController.php` |
| UC-EXT-004 | `WhatsAppWebhookController.php` |
| UC-EXT-005 | `MonitoringWebhookController.php` |

---

## Coverage gaps (not documented as use cases)

| Area | Status in repo |
|------|----------------|
| Moodle sync | Scheduled/queued outbound jobs only; no inbound actor |
| Student self-service web portal | Not implemented as separate panel |
| SSO / OAuth login | Standard Filament email auth only on panels |
| Generic PDF exports | Many module `routes/web.php` download routes exist; treated as sub-actions of UC-ADM-000 unless dedicated workflow (e.g. leave PDF) |
