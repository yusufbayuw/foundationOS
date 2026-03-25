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
