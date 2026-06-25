# 12 — BPMN (Rekonstruksi Proses Bisnis)

**Commit analisis:** `d3be06aa`  
**Sumber bukti:** service, listener, job, command, model observer, Filament action — **bukan** file `.bpmn` native.

**TIDAK TERDETEKSI DI KODE:** diagram BPMN 2.0 XML, event subprocess, compensation handler.

---

## 1. Metodologi & Legenda BPMN (Mermaid)

Notasi BPMN 2.0 disimulasikan dengan `flowchart` Mermaid karena repositori tidak menyimpan artefak BPMN resmi.

| Elemen BPMN | Notasi Mermaid | Contoh dalam diagram |
|-------------|----------------|----------------------|
| **Start Event** | `((Start))` atau `([Start: …])` | `((Start))` pendaftaran applicant |
| **End Event** | `((End))` atau `([End: …])` | `((End: Siswa aktif))` |
| **Intermediate Event** | `{{Event: …}}` | `{{Event: ApplicantAccepted}}` |
| **Task** | `[Task: …]` | `[Task: Verifikasi pembayaran]` |
| **User Task** | `[User: …]` | `[User: Approve step workflow]` |
| **Service Task** | `[Svc: …]` | `[Svc: Post jurnal akuntansi]` |
| **Gateway (XOR)** | `{Kondisi?}` | `{Status accepted?}` |
| **Gateway (AND)** | `{AND: …}` | `{AND: Semua approver parallel}` |
| **Approval** | `[Approval: …]` | `[Approval: Advance approve]` |
| **Escalation** | `[Escalation: …]` | `[Escalation: SLA overdue notify]` |
| **Notification** | `[Notify: …]` | `[Notify: InternalWorkflowNotification]` |

**Klasifikasi proses (permintaan user):**

| Kategori | Definisi dalam dokumen ini |
|----------|----------------------------|
| **Core process** | Jalur nilai bisnis utama (pendaftaran, keuangan, approval, procurement) |
| **Supporting process** | Tenancy, auth, sync, messaging, import — mendukung core |
| **Exception process** | Error, SLA breach, escalation, rejection, retry |

---

## 2. Inventaris Elemen BPMN per Domain

| Domain | Event | Task | Gateway | Approval | Escalation | Notification | Bukti utama |
|--------|-------|------|---------|----------|------------|--------------|-------------|
| **Enrollment** | `ApplicantAccepted`, `ApplicantAcceptanceReverted` | Registrasi, seleksi nilai, promote | Status `accepted?`, auto-promote enabled? | Set status `accepted` (admin) | — | **TIDAK TERDETEKSI** notifikasi khusus acceptance | `Applicant.php` booted, `ApplicantPromotionService` |
| **Finance** | `StudentInvoicePaid` | Issue invoice, record payment, verify, post journal | Payment `pending?`, invoice mutable? | `verifyPayment()` | — | `NotificationService::paymentVerified/Rejected` | `FinanceControlService` |
| **Workflow V2** | `WorkflowStarted`, `WorkflowAdvanced`, `WorkflowSlaBreached`, `WorkflowCancelled`, `WorkflowReturned` | Start instance, assign, advance, return, cancel | JSONLogic transition, parallel quorum, evidence required? | `advance()` approve/reject | `escalateOverdue()`, delegation | `InternalWorkflowNotification`, automation `internal_notification` | `DatabaseWorkflowEngine`, `WorkflowEscalationService` |
| **Procurement** | `PurchaseRequisitionApproved` | Start workflow PR, three-way match | Workflow running?, match OK? | Manual `startWorkflow` + engine advance | Sama workflow SLA | Filament toast | `ViewPurchaseRequisition`, `SyncWorkflowSubjectState`, `ThreeWayMatchValidator` |
| **School** | — (CRUD) | Create/update attendance record | — | — | — | — | Filament `AttendanceResource` — **TIDAK TERDETEKSI** engine terpusat |
| **Moodle** | Observer model change | Enqueue outbox, sync job | `moodle.enabled?`, terminal attempts? | — | Exponential retry | — | `MoodleOutboxService`, `ProcessMoodleSyncOutboxJob` |
| **Messaging** | — | Dispatch multi-channel | Channel enabled per tenant? | — | — | database / mail / whatsapp | `NotificationDispatcher` |
| **Tenancy** | Filament register | Create tenant, provision roles/modules | `isLocked?` (subscription) | — | Grace period | Billing redirect | `RegisterTenant`, `EnsureTenantSubscriptionActive` |
| **Import** | User upload CSV | Filament ImportAction | Valid rows? | — | Failed rows log | Filament import notification | `ImportTableActions`, `BaseModelImporter` |

---

## 3. Core Processes

### CP-01 Enrollment: Lead → Applicant → Seleksi → Siswa

**Nilai bisnis:** calon siswa menjadi record `students` aktif.

```mermaid
flowchart TB
    subgraph CP01["CP-01 Core — Enrollment"]
        SE((Start: Inquiry/Registrasi))
        SE --> T1[Task: Buat Lead / Applicant]
        T1 --> T2[Task: Input hasil tes & wawancara]
        T2 --> T3[Svc: synchronizeAssessmentSummary]
        T3 --> GW1{Gateway: Admin set status accepted?}
        GW1 -->|tidak| E1((End: Status lain))
        GW1 -->|ya| EV{{Event: ApplicantAccepted}}
        EV --> GW2{Gateway: auto_promote enabled?}
        GW2 -->|tidak| E2((End: Accepted tanpa siswa))
        GW2 -->|ya| GW3{Gateway: converted_to_student_id ada?}
        GW3 -->|ya| E3((End: Idempotent skip))
        GW3 -->|tidak| T4[Svc: ApplicantPromotionService.promote]
        T4 --> T5[Svc: Buat Student + link applicant]
        T5 --> E4((End: Siswa aktif))
    end
```

| Langkah | Tipe BPMN | Bukti |
|---------|-----------|-------|
| Buat Lead | Task | `LeadInquiryService::createFromInquiry()` |
| Lead → Applicant | Task | `LeadInquiryService::convertToApplicant()` |
| Agregasi nilai | Service Task | `Applicant::synchronizeAssessmentSummary()` |
| Status accepted | Gateway + Event | `Applicant::booted()` → `ApplicantAccepted::dispatch()` |
| Auto-promote gate | Gateway | `ApplicantPromotionService::isEnabledFor()` setting `enrollment.auto_promote_accepted_applicant` |
| Skip jika sudah convert | Gateway | `ApplicantPromotionService::promote()` baris 34–36 |
| Buat siswa | Service Task | `ApplicantPromotionService::promote()` + listener `CreateStudentFromAcceptedApplicant` |

**Listener terkait (supporting hook):** `CreateLibraryMemberFromAcceptedApplicant` — anggota perpustakaan otomatis.

---

### CP-02 Finance: Invoice → Pembayaran → Verifikasi

**Nilai bisnis:** tagihan siswa lunas dengan jurnal akuntansi terverifikasi.

```mermaid
flowchart TB
    subgraph CP02["CP-02 Core — Finance"]
        S((Start: Invoice draft))
        S --> T1[Task: Buat student_invoice]
        T1 --> T2[Approval: markInvoiceIssued]
        T2 --> GW1{Gateway: Invoice locked?}
        GW1 -->|ya| ERR1[Exception: RuntimeException]
        GW1 -->|tidak| T3[Task: Record payment status=pending]
        T3 --> GW2{Gateway: Finance review}
        GW2 -->|verify| AP1[Approval: verifyPayment]
        AP1 --> T4[Svc: recalculateInvoice]
        T4 --> T5[Svc: createPaymentJournalEntry]
        T5 --> N1[Notify: paymentVerified]
        T5 --> EV{{Event: StudentInvoicePaid jika lunas}}
        EV --> E1((End: Invoice paid))
        GW2 -->|reject| AP2[Approval: rejectPayment]
        AP2 --> N2[Notify: paymentRejected]
        AP2 --> E2((End: Payment rejected))
    end
```

| Langkah | Tipe BPMN | Bukti |
|---------|-----------|-------|
| Issue invoice | Approval/Task | `FinanceControlService::markInvoiceIssued()` |
| Immutable issued | Gateway | `assertInvoiceMutable()` / `isLockedForMutation()` |
| Record payment | Task | Model `Payment` status default `pending` |
| Verify | Approval | `FinanceControlService::verifyPayment()` |
| Recalc status | Service Task | `recalculateInvoice()` — `draft/issued/partial/paid` |
| Post journal | Service Task | `createPaymentJournalEntry()` |
| Notifikasi | Notification | `NotificationService::paymentVerified()` |
| Invoice lunas | Event | `StudentInvoicePaid::dispatch()` |

**UI trigger:** `ViewPayment` action `verifyPayment` / `rejectPayment`.

---

### CP-03 Workflow V2: Approval Generik

**Nilai bisnis:** metadata-driven approval multi-step dengan SLA, parallel, delegation.

```mermaid
flowchart TB
    subgraph CP03["CP-03 Core — Workflow Engine"]
        S((Start: Subject butuh approval))
        S --> T1[Svc: WorkflowResolver.resolveForSubject]
        T1 --> T2[Svc: WorkflowInstanceStarter.start]
        T2 --> EV1{{Event: WorkflowStarted}}
        EV1 --> T3[Svc: Snapshot workflow + initial step]
        T3 --> T4[Svc: CreateAssignmentsForCurrentStep]
        T4 --> T5[Svc: Delegation check + SLA due_at]
        T5 --> UT[User: Assignee review]
        UT --> GW1{Gateway: Evidence required?}
        GW1 -->|kurang| ERR1[Exception: WorkflowEvidenceRequiredException]
        GW1 -->|cukup| GW2{Gateway: Action name}
        GW2 -->|approve/advance| AP1[Approval: advance]
        GW2 -->|reject| AP2[Approval: advance reject]
        GW2 -->|return| T6[Task: returnToStep]
        GW2 -->|cancel| T7[Task: cancel]
        AP1 --> GW3{Gateway: JSONLogic + parallel quorum}
        GW3 --> T8[Svc: Transition ke step berikutnya]
        T8 --> EV2{{Event: WorkflowAdvanced}}
        EV2 --> GW4{Gateway: Terminal?}
        GW4 -->|completed| E1((End: Completed))
        GW4 -->|running| T4
        AP2 --> E2((End: Rejected))
        T7 --> E3((End: Cancelled))
    end
```

| Langkah | Tipe BPMN | Bukti |
|---------|-----------|-------|
| Resolve definisi | Service Task | `DatabaseWorkflowResolver::resolveForSubject()` |
| Start instance | Service Task | `DatabaseWorkflowInstanceStarter::start()` |
| Buat assignment | Service Task | `CreateAssignmentsForCurrentStep` + `WorkflowEscalationService::createAssignmentWithDelegation()` |
| Delegation | Gateway implisit | `WorkflowDelegation` aktif → assign ke `toUser` |
| Authorize actor | Gateway | `DatabaseWorkflowEngine::authorizeActor()` |
| Evidence gate | Gateway | `requiresEvidence()` + count evidences |
| Approve / reject | Approval | `DatabaseWorkflowEngine::advance()` |
| Status reject/cancel | Gateway | `determineStatus()` — action `reject` / `cancel` |
| Parallel | AND Gateway | `WorkflowParallelCoordinator` |
| Transisi | Gateway | `JsonLogicWorkflowTransitionResolver` |
| Automation hook | Notification (opsional) | `WorkflowAutomatedActionRunner` trigger `started/advanced/completed/...` |

**Config:** `config/workflow.php` — queue, SLA queue, escalation notify flag.

---

### CP-04 Procurement: PR → Workflow → Sourcing

**Nilai bisnis:** purchase requisition disetujui sebelum sourcing/PO.

```mermaid
flowchart TB
    subgraph CP04["CP-04 Core — Procurement Approval"]
        S((Start: PR dibuat))
        S --> T1[Task: Admin buka ViewPurchaseRequisition]
        T1 --> GW1{Gateway: Workflow running?}
        GW1 -->|tidak| T2[User: Start Approval Workflow]
        T2 --> CP03_START([CP-03 Workflow Engine])
        CP03_START --> EV1{{Event: WorkflowStarted}}
        EV1 --> T3[Svc: SyncWorkflowSubjectState → submitted]
        T3 --> LOOP[User: Multi-step approval]
        LOOP --> EV2{{Event: WorkflowAdvanced}}
        EV2 --> GW2{Gateway: Instance status}
        GW2 -->|completed| T4[Svc: PR status=approved ready_for_sourcing]
        T4 --> EV3{{Event: PurchaseRequisitionApproved}}
        EV3 --> E1((End: Siap RFQ/PO))
        GW2 -->|rejected| E2((End: PR rejected))
        GW2 -->|returned| E3((End: revision_required))
        GW1 -->|ya| LOOP
    end
```

| Langkah | Tipe BPMN | Bukti |
|---------|-----------|-------|
| Start workflow manual | User Task | `ViewPurchaseRequisition::startWorkflow` |
| Sync status PR | Service Task | `SyncWorkflowSubjectState` |
| Approved → event | Event | `PurchaseRequisitionApproved::dispatch()` |
| Three-way match (pasca-PO) | Gateway terpisah | `ThreeWayMatchValidator` — **setelah** GR/VB, bukan di workflow PR |

**Catatan:** Trigger workflow **manual** dari UI PR — **TIDAK TERDETEKSI** auto-start pada create PR.

---

### CP-05 Procurement Fulfillment (Pasca-Approval)

```mermaid
flowchart LR
    subgraph CP05["CP-05 Core — P2P Chain"]
        A((Start: PR approved)) --> PO[Task: Purchase Order]
        PO --> GR[Task: Goods Receipt]
        GR --> VB[Task: Vendor Bill]
        VB --> GW{Gateway: ThreeWayMatchValidator}
        GW -->|fail| BLOCK((End: Blocked))
        GW -->|ok| PAY[Task: Finance payment]
        PAY --> END((End: Paid))
    end
```

**Bukti:** `ThreeWayMatchValidator`, model `PurchaseOrder`, `GoodsReceipt`, `VendorBill`.

---

## 4. Supporting Processes

### SP-01 Tenant Onboarding & Keanggotaan User

```mermaid
flowchart TB
    subgraph SP01["SP-01 Supporting — Tenancy"]
        S((Start: User login / register tenant))
        S --> T1[Task: RegisterTenant.handleRegistration]
        T1 --> T2[Svc: Tenant::create]
        T2 --> T3[Svc: TenantAdminProvisioner.ensureTenantOwnerRole]
        T3 --> T4[Svc: assignShieldSuperAdmin]
        T4 --> T5[Svc: TenantModuleProvisioner enable core+global]
        T5 --> GW{Gateway: canAccessPanel?}
        GW -->|membership OK| E1((End: Akses panel))
        GW -->|no role| E2((End: Ditolak))
    end
```

| Langkah | Bukti |
|---------|-------|
| Registrasi tenant | `app/Filament/Pages/Tenancy/RegisterTenant.php` |
| Role owner + Shield | `TenantAdminProvisioner` |
| Modul default | `TenantModuleProvisioner::enableForTenant()` |
| Akses panel | `User::canAccessPanel()` + `user_tenant_roles` |

**Assignment role tambahan:** CRUD `UserTenantRole` via Filament Core — **TIDAK TERDETEKSI** service orchestration terpusat selain provisioner.

---

### SP-02 Subscription Gate (Akses Tenant Terkunci)

```mermaid
flowchart TB
    subgraph SP02["SP-02 Supporting — Billing Gate"]
        S((Start: Request /admin))
        S --> GW{Gateway: Tenant.isLocked?}
        GW -->|tidak| OK((End: Lanjut))
        GW -->|ya| T1[Task: Redirect billing / Snap]
        T1 --> T2[Svc: BillingService webhook]
        T2 --> E((End: subscription updated))
    end
```

**Bukti:** `EnsureTenantSubscriptionActive`, `Tenant::isLocked()`, `BillingService`.

---

### SP-03 Moodle Sync Outbox

```mermaid
flowchart TB
    subgraph SP03["SP-03 Supporting — Moodle"]
        S((Start: Model observer / manual enqueue))
        S --> GW1{Gateway: moodle.enabled?}
        GW1 -->|tidak| E0((End: Skip))
        GW1 -->|ya| T1[Svc: MoodleOutboxService.enqueue]
        T1 --> T2[Svc: ProcessMoodleSyncOutboxJob dispatch]
        T2 --> T3[Svc: MoodleSyncService.syncOutboxItem]
        T3 --> E1((End: synced))
    end
```

**Bukti:** `MoodleOutboxService`, `ProcessMoodleSyncOutboxJob`, observers (`CourseObserver`, `StudentObserver`, dll.).

---

### SP-04 Import / Export Data (Filament)

```mermaid
flowchart TB
    subgraph SP04["SP-04 Supporting — Import"]
        S((Start: User klik Import))
        S --> T1[Task: ImportAction + tenant_id option]
        T1 --> T2[Svc: BaseModelImporter process rows]
        T2 --> GW{Gateway: Row valid?}
        GW -->|ya| T3[Task: Persist model]
        GW -->|tidak| T4[Task: failed_import_rows]
        T3 --> E1((End: Import complete))
        T4 --> E1
    end
```

**Bukti:** `ImportTableActions::make()`, `app/Filament/Imports/BaseModelImporter.php`, tabel `imports` / `failed_import_rows`.

---

### SP-05 Messaging / Notification Dispatch

```mermaid
flowchart TB
    subgraph SP05["SP-05 Supporting — Messaging"]
        S((Start: dispatch dipanggil))
        S --> T1[Svc: NotificationDispatcher.dispatch]
        T1 --> GW1{Gateway: idempotency key exists?}
        GW1 -->|ya| E0((End: Return existing))
        GW1 -->|tidak| T2[Task: Create NotificationDelivery queued]
        T2 --> GW2{Gateway: Channel enabled?}
        GW2 -->|database| N1[Notify: GenericDatabaseNotification]
        GW2 -->|mail| N2[Notify: mail stub]
        GW2 -->|whatsapp| N3[Notify: WhatsAppProvider]
        N1 --> T3[Task: status=sent]
        N2 --> T3
        N3 --> T3
        T3 --> E1((End: Delivered))
    end
```

**Bukti:** `Modules/Messaging/app/Services/NotificationDispatcher.php`.

**Paralel:** `NotificationService` (Core) mengirim notifikasi Filament database untuk event finance/workflow — jalur terpisah dari Messaging module.

---

## 5. Exception Processes

### EP-01 Workflow SLA Breach & Escalation

```mermaid
flowchart TB
    subgraph EP01["EP-01 Exception — Workflow SLA"]
        S((Start: Instance running + due_at))
        S --> T1[Svc: QueuedWorkflowSlaService.scheduleCheck]
        T1 --> T2[Svc: CheckWorkflowSlaJob delayed]
        T2 --> GW1{Gateway: due_at past?}
        GW1 -->|tidak| E0((End: No breach))
        GW1 -->|ya| T3[Svc: markBreached + audit log]
        T3 --> EV{{Event: WorkflowSlaBreached}}
        EV --> T4[Svc: RunWorkflowAutomatedActions sla_breached]
        T4 --> E1((End: SLA breached logged))

        S2((Start: Scheduler/Cron)) --> T5[Svc: workflow:escalate-overdue]
        T5 --> T6[Escalation: escalateOverdue assignments]
        T6 --> GW2{Gateway: assignment overdue?}
        GW2 -->|ya| N1[Notify: InternalWorkflowNotification overdue]
        GW2 -->|sudah escalated| E2((End: Skip))
        N1 --> E3((End: Assignee notified))

        S3((Start: Admin CLI)) --> T7[Svc: fos:workflow:retry-sla]
        T7 --> T2
    end
```

| Langkah | Tipe | Bukti |
|---------|------|-------|
| Schedule SLA check | Service Task | `QueuedWorkflowSlaService::scheduleCheck()` |
| Breach detection | Gateway + Event | `CheckWorkflowSlaJob` → `markBreached()` → `WorkflowSlaBreached` |
| Assignment escalation | Escalation | `WorkflowEscalationService::escalateOverdue()` |
| Notify assignee | Notification | `InternalWorkflowNotification` jika `workflow.escalation.notify_assignee` |
| Retry SLA jobs | Task (manual) | `fos:workflow:retry-sla` (`WorkflowRetrySlaCommand`) |

---

### EP-02 Workflow Rejection / Cancel / Return

```mermaid
flowchart TB
    subgraph EP02["EP-02 Exception — Workflow Terminal/Return"]
        S((Start: Assignee action))
        S --> GW{Gateway: Action}
        GW -->|reject| T1[Approval: advance reject]
        T1 --> T2[Svc: status=Rejected rejected_at]
        T2 --> EV1{{Event: automation rejected}}
        EV1 --> T3[Svc: SyncWorkflowSubjectState PR=rejected]
        T3 --> E1((End: Rejected))

        GW -->|cancel| T4[Task: cancel]
        T4 --> T5[Svc: Cancel pending assignments]
        T5 --> EV2{{Event: WorkflowCancelled}}
        EV2 --> E2((End: Cancelled))

        GW -->|return| T6[Task: returnToStep]
        T6 --> EV3{{Event: WorkflowReturned}}
        EV3 --> T7[Svc: PR status=revision_required]
        T7 --> E3((End: Returned))

        GW -->|unauthorized| ERR[Exception: WorkflowAuthorizationException]
    end
```

**Bukti:** `DatabaseWorkflowEngine::cancel()`, `returnToStep()`, `determineStatus()`, `SyncWorkflowSubjectState`.

---

### EP-03 Payment Verification Failure

```mermaid
flowchart TB
    subgraph EP03["EP-03 Exception — Finance Payment"]
        S((Start: verify/reject action))
        S --> GW1{Gateway: isLockedForMutation?}
        GW1 -->|ya| ERR1[Exception: RuntimeException finalized]
        GW1 -->|tidak| GW2{Gateway: Action}
        GW2 -->|verify fail UI| ERR2[Notify: Filament danger toast]
        GW2 -->|reject| T1[Approval: rejectPayment]
        T1 --> N1[Notify: paymentRejected]
        T1 --> E1((End: Payment rejected))
    end
```

**Bukti:** `FinanceControlService::rejectPayment()`, `ViewPayment` try/catch + Filament `Notification`.

---

### EP-04 Moodle Sync Retry & Terminal Failure

```mermaid
flowchart TB
    subgraph EP04["EP-04 Exception — Moodle Sync"]
        S((Start: Job handle))
        S --> GW1{Gateway: moodle.enabled?}
        GW1 -->|tidak| E0((End: Skip))
        GW1 -->|ya| T1[Svc: tryClaim outbox]
        T1 --> GW2{Gateway: Claim OK?}
        GW2 -->|tidak| E1((End: Already processing))
        GW2 -->|ya| T2[Svc: syncOutboxItem]
        T2 --> GW3{Gateway: Exception?}
        GW3 -->|readonly| E2((End: status=skipped))
        GW3 -->|tidak| E3((End: status=synced))
        GW3 -->|ya| T3[Svc: attempts++]
        T3 --> GW4{Gateway: attempts >= max?}
        GW4 -->|tidak| T4[Task: status=pending next_retry_at]
        GW4 -->|ya| T5[Task: status=failed report]
        T4 --> E4((End: Retry scheduled))
        T5 --> E5((End: Terminal failure))
    end
```

**Bukti:** `ProcessMoodleSyncOutboxJob`, `MoodleSyncRetry::nextRetryAt()`, `config/moodle.php` `max_attempts`.

---

### EP-05 Workflow Automation Failure & Retry

```mermaid
flowchart TB
    subgraph EP05["EP-05 Exception — Automation"]
        S((Start: Automated action run))
        S --> T1[Svc: WorkflowAutomatedActionRunner.run]
        T1 --> GW1{Gateway: Action throws?}
        GW1 -->|tidak| E1((End: OK))
        GW1 -->|ya| T2[Svc: writeFailureAudit + report]
        T2 --> GW2{Gateway: rethrow_on_failure?}
        GW2 -->|ya| ERR[Exception: propagated]
        GW2 -->|tidak| E2((End: Swallowed — HTTP OK))

        S2((Start: Admin CLI)) --> T3[Svc: fos:workflow:retry-automation]
        T3 --> T1
    end
```

**Bukti:** `WorkflowAutomatedActionRunner::runMappedAction()` catch block, `config/workflow.php` `automation.rethrow_on_failure`, `WorkflowRetryAutomationCommand`.

---

### EP-06 Enrollment: Acceptance Reverted

```mermaid
flowchart TB
    subgraph EP06["EP-06 Exception — Enrollment Revert"]
        S((Start: status changed from accepted))
        S --> EV{{Event: ApplicantAcceptanceReverted}}
        EV --> T1[Listener: cleanup terkait]
        T1 --> E((End: Rollback side-effects))
    end
```

**Bukti:** `Applicant::booted()` baris 82–87, event `ApplicantAcceptanceReverted`.

---

## 6. Peta Hub Core ↔ Supporting ↔ Exception

```mermaid
flowchart LR
    subgraph Core
        CP01[CP-01 Enrollment]
        CP02[CP-02 Finance]
        CP03[CP-03 Workflow]
        CP04[CP-04 Procurement]
    end
    subgraph Supporting
        SP01[SP-01 Tenancy]
        SP03[SP-03 Moodle]
        SP05[SP-05 Messaging]
    end
    subgraph Exception
        EP01[EP-01 SLA/Escalation]
        EP04[EP-04 Moodle retry]
        EP05[EP-05 Automation retry]
    end
    SP01 --> CP01
    SP01 --> CP02
    CP03 --> CP04
    CP01 --> SP03
    CP02 --> SP05
    CP03 --> EP01
    CP03 --> EP05
    SP03 --> EP04
```

---

## 7. TIDAK TERDETEKSI DI KODE

| Proses | Status |
|--------|--------|
| Payroll end-to-end BPMN | **TIDAK TERDETEKSI DI KODE** |
| Attendance recording engine (selain CRUD Filament) | **TIDAK TERDETEKSI DI KODE** |
| Auto-start workflow pada create PR | **TIDAK TERDETEKSI DI KODE** — start manual |
| Marketplace order fulfillment | **TERINDIKASI NAMUN BELUM TERBUKTI PENUH** |
| File BPMN 2.0 XML | **TIDAK TERDETEKSI DI KODE** |
| Escalation ke manager hierarchy otomatis | **TIDAK TERDETEKSI DI KODE** — hanya notify assignee + automation hooks |

---

## 8. Referensi Silang

| Dokumen | Isi terkait |
|---------|-------------|
| `08_activity_diagram.md` | AD-04 workflow, AD-05 finance, AD-07 enrollment |
| `07_use_case_diagram.md` | UC-001–020 |
| `15_business_rules.md` | BR-W*, BR-F*, BR-E*, BR-P* |
| `WORKFLOW.md` | Runbook workflow V2 |
| `PROCUREMENT.md` | Rantai P2P |
| `FINANCE.md` | Golden path keuangan |

**Regenerasi bukti otomatis:** `scripts/build-evidence-registry.php`
