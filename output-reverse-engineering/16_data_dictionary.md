# 16 — Data Dictionary

## Ringkasan Singkat

Kamus data untuk **entitas inti** yang menjadi fondasi multi-tenancy dan domain prioritas. Field dan tipe bersumber dari migration Laravel `Schema::` — bukan dari DB live.

**Cakupan penuh 400+ tabel:** TIDAK TERDETEKSI di git (`entity-catalog.json` tidak ter-commit).

---

## tenants

**Sumber:** `Modules/Core/database/migrations/2026_03_24_151400_create_tenants_table.php`

| Kolom | Tipe (migration) | Keterangan | Nullable |
|-------|------------------|------------|----------|
| id | bigint PK | Identitas internal | No |
| uuid | uuid UK | Route key Filament | No |
| code | string UK | Kode tenant | No |
| name | string | Nama tampilan | No |
| domain | string UK | Domain custom | Yes |
| subdomain | string UK | Subdomain | Yes |
| status | string | Default `trial` | No |
| trial_ends_at | timestamp | Akhir trial | Yes |
| subscription_expires_at | timestamp | Kedaluwarsa langganan | Yes |
| subscription_plan_id | bigint FK | Plan SaaS | Yes |
| settings | json | Pengaturan bebas | Yes |
| max_users | unsigned int | Kuota user | Yes |
| locale | string(10) | Default `en` | No |
| currency | string(3) | Default `USD` | No |
| created_at / updated_at | timestamp | Audit | No |

---

## users

**Sumber:** `Modules/Core/database/migrations/2026_03_24_151842_create_users_table.php` + migrasi tambahan

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| name | string | |
| email | string UK | |
| password | string | hashed |
| email_verified_at | timestamp | |
| preferred_locale | string | `id` / `en` — migrasi locale |
| is_super_admin | boolean | Global bypass |
| remember_token | string | |

**Model bridge:** `app/Models/User` extends `Modules\Core\Models\User`

---

## user_tenant_roles

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| tenant_id | bigint FK | → tenants |
| user_id | bigint FK | → users |
| tenant_role_id | bigint FK | → tenant_roles |
| organization_id | bigint FK | Opsional scope org |

**Bukti:** migration `create_user_tenant_roles_table.php`

---

## organizations

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| tenant_id | bigint FK | Wajib |
| name | string | Nama organisasi/cabang |
| code | string | Kode internal |

---

## students (School)

**Sumber:** `Modules/School/database/migrations/2026_03_24_152528_create_students_table.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| tenant_id | bigint FK | |
| organization_id | bigint FK | Opsional |
| nis | string | Nomor induk |
| name | string | |
| gender | string | |
| birth_date | date | |
| status | string | Status akademik |

**Catatan:** Kolom pasti setelah migrasi tambahan — verifikasi file migration lengkap untuk field tambahan.

---

## workflows

**Sumber:** `Modules/Workflow/database/migrations/2026_03_26_210000_create_workflows_table.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| tenant_id | bigint FK | |
| organization_id | bigint FK | Null = tenant-wide |
| code | string | Kode definisi |
| name | string | |
| subject_type | string | FQCN subjek |
| status | string | Draft/Active/... |
| version | int | Versi definisi |
| is_active | boolean | |

---

## workflow_instances

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| workflow_id | bigint FK | |
| tenant_id | bigint FK | |
| workflow_snapshot | json | Definisi beku |
| context_data | json | Konteks runtime |
| current_step_id | bigint | Step aktif |
| status | string | Running/Completed/... |
| due_at | timestamp | SLA |

---

## student_invoices

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| tenant_id | bigint FK | |
| student_id | bigint FK | |
| invoice_number | string | |
| status | string | draft/issued/partial/paid |
| total_amount | decimal | |
| issued_at | timestamp | |

---

## purchase_requisitions

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| tenant_id | bigint FK | |
| requester_id | bigint FK | User |
| status | string | Status PR |
| total_amount | decimal | |

---

## moodle_sync_outbox

**Sumber:** `database/migrations/2026_03_25_220000_create_moodle_sync_tables.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| tenant_id | bigint FK | |
| entity_type | string | user/course/... |
| entity_id | bigint | ID FOS |
| action | string | upsert/delete |
| payload | json | |
| status | string | pending/processed/failed |

---

## Tabel Sistem Laravel

| Tabel | Fungsi | Bukti |
|-------|--------|-------|
| jobs, job_batches, failed_jobs | Queue | `create_jobs_table.php` |
| cache, cache_locks | Cache | `create_cache_table.php` |
| personal_access_tokens | Sanctum | migration |
| permissions, roles, model_has_* | Spatie | `create_permission_tables.php` |
| activity_log | Spatie activity | migration |
| imports, exports, failed_import_rows | Filament import/export | migrations |

---

## Pola Naming

| Pola | Contoh | Bukti |
|------|--------|-------|
| snake_case tabel | `student_invoices` | migrations |
| `tenant_id` FK | hampir semua operasional | `BelongsToTenant` |
| soft deletes | `deleted_at` | migration soft deletes |

---

## Cara Regenerasi Kamus Lengkap

```bash
php scripts/extract-entity-catalog.php
php scripts/extract-verified-fks.php
```

**Bukti:** `scripts/` — output ke `storage/app/` (gitignored).

---

## Catatan Ketidakpastian

| Item | Status |
|------|--------|
| Dictionary 434 tabel | GAP — jalankan script lokal |
| Enum values pasti per kolom status | PARSIAL — banyak string bebas, beberapa Enum PHP |
| Index/unique constraint lengkap | PARSIAL — perlu baca setiap migration |
