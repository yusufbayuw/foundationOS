# 11 — ER Diagram

## Ringkasan Singkat

Entity-Relationship diagram **parsial** berdasarkan migrasi dan model inti. Katalog FK lengkap (`verified-fks.json`) **TIDAK TERDETEKSI DI GIT** pada commit analisis.

**Jumlah tabel dikutip di dokumentasi existing:** 434 — **PARSIAL/UNVERIFIED** (sumber `entity-catalog.json` tidak ter-commit).

---

## Hub Entitas Inti (Tenancy)

```mermaid
erDiagram
    tenants ||--o{ organizations : contains
    tenants ||--o{ user_tenant_roles : scopes
    tenants ||--o{ tenant_modules : enables
    tenants ||--o{ tenant_settings : configures
    users ||--o{ user_tenant_roles : has
    tenant_roles ||--o{ user_tenant_roles : defines
    organizations ||--o{ departments : has
    tenants ||--o{ academic_years : owns

    tenants {
        bigint id PK
        uuid uuid UK
        string code UK
        string name
        string status
        bigint subscription_plan_id FK
    }

    users {
        bigint id PK
        string email UK
        boolean is_super_admin
        string preferred_locale
    }

    organizations {
        bigint id PK
        bigint tenant_id FK
        string name
    }
```

**Bukti migrasi:**
- `Modules/Core/database/migrations/2026_03_24_151400_create_tenants_table.php`
- `Modules/Core/database/migrations/2026_03_24_151842_create_users_table.php`
- `Modules/Core/database/migrations/2026_03_24_151843_create_user_tenant_roles_table.php`

---

## Domain School (K-12) — Parsial

```mermaid
erDiagram
    tenants ||--o{ students : scopes
    tenants ||--o{ school_classes : scopes
    tenants ||--o{ teachers : scopes
    school_classes ||--o{ class_students : enrolls
    students ||--o{ class_students : member
    students ||--o{ attendances : has
    students ||--o{ student_grades : receives
    curricula ||--o{ subjects : contains

    students {
        bigint id PK
        bigint tenant_id FK
        string nis
        string name
    }

    school_classes {
        bigint id PK
        bigint tenant_id FK
        string name
    }

    class_students {
        bigint student_id FK
        bigint school_class_id FK
    }
```

**Bukti:** `Modules/School/database/migrations/2026_03_24_152528_create_students_table.php` dan migrasi terkait.

---

## Domain Finance — Parsial

```mermaid
erDiagram
    tenants ||--o{ chart_of_accounts : owns
    tenants ||--o{ student_invoices : issues
    student_invoices ||--o{ student_invoice_items : lines
    student_invoices ||--o{ payments : receives
    tenants ||--o{ journal_entries : posts
    journal_entries ||--o{ journal_entry_lines : lines

    student_invoices {
        bigint id PK
        bigint tenant_id FK
        string status
        decimal total_amount
    }

    payments {
        bigint id PK
        bigint student_invoice_id FK
        string status
        decimal amount
    }
```

**Bukti:** `Modules/Finance/database/migrations/`

---

## Domain Workflow

```mermaid
erDiagram
    tenants ||--o{ workflows : defines
    workflows ||--o{ workflow_steps : has
    workflow_steps ||--o{ workflow_transitions : from
    workflows ||--o{ workflow_instances : runs
    workflow_instances ||--o{ workflow_assignments : tasks
    workflow_instances ||--o{ workflow_instance_logs : audits

    workflows {
        bigint id PK
        bigint tenant_id FK
        string subject_type
        string status
    }

    workflow_instances {
        bigint id PK
        json workflow_snapshot
        string status
        bigint current_step_id
    }
```

**Bukti:** `Modules/Workflow/database/migrations/2026_03_26_210*.php`

---

## Domain Procurement — Parsial

```mermaid
erDiagram
    purchase_requisitions ||--o| workflows : may_trigger
    purchase_requisitions ||--o{ purchase_orders : becomes
    purchase_orders ||--o{ goods_receipts : receives
    purchase_orders ||--o{ vendor_bills : billed
    vendors ||--o{ vendor_bills : from
```

**Bukti:** `Modules/Procurement/database/migrations/`

---

## Pola Kolom Umum (TERVERIFIKASI)

| Kolom | Frekuensi | Bukti |
|-------|-----------|-------|
| `tenant_id` | Mayoritas tabel operasional | `BelongsToTenant`, migrations |
| `organization_id` | Banyak tabel domain | migrations per modul |
| `deleted_at` | Soft deletes domain | migration `add_soft_deletes_to_all_domain_tables` |
| `created_at`, `updated_at` | Standar Laravel | migrations |

---

## Kardinalitas

Kardinalitas di diagram di atas bersumber dari:
1. Nama relasi Eloquent di model (jika ada)
2. Foreign key di migration `foreignId()->constrained()`

**Catatan:** Tanpa `verified-fks.json`, kardinalitas untuk 400+ tabel lainnya = **GAP**.

---

## Catatan Ketidakpastian

| Item | Status |
|------|--------|
| ERD lengkap 434 tabel | TIDAK TERDETEKSI di repo git |
| Diagram per modul leaf (Clinic, Risk, dll.) | PARSIAL — migrasi batch `create_*_tables.php` |
| Polymorphic relations (`subject_type`) | TERVERIFIKASI di workflow, audit — detail per tabel: GAP |
