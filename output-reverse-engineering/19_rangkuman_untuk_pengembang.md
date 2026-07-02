# 19 — Rangkuman untuk Pengembang

## Ringkasan Singkat

Panduan cepat bagi developer yang baru masuk ke codebase FoundationOS — berbasis fakta kode, bukan tutorial generik Laravel.

---

## Mulai dari Sini

| Kebutuhan | Lokasi | Perintah |
|-----------|--------|----------|
| Setup lokal | `README.md` | `composer install`, `php artisan migrate`, `composer run dev` |
| Panduan AI/agent | `CLAUDE.md`, `AGENTS.md` | — |
| Arsitektur detail | `ARCHITECTURE.md`, `03_arsitektur_aplikasi.md` (folder ini) | — |
| Roadmap | `ROADMAP.md` | — |

**URL lokal (Herd):** `https://foundationos.test/admin` — gunakan `php artisan make:super-admin` untuk user awal.

---

## Mental Model Arsitektur

```
Request → Middleware → Filament/API Controller
         → Module Service (aturan bisnis)
         → Eloquent (tenant scoped)
         → DB
```

**Aturan emas:**
1. Selalu scope `tenant_id` — jangan bypass `TenantScope` tanpa alasan.
2. Resource Filament **wajib** extend `ModuleResource`, bukan `Resource` langsung.
3. Label UI **wajib** lewat `FilamentUi` — lihat `CLAUDE.md` section Translasi.
4. Otorisasi: Shield permission + policy; super admin bypass di `AppServiceProvider`.

---

## File Paling Penting

| File | Mengapa |
|------|---------|
| `Modules/Core/app/Filament/Support/ModuleResource.php` | Base semua admin CRUD |
| `Modules/Core/app/Models/Concerns/BelongsToTenant.php` | Multi-tenancy |
| `app/Providers/Filament/AdminPanelProvider.php` | Panel admin |
| `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` | Approval engine |
| `routes/api.php` | API mobile inti |
| `config/permission.php` | Spatie teams |
| `config/tenancy.php` | Fail-closed scoping |

---

## Menambah Fitur Baru

Ikuti urutan di `CLAUDE.md` — ringkasan:

1. `php artisan make:filament-resource` di `Modules/{Name}/app/Filament/Resources/`
2. Extend `ModuleResource`; pisahkan Form/Table/Infolist schemas
3. Tambah importer di `app/Filament/Imports/`
4. Update `navigationSortMap()` di `ModuleResource.php`
5. `php artisan shield:generate --all --panel=admin`
6. `php artisan optimize:clear`
7. Tulis feature test PHPUnit + `RefreshDatabase`

---

## Workflow (V2)

| Task | Command / Class |
|------|-----------------|
| Health check | `php artisan fos:workflow:health-check --tenant=1` |
| Setup procurement pilot | `php artisan fos:workflow:setup-procurement-pilot 1` |
| Setup budget | `php artisan fos:workflow:setup-budget-workflow 1` |
| Engine | `DatabaseWorkflowEngine`, `WorkflowResolver` |

Transisi memakai **JSONLogic** — rules di `workflow_transitions.condition_rules`.

---

## Integrasi

| Sistem | Entry point |
|--------|-------------|
| Moodle | `app/Integrations/Moodle/`, `php artisan moodle:health-check` |
| Midtrans billing | `app/Services/BillingService.php` |
| WhatsApp | `Modules/Messaging/` — default `LogWhatsAppProvider` |

---

## Testing

```bash
# Semua test
php artisan test --compact

# Satu file
php artisan test --compact tests/Feature/WorkflowBudgetApprovalTest.php

# Format kode
vendor/bin/pint --dirty --format agent
```

**173** file test — prioritaskan test domain yang Anda ubah.

---

## Pitfall Umum

| Masalah | Penyebab | Solusi |
|---------|----------|--------|
| Resource tidak muncul di nav | Cache / modul tidak aktif | `optimize:clear`, cek `tenant_modules` |
| 403 di panel | Shield permission | `shield:generate`, assign role |
| Data tenant lain terlihat | Bypass scope | Cek `BelongsToTenant`, jangan `allTenants()` sembarangan |
| Label hardcoded CI gagal | `lint-translations` | Pakai `FilamentUi::text()` |
| Vite manifest error | Assets belum build | `npm run dev` atau `npm run build` |

---

## Dokumentasi Reverse Engineering (Folder Ini)

Gunakan indeks `README.md` untuk:
- Requirements (`05`, `06`)
- Diagram proses (`07`–`12`)
- API (`14`)
- Business rules (`15`)
- Gap yang belum pasti (`17`)

---

## Kontak Artefak Otomatis

Regenerasi katalog:

```bash
php scripts/extract-api-routes.php
php scripts/extract-authorization-matrix.php
php scripts/extract-entity-catalog.php
php scripts/build-evidence-registry.php
```

Output entity/FK masuk `storage/app/` — pertimbangkan commit ke `docs/catalogs/` untuk tim.

---

## Statistik Cepat (Commit `d3be06aa`)

| Metrik | Nilai |
|--------|-------|
| Modul | 45 |
| ModuleResource | 257 |
| API routes | 101 |
| Migrasi | 244 |
| PHPUnit files | 173 |
| Panel Filament | 3 |

---

## Langkah Selanjutnya yang Disarankan

1. Baca `17_gap_analysis.md` sebelum mengasumsikan kelengkapan modul.
2. Jalankan test suite setelah pull dari `main`.
3. Untuk audit otorisasi tenant spesifik: query DB staging `roles`/`permissions`.
4. Tanyakan ke tim produk untuk roadmap modul leaf vs core depth.
