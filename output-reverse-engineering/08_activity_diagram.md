# 08 — Activity Diagram

## Ringkasan Singkat

Diagram aktivitas untuk proses bisnis yang **terbukti** di service, engine, atau listener. Langkah tanpa jejak kode ditandai `[GAP]`.

---

## AD-01: Workflow Approval (Linear)

**Bukti:** `DatabaseWorkflowEngine.php`, `CreateAssignmentsForCurrentStep.php`

```mermaid
flowchart TD
    Start([Start Instance]) --> LoadInit[Load initial step<br/>DatabaseWorkflowInstanceStarter]
    LoadInit --> Snapshot[Persist workflow_snapshot]
    Snapshot --> FireStart[Dispatch WorkflowStarted]
    FireStart --> Assign[CreateAssignmentsForCurrentStep]
    Assign --> Notify[NotifyWorkflowAssignees]
    Notify --> Wait{Assignee action?}

    Wait -->|Advance| AuthZ{authorizeActor}
    AuthZ -->|Fail| Deny([Reject unauthorized])
    AuthZ -->|OK| Evidence{Evidence required?}
    Evidence -->|Missing| Gap1([GAP: block advance])
    Evidence -->|OK| Resolve[Resolve transition JSONLogic]
    Resolve --> Update[Update instance step/status]
    Update --> FireAdv[Dispatch WorkflowAdvanced]
    FireAdv --> Terminal{Terminal step?}
    Terminal -->|Yes| Complete([Status Completed])
    Terminal -->|No| Assign

    Wait -->|Return| ReturnStep[returnToStep]
    Wait -->|Cancel| CancelInst[cancel instance]
```

**File:** `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php`

---

## AD-02: Applicant Diterima → Student

**Bukti:** `ApplicantPromotionService.php`, test `ApplicantAcceptedPipelineTest.php`

```mermaid
flowchart TD
    A([Applicant status accepted]) --> CheckSetting{auto_promote setting<br/>default true?}
    CheckSetting -->|false| EndSkip([Skip promotion])
    CheckSetting -->|true| CheckConverted{converted_to_student_id set?}
    CheckConverted -->|yes| EndSkip
    CheckConverted -->|no| CreateStudent[Create Student record]
    CreateStudent --> GenNIS[Generate unique NIS]
    GenNIS --> Link[Set applicant.converted_to_student_id]
    Link --> EndOK([Student created])
```

**File:** `Modules/Enrollment/app/Services/ApplicantPromotionService.php:17-87`

---

## AD-03: Lead Inquiry → Applicant

**Bukti:** `LeadInquiryService.php`

```mermaid
flowchart TD
    I([POST api/inquiry]) --> CreateLead[createFromInquiry<br/>stage=new]
    CreateLead --> FollowUp[Set next_follow_up_at +1 day]
    FollowUp --> Manual{Admin convert?}
    Manual -->|convertToApplicant| Applied[stage=applied<br/>converted_at=now]
```

**File:** `Modules/Enrollment/app/Services/LeadInquiryService.php:29-58`

---

## AD-04: Pembayaran Invoice Siswa

**Bukti:** `FinanceControlService.php`, `StudentInvoicePaidPipelineTest.php`

```mermaid
flowchart TD
    P([Payment recorded]) --> Verify{verify payment}
    Verify --> Journal[Auto journal entry]
    Journal --> Recalc[Recalculate invoice status]
    Recalc --> Status{paid amount}
    Status -->|full| Paid([status=paid])
    Status -->|partial| Partial([status=partial])
    Status -->|none| Issued([status=issued])
```

**File:** `Modules/Finance/app/Services/FinanceControlService.php:52-70,196-201`

---

## AD-05: Three-Way Match (Procurement)

**Bukti:** `ThreeWayMatchValidator.php`

```mermaid
flowchart TD
    V([Validate vendor bill line]) --> GR{GR belongs to PO?}
    GR -->|no| Fail1([Exception])
    GR -->|yes| Qty{billed ≤ received ≤ ordered?}
    Qty -->|no| Fail2([Exception])
    Qty -->|yes| Price{unit price within ±5% PO?}
    Price -->|no| Fail3([Exception])
    Price -->|yes| OK([Validation passed])
```

**File:** `Modules/Procurement/app/Services/ThreeWayMatchValidator.php:30-67`

---

## AD-06: Moodle Sync Outbox

**Bukti:** `MoodleOutboxService.php`, `ProcessMoodleSyncOutboxJob.php`

```mermaid
flowchart TD
    Obs([Model created/updated]) --> Enqueue[MoodleOutboxService::enqueue]
    Enqueue --> Outbox[(moodle_sync_outbox)]
    Outbox --> Job[ProcessMoodleSyncOutboxJob]
    Job --> Client[MoodleClient::call REST]
    Client -->|success| Done([Mark processed])
    Client -->|fail| Retry([Retry policy])
```

**File:** `app/Integrations/Moodle/MoodleOutboxService.php`, `app/Jobs/ProcessMoodleSyncOutboxJob.php`

---

## AD-07: Tenant Subscription Gate

**Bukti:** `EnsureTenantSubscriptionActive.php`, `Tenant::isLocked()`

```mermaid
flowchart TD
    R([Request /admin]) --> Check{Tenant locked?}
    Check -->|yes| Bill[Redirect billing page]
    Check -->|no| Allow([Continue request])
```

**File:** `app/Http/Middleware/EnsureTenantSubscriptionActive.php:14-21`

---

## Proses TIDAK TERDETEKSI (Activity)

| Proses | Status |
|--------|--------|
| End-to-end payroll calculation detail | PARSIAL — model ada, engine payroll tidak diaudit penuh |
| Full SLIMS import mapping | PARSIAL — command ada |
| Marketplace checkout | PARSIAL — resources ada |

---

## Catatan Ketidakpastian

- Parallel workflow quorum: lihat `DatabaseWorkflowEngine::advanceParallel` — diagram terpisah tidak dibuat karena kompleksitas; **PARSIAL**.
- BPMN formal: lihat `12_bpmn.md`.
