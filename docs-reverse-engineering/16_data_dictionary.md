# 16 — Data Dictionary

**Commit analisis:** `d3be06aa`  
**Metodologi:** Kolom di bawah diverifikasi manual dari file migrasi Laravel (`Blueprint`). Tipe DB diinfer dari helper Blueprint (e.g. `foreignId` → `BIGINT UNSIGNED`).  
**Cakupan:** Tabel domain kritis (tenancy, school, enrollment, finance, workflow, Moodle). **434 tabel** total — katalog lengkap: `storage/app/entity-catalog.json` (regenerasi: `php scripts/extract-entity-catalog.php`).

**Legenda nullable:** `NO` = NOT NULL di migrasi; `YES` = `->nullable()` atau kolom opsional implisit.

---

## Ringkasan inventaris

| Objek | Jumlah | Bukti |
|-------|-------:|-------|
| Table | 434 | `entity-catalog.json` |
| View | 0 | **TIDAK TERDETEKSI DI KODE** |
| Foreign key (verified) | 5.473 | `storage/app/verified-fks.json` |

---

## `tenants`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| tenants | id | BIGINT UNSIGNED | NO | auto-increment | Primary key tenant SaaS | `Modules/Core/database/migrations/2026_03_24_151400_create_tenants_table.php` |
| tenants | uuid | CHAR(36) / UUID | NO | TIDAK TERDETEKSI | Identifier unik global tenant | idem baris 16 |
| tenants | code | VARCHAR(255) | NO | TIDAK TERDETEKSI | Kode singkat tenant (unique) | idem baris 17 |
| tenants | name | VARCHAR(255) | NO | TIDAK TERDETEKSI | Nama tenant | idem baris 18 |
| tenants | domain | VARCHAR(255) | YES | NULL | Domain kustom | idem baris 19 |
| tenants | subdomain | VARCHAR(255) | YES | NULL | Subdomain | idem baris 20 |
| tenants | logo | VARCHAR(255) | YES | NULL | Path/logo URL branding | idem baris 21 |
| tenants | favicon | VARCHAR(255) | YES | NULL | Favicon branding | idem baris 22 |
| tenants | primary_color | VARCHAR(255) | YES | NULL | Warna primer UI | idem baris 23 |
| tenants | secondary_color | VARCHAR(255) | YES | NULL | Warna sekunder UI | idem baris 24 |
| tenants | timezone | VARCHAR(255) | NO | UTC | Zona waktu default tenant | idem baris 25 |
| tenants | currency | VARCHAR(3) | NO | USD | Mata uang default | idem baris 26 |
| tenants | locale | VARCHAR(10) | NO | en | Locale default tenant | idem baris 27 |
| tenants | billing_cycle | VARCHAR(255) | YES | NULL | Siklus penagihan langganan | idem baris 28 |
| tenants | status | VARCHAR(255) | NO | trial | Status langganan tenant | idem baris 29 |
| tenants | trial_ends_at | TIMESTAMP | YES | NULL | Akhir masa trial | idem baris 30 |
| tenants | subscribed_at | TIMESTAMP | YES | NULL | Waktu mulai berlangganan | idem baris 31 |
| tenants | subscription_expires_at | TIMESTAMP | YES | NULL | Kedaluwarsa langganan | idem baris 32 |
| tenants | grace_period_ends_at | TIMESTAMP | YES | NULL | Akhir grace period setelah expire | `database/migrations/2026_05_22_151424_add_grace_period_to_tenants.php` |
| tenants | midtrans_customer_id | VARCHAR(255) | YES | NULL | ID pelanggan Midtrans | idem |
| tenants | settings | JSON | YES | NULL | Pengaturan tenant (JSON) | `create_tenants_table.php` baris 33 |
| tenants | max_users | INT UNSIGNED | YES | NULL | Batas maksimum user | idem baris 34 |
| tenants | max_organizations | INT UNSIGNED | YES | NULL | Batas maksimum organisasi | idem baris 35 |
| tenants | max_storage_mb | INT UNSIGNED | YES | NULL | Kuota penyimpanan (MB) | idem baris 36 |
| tenants | meta_title | VARCHAR(255) | YES | NULL | SEO meta title | idem baris 37 |
| tenants | meta_description | TEXT | YES | NULL | SEO meta description | idem baris 38 |
| tenants | subscription_plan_id | BIGINT UNSIGNED | YES | NULL | FK ke subscription_plans (index only) | idem baris 39 |
| tenants | created_by | BIGINT UNSIGNED | YES | NULL | FK users — pembuat tenant | idem baris 40; FK `151842_create_users_table.php` baris 34–38 |
| tenants | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | `create_tenants_table.php` baris 41 |
| tenants | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

---

## `users`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| users | id | BIGINT UNSIGNED | NO | auto-increment | Primary key user global | `Modules/Core/database/migrations/2026_03_24_151842_create_users_table.php` |
| users | name | VARCHAR(255) | NO | TIDAK TERDETEKSI | Nama tampilan | idem baris 16 |
| users | username | VARCHAR(255) | YES | NULL | Username opsional (unique) | idem baris 17 |
| users | email | VARCHAR(255) | NO | TIDAK TERDETEKSI | Email login (unique) | idem baris 18 |
| users | phone | VARCHAR(255) | YES | NULL | Nomor telepon | idem baris 19 |
| users | password | VARCHAR(255) | NO | TIDAK TERDETEKSI | Hash password | idem baris 20 |
| users | avatar | VARCHAR(255) | YES | NULL | Path avatar | idem baris 21 |
| users | email_verified_at | TIMESTAMP | YES | NULL | Verifikasi email | idem baris 22 |
| users | last_login_at | TIMESTAMP | YES | NULL | Login terakhir | idem baris 23 |
| users | last_login_ip | VARCHAR(45) | YES | NULL | IP login terakhir | idem baris 24 |
| users | login_attempts | INT UNSIGNED | NO | 0 | Percobaan login gagal | idem baris 25 |
| users | locked_until | TIMESTAMP | YES | NULL | Kunci akun sampai | idem baris 26 |
| users | timezone | VARCHAR(255) | NO | UTC | Zona waktu user | idem baris 27 |
| users | locale | VARCHAR(10) | NO | en | Locale sistem | idem baris 28 |
| users | preferred_locale | ENUM('id','en') | YES | id | Preferensi bahasa UI | `database/migrations/2026_05_22_052030_add_preferred_locale_to_users_table.php` |
| users | status | VARCHAR(255) | NO | active | Status akun | `create_users_table.php` baris 29 |
| users | is_super_admin | BOOLEAN | NO | false | Flag super admin platform | `Modules/Core/database/migrations/2026_03_25_170000_add_is_super_admin_to_users_table.php` |
| users | remember_token | VARCHAR(100) | YES | NULL | Token remember me | `create_users_table.php` baris 30 |
| users | deleted_at | TIMESTAMP | YES | NULL | Soft delete | `Modules/Core/database/migrations/2026_03_25_171000_add_soft_deletes_to_all_domain_tables.php` |
| users | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | `create_users_table.php` baris 31 |
| users | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

---

## `user_tenant_roles`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| user_tenant_roles | id | BIGINT UNSIGNED | NO | auto-increment | Primary key | `Modules/Core/database/migrations/2026_03_24_151843_create_user_tenant_roles_table.php` |
| user_tenant_roles | user_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK users | idem baris 16 |
| user_tenant_roles | tenant_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK tenants | idem baris 17 |
| user_tenant_roles | organization_id | BIGINT UNSIGNED | YES | NULL | FK organizations (scope org) | idem baris 18 |
| user_tenant_roles | tenant_role_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK tenant_roles | idem baris 19 |
| user_tenant_roles | assigned_by | BIGINT UNSIGNED | YES | NULL | FK users penugas | idem baris 20 |
| user_tenant_roles | assigned_at | TIMESTAMP | YES | NULL | Waktu penugasan | idem baris 21 |
| user_tenant_roles | expires_at | TIMESTAMP | YES | NULL | Kedaluwarsa keanggotaan | idem baris 22 |
| user_tenant_roles | is_primary | BOOLEAN | NO | false | Tenant utama user | idem baris 23 |
| user_tenant_roles | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | idem baris 26 |
| user_tenant_roles | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

**Constraint:** UNIQUE `(user_id, tenant_id, organization_id, tenant_role_id)` — baris 24.

---

## `students`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| students | id | BIGINT UNSIGNED | NO | auto-increment | Primary key siswa | `Modules/School/database/migrations/2026_03_24_152528_create_students_table.php` |
| students | tenant_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK tenants | idem baris 16 |
| students | organization_id | BIGINT UNSIGNED | YES | NULL | FK organizations | idem baris 17 |
| students | academic_year_id | BIGINT UNSIGNED | YES | NULL | FK academic_years | idem baris 18 |
| students | user_id | BIGINT UNSIGNED | YES | NULL | FK users akun portal | idem baris 19 |
| students | nis | VARCHAR(255) | YES | NULL | Nomor Induk Siswa | idem baris 20 |
| students | nisn | VARCHAR(255) | YES | NULL | NISN nasional | idem baris 21 |
| students | entry_date | DATE | YES | NULL | Tanggal masuk | idem baris 22 |
| students | entry_type | VARCHAR(255) | YES | NULL | Jenis masuk (baru/pindahan) | idem baris 23 |
| students | previous_school | VARCHAR(255) | YES | NULL | Sekolah asal | idem baris 24 |
| students | previous_school_npsn | VARCHAR(255) | YES | NULL | NPSN sekolah asal | idem baris 25 |
| students | status | VARCHAR(255) | NO | active | Status siswa | idem baris 26 |
| students | graduation_date | DATE | YES | NULL | Tanggal lulus | idem baris 27 |
| students | ijazah_number | VARCHAR(255) | YES | NULL | Nomor ijazah | idem baris 28 |
| students | skhun_number | VARCHAR(255) | YES | NULL | Nomor SKHUN | idem baris 29 |
| students | track | VARCHAR(255) | YES | NULL | Jurusan/lintasan | idem baris 30 |
| students | extracurricular_activities | JSON | YES | NULL | Ekstrakurikuler | idem baris 31 |
| students | achievements | JSON | YES | NULL | Prestasi | idem baris 32 |
| students | health_notes | JSON | YES | NULL | Catatan kesehatan | idem baris 33 |
| students | special_needs | JSON | YES | NULL | Kebutuhan khusus | idem baris 34 |
| students | scholarship_status | VARCHAR(255) | YES | NULL | Status beasiswa | idem baris 35 |
| students | family_card_number | VARCHAR(255) | YES | NULL | Nomor KK | idem baris 36 |
| students | father_name | VARCHAR(255) | YES | NULL | Nama ayah | idem baris 37 |
| students | father_nik | VARCHAR(255) | YES | NULL | NIK ayah | idem baris 38 |
| students | father_education | VARCHAR(255) | YES | NULL | Pendidikan ayah | idem baris 39 |
| students | father_job | VARCHAR(255) | YES | NULL | Pekerjaan ayah | idem baris 40 |
| students | father_phone | VARCHAR(255) | YES | NULL | Telepon ayah | idem baris 41 |
| students | mother_name | VARCHAR(255) | YES | NULL | Nama ibu | idem baris 42 |
| students | mother_nik | VARCHAR(255) | YES | NULL | NIK ibu | idem baris 43 |
| students | mother_education | VARCHAR(255) | YES | NULL | Pendidikan ibu | idem baris 44 |
| students | mother_job | VARCHAR(255) | YES | NULL | Pekerjaan ibu | idem baris 45 |
| students | mother_phone | VARCHAR(255) | YES | NULL | Telepon ibu | idem baris 46 |
| students | guardian_name | VARCHAR(255) | YES | NULL | Nama wali | idem baris 47 |
| students | guardian_relation | VARCHAR(255) | YES | NULL | Hubungan wali | idem baris 48 |
| students | guardian_phone | VARCHAR(255) | YES | NULL | Telepon wali | idem baris 49 |
| students | guardian_address | TEXT | YES | NULL | Alamat wali | idem baris 50 |
| students | residence_type | VARCHAR(255) | YES | NULL | Jenis tempat tinggal | idem baris 51 |
| students | transport_type | VARCHAR(255) | YES | NULL | Moda transportasi | idem baris 52 |
| students | travel_time_minutes | INT UNSIGNED | YES | NULL | Waktu tempuh (menit) | idem baris 53 |
| students | distance_km | DECIMAL(8,2) | YES | NULL | Jarak ke sekolah (km) | idem baris 54 |
| students | deleted_at | TIMESTAMP | YES | NULL | Soft delete | `2026_03_25_171000_add_soft_deletes_to_all_domain_tables.php` |
| students | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | `create_students_table.php` baris 57 |
| students | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

**Constraint:** UNIQUE `(tenant_id, nis)`, UNIQUE `(tenant_id, nisn)` — baris 55–56.

---

## `applicants`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| applicants | id | BIGINT UNSIGNED | NO | auto-increment | Primary key pendaftar | `Modules/Enrollment/database/migrations/2026_03_24_152536_create_applicants_table.php` |
| applicants | tenant_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK tenants | idem baris 13 |
| applicants | admission_period_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK admission_periods | idem baris 14 |
| applicants | registration_number | VARCHAR(255) | NO | TIDAK TERDETEKSI | Nomor pendaftaran | idem baris 15 |
| applicants | full_name | VARCHAR(255) | NO | TIDAK TERDETEKSI | Nama lengkap calon siswa | idem baris 16 |
| applicants | birth_place | VARCHAR(255) | YES | NULL | Tempat lahir | idem baris 17 |
| applicants | birth_date | DATE | YES | NULL | Tanggal lahir | idem baris 18 |
| applicants | gender | VARCHAR(255) | YES | NULL | Jenis kelamin | idem baris 19 |
| applicants | religion | VARCHAR(255) | YES | NULL | Agama | idem baris 20 |
| applicants | address | TEXT | YES | NULL | Alamat | idem baris 21 |
| applicants | phone | VARCHAR(255) | YES | NULL | Telepon | idem baris 22 |
| applicants | email | VARCHAR(255) | YES | NULL | Email | idem baris 23 |
| applicants | parent_name | VARCHAR(255) | YES | NULL | Nama orang tua | idem baris 24 |
| applicants | parent_phone | VARCHAR(255) | YES | NULL | Telepon orang tua | idem baris 25 |
| applicants | previous_school | VARCHAR(255) | YES | NULL | Sekolah asal | idem baris 26 |
| applicants | previous_school_address | TEXT | YES | NULL | Alamat sekolah asal | idem baris 27 |
| applicants | nisn | VARCHAR(255) | YES | NULL | NISN | idem baris 28 |
| applicants | ijazah_number | VARCHAR(255) | YES | NULL | Nomor ijazah | idem baris 29 |
| applicants | average_score | DECIMAL(8,2) | YES | NULL | Rata-rata nilai | idem baris 30 |
| applicants | achievement_count | INT UNSIGNED | NO | 0 | Jumlah prestasi | idem baris 31 |
| applicants | achievement_details | JSON | YES | NULL | Detail prestasi | idem baris 32 |
| applicants | program_choice_1_id | BIGINT UNSIGNED | YES | NULL | FK departments pilihan 1 | idem baris 33 |
| applicants | program_choice_2_id | BIGINT UNSIGNED | YES | NULL | FK departments pilihan 2 | idem baris 34 |
| applicants | status | VARCHAR(255) | NO | registered | Status proses pendaftaran | idem baris 35 |
| applicants | test_score | DECIMAL(8,2) | YES | NULL | Nilai tes | idem baris 36 |
| applicants | interview_score | DECIMAL(8,2) | YES | NULL | Nilai wawancara | idem baris 37 |
| applicants | final_score | DECIMAL(8,2) | YES | NULL | Nilai akhir seleksi | idem baris 38 |
| applicants | ranking | INT UNSIGNED | YES | NULL | Peringkat | idem baris 39 |
| applicants | is_passed | BOOLEAN | YES | NULL | Lulus seleksi | idem baris 40 |
| applicants | accepted_program_id | BIGINT UNSIGNED | YES | NULL | FK program diterima | idem baris 41 |
| applicants | enrollment_date | DATE | YES | NULL | Tanggal daftar ulang | idem baris 42 |
| applicants | converted_to_student_id | BIGINT UNSIGNED | YES | NULL | FK students setelah konversi | idem baris 43 |
| applicants | photo | VARCHAR(255) | YES | NULL | Path foto | idem baris 44 |
| applicants | documents | JSON | YES | NULL | Dokumen lampiran | idem baris 45 |
| applicants | notes | TEXT | YES | NULL | Catatan | idem baris 46 |
| applicants | deleted_at | TIMESTAMP | YES | NULL | Soft delete | `2026_03_25_171000_add_soft_deletes_to_all_domain_tables.php` |
| applicants | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | `create_applicants_table.php` baris 48 |
| applicants | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

**Constraint:** UNIQUE `(tenant_id, registration_number)` — baris 47.

---

## `student_invoices`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| student_invoices | id | BIGINT UNSIGNED | NO | auto-increment | Primary key invoice | `Modules/Finance/database/migrations/2026_03_24_152535_create_student_invoices_table.php` |
| student_invoices | tenant_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK tenants | idem baris 13 |
| student_invoices | tuition_type_id | BIGINT UNSIGNED | YES | NULL | FK tuition_types | idem baris 14 |
| student_invoices | invoice_number | VARCHAR(255) | NO | TIDAK TERDETEKSI | Nomor invoice | idem baris 15 |
| student_invoices | invoice_type | VARCHAR(255) | YES | NULL | Jenis invoice | idem baris 16 |
| student_invoices | issue_date | DATE | NO | TIDAK TERDETEKSI | Tanggal terbit | idem baris 17 |
| student_invoices | due_date | DATE | NO | TIDAK TERDETEKSI | Jatuh tempo | idem baris 18 |
| student_invoices | amount | DECIMAL(10,2) | NO | TIDAK TERDETEKSI | Nominal dasar | idem baris 19 |
| student_invoices | discount_amount | DECIMAL(10,2) | NO | 0 | Potongan | idem baris 20 |
| student_invoices | discount_reason | TEXT | YES | NULL | Alasan diskon | idem baris 21 |
| student_invoices | penalty_amount | DECIMAL(10,2) | NO | 0 | Denda | idem baris 22 |
| student_invoices | total_amount | DECIMAL(10,2) | NO | TIDAK TERDETEKSI | Total tagihan | idem baris 23 |
| student_invoices | paid_amount | DECIMAL(10,2) | NO | 0 | Sudah dibayar | idem baris 24 |
| student_invoices | remaining_amount | DECIMAL(10,2) | NO | TIDAK TERDETEKSI | Sisa tagihan | idem baris 25 |
| student_invoices | status | VARCHAR(255) | NO | draft | Status invoice | idem baris 26 |
| student_invoices | description | TEXT | YES | NULL | Deskripsi | idem baris 27 |
| student_invoices | notes | TEXT | YES | NULL | Catatan | idem baris 28 |
| student_invoices | is_sent | BOOLEAN | NO | false | Sudah dikirim ke penerima | idem baris 29 |
| student_invoices | sent_at | TIMESTAMP | YES | NULL | Waktu pengiriman | idem baris 30 |
| student_invoices | sent_via | VARCHAR(255) | YES | NULL | Kanal pengiriman | idem baris 31 |
| student_invoices | invoiceable_type | VARCHAR(255) | NO | TIDAK TERDETEKSI | Polymorphic type (siswa/dll) | idem baris 32 `morphs` |
| student_invoices | invoiceable_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | Polymorphic id | idem |
| student_invoices | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | idem baris 34 |
| student_invoices | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

**Constraint:** UNIQUE `(tenant_id, invoice_number)` — baris 33.

---

## `payments`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| payments | id | BIGINT UNSIGNED | NO | auto-increment | Primary key pembayaran | `Modules/Finance/database/migrations/2026_03_24_152535_create_payments_table.php` |
| payments | tenant_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK tenants | idem baris 13 |
| payments | student_invoice_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK student_invoices | idem baris 14 |
| payments | chart_of_account_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK chart_of_accounts | idem baris 15 |
| payments | verified_by | BIGINT UNSIGNED | YES | NULL | FK users verifikator | idem baris 16 |
| payments | payment_number | VARCHAR(255) | NO | TIDAK TERDETEKSI | Nomor pembayaran | idem baris 17 |
| payments | payment_date | DATE | NO | TIDAK TERDETEKSI | Tanggal bayar | idem baris 18 |
| payments | amount | DECIMAL(10,2) | NO | TIDAK TERDETEKSI | Jumlah dibayar | idem baris 19 |
| payments | payment_method | VARCHAR(255) | YES | NULL | Metode (tunai/transfer) | idem baris 20 |
| payments | payment_channel | VARCHAR(255) | YES | NULL | Kanal pembayaran | idem baris 21 |
| payments | reference_number | VARCHAR(255) | YES | NULL | Nomor referensi bank | idem baris 22 |
| payments | account_number | VARCHAR(255) | YES | NULL | Nomor rekening | idem baris 23 |
| payments | account_holder | VARCHAR(255) | YES | NULL | Nama pemegang rekening | idem baris 24 |
| payments | bank_name | VARCHAR(255) | YES | NULL | Nama bank | idem baris 25 |
| payments | proof_file | VARCHAR(255) | YES | NULL | Bukti transfer | idem baris 26 |
| payments | verified_at | TIMESTAMP | YES | NULL | Waktu verifikasi | idem baris 27 |
| payments | verification_notes | TEXT | YES | NULL | Catatan verifikasi | idem baris 28 |
| payments | status | VARCHAR(255) | NO | pending | Status pembayaran | idem baris 29 |
| payments | is_reconciled | BOOLEAN | NO | false | Sudah direkonsiliasi | idem baris 30 |
| payments | reconciliation_date | DATE | YES | NULL | Tanggal rekonsiliasi | idem baris 31 |
| payments | deleted_at | TIMESTAMP | YES | NULL | Soft delete | `2026_03_25_171000_add_soft_deletes_to_all_domain_tables.php` |
| payments | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | `create_payments_table.php` baris 33 |
| payments | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

**Constraint:** UNIQUE `(tenant_id, payment_number)` — baris 32.

---

## `workflows`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| workflows | id | BIGINT UNSIGNED | NO | auto-increment | Primary key definisi workflow | `Modules/Workflow/database/migrations/2026_03_26_210000_create_workflows_table.php` |
| workflows | tenant_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK tenants | idem baris 13 |
| workflows | organization_id | BIGINT UNSIGNED | YES | NULL | FK organizations (opsional) | idem baris 14 |
| workflows | code | VARCHAR(255) | NO | TIDAK TERDETEKSI | Kode workflow | idem baris 15 |
| workflows | name | VARCHAR(255) | NO | TIDAK TERDETEKSI | Nama workflow | idem baris 16 |
| workflows | description | TEXT | YES | NULL | Deskripsi | idem baris 17 |
| workflows | module | VARCHAR(255) | YES | NULL | Modul pemicu | idem baris 18 |
| workflows | subject_type | VARCHAR(255) | YES | NULL | Model class subjek | idem baris 19 |
| workflows | trigger_mode | VARCHAR(255) | NO | manual | Mode pemicu | idem baris 20 |
| workflows | version | INT UNSIGNED | NO | 1 | Versi definisi | idem baris 21 |
| workflows | status | VARCHAR(255) | NO | draft | Status definisi | idem baris 22 |
| workflows | is_active | BOOLEAN | NO | false | Aktif dipakai | idem baris 23 |
| workflows | published_at | TIMESTAMP | YES | NULL | Waktu publish | idem baris 24 |
| workflows | created_by | BIGINT UNSIGNED | YES | NULL | FK users pembuat | idem baris 25 |
| workflows | updated_by | BIGINT UNSIGNED | YES | NULL | FK users pengubah | idem baris 26 |
| workflows | deleted_at | TIMESTAMP | YES | NULL | Soft delete | idem baris 28 |
| workflows | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | idem baris 27 |
| workflows | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

---

## `workflow_instances`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| workflow_instances | id | BIGINT UNSIGNED | NO | auto-increment | Primary key instance | `Modules/Workflow/database/migrations/2026_03_26_210300_create_workflow_instances_table.php` |
| workflow_instances | tenant_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK tenants | idem baris 13 |
| workflow_instances | organization_id | BIGINT UNSIGNED | YES | NULL | FK organizations | idem baris 14 |
| workflow_instances | workflow_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK workflows | idem baris 15 |
| workflow_instances | workflow_version | INT UNSIGNED | NO | TIDAK TERDETEKSI | Versi snapshot | idem baris 16 |
| workflow_instances | workflow_snapshot | JSON | NO | TIDAK TERDETEKSI | Salinan definisi saat start | idem baris 17 |
| workflow_instances | current_step_id | BIGINT UNSIGNED | YES | NULL | FK workflow_steps langkah aktif | idem baris 18 |
| workflow_instances | requester_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK users pemohon | idem baris 19 |
| workflow_instances | started_by | BIGINT UNSIGNED | YES | NULL | FK users yang memulai | idem baris 20 |
| workflow_instances | subject_type | VARCHAR(255) | YES | NULL | Polymorphic class subjek bisnis | idem baris 21 |
| workflow_instances | subject_id | BIGINT UNSIGNED | YES | NULL | Polymorphic id subjek | idem baris 22 |
| workflow_instances | subject_label | VARCHAR(255) | YES | NULL | Label tampilan subjek | idem baris 23 |
| workflow_instances | context_data | JSON | YES | NULL | Konteks evaluasi rules | idem baris 24 |
| workflow_instances | form_data | JSON | YES | NULL | Data form pengajuan | idem baris 25 |
| workflow_instances | computed_data | JSON | YES | NULL | Data terhitung engine | idem baris 26 |
| workflow_instances | status | VARCHAR(255) | NO | running | Status instance | idem baris 27 |
| workflow_instances | current_assignees | JSON | YES | NULL | Daftar assignee aktif | idem baris 28 |
| workflow_instances | started_at | TIMESTAMP | NO | TIDAK TERDETEKSI | Waktu mulai | idem baris 29 |
| workflow_instances | due_at | TIMESTAMP | YES | NULL | SLA deadline | idem baris 30 |
| workflow_instances | completed_at | TIMESTAMP | YES | NULL | Waktu selesai | idem baris 31 |
| workflow_instances | cancelled_at | TIMESTAMP | YES | NULL | Waktu batal | idem baris 32 |
| workflow_instances | rejected_at | TIMESTAMP | YES | NULL | Waktu ditolak | idem baris 33 |
| workflow_instances | deleted_at | TIMESTAMP | YES | NULL | Soft delete | idem baris 35 |
| workflow_instances | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | idem baris 34 |
| workflow_instances | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

---

## `workflow_assignments`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| workflow_assignments | id | BIGINT UNSIGNED | NO | auto-increment | Primary key tugas approval | `Modules/Workflow/database/migrations/2026_03_26_210500_create_workflow_assignments_table.php` |
| workflow_assignments | workflow_instance_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK workflow_instances | idem baris 13 |
| workflow_assignments | step_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | FK workflow_steps | idem baris 14 |
| workflow_assignments | assigned_to_type | VARCHAR(255) | NO | user | Tipe assignee (polymorphic) | idem baris 15 |
| workflow_assignments | assigned_to_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | ID assignee | idem baris 16 |
| workflow_assignments | assignment_role | VARCHAR(255) | YES | NULL | Peran dalam step | idem baris 17 |
| workflow_assignments | status | VARCHAR(255) | NO | pending | Status tugas | idem baris 18 |
| workflow_assignments | assigned_at | TIMESTAMP | NO | TIDAK TERDETEKSI | Waktu penugasan | idem baris 19 |
| workflow_assignments | claimed_at | TIMESTAMP | YES | NULL | Waktu diklaim | idem baris 20 |
| workflow_assignments | completed_at | TIMESTAMP | YES | NULL | Waktu diselesaikan | idem baris 21 |
| workflow_assignments | due_at | TIMESTAMP | YES | NULL | Deadline tugas | idem baris 22 |
| workflow_assignments | meta | JSON | YES | NULL | Metadata tambahan | idem baris 23 |
| workflow_assignments | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | idem baris 24 |
| workflow_assignments | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

---

## `moodle_sync_outbox`

| Table | Column | Type | Nullable | Default | Description | Evidence |
|-------|--------|------|----------|---------|-------------|----------|
| moodle_sync_outbox | id | BIGINT UNSIGNED | NO | auto-increment | Primary key event outbox | `database/migrations/2026_03_25_220000_create_moodle_sync_tables.php` |
| moodle_sync_outbox | entity_type | VARCHAR(32) | NO | TIDAK TERDETEKSI | Tipe entitas FOS | idem baris 13 |
| moodle_sync_outbox | entity_id | BIGINT UNSIGNED | NO | TIDAK TERDETEKSI | ID entitas FOS | idem baris 14 |
| moodle_sync_outbox | tenant_id | BIGINT UNSIGNED | YES | NULL | Tenant (tanpa FK eksplisit) | idem baris 15 |
| moodle_sync_outbox | action | VARCHAR(32) | NO | TIDAK TERDETEKSI | Aksi sync (create/update/delete) | idem baris 16 |
| moodle_sync_outbox | payload | JSON | YES | NULL | Payload ke Moodle API | idem baris 17 |
| moodle_sync_outbox | dedupe_key | VARCHAR(255) | NO | TIDAK TERDETEKSI | Kunci deduplikasi (unique) | idem baris 18 |
| moodle_sync_outbox | status | VARCHAR(16) | NO | pending | Status antrian | idem baris 19 |
| moodle_sync_outbox | attempts | INT UNSIGNED | NO | 0 | Jumlah percobaan | idem baris 20 |
| moodle_sync_outbox | next_retry_at | TIMESTAMP | YES | NULL | Jadwal retry | idem baris 21 |
| moodle_sync_outbox | last_error | TEXT | YES | NULL | Error terakhir | idem baris 22 |
| moodle_sync_outbox | synced_at | TIMESTAMP | YES | NULL | Waktu sukses sync | idem baris 23 |
| moodle_sync_outbox | created_at | TIMESTAMP | YES | NULL | Waktu dibuat | idem baris 24 |
| moodle_sync_outbox | updated_at | TIMESTAMP | YES | NULL | Waktu diubah | idem |

---

## Tabel lain (428 tabel)

Kolom lengkap per tabel tersedia di `storage/app/entity-catalog.json`.  
**Catatan parser:** Field `nullable` di JSON hasil script dapat salah untuk beberapa kolom — gunakan file migrasi sebagai sumber kebenaran untuk audit formal.

**ORM mapping semua model:** `entity-catalog.json` → key `models[].table` / `models[].class`.

---

## TIDAK TERDETEKSI DI KODE

| Item | Status |
|------|--------|
| Database VIEW | **TIDAK TERDETEKSI DI KODE** |
| Repository / DTO layer | **TIDAK TERDETEKSI DI KODE** |
| CHECK constraint SQL | **TIDAK TERDETEKSI DI KODE** |

**Referensi:** `11_er_diagram.md`, `10_class_diagram.md`
