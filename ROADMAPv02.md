# ROADMAP v0.2 — Fase 2 Operasional Inti (HRM, Finance, Inventory & Procurement)

Audit gap Fase 2 terhadap codebase saat ini, beserta urutan kerja yang direkomendasikan.

---

## Status Audit Fase 2

### 1. Modul HRM & Payroll (Bulan 3)
- [x] Employee Directory — model `Employee`, `EmploymentContract`, `Position` ada; dokumen digital KTP/NPWP sudah terimplementasi (`EmployeeDocument`)
- [x] Absensi & Cuti — `AttendanceLog`, `LeaveRequest`, `Shift` ada; GPS/foto sudah terimplementasi.
- [x] Payroll Engine (RBE) — `PayrollCalculationService` dan command generation gaji sudah ada.
  - [x] Formula: Gaji Pokok + Tunjangan − BPJS − PPh21 (resolver sudah dibangun)
- [x] Slip Gaji Digital PDF — sudah ada controller/template untuk Salary Slip dan action download.

### 2. Modul Finance & Accounting (Bulan 4)
- [x] Chart of Accounts — `ChartOfAccount`
- [x] General Ledger — `JournalEntry` + `JournalEntryLine`
- [~] AP & AR
  - [x] AP via `VendorBill` + `VendorBillAutoCreationService`
  - [~] AR hanya `StudentInvoice` (khusus siswa); **AR umum non-student belum ada**
- [~] Automatic Journaling
  - [x] Procurement → VendorBill draft journal (`VendorBillAutoCreationService::postDraftJournal`)
  - [x] Payroll → journal (sudah diimplementasikan via `PayrollJournalService`)
  - [ ] Stock-out → journal (belum, karena modul Inventory belum ada)
- [ ] Laporan Keuangan Standar (Laba Rugi, Neraca, Arus Kas) — tidak terlihat sebagai Filament resource/page

### 3. Modul Inventory & Procurement (Bulan 5)
- [ ] **Warehouse Management — gap terbesar.** Modul Inventory/Warehouse belum ada sama sekali. Tidak ada `Warehouse`, `StockItem`, `StockMove`, valuation FIFO/LIFO/Average
- [ ] Stock Move & Adjustment — N/A
- [x] Purchase Order — full chain: `PurchaseRequisition` → `RequestForQuotation` → `PurchaseOrder` → `GoodsReceipt` → `VendorBill`
- [x] Supplier Management — `Vendor`

### Integrasi Workflow & SOP
- [~] Approval Gate
  - [x] Procurement (`PurchaseRequisition`) & Finance (`Budget`) sudah terintegrasi Workflow V2 + RelationManager
  - [x] HRM (`LeaveRequest`, lembur, `SalarySlip`) sudah terintegrasi
- [ ] Evidence Requirement (upload bukti sebelum tombol Approve aktif) — fitur ini belum ada di Workflow V2
- [~] Audit Trail — `Monitoring\AuditLog` custom ada, tapi belum konsisten dipasang per-transaksi

### Tantangan Teknis (Inter-Module Communication)
- [~] Event Listener — baru ada satu pasang: `PurchaseRequisitionApproved` → `CreateRfqFromApprovedPurchaseRequisition`
- [x] PDF library — `barryvdh/laravel-dompdf` terpasang
- [x] Rule-Based Approval Limit — `RuleEngine` (JsonLogic) di Workflow V2 ada & mendukung limit nominal; seeder workflow default (mis. PO > 10 jt → Direktur) juga dikerjakan sebagian (`SetupHrmWorkflowsCommand`).

---

## Urutan Kerja yang Direkomendasikan

### Sprint 1 — HRM matang (semua quick wins) [SELESAI]

#### 1.1 Payroll Calculation Service [x]
- Buat `Modules/Employee/app/Services/PayrollCalculationService.php`
- Resolver `PayrollComponent.formula` (fixed / percentage / expression) — pertimbangkan re-use JsonLogic atau `symfony/expression-language`
- Agregat `AttendanceLog` stats per periode (hadir, terlambat, lembur)
- Artisan command: `employee:generate-payroll --tenant= --month= --year= --dry-run`

#### 1.2 Slip Gaji PDF [x]
- Ikuti pola `Modules/School/app/Http/Controllers/ReportCardController.php`
- Blade template `employee::salary-slip-pdf`
- Action "Download PDF" di `ViewSalarySlip` page

#### 1.3 Auto-Journal Payroll [x]
- Setelah SalarySlip di-mark Paid → trigger journal entry (Dr Beban Gaji / Cr Kas/Bank/Hutang Gaji)
- Pola: contek `Modules/Procurement/app/Services/VendorBillAutoCreationService::postDraftJournal`
- Mapping `PayrollComponent.coa_id` untuk fleksibilitas akun beban

#### 1.4 HRM Workflow Integration [x]
- Duplikasi `WorkflowInstancesRelationManager` dari Procurement ke `LeaveRequest`, lembur, `SalarySlip`
- Seeder workflow definition: cuti > 3 hari → Manager + HR; lembur > 4 jam → Manager

#### 1.5 GPS / Foto Attendance (tanpa mobile app dulu) [x]
- Migration: kolom `check_in_latitude`, `check_in_longitude`, `check_in_photo_path`, idem untuk check_out
- Form Filament dengan geolocation HTML5 + file upload
- Validasi radius (opsional, via `TenantSetting`)

#### 1.6 Dokumen Digital Karyawan [x]
- RelationManager `EmployeeDocument` di `Employee` resource
- Tipe dokumen: KTP, NPWP, Ijazah, Kontrak (enum), file upload dengan visibility private

### Sprint 2 — Finance lengkap

#### 2.1 Laporan Keuangan Standar
- Service `FinancialReportService`: P&L, Neraca, Arus Kas
- Agregasi `JournalEntryLine` per `ChartOfAccount.type` (asset/liability/equity/revenue/expense)
- Filament page per laporan + export PDF/Excel
- Filter periode + organization

#### 2.2 AR Umum (non-student)
- Keputusan arsitektur: polymorphic `Invoice` vs `CustomerInvoice` paralel
- Rekomendasi: model `Invoice` baru dengan `invoiceable` polymorphic; `StudentInvoice` jadi sub-type
- Migration + backfill data lama

#### 2.3 Evidence Requirement di Workflow
- Extend `WorkflowStep` dengan field `required_evidence` (array: file_count, types, label)
- Gate `WorkflowEngine::advance()` cek attachment terlampir
- UI: zone upload di `WorkflowInstancesRelationManager`

#### 2.4 Seeder Workflow Approval Limit
- Workflow default: PO > 10 jt → Direktur; PO > 50 jt → Direktur + Komisaris
- Pakai `RuleEngine` JsonLogic condition pada transition
- Artisan command: `fos:workflow:setup-approval-limits --tenant=`

### Sprint 3 — Modul Inventory MVP (modul baru)

#### 3.1 Skeleton Modul
- `php artisan module:make Inventory`
- Models: `Warehouse`, `StockItem`, `StockLevel` (per warehouse × item), `StockMove`, `StockAdjustment`
- Migration `tenant_id` + `organization_id` di semua tabel

#### 3.2 Valuation
- Field `valuation_method` enum (FIFO/LIFO/AVG) per item atau per warehouse
- Service `StockValuationService` — hitung COGS saat stock-out

#### 3.3 Integrasi Procurement
- Listener: `GoodsReceiptConfirmed` → buat `StockMove` (stock-in) ke `Warehouse` tujuan
- Update `StockLevel` atomik (lock for update)

#### 3.4 Integrasi Finance
- Stock-in → Dr Persediaan / Cr GR/IR
- Stock-out → Dr COGS / Cr Persediaan
- Auto-journal via listener `StockMoveCommitted`

#### 3.5 Stock Adjustment + Workflow
- Form adjustment dengan reason enum
- Wajib approval (Workflow V2) untuk adjustment > threshold

---

## Backlog / Tunda

- **Integrasi mesin absensi fisik** (Fingerspot/Solution X100 via SDK atau push HTTP) — tunda sampai ada pilot tenant yang meminta. Butuh hardware testing 1–2 minggu.
- **Mobile app absensi** — di luar scope ERP web; ditangani sebagai project terpisah.
- **AR aging & dunning automation** — setelah AR umum siap.

---

## Definition of Done per Item

Setiap item Sprint dianggap selesai bila:
1. Feature test PHPUnit lulus (happy path + minimal 1 edge case + 1 failure path)
2. Label UI lewat `FilamentUi` (id/en); frasa baru sudah ada di `PHRASES`/`WORDS`
3. `php artisan shield:generate --all --panel=admin --option=permissions --no-interaction` jika ada resource baru
4. `vendor/bin/pint --dirty --format agent` bersih
5. Inter-module event listener punya test integrasi (untuk Sprint 3 khususnya)
6. Auto-journal punya assertion `assertDatabaseHas(JournalEntryLine::class, ...)` dengan jumlah debit = kredit
