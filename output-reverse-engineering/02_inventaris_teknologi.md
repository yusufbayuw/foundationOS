# 02 — Inventaris Teknologi

## Ringkasan Singkat

FoundationOS dibangun di atas ekosistem Laravel 13 modern dengan PHP 8.4, Filament 5, Livewire 4, dan Tailwind CSS v4. Otorisasi memakai Spatie Permission + Filament Shield; API memakai Laravel Sanctum.

---

## Temuan Utama

### Runtime & Framework

| Teknologi | Versi (constraint) | Bukti |
|-----------|-------------------|-------|
| PHP | `^8.4` | `composer.json:12` |
| Laravel Framework | `^13.0` | `composer.json:18` |
| Filament | `^5.4` | `composer.json:16` |
| Livewire | v4 (via Filament) | `README.md`, `AGENTS.md` |
| Laravel Sanctum | `^4.3` | `composer.json:20` |

### Paket Produksi Penting

| Paket | Fungsi | Bukti |
|-------|--------|-------|
| `coolsam/modules` | Modular monolith (nwidart-compatible) | `composer.json:15` |
| `bezhansalleh/filament-shield` | RBAC Filament + Spatie | `composer.json:14`, `config/filament-shield.php` |
| `spatie/laravel-activitylog` | Audit aktivitas | `composer.json:24` |
| `jwadhams/json-logic-php` | Aturan transisi workflow | `composer.json:17`, `JsonLogicEvaluator.php` |
| `midtrans/midtrans-php` | Pembayaran SaaS billing | `composer.json:22`, `BillingService.php` |
| `barryvdh/laravel-dompdf` | PDF | `composer.json:13` |
| `simplesoftwareio/simple-qrcode` | QR code | `composer.json:23` |
| `laravel/pulse` | Observability | `composer.json:19` |

### Frontend Build

| Teknologi | Versi | Bukti |
|-----------|-------|-------|
| Vite | `^8.0.0` | `package.json:17` |
| Tailwind CSS | `^4.3.0` | `package.json:17` |
| `@tailwindcss/vite` | `^4.3.0` | `package.json:10` |
| laravel-vite-plugin | `^3.0.0` | `package.json:16` |
| cytoscape + dagre | workflow designer | `package.json:13-15`, `resources/js/workflow-designer.js` |

### Dev & Quality

| Tool | Bukti |
|------|-------|
| PHPUnit 12 | `composer.json:35`, `php artisan test` |
| Laravel Pint | `composer.json:31` |
| Larastan / PHPStan | `composer.json:28-29` |
| Laravel Boost (MCP) | `composer.json:29` |
| Laravel Pail | `composer.json:30` |
| Faker | `composer.json:27` |

---

## Struktur Folder Inti

| Path | Peran | Bukti |
|------|-------|-------|
| `app/` | Layer aplikasi (Filament panels, Moodle, jobs, imports) | 318 file PHP — `00-repository-manifest.md` |
| `Modules/` | 45 modul domain | `ls Modules/` |
| `routes/` | `web.php`, `api.php`, `console.php` | `bootstrap/app.php:18-23` |
| `config/` | 24 file konfigurasi aplikasi | manifest |
| `database/migrations/` | Migrasi core | 37 + 207 modul = 244 total |
| `tests/` | 173 file PHPUnit | manifest |
| `scripts/` | Ekstraksi docs, lint terjemahan | 21 file |
| `docs/catalogs/` | JSON katalog API & otorisasi | `api-routes-catalog.json` |
| `mobile/` | Konfigurasi Capacitor shell | `02-structure-catalog.md` |

**Dikecualikan dari audit:** `vendor/`, `node_modules/` — `.gitignore`.

---

## Entry Point & Proses

| Entry | Path / Perintah | Bukti |
|-------|-----------------|-------|
| HTTP | `public/index.php` | Laravel standard |
| Artisan | `artisan` | `composer.json` scripts |
| Dev orchestration | `composer run dev` | serve + queue + pail + vite |
| Scheduler | `routes/console.php` | 28 `Schedule::command` — `REAP_AUDIT.md` |
| Queue default | `database` driver | `config/queue.php` (dikutip `ARCHITECTURE.md`) |

---

## Database

| Aspek | Temuan | Keyakinan |
|-------|--------|-----------|
| ORM | Eloquent | TERVERIFIKASI — `Modules/*/Models/` |
| Migrasi | 244 file | TERVERIFIKASI |
| Engine DB spesifik produksi | TIDAK TERDETEKSI | `README.md` menyebut SQLite/relasional generik |
| `stancl/tenancy` | Tidak digunakan | TERVERIFIKASI — tidak ada di `composer.json` |

---

## CI/CD

| Item | Bukti |
|------|-------|
| GitHub Actions | 5 workflow — `.github/workflows/*.yml` |
| Docker / Terraform / K8s | **TIDAK TERDETEKSI DI KODE** |

---

## Catatan Ketidakpastian

- Versi patch pasti paket di runtime produksi: **TIDAK TERDETEKSI** (hanya constraint semver di `composer.lock` jika ada).
- Redis wajib atau opsional: **INFERENSI** — env override disebut di dokumentasi setup, bukan hardcoded di kode.
