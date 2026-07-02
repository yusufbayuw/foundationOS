# 15 — Business Rules

## Ringkasan Singkat

Aturan bisnis berikut diekstrak dari **service classes**, **validators**, **middleware**, dan **model methods** — bukan dari dokumen kebijakan organisasi.

Format: **BR-{MOD}-{NN}** | Aturan | Bukti | Keyakinan

---

## Tenancy & Akses

| ID | Aturan | Bukti |
|----|--------|-------|
| BR-CORE-01 | Query model `BelongsToTenant` otomatis filter `tenant_id` aktif | `TenantScope.php:22-57` |
| BR-CORE-02 | Jika tenant tidak terselesaikan dan `scope_fail_closed=true`, query gagal | `config/tenancy.php` |
| BR-CORE-03 | Admin panel: user harus punya `user_tenant_roles` kecuali global super admin | `User.php:471-476` |
| BR-CORE-04 | Tenant terkunci (`isLocked`) diarahkan ke billing | `EnsureTenantSubscriptionActive.php` |
| BR-CORE-05 | Global super admin bypass semua Gate | `AppServiceProvider.php:93-98` |

---

## Workflow

| ID | Aturan | Bukti |
|----|--------|-------|
| BR-WF-01 | Resolver pilih workflow paling spesifik: tenant+org+subject > tenant-wide | `DatabaseWorkflowResolver.php:37-44` |
| BR-WF-02 | Instance menyimpan snapshot definisi workflow saat start | `DatabaseWorkflowInstanceStarter.php:45-67` |
| BR-WF-03 | Hanya assignee dengan pending assignment boleh advance (kecuali global super admin) | `DatabaseWorkflowEngine.php:416-430` |
| BR-WF-04 | Transisi dievaluasi dengan JSONLogic; rules kosong = unconditional | `JsonLogicWorkflowTransitionResolver.php:69-78` |
| BR-WF-05 | Action `reject` → status Rejected; `cancel` → Cancelled | `DatabaseWorkflowEngine.php:433-447` |
| BR-WF-06 | Parallel step: quorum coordinator menentukan lanjut/tunggu | `DatabaseWorkflowEngine.php:158-246` |

---

## Finance

| ID | Aturan | Bukti |
|----|--------|-------|
| BR-FIN-01 | Invoice berstatus issued tidak dapat dimutasi | `FinanceControlService.php:26-28` |
| BR-FIN-02 | Pembayaran verified memicu jurnal otomatis | `FinanceControlService.php:52-70` |
| BR-FIN-03 | Jurnal tidak boleh diposting jika debit ≠ kredit | `FinanceControlService.php:139-141` |
| BR-FIN-04 | Status invoice: draft/issued/partial/paid berdasarkan pembayaran terverifikasi | `FinanceControlService.php:196-201` |
| BR-FIN-05 | Akun piutang wajib dari `tenant_settings` group `finance` | `FinanceControlService.php:271-284` |

---

## Procurement

| ID | Aturan | Bukti |
|----|--------|-------|
| BR-PROC-01 | Goods receipt harus milik PO yang sama | `ThreeWayMatchValidator.php:30-32` |
| BR-PROC-02 | `billed_qty ≤ received_qty ≤ ordered_qty` | `ThreeWayMatchValidator.php:54-60` |
| BR-PROC-03 | Harga unit vendor bill dalam toleransi ±5% dari harga PO | `ThreeWayMatchValidator.php:62-67` |
| BR-PROC-04 | RFQ sudah awarded ke vendor lain → exception | `PurchaseOrderAutoCreationService.php:34-36` |
| BR-PROC-05 | PO draft untuk RFQ+vendor yang sama bersifat idempotent | `PurchaseOrderAutoCreationService.php:38-47` |

---

## Enrollment

| ID | Aturan | Bukti |
|----|--------|-------|
| BR-ENR-01 | Auto-promote applicant gated by setting `enrollment.auto_promote_accepted_applicant` (default true) | `ApplicantPromotionService.php:17-30` |
| BR-ENR-02 | Skip promote jika `converted_to_student_id` sudah terisi | `ApplicantPromotionService.php:34-36` |
| BR-ENR-03 | Lead baru: stage `new`, follow-up +1 hari | `LeadInquiryService.php:29-39` |
| BR-ENR-04 | Konversi lead: stage `applied`, set `converted_at` | `LeadInquiryService.php:52-58` |
| BR-ENR-05 | NIS siswa baru digenerate unik | `ApplicantPromotionService.php:73-87` |

---

## School

| ID | Aturan | Bukti |
|----|--------|-------|
| BR-SCH-01 | Konflik jadwal dicek saat penyimpanan schedule | `ScheduleConflictChecker.php` |
| BR-SCH-02 | Rekap absensi dihitung via service | `AttendanceRecapService.php` |

---

## API

| ID | Aturan | Bukti |
|----|--------|-------|
| BR-API-01 | Rate limit 60 request/menit per token atau IP | `routes/api.php:24-28` |
| BR-API-02 | POST applicants/payments/leave-requests mendukung idempotency key | `routes/api.php:68-72`, `IdempotencyKey` |

---

## Authorization (Shield)

| ID | Aturan | Bukti |
|----|--------|-------|
| BR-AUTH-01 | Permission Spatie scoped per `tenant_id` (teams) | `config/permission.php:100,138` |
| BR-AUTH-02 | Role `super_admin` Shield per tenant via provisioner | `TenantAdminProvisioner.php:15-34` |
| BR-AUTH-03 | Domain role `owner` punya permissions `['*']` | `TenantAdminProvisioner.php:38-51` |

---

## Exception Flow (Terbukti)

| Rule cluster | Exception | Bukti |
|--------------|-----------|-------|
| BR-WF-03 | `authorizeActor` gagal → advance ditolak | `DatabaseWorkflowEngine` |
| BR-PROC-01–03 | `ValidationException` / domain exception | `ThreeWayMatchValidator` |
| BR-FIN-01 | Mutasi invoice issued → exception | `FinanceControlService` |

---

## Aturan TIDAK TERDETEKSI

| Domain | Status |
|--------|--------|
| Aturan grading bobot penilaian K-12 | PARSIAL — model assessment ada |
| Formula payroll lengkap | TIDAK TERDETEKSI service terpusat |
| Aturan akreditasi Education QA | PARSIAL — CRUD only |

---

## Catatan Ketidakpastian

- Business rules di Filament form validation (`->rules()`) per resource: **tidak diaudit per 257 resource**.
- Rules di policy classes: pola `return $user->can(...)` — detail per resource di `authorization-matrix.json`.
