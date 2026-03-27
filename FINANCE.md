# Finance Golden Path Runbook

Panduan ini menjelaskan control dasar Finance yang sekarang sudah diprioritaskan untuk mengejar production-ready pada gelombang awal: `Invoice -> Payment -> Journal`, ditambah kontrol dasar `Budget`.

## Scope

Golden path yang sudah dibangun:

- `StudentInvoice`
- `Payment`
- `JournalEntry`
- `Budget`
- `Budget Approval Workflow`
- audit trail untuk aksi penting
- lock setelah status final
- ringkasan tenant via `Finance Overview`

## Komponen Inti

- finance service: [FinanceControlService.php](/Users/yusuf/Herd/foundationOS/Modules/Finance/app/Services/FinanceControlService.php)
- overview page: [FinanceOverviewPage.php](/Users/yusuf/Herd/foundationOS/Modules/Finance/app/Filament/Pages/FinanceOverviewPage.php)
- invoice resource: [StudentInvoiceResource.php](/Users/yusuf/Herd/foundationOS/Modules/Finance/app/Filament/Resources/StudentInvoices/StudentInvoiceResource.php)
- payment resource: [PaymentResource.php](/Users/yusuf/Herd/foundationOS/Modules/Finance/app/Filament/Resources/Payments/PaymentResource.php)
- journal resource: [JournalEntryResource.php](/Users/yusuf/Herd/foundationOS/Modules/Finance/app/Filament/Resources/JournalEntries/JournalEntryResource.php)
- budget resource: [BudgetResource.php](/Users/yusuf/Herd/foundationOS/Modules/Finance/app/Filament/Resources/Budgets/BudgetResource.php)

## Konfigurasi Tenant yang Wajib

Auto-post journal saat verifikasi payment memerlukan akun receivable default di `tenant_settings`.

Nilai yang dibutuhkan:

- `group = finance`
- `key = default_receivable_account_id`
- `value = <chart_of_account_id>`

Contoh data:

```text
tenant_id = 1
group = finance
key = default_receivable_account_id
value = 25
type = integer
```

Tanpa setting ini, verifikasi payment akan gagal dengan pesan konfigurasi receivable account belum ada.

## Status Machine

### Student Invoice

Status yang dipakai:

- `draft`
- `issued`
- `partial`
- `paid`
- `void`
- `cancelled`

Lock rule:

- invoice tidak boleh diedit lagi bila `paid`, `void`, atau `cancelled`

### Payment

Status yang dipakai:

- `pending`
- `verified`
- `rejected`
- `reversed`

Lock rule:

- payment tidak boleh dimutasi lagi bila sudah `verified`, `rejected`, atau `reversed`

### Journal Entry

Flag penting:

- `is_posted`
- `is_reversed`

Lock rule:

- journal tidak boleh diedit bila sudah posted atau reversed

### Budget

Status yang dipakai:

- `draft`
- `submitted`
- `in_review`
- `revision_required`
- `approved`
- `rejected`
- `closed`
- `cancelled`

Lock rule:

- budget tidak boleh diedit bila `submitted`, `in_review`, `approved`, `rejected`, `closed`, atau `cancelled`
- budget tetap bisa direvisi saat `draft` atau `revision_required`

## Budget Approval Workflow

`Budget` sekarang menjadi workflow subject resmi.

Setup default per tenant/organization:

```bash
php artisan fos:workflow:setup-budget-workflow 1 --organization=5 --finance=11 --executive=12 --executive-threshold=50000000
```

Flow default:

- `draft` -> `submitted`
- `submitted` / `in_review`
- `revision_required`
- `approved` / `rejected` / `cancelled`

Aturan default:

- budget kecil selesai di finance approver
- budget dengan `allocated_amount >= executive-threshold` lanjut ke executive

## Operasional Harian

### 1. Terbitkan invoice

Dari halaman `Student Invoice`, gunakan action:

- `Mark Issued`

Efek:

- status menjadi `issued`
- audit log tercatat

### 2. Catat payment

Dari halaman `Payment`, buat payment baru dengan:

- tenant aktif yang benar
- invoice yang sesuai tenant
- akun kas/bank yang sesuai tenant

Status awal:

- `pending`

### 3. Verifikasi payment

Dari halaman `Payment`, gunakan action:

- `Verify Payment`

Efek:

- payment menjadi `verified`
- invoice dihitung ulang:
  - `issued`
  - `partial`
  - `paid`
- journal entry otomatis dibuat dan langsung posted
- audit log payment tercatat

### 4. Tolak payment

Dari halaman `Payment`, gunakan action:

- `Reject Payment`

Efek:

- payment menjadi `rejected`
- invoice dihitung ulang tanpa mengakui payment tersebut
- audit log tercatat

### 5. Post journal manual

Untuk journal entry manual yang sudah seimbang, gunakan action:

- `Post Journal`

Syarat:

- total debit = total credit

### 6. Reverse journal

Untuk koreksi, gunakan action:

- `Reverse Journal`

Efek:

- journal ditandai reversed
- alasan reversal tersimpan
- audit log tercatat

### 7. Approve budget

Dari halaman `Budget`, gunakan action:

- `Approve Budget`

Efek:

- status menjadi `approved`
- approver dan waktu approval tersimpan
- audit log tercatat

## Reporting Minimal

Halaman:

- `Finance Overview`

Ringkasan tenant yang tersedia saat ini:

- draft invoices
- issued / partial invoices
- paid invoices
- pending payments
- verified payments
- posted journals
- approved budgets
- outstanding amount
- verified payment amount

## Troubleshooting

### Payment verify gagal

Periksa:

- `tenant_settings` untuk receivable account sudah ada
- akun kas/bank milik tenant aktif
- invoice dan payment berada di tenant yang sama

### Journal tidak bisa dipost

Periksa:

- debit dan credit benar-benar seimbang
- journal belum pernah dipost
- journal belum reversed

### Data tidak bisa diedit

Kemungkinan memang sudah masuk status final dan terkunci. Ini sengaja sebagai guardrail produksi.

## Rekomendasi Operasional

- gunakan `Finance Overview` setiap hari untuk memantau outstanding dan verification backlog
- jangan ubah record final langsung lewat database
- lakukan koreksi melalui reversal/reject/aksi resmi
- dokumentasikan akun receivable default per tenant saat onboarding finance
