# FoundationOS

FoundationOS adalah fondasi aplikasi SaaS modular berbasis Laravel, Filament, dan sistem module. Arsitekturnya dirancang untuk shared-database multi-tenancy, sehingga satu instance aplikasi dapat melayani banyak tenant dengan boundary domain yang tetap jelas.

## Setup Cepat

Urutan setup minimal untuk menjalankan aplikasi:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
```

Jika ingin verifikasi fondasi aplikasi setelah setup:

```bash
php artisan test
```

Panduan setup Moodle terpisah tersedia di:

- [MOODLE.md](/Users/yusuf/Herd/foundationOS/MOODLE.md)
- [MOODLE_HARDENING_CHECKLIST.md](/Users/yusuf/Herd/foundationOS/MOODLE_HARDENING_CHECKLIST.md)
- [LIBRARY.md](/Users/yusuf/Herd/foundationOS/LIBRARY.md)
- [WORKFLOW.md](/Users/yusuf/Herd/foundationOS/WORKFLOW.md)

## Konfigurasi Dasar Aplikasi

Beberapa keputusan konfigurasi penting pada project ini:

- auth model utama memakai `Modules\Core\Models\User`
- panel admin Filament berada di path `admin`
- multi-tenancy saat ini memakai `shared database`, bukan database-per-tenant
- semua resource Filament modular didiscover dari folder `Modules/*/app/Filament`
- `app/Models/User.php` hanya bridge untuk kompatibilitas Laravel ecosystem

File yang paling penting untuk dicek saat onboarding:

- [config/auth.php](/Users/yusuf/Herd/foundationOS/config/auth.php)
- [app/Providers/Filament/AdminPanelProvider.php](/Users/yusuf/Herd/foundationOS/app/Providers/Filament/AdminPanelProvider.php)
- [config/filament-modules.php](/Users/yusuf/Herd/foundationOS/config/filament-modules.php)
- [config/filament-shield.php](/Users/yusuf/Herd/foundationOS/config/filament-shield.php)
- [config/permission.php](/Users/yusuf/Herd/foundationOS/config/permission.php)

## Konfigurasi Multi-Tenancy MVP

Project ini belum memakai package tenancy terpisah seperti `stancl/tenancy` atau `spatie/laravel-multitenancy`. Untuk MVP, tenancy dijalankan secara manual dengan pola berikut:

- `Tenant` adalah boundary utama akun SaaS
- `User` bersifat global dan dapat bergabung ke banyak tenant
- assignment akses user ke tenant diatur melalui `user_tenant_roles`
- seluruh data operasional wajib membawa `tenant_id`
- data unit/lembaga tertentu juga membawa `organization_id`
- panel Filament memakai tenant model `Modules\Core\Models\Tenant`

Konsekuensi praktisnya:

- sebelum panel admin bisa dipakai penuh, harus ada minimal `1 tenant`
- user harus punya assignment ke tenant tersebut
- query dan policy aplikasi harus tenant-aware

## Konfigurasi Filament

Panel utama project ini:

- panel id: `admin`
- URL: `/admin`
- tenant model: `Modules\Core\Models\Tenant`

Panel ini sekarang mendiscover:

- resource utama app dari `app/Filament/Resources`
- resource modular dari `Modules/*/app/Filament/Resources`
- pages dan widgets modular dari setiap module aktif

Jika setelah menambah resource baru panel belum membaca perubahan, jalankan:

```bash
php artisan optimize:clear
```

### Import Data dan Template CSV

Semua table resource sekarang memiliki dua tombol di header:

- `Import Data` (menggunakan Filament `ImportAction`)
- `Download Template` (mengunduh template CSV sesuai kolom importer)

Implementasi utama:

- base importer dinamis: [app/Filament/Imports/BaseModelImporter.php](/Users/yusuf/Herd/foundationOS/app/Filament/Imports/BaseModelImporter.php)
- helper action tombol import/template: [Modules/Core/app/Filament/Support/ImportTableActions.php](/Users/yusuf/Herd/foundationOS/Modules/Core/app/Filament/Support/ImportTableActions.php)

Prasyarat migration import Filament yang sudah dipasang:

- `create_notifications_table`
- `create_imports_table`
- `create_exports_table`
- `create_failed_import_rows_table`

### Bilingual Resource UI

Semua label Filament resource sekarang mengikuti locale aplikasi aktif:

- `id` untuk bahasa Indonesia
- `en` untuk bahasa Inggris

Source of truth untuk label dan ikon resource ada di:

- [Modules/Core/app/Support/FilamentUi.php](/Users/yusuf/Herd/foundationOS/Modules/Core/app/Support/FilamentUi.php)
- [Modules/Core/app/Filament/Support/ModuleResource.php](/Users/yusuf/Herd/foundationOS/Modules/Core/app/Filament/Support/ModuleResource.php)

Aturan yang dipakai:

- `modelLabel`, `pluralModelLabel`, `navigationLabel`, dan field label tidak lagi hardcoded di resource generator
- ikon navigasi dibedakan per resource/domain agar panel lebih mudah dipindai
- top navigation dimatikan untuk menghindari menu atas yang terlalu penuh

Kalau nanti kita menambah resource baru, ikuti pola:

- extend `Modules\Core\Filament\Support\ModuleResource`
- gunakan label field dari `Modules\Core\Support\FilamentUi`
- jangan tambahkan trash action kalau model tidak memakai `SoftDeletes`

## Konfigurasi Filament Shield

Shield sudah bisa berjalan pada project ini, dengan beberapa syarat konfigurasi berikut:

- package `bezhansalleh/filament-shield` terpasang
- `spatie/laravel-permission` ikut terpasang sebagai dependency
- user model utama memakai trait `HasRoles`
- config Shield diarahkan ke `Modules\Core\Models\User`
- mode `teams` di Spatie Permission aktif
- `team_foreign_key` menggunakan `tenant_id`
- tenant model Shield diarahkan ke `Modules\Core\Models\Tenant`

Konfigurasi yang dipakai saat ini:

- [Modules/Core/app/Models/User.php](/Users/yusuf/Herd/foundationOS/Modules/Core/app/Models/User.php)
- [config/filament-shield.php](/Users/yusuf/Herd/foundationOS/config/filament-shield.php)
- [config/permission.php](/Users/yusuf/Herd/foundationOS/config/permission.php)

Langkah setup Shield yang direkomendasikan:

```bash
php artisan shield:setup --tenant=Modules\\Core\\Models\\Tenant
php artisan shield:generate --all --panel=admin --relationships
```

Keterangan:

- `shield:setup` menyiapkan fondasi Shield
- `shield:generate` membuat policy, permission, dan relationship permission untuk entity Filament di panel `admin`
- opsi `--relationships` penting karena panel ini memakai tenancy

Status setup saat ini di project:

- migration permission tables sudah ada
- permission berhasil tergenerate untuk resource modular
- policy Shield berhasil tergenerate untuk entity panel admin

### Runbook Shield Per Tenant

Untuk modul Workflow dan modul lain yang tenant-aware, pola operasional yang disarankan:

1. pastikan user sudah menjadi member tenant melalui `user_tenant_roles`
2. pilih tenant aktif di panel admin
3. generate ulang permission jika ada resource/page baru
4. assign role Shield pada tenant aktif
5. verifikasi page/resource muncul di sidebar tenant tersebut

Command yang paling sering dipakai:

```bash
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
php artisan optimize:clear
```

Kalau ingin membuat tenant super admin berbasis Shield:

```bash
php artisan shield:super-admin --user=1 --tenant=1 --panel=admin
```

Catatan:

- Shield adalah source of truth authorization panel
- `user_tenant_roles` tetap source of truth membership tenant/organization
- keduanya harus sama-sama benar agar menu tenant-aware tampil normal

## Workflow V2

FoundationOS sekarang memiliki modul `Workflow` V2 dengan pendekatan metadata-driven:

- Rule Engine terpisah dari Workflow Engine
- form runtime dibangun dari `form_schema`
- inbox/worklist dipisahkan dari engine
- snapshot workflow bersifat immutable per instance
- automated actions tersedia sebagai hook database-driven

Dokumentasi lengkap ada di:

- [WORKFLOW.md](/Users/yusuf/Herd/foundationOS/WORKFLOW.md)

### Ringkasan Operasional

Halaman operasional yang tersedia:

- `Workflow Worklist`
- `Workflow My Tasks`
- `Workflow Team Inbox`
- `Workflow Task History`

Pilot pertama saat ini adalah Procurement Purchase Requisition.

Setup cepat workflow pilot Procurement:

```bash
php artisan fos:workflow:setup-procurement-pilot 1 --manager=10 --finance=11 --executive=12 --finance-threshold=10000000 --executive-threshold=50000000
```

Setelah workflow aktif:

- buka Purchase Requisition
- klik `Start Approval Workflow`
- approver memproses dari worklist atau halaman workflow instance

Untuk setup, tutorial penggunaan, dan troubleshooting lengkap, gunakan:

- [WORKFLOW.md](/Users/yusuf/Herd/foundationOS/WORKFLOW.md)

## Role dan Permission Tenant

Karena Spatie Permission dipakai dalam mode teams, role dan permission bersifat tenant-aware.

Artinya:

- role yang sama dapat eksis dalam konteks tenant berbeda
- assignment role ke user perlu dilakukan dalam konteks tenant aktif
- user tidak cukup hanya ada di tabel `users`, tetapi juga harus masuk ke boundary tenant aplikasi

Langkah minimal setelah install:

1. Buat tenant pertama.
2. Pastikan user admin sudah ada.
3. Assign user ke tenant tersebut.
4. Buat atau assign role Shield pada tenant itu.

Contoh assign super admin tenant:

```bash
php artisan shield:super-admin --user=1 --tenant=1 --panel=admin
```

Nilai `--user` dan `--tenant` harus disesuaikan dengan data yang ada di database.

## Catatan Penting Shield di Project Ini

- `TenantRole` dan `UserTenantRole` di domain `Core` bukan pengganti Spatie Permission; keduanya adalah layer domain membership aplikasi.
- Shield + Spatie Permission dipakai untuk authorization panel dan action-level permission.
- `user_tenant_roles` tetap dibutuhkan untuk menentukan user tergabung ke tenant/organization mana.
- role Shield menentukan apa yang boleh dilakukan user di panel.

Singkatnya:

- `user_tenant_roles` menjawab: user ini masuk tenant mana?
- `roles/permissions` dari Shield menjawab: user ini boleh melakukan apa di tenant tersebut?

## Global Super Admin

Project ini mendukung `global super-admin` yang dapat:

- mengakses seluruh tenant tanpa assignment manual di `user_tenant_roles`
- melewati authorization gate untuk kebutuhan administrasi platform

Implementasinya memakai flag `users.is_super_admin`.

### Buat atau Promote Global Super Admin

Gunakan command berikut:

```bash
php artisan foundation:make-super-admin admin@example.com
```

Opsional dengan nama, username, dan password:

```bash
php artisan foundation:make-super-admin admin@example.com --name="Platform Admin" --username="platform_admin" --password="StrongPassword123!"
```

Perilaku command:

- jika user belum ada, command akan membuat user baru dan menandai sebagai global super-admin
- jika user sudah ada, command akan mempromosikan user tersebut menjadi global super-admin
- jika membuat user baru tanpa `--password`, sistem akan generate password sementara

## Integrasi Moodle (Observer + Outbox + Queue)

Project ini mendukung sinkronisasi satu arah dari FOS ke Moodle dengan pola:

- `Eloquent Observer -> Outbox -> Queue Job -> Moodle Web Service`
- target near real-time (drain outbox tiap menit)
- idempotent dengan `dedupe_key`
- soft delete diterjemahkan menjadi nonaktif/suspend, bukan hard delete

### Scope V1

- `User` (`Modules\Core\Models\User`)
- `Course` (`Modules\Campus\Models\Course`)
- Enrollment dari `Student + ClassStudent` (`Modules\School\Models\Student`, `Modules\School\Models\ClassStudent`)

### Tabel Integrasi

- `moodle_sync_outbox`
- `moodle_entity_mappings`
- `moodle_class_course_mappings`

### Konfigurasi Environment

Tambahkan variabel berikut di `.env`:

```bash
MOODLE_SYNC_ENABLED=true
MOODLE_SYNC_READONLY=false
MOODLE_BASE_URL=https://moodle.test
MOODLE_WS_TOKEN=your_token_here
MOODLE_WS_FORMAT=json
MOODLE_TIMEOUT=15
MOODLE_VERIFY_SSL=true
MOODLE_CATEGORY_PARENT_ID=
MOODLE_ENROL_ROLE_ID=5
MOODLE_COHORT_SYNC_ENABLED=true
MOODLE_ROLE_STUDENT=5
MOODLE_ROLE_TEACHER=3
MOODLE_ROLE_MANAGER=1
MOODLE_CALENDAR_SYNC_ENABLED=false
MOODLE_LEARNING_PULL_ENABLED=false
MOODLE_ATTENDANCE_PULL_ENABLED=false
MOODLE_SYNC_QUEUE=moodle-sync
MOODLE_SYNC_MAX_ATTEMPTS=7
MOODLE_SYNC_BATCH_LIMIT=100
```

### Prasyarat Moodle

- Web services aktif
- Buat external service + token
- Pastikan token memiliki akses fungsi:
  - `core_webservice_get_site_info`
  - `core_user_create_users`
  - `core_user_update_users`
  - `core_course_create_categories`
  - `core_course_update_categories`
  - `core_course_create_courses`
  - `core_course_update_courses`
  - `enrol_manual_enrol_users`
  - `enrol_manual_unenrol_users`

Fungsi tambahan (opsional) sesuai toggle:

- `MOODLE_COHORT_SYNC_ENABLED=true`:
  - `core_cohort_get_cohorts` atau `core_cohort_search_cohorts`
  - `core_cohort_create_cohorts`
  - `core_cohort_add_cohort_members`
- `MOODLE_CALENDAR_SYNC_ENABLED=true`:
  - `core_calendar_create_calendar_events`
- `MOODLE_LEARNING_PULL_ENABLED=true`:
  - `gradereport_overview_get_course_grades`
  - `core_completion_get_course_completion_status`
  - `core_completion_get_activities_completion_status`
- `MOODLE_ATTENDANCE_PULL_ENABLED=true` (plugin attendance):
  - `mod_attendance_get_courses_with_today_sessions` atau `mod_attendance_get_user_absences`

Untuk bootstrap cepat function token service, jalankan:

```bash
php scripts/setup_moodle_foundation_service.php
```

Script akan menautkan function integrasi yang tersedia di Moodle Anda ke external service token.
Jika plugin attendance belum terpasang, function attendance akan dilewati otomatis.

### Command Operasional

```bash
php artisan fos:moodle:health-check
php artisan fos:moodle:backfill all --tenant=1
php artisan fos:moodle:drain-outbox --limit=100
php artisan fos:moodle:retry-failed --limit=100
php artisan fos:moodle:sync-cohorts --tenant=1
php artisan fos:moodle:sync-calendar --tenant=1 --limit=100
php artisan fos:moodle:pull-grades --tenant=1
php artisan fos:moodle:pull-progress --tenant=1 --limit=200
php artisan fos:moodle:pull-attendance --tenant=1 --limit=100
php artisan fos:moodle:reconcile all --dry-run
php artisan fos:moodle:reconcile all --fix --limit=500
php artisan fos:moodle:ops-report
```

`drain-outbox` dijadwalkan setiap menit, dan `reconcile --dry-run` setiap jam melalui scheduler Laravel.
Pastikan worker queue dan scheduler aktif:

```bash
php artisan queue:work --queue=moodle-sync,default
php artisan schedule:work
```

### Monitoring Operasional

Gunakan report cepat untuk observability:

```bash
php artisan fos:moodle:ops-report
php artisan fos:moodle:ops-report --json
```

Report mencakup:

- backlog outbox (`pending`, `processing`, `failed`)
- umur item pending tertua
- distribusi gagal per entity
- jumlah snapshot learning pull 24 jam terakhir

### Catatan Enrollment

Enrollment membaca `moodle_class_course_mappings` untuk menentukan kelas FOS (`class_id`) diarahkan ke course Moodle yang mana.
Isi mapping ini sebelum menjalankan backfill enrollment agar sinkronisasi berhasil.

### Catatan Attendance Pull

`pull-attendance` bersifat plugin-aware:

- command cek fungsi attendance yang tersedia dari Moodle site info
- jika fungsi plugin tidak tersedia, proses akan skip dengan warning (bukan gagal total)
- hasil pull disimpan sebagai snapshot di `moodle_learning_metrics`

### Inbound Sync Policy (Moodle -> FOS)

Kebijakan default integrasi bersifat **strict source-of-truth**:

- FOS tetap master data untuk user dan course
- perubahan destruktif dari Moodle tidak langsung menghapus data di FOS
- drift dari Moodle ditangani lewat reconcile dan remediasi ke Moodle

Contoh: user terhapus di Moodle

- `fos:moodle:reconcile --dry-run` akan mendeteksi user FOS aktif yang hilang di Moodle
- `fos:moodle:reconcile --fix` akan enqueue remediasi (`upsert`) ke outbox agar user dipulihkan di Moodle
- jika user FOS sudah nonaktif/soft-delete, reconcile akan menjaga kondisi suspend di Moodle (bukan hard delete)

## Arsitektur Inti

Tiga entitas inti yang menjadi tulang punggung sistem:

- `User`
  User bersifat global. Satu user dapat bergabung ke banyak tenant dan banyak organization melalui pivot `user_tenant_roles`.
- `Tenant`
  Tenant adalah akun SaaS utama. Semua data operasional wajib di-scope minimal dengan `tenant_id`.
- `Organization`
  Organization adalah unit operasional di dalam tenant, misalnya sekolah, kampus, cabang, atau lembaga.

Konsekuensinya:

- `users` tidak memiliki `tenant_id` langsung.
- akses lintas tenant ditentukan oleh `user_tenant_roles`.
- data global seperti `subscription_plans`, `modules`, dan referensi wilayah tidak membawa `tenant_id`.
- data operasional membawa `tenant_id`, dan bila relevan juga `organization_id`.

## Struktur Modul

- `Core`
  Tenancy, organization, roles, academic year/period, module entitlement, dan settings.
- `Global`
  Referensi global seperti negara, provinsi, kota, kecamatan, desa, dan timezone.
- `School`
  Domain sekolah: siswa, guru, kelas, kurikulum, jadwal, penilaian, pelanggaran, prestasi.
- `Campus`
  Domain perguruan tinggi: fakultas, prodi, dosen, mahasiswa, penawaran mata kuliah, KRS, hasil studi, tesis, feeder log.
- `Enrollment`
  Domain penerimaan: admission period, applicant, exam schedule/result, registration.
- `Finance`
  COA, tuition, invoice, payment, journal entry, budget.
- `Library`
  Katalog buku, eksemplar, anggota, peminjaman, denda.
- `Employee`
  Karyawan, jabatan, shift, kontrak, absensi, cuti, payroll, KPI.
- `Procurement`
  Vendor, item pengadaan, requisition, RFQ, PO, goods receipt, vendor bill.
- `Monitoring`
  Audit trail dan file attachment yang bersifat lintas domain.

## Migrasi Legacy SLiMS ke FOS Library

Untuk antisipasi tenant yang sebelumnya sudah memakai SLiMS, tersedia importer khusus per-tenant:

```bash
php artisan fos:library:import-slims --tenant=1 --organization=1 --entity=all --dry-run
php artisan fos:library:import-slims --tenant=1 --entity=all --dry-run
php artisan fos:library:import-slims --tenant=1 --entity=all
php artisan fos:library:import-slims --tenant=1 --entity=all --skip-existing
php artisan fos:library:import-slims --tenant=1 --entity=all --since="2026-03-01 00:00:00"
```

Konfigurasi legacy SLiMS tidak lagi memakai `.env` global.
Jika library berjalan tenant-wide, gunakan `tenant_settings`.
Jika library berjalan per-organization, gunakan `organization_settings`.

Contoh key yang perlu diisi:

```bash
slims_import_enabled=true
slims_db_host=127.0.0.1
slims_db_port=3306
slims_db_database=slims
slims_db_username=root
slims_db_password=
slims_db_prefix=
slims_import_email_domain=slims.local
slims_import_member_status=active
```

Contoh insert cepat untuk mode tenant-wide:

```sql
INSERT INTO tenant_settings (tenant_id, `group`, `key`, `value`, `type`, created_at, updated_at)
VALUES
(1, 'integration', 'slims_import_enabled', 'true', 'boolean', NOW(), NOW()),
(1, 'integration', 'slims_db_host', '127.0.0.1', 'string', NOW(), NOW()),
(1, 'integration', 'slims_db_port', '3306', 'integer', NOW(), NOW()),
(1, 'integration', 'slims_db_database', 'slims', 'string', NOW(), NOW()),
(1, 'integration', 'slims_db_username', 'root', 'string', NOW(), NOW()),
(1, 'integration', 'slims_db_password', '', 'string', NOW(), NOW());
```

Karakteristik importer:

- wajib `--tenant`, sehingga import selalu terikat ke satu tenant
- `--organization` opsional:
  - isi `--organization=ID` untuk mode per-organization
  - kosongkan untuk mode tenant-wide (terpusat)
- bila `--organization` diisi, konfigurasi SLiMS diprioritaskan dari `organization_settings`
- fallback ke `tenant_settings` hanya dipakai untuk mode tenant-wide
- idempotent per tenant melalui tabel mapping `library_slims_mappings`
- bisa dijalankan bertahap per entitas: `catalog`, `members`, `loans`, atau `all`
- aman untuk re-run (update mapping yang sudah ada, tidak duplikasi membabi buta)

Catatan:

- importer ini khusus skenario migrasi tenant legacy; tenant baru tanpa SLiMS tidak perlu menjalankannya
- importer sekarang mendukung mode incremental dengan `--skip-existing` dan `--since`

### Operasional Library

Command operasional yang tersedia:

```bash
php artisan fos:library:recalc-fines
php artisan fos:library:recalc-fines --tenant=1
php artisan fos:library:stock-audit
php artisan fos:library:stock-audit --tenant=1
```

Catatan arsitektur:

- scope Library memakai `tenant-first`
- `organization_id` boleh `null` untuk mode library terpusat lintas organization dalam satu tenant
- policy sirkulasi mengikuti precedence `member -> organization -> tenant -> default`
- OPAC publik tersedia di `/opac/{tenant_code}`
- OPAC per-organization tersedia di `/opac/{tenant_code}/organizations/{organization_code}`
- role assignment member ke tenant memakai `--tenant-role-id` jika ingin role spesifik; jika tidak diisi, sistem cari role default/member/student

## Aturan Relasi

Aturan relasi yang dipakai di seluruh project:

- setiap `belongsTo()` harus memiliki inverse relation yang sesuai di model induk.
- setiap foreign key konkret harus diikat dengan constraint database bila target tabelnya jelas.
- kolom generic hanya dipertahankan untuk payload bebas yang memang tidak cocok menjadi FK keras.
- relasi yang mewakili entitas lintas domain harus menggunakan Eloquent native, bukan pseudo relation manual.

Konvensi foreign key:

- `tenant_id`
  Wajib untuk semua tabel operasional.
- `organization_id`
  Dipakai bila data memang milik unit/lembaga tertentu di dalam tenant.
- `user_id`
  Dipakai hanya saat entitas benar-benar dimiliki atau dioperasikan oleh user.
- kolom seperti `created_by`, `approved_by`, `verified_by`, `processed_by`, `reported_by`, `handled_by`, `uploaded_by`, `synced_by`
  Harus mengarah ke `users.id` dan punya inverse relation di model `User`.

## Relasi Polymorphic Resmi

Project ini sekarang mengunci polymorphic relation berikut:

### Billing

`Finance\StudentInvoice` memakai:

- `invoiceable(): MorphTo`

Inverse resmi:

- `School\Student::studentInvoices(): MorphMany`
- `Enrollment\Applicant::studentInvoices(): MorphMany`
- `Campus\CollageStudent::studentInvoices(): MorphMany`

Ini berarti invoice pendidikan dapat diarahkan ke:

- siswa sekolah,
- calon siswa atau pendaftar,
- mahasiswa kampus.

### Monitoring

`Monitoring\AuditLog` memakai:

- `auditable(): MorphTo`

`Monitoring\FileUpload` memakai:

- `fileable(): MorphTo`

Model yang saat ini secara eksplisit disiapkan untuk audit trail atau attachment domain:

- `Tenant`
- `Organization`
- `School\Student`
- `Enrollment\Applicant`
- `Campus\CollageStudent`
- `Campus\StudyProgram`
- `Library\Book`
- `Procurement\Vendor`
- `Procurement\PurchaseOrder`
- `Procurement\GoodsReceipt`
- `Procurement\VendorBill`

Selain morph, `audit_logs` dan `file_uploads` tetap membawa `tenant_id` dan `organization_id` agar bisa di-query sebagai data tenant-scoped tanpa custom join tambahan.

## Matriks Relasi Inti

Ringkasan relasi inti yang paling sering dipakai service layer dan panel admin:

- `Tenant`
  Punya banyak organization, userTenantRoles, tenantRoles, tenantModules, academicYears, departments, dan seluruh entitas operasional lintas modul.
- `Organization`
  Punya banyak academicYears, departments, admissionPeriods, school entities, finance entities, employee entities, procurement categories, dan campus entities.
- `User`
  Punya banyak assignment tenant lewat `user_tenant_roles`, serta inverse relation ke semua kolom user-based seperti `createdTenants`, `principalOrganizations`, `verifiedPayments`, `approvedPurchaseOrders`, `processedVendorBills`, `uploadedFiles`, `auditLogs`, dan lainnya.
- `TenantRole`
  Punya banyak `userTenantRoles`.
- `UserTenantRole`
  Menghubungkan `user`, `tenant`, `organization`, dan `tenantRole`.
- `Module`
  Terhubung ke tenant melalui pivot `tenant_modules`.

## Guidelines Pengembangan

Gunakan panduan ini saat menambah tabel atau model baru:

1. Mulai dari boundary:
   apakah data ini global, tenant-scoped, atau organization-scoped.
2. Jika data operasional:
   tambahkan `tenant_id`.
3. Jika data milik unit tertentu:
   tambahkan `organization_id`.
4. Jika relasi target jelas:
   pakai `foreignId(...)->constrained()` atau constraint tambahan di migration alter.
5. Jika entitas butuh attachment atau audit trail:
   pakai `morphMany()` ke `FileUpload` atau `AuditLog`.
6. Jika menambah `belongsTo()`:
   selalu tambahkan inverse relation yang sesuai.

## Catatan Implementasi

- Model user utama aplikasi berada di `Modules/Core/Models/User.php`, sedangkan `app/Models/User.php` adalah bridge agar komponen Laravel tetap kompatibel.
- `Monitoring` sudah memakai polymorphic native Laravel, bukan lagi pasangan kolom manual `entity_type/entity_id`.
- Morph map dikunci di `AppServiceProvider` agar tipe polymorphic stabil dan tidak bergantung pada refactor namespace.
- Beberapa FK yang semula berupa kolom lepas kini dipasang constraint tambahan lewat migration alter, supaya urutan migration lama tetap aman.

## Verifikasi

Perintah minimal untuk memverifikasi fondasi relasi:

```bash
php artisan migrate:fresh
php artisan test
```

Test yang penting setelah perubahan relasi:

- inverse relation tenant dan organization tetap terbaca,
- `invoiceable` resolve ke model sumber yang benar,
- `auditable` dan `fileable` resolve ke entitas domain yang benar,
- fresh migration tidak gagal karena urutan FK.

Perintah tambahan untuk memverifikasi setup panel dan Shield:

```bash
php artisan optimize:clear
php artisan route:list | grep filament.admin.resources
php artisan test
```
