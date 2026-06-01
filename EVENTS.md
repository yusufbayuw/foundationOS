# FoundationOS — Public Domain Events

Katalog event bisnis yang boleh di-subscribe modul lain. Event **internal** (hanya dalam modul yang sama) tidak perlu didaftarkan di sini.

## Konvensi

| Aturan | Detail |
|--------|--------|
| Penamaan | `PastTense` + domain (`ApplicantAccepted`) |
| Dispatch | Setelah transaksi DB commit (model `saved` / service) |
| Listener | `ShouldQueue` + `InteractsWithTenant` |
| Idempotensi | Wajib — cek FK / unique sebelum create |
| Observability | Bungkus dengan `AutomationRunLogger` |
| Kompensasi | Setiap event forward wajib punya baris **Compensate** |

---

## Procurement

### `Modules\Procurement\Events\PurchaseRequisitionApproved`

| | |
|---|---|
| **Trigger** | PR status → `approved` via workflow (`SyncWorkflowSubjectState`) |
| **Payload** | `PurchaseRequisition $requisition`, `?User $approver` |
| **Subscribers** | `CreateRfqFromApprovedPurchaseRequisition` |
| **Idempotent** | Ya — skip jika draft RFQ sudah ada |
| **Setting** | `procurement.auto_create_rfq_from_pr` (default: on) |
| **Compensate** | Manual — batalkan PR tidak auto-hapus RFQ |

---

## Enrollment

### `Modules\Enrollment\Events\ApplicantAccepted`

| | |
|---|---|
| **Trigger** | `Applicant.status` berubah menjadi `accepted` |
| **Payload** | `Applicant $applicant`, `?User $actor` |
| **Subscribers** | `CreateStudentFromAcceptedApplicant` (School), `CreateInitialInvoiceFromAcceptedApplicant` (Finance), `CreateLibraryMemberFromAcceptedApplicant` (Library) |
| **Idempotent** | Ya — `converted_to_student_id`, invoice morph, `member_number` |
| **Settings** | `enrollment.auto_promote_accepted_applicant`, `enrollment.auto_invoice_on_accept`, `library.auto_member_on_accept` |
| **Compensate** | `ApplicantAcceptanceReverted` → void invoice draft/issued |

### `Modules\Enrollment\Events\ApplicantAcceptanceReverted`

| | |
|---|---|
| **Trigger** | `Applicant.status` keluar dari `accepted` |
| **Payload** | `Applicant $applicant`, `?User $actor`, `string $previousStatus` |
| **Subscribers** | `CompensateApplicantAcceptance` |
| **Compensate** | Void tagihan non-`paid`; tidak menghapus student |

---

## Finance

### `Modules\Finance\Events\StudentInvoicePaid`

| | |
|---|---|
| **Trigger** | `StudentInvoice.status` → `paid` setelah `FinanceControlService::recalculateInvoice()` |
| **Payload** | `StudentInvoice $invoice`, `?User $actor` |
| **Subscribers** | `UpdateRegistrationOnInvoicePaid` (Enrollment), notifikasi via `NotificationService` |
| **Idempotent** | Ya — skip jika `registration.payment_status` sudah `paid` |
| **Compensate** | Pembatalan pembayaran → `recalculateInvoice` turunkan status; registration ikut `partial`/`unpaid` |

---

## Workflow

### `Modules\Workflow\Events\WorkflowAdvanced`

Lihat `WORKFLOW.md`. Dipakai internal engine; cross-module via `SyncWorkflowSubjectState` (mis. dispatch `PurchaseRequisitionApproved`).

---

## Exam / Sales / Inventory / Helpdesk / Legal / ItOps

Event sudah ada di `Modules/*/app/Events/` — belum ada subscriber lintas modul. Daftarkan di sini saat ada integrasi resmi.

---

## Menambah event baru

1. Tambahkan baris di file ini.
2. Buat class event + test dispatch.
3. Daftarkan listener di `EventServiceProvider` modul subscriber.
4. Bungkus side-effect dengan `AutomationRunLogger`.
5. Tambahkan test idempotensi (handle 2×).
