# 12 — BPMN (Proses Bisnis)

## Ringkasan Singkat

Dokumen ini menyajikan alur proses bisnis dalam notasi **BPMN-like** menggunakan Mermaid flowchart. Mermaid tidak mendukung BPMN 2.0 penuh — gateway dan swimlane disimulasikan dengan label eksplisit.

**Keterbatasan:** Hanya proses dengan bukti service/engine; tidak ada file `.bpmn` di repositori.

---

## BPMN-01: Procurement-to-Pay (Terbukti Sebagian)

**Swimlane:** Requester → Approver (Workflow) → Procurement → Finance

```mermaid
flowchart TB
    subgraph Requester
        A1([Buat Purchase Requisition])
    end

    subgraph Workflow
        G1{Approval<br/>Gateway}
        A2([Workflow Completed])
    end

    subgraph Procurement
        A3([Buat RFQ / PO])
        A4([Goods Receipt])
        A5([Vendor Bill])
        G2{Three-Way<br/>Match OK?}
    end

    subgraph Finance
        A6([Post Payment / Journal])
    end

    A1 --> G1
    G1 -->|Reject| END1([Rejected])
    G1 -->|Approve| A2
    A2 --> A3
    A3 --> A4
    A4 --> A5
    A5 --> G2
    G2 -->|Fail| END2([Block bill])
    G2 -->|Pass| A6
    A6 --> END3([Paid / Posted])
```

**Bukti:**
- Workflow: `DatabaseWorkflowEngine`, setup command `fos:workflow:setup-procurement-pilot`
- Three-way match: `ThreeWayMatchValidator.php`
- PO auto: `PurchaseOrderAutoCreationService.php`

**[GAP]:** Trigger otomatis PR→workflow tidak diaudit baris-per-baris di dokumen ini.

---

## BPMN-02: Budget Approval

```mermaid
flowchart LR
    A([Budget draft]) --> B[Start workflow<br/>setup-budget-workflow]
    B --> C{Finance approve?}
    C -->|No| D([Returned / Rejected])
    C -->|Yes| E{Executive approve?}
    E -->|Yes| F([Budget approved<br/>SyncBudgetWorkflowState])
    E -->|No| D
```

**Bukti:** `SetupBudgetWorkflowCommand.php`, listener `SyncBudgetWorkflowState`, test `WorkflowBudgetApprovalTest.php`

---

## BPMN-03: Enrollment Funnel

```mermaid
flowchart TD
    P([Public inquiry]) --> L[Lead stage=new]
    L --> F{Follow-up}
    F --> C[Convert to Applicant]
    C --> E{Exam / selection}
    E -->|Accepted| ACC[Applicant accepted]
    ACC --> SET{auto_promote?}
    SET -->|true| STU[Create Student]
    SET -->|false| WAIT([Manual promote])
    STU --> END([Enrolled])
```

**Bukti:** `LeadInquiryService`, `ApplicantPromotionService`, `ApplicantAcceptedPipelineTest`

---

## BPMN-04: Leave Request (API + Admin)

```mermaid
flowchart TD
    API([POST leave-requests]) --> DRAFT[Status draft]
    DRAFT --> ADM{Admin review}
    ADM -->|Approve| APP([Approved])
    ADM -->|Reject| REJ([Rejected])
```

**Bukti:** `LeaveRequestController`, model `LeaveRequest` — detail state machine: **PARSIAL** (lihat `STATE_DIAGRAMS.md` ST-07 jika diverifikasi).

---

## BPMN-05: SaaS Subscription Billing

```mermaid
flowchart TD
    T([Tenant trial/subscribed]) --> LOCK{subscription active?}
    LOCK -->|no| BILL[Billing page Midtrans Snap]
    BILL --> PAY([User pays])
    PAY --> WH[Midtrans webhook]
    WH --> UPD[Update tenant status]
    UPD --> ACCESS([Admin access restored])
    LOCK -->|yes| ACCESS
```

**Bukti:** `Tenant::isLocked()`, `EnsureTenantSubscriptionActive`, `BillingService`

---

## BPMN TIDAK Dibuat

| Proses | Alasan |
|--------|--------|
| Full payroll bulanan | Engine terpusat **TIDAK TERDETEKSI** |
| Akreditasi Education QA end-to-end | **PARSIAL** — CRUD resources |
| Marketplace order fulfillment | **PARSIAL** |

---

## Catatan Ketidakpastian

- Event subprocess, compensation, message throw/catch BPMN 2.0: **tidak dimodelkan**.
- Untuk state machine formal per entitas: `STATE_DIAGRAMS.md` (status Draft sebagian).
