# ROADMAP v0.3 — Fase 3 BI, Reporting, Audit & Edutech Polish

Audit gap Fase 3 (Global Dashboard, Reporting Engine, Global Search, Audit Trail, Inter-Module Bridging) + Edisi Pendidikan terhadap codebase saat ini, beserta urutan kerja yang direkomendasikan.

---

## Status Audit Fase 3

### 1. Global Dashboard & BI
- [x] Executive Summary — tab **Executive** di `TabbedDashboard` + `ExecutiveStatsOverview` via `ExecutiveMetricsService`
- [x] Real-time Analytics — 4 chart widget time-series (Revenue, Payroll, AR/AP, Workflow) dengan cache 5 menit
- [ ] Custom Widget Builder — **tunda** (backlog ROADMAP)

### 2. Centralized Reporting Engine
- [x] Cross-Module Reporting — `CrossModuleReportService` + halaman `CostEfficiencyReportPage`
- [x] Export Center — `ExportCenterPage` (re-download, retry)
- [x] Scheduled Reports — `reports:weekly-summary` + schedule Senin 07:00 di `routes/console.php`

### 3. Global Search & Command Center
- [x] Deep Search — 10 resource utama + trait `ConfiguresGlobalSearch`
- [~] Keyboard Shortcuts — shortcut bawaan Filament v5; kustomisasi spotlight belum

### 4. Advanced Audit Trail & Security
- [x] Audit Trail Explorer — diff renderer `components/audit-diff` di view Audit Log
- [x] Security Logs — listener `LogSecurityAuthEvents` + filter kategori `security`
- [ ] Database Archiving — **tunda** (backlog)

### 5. Sinkronisasi Antar-Modul (Bridging)
- [x] Inventory → Finance — modul Inventory + integrasi procurement (ROADMAPv02)
- [ ] HR → Finance — auto-journal payroll belum ada (PayrollService belum dibangun — lihat ROADMAPv02 Sprint 1.3)
- [~] Sales/POS → Finance & Inventory — modul **Sales** (migration + model skeleton); POS UI & auto-journal listener belum lengkap
- [x] School (SPP) → Finance — `Student` morphMany `StudentInvoice`, `TuitionType` + `Payment` aktif

### Langkah Teknis Penting
- [x] Laravel Pulse — terpasang; gate `viewPulse` untuk global super-admin
- [x] Database Indexing — migration `2026_05_23_210000_phase3_audit_category_and_performance_indexes`

---

## Status Audit Edisi Pendidikan (Edutech)

- [x] SIAKAD K-12 — `Curriculum`, `Subject`, `SchoolClass`, `Teacher`, `Student`, `Schedule`, `Attendance`, `Assessment`, `StudentGrade`
- [x] SIAKAD Higher-Ed — `Faculty`, `StudyProgram`, `Course`, `Lecturer`, `CollageStudent`, `StudyPlan`, `Thesis`, `StudyResult`
- [x] Student Ledger bridge — `Student` ↔ `StudentInvoice` ↔ `Payment` ↔ `TuitionType`
- [x] SPP otomatis — `school:generate-monthly-tuition`, `school:apply-late-fees`, `RegistrationFeeInstallmentService`
- [~] LMS Lite — **tidak ada LMS native**; integrasi **Moodle** matang (`MoodleClient`, `MoodleOutboxService`, `MoodleSyncService`, `MoodleEnrollmentReconciler`, `MoodleDetailedGradePullService`)
- [x] Raport — workflow revisi nilai (`student-grade-revision`) + `StudentGradeObserver` + RelationManager workflow

---

## Urutan Kerja yang Direkomendasikan

### Sprint 1 — Fase 3 Quick Wins

#### 1.1 Aktivasi Global Search per Resource
- Tambahkan `getGloballySearchableAttributes()` di 10 resource utama: Student, CollageStudent, Employee, StudentInvoice, PurchaseOrder, Vendor, JournalEntry, Book, Course, Tenant
- `getGlobalSearchResultDetails()` untuk konteks (mis. tenant + status)
- Test: `Livewire::test(GlobalSearch::class)`

#### 1.2 Laravel Pulse
- `composer require laravel/pulse`
- Route `/pulse` di-protect dengan policy super-admin
- Dashboard widget: slow queries, queue throughput, exceptions per modul

#### 1.3 Executive Dashboard Tab
- Tambah tab "Executive" di `TabbedDashboard` (super-admin / direktur only)
- 6 widget cross-modul: Revenue MTD, Payroll MTD, Outstanding AP, Outstanding AR (StudentInvoice), Active Tenants, Pending Approvals
- Widget berdiri di atas service `ExecutiveMetricsService` (single source)

#### 1.4 Security Logs
- Listener untuk `Illuminate\Auth\Events\Failed`, `Lockout`, `Login` (sensitive resource access)
- Tulis ke `Monitoring\AuditLog` dengan kategori `security`
- Filter di `AuditLogs` resource: tab "Security Events"

#### 1.5 Database Index Audit
- Review migration tabel besar: `journal_entry_lines`, `attendance_logs`, `moodle_sync_outbox`, `audit_logs`, `salary_slip_components`, `student_grades`
- Tambah composite index `(tenant_id, created_at)` + index sesuai query pattern Filament
- Bukti: jalankan `EXPLAIN` via `database-query` tool sebelum/sesudah

### Sprint 2 — BI & Reporting Engine

#### 2.1 Cross-Module Reporting
- `CrossModuleReportService` dengan method:
  - `costEfficiency(period)` — Payroll vs Revenue
  - `profitabilityByStudyProgram(period)` — StudentInvoice paid vs cost allocation
  - `procurementBudgetVariance(period)` — PO actual vs Budget plan
- Filament page per laporan (`/admin/reports/...`)

#### 2.2 Real-time Chart Widgets
- 4 chart widget time-series pakai `Filament\Widgets\ChartWidget`
- Cache result 5 menit via `Cache::remember`
- Filter periode (last 7/30/90 days)

#### 2.3 Scheduled Reports
- Artisan command `reports:weekly-summary --tenant=`
- Register di `app/Console/Kernel.php` (Mon 07:00)
- Mail dengan PDF attachment (dompdf) ke admin tenant
- Setting per tenant: opt-in & email recipients (di `TenantSetting`)

#### 2.4 Export Center
- Filament page list table `exports` (built-in Filament) per tenant
- Action: Re-download, Retry failed
- Notification on completion via `databaseNotifications` (sudah aktif di panel)

### Sprint 3 — Audit & Edutech Polish

#### 3.1 Audit Trail Explorer + Diff
- Pastikan `audit_logs` punya kolom `old_values`, `new_values` (json) — kalau belum, tambah migration
- View page `AuditLog` dengan diff renderer (gunakan `jfcherng/php-diff` atau diff Blade component sederhana)
- Filter: tanggal, user, model, action

#### 3.2 Workflow Approval Revisi Nilai Raport
- Duplikasi pola Procurement → trait + RelationManager `WorkflowInstances` di `StudentGrade`
- Trigger workflow saat `StudentGrade` di-update setelah `posted_at`
- Evidence Requirement (lihat ROADMAPv02 Sprint 2.3) untuk catatan revisi
- Seeder workflow definition: revisi nilai → wali kelas → kepala sekolah

#### 3.3 SPP Auto-Generate Scheduler
- Artisan command `school:generate-monthly-tuition --tenant= --month=`
- Generate `StudentInvoice` per active student berdasarkan `TuitionType` aktif
- Cicilan uang pangkal: service `RegistrationFeeInstallmentService`
- Denda keterlambatan: scheduled command harian cek due date + auto add late fee item
- Register di `Kernel`

### Sprint 4 — Modul Sales/POS (untuk klien general/non-edu)

**Prasyarat:** Inventory MVP selesai (ROADMAPv02 Sprint 3).

#### 4.1 Skeleton
- `php artisan module:make Sales`
- Models: `Customer`, `SalesOrder`, `SalesOrderItem`, `Invoice` (umum / non-student), `InvoicePayment`

#### 4.2 POS Module
- Models: `POSTerminal`, `POSSession`, `POSTransaction`, `POSTransactionItem`
- Filament page POS UI (Livewire fullscreen): scan barcode, qty, cash/QR payment
- Print receipt (dompdf 80mm template)

#### 4.3 Auto-Journal & Stock Bridge
- Listener `SalesOrderConfirmed` → reserve stock
- Listener `InvoicePaid` → Dr Kas/Bank Cr Penjualan + Dr COGS Cr Persediaan
- Listener `POSTransactionCompleted` → idem instan (B2C)

---

## Backlog / Tunda

- **LMS Lite in-house** — Moodle integration sudah matang dan production-grade. Build in-house LMS adalah 4–6 minggu effort dengan ROI rendah. **Skip.**
- **Custom Widget Builder UI** — high effort (drag-drop, layout state), low priority. Pertimbangkan setelah ada permintaan eksplisit dari tenant.
- **Database Archiving** — tunda sampai tenant pertama punya >2 tahun data atau ada keluhan performa nyata. Pulse (Sprint 1.2) akan jadi indikator kapan ini perlu.

---

## Target Akhir Fase 3 (Milestone)

- **Data Integrity** — semua bridge auto-journal sudah ada test integrasi: Procurement (✅ sudah), Payroll (target ROADMAPv02), Inventory (target ROADMAPv02), Sales/POS (target Sprint 4 di sini).
- **Ready for Beta** — Sprint 1–3 selesai = stabil untuk klien edu pilot. Sprint 4 selesai = stabil untuk klien general pilot.

---

## Definition of Done per Item

1. Feature test PHPUnit lulus (happy + minimal 1 edge + 1 failure path)
2. Label UI lewat `FilamentUi` (id/en)
3. `php artisan shield:generate --all --panel=admin --option=permissions --no-interaction` jika ada resource baru
4. `vendor/bin/pint --dirty --format agent` bersih
5. Untuk widget/report: query di-cache + ada index pendukung
6. Untuk bridge (Sprint 4): assertion `assertDatabaseHas(JournalEntryLine::class, …)` dengan debit = kredit + assertion stock level berubah
7. Untuk Workflow integration (Sprint 3.2): test transition happy + reject path
