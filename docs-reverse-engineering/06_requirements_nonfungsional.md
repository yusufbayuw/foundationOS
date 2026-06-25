# 06 — Requirements Non-Fungsional (Diturunkan dari Source Code)

**Commit analisis:** `d3be06aa` · **Metode:** ekstraksi dari `config/`, middleware, job, scheduler, migrasi — bukan dari dokumen SLA formal.

**Legenda:** jika tidak ada bukti implementasi di kode → **TIDAK TERDETEKSI DI KODE**.

---

## Ringkasan Dimensi

| Kategori | Temuan utama | Bukti utama |
|----------|--------------|-------------|
| Security | Session + MFA + Sanctum + RBAC + CSRF + throttle | `AdminPanelProvider.php`, `routes/api.php`, `bootstrap/app.php` |
| Logging | Channel stack Monolog, default `LOG_CHANNEL` | `config/logging.php` |
| Audit | `AuditLog` domain + Spatie Activity Log + workflow log | `AuditLog.php`, `activity_log` migration |
| Performance | Cache dashboard 5 menit; Pulse | `StudentDashboardController`, Pulse migration |
| Caching | Default store `database`; idempotency cache | `config/cache.php`, `IdempotencyKey.php` |
| Scalability | Queue database; modular monolith | `config/queue.php`, `Modules/` |
| Reliability | Scheduler overlap lock; job retry Moodle | `routes/console.php`, `ProcessMoodleSyncOutboxJob` |

---

## Security

### NFR-SEC-001
**Autentikasi Sesi Panel Admin**

Panel admin memakai session Laravel dengan cookie terenkripsi dan CSRF pada request web.

**Bukti:**
- `app/Providers/Filament/AdminPanelProvider.php` — middleware `StartSession`, `EncryptCookies`, `PreventRequestForgery`
- `bootstrap/app.php` — `validateCsrfTokens`

---

### NFR-SEC-002
**Multi-Factor Authentication Admin**

Admin dapat mengaktifkan MFA via authenticator app (recoverable) dan email.

**Bukti:**
- `AdminPanelProvider.php` baris 53–56 — `multiFactorAuthentication([AppAuthentication::make()->recoverable(), EmailAuthentication::make()])`

---

### NFR-SEC-003
**API Bearer Token (Sanctum)**

API v1/v2 memerlukan token personal access Sanctum untuk endpoint terproteksi.

**Bukti:**
- `routes/api.php` — `middleware(['auth:sanctum', 'resolve.api.tenant'])`
- `database/migrations/2026_05_22_003518_create_personal_access_tokens_table.php`

---

### NFR-SEC-004
**RBAC dengan Team Scope Tenant**

Permission Spatie berjalan mode teams; `team_foreign_key` = `tenant_id`.

**Bukti:**
- `config/permission.php` — `'teams' => true`
- `Modules/School/app/Policies/StudentPolicy.php` — `can('ViewAny:Student')`
- `BezhanSalleh\FilamentShield\FilamentShieldPlugin` — `AdminPanelProvider.php`

---

### NFR-SEC-005
**Super Admin Global Bypass**

User super admin melewati semua ability Gate.

**Bukti:**
- `app/Providers/AppServiceProvider.php` baris 93–98 — `Gate::before()` + `isGlobalSuperAdmin()`

---

### NFR-SEC-006
**CSRF Exception untuk Webhook Billing/Donasi**

Endpoint webhook pembayaran dikecualikan dari CSRF verification.

**Bukti:**
- `bootstrap/app.php` baris 30–33 — `validateCsrfTokens(except: ['billing/webhook', 'donation/webhook'])`

---

### NFR-SEC-007
**Rate Limiting API**

API dibatasi 60 request per menit per token atau IP.

**Bukti:**
- `routes/api.php` baris 24–29 — `RateLimiter::for('api')` → `Limit::perMinute(60)`
- Public inquiry: `throttle:10,1` — `api-routes-catalog.json` route `api/inquiry`

---

### NFR-SEC-008
**Fail-Closed Tenant Scope (Produksi)**

Query model `BelongsToTenant` tanpa `CurrentTenant` dapat throw di produksi.

**Bukti:**
- `config/tenancy.php` — `scope_fail_closed` default true saat `APP_ENV=production`
- `Modules/Core/app/Models/Concerns/BelongsToTenant.php`

---

### NFR-SEC-009
**API Token Wajib Scoped Tenant**

Token Sanctum tanpa `tenant_id` ditolak jika `api_require_tenant` aktif.

**Bukti:**
- `app/Http/Middleware/ResolveApiTenant.php` baris 23–26 — `abort(403, 'API token must be scoped to a tenant.')`
- `config/tenancy.php` — `api_require_tenant` default true

---

### NFR-SEC-010
**Langganan Aktif untuk Akses Panel**

Middleware memblokir user jika subscription tenant tidak valid.

**Bukti:**
- `app/Http/Middleware/EnsureTenantSubscriptionActive.php` — `handle()`
- `AdminPanelProvider.php` — `authMiddleware` includes middleware ini

---

### NFR-SEC-011
**Enkripsi Data at Rest / TLS**

**TIDAK TERDETEKSI DI KODE** — konfigurasi TLS/encryption disk tidak didefinisikan di aplikasi (infrastruktur Herd/server).

---

### NFR-SEC-012
**OWASP / Penetration Test Policy**

**TIDAK TERDETEKSI DI KODE**

---

## Logging

### NFR-LOG-001
**Channel Log Stack Default**

Aplikasi menulis log ke channel `stack` (konfigurasi Monolog).

**Bukti:**
- `config/logging.php` — `'default' => env('LOG_CHANNEL', 'stack')`
- Channels: `single`, `daily`, `stderr`, `syslog`, dll.

---

### NFR-LOG-002
**Deprecation Log Channel**

Warning deprecasi PHP/library dapat diarahkan ke channel terpisah.

**Bukti:**
- `config/logging.php` — `'deprecations' => ['channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null')]`

---

### NFR-LOG-003
**Structured API Error JSON**

Exception validation, auth, not found pada API dirender sebagai JSON terstruktur.

**Bukti:**
- `bootstrap/app.php` baris 45–71 — `ValidationException`, `AuthenticationException`, `ModelNotFoundException` renderables

---

### NFR-LOG-004
**Centralized Log Aggregation (ELK/Datadog)**

**TIDAK TERDETEKSI DI KODE** — tidak ada driver/shipper khusus di repo.

---

### NFR-LOG-005
**PII Masking di Log**

**TIDAK TERDETEKSI DI KODE**

---

## Audit

### NFR-AUD-001
**Audit Log Domain (Monitoring)**

Aktivitas bisnis dicatat ke model `AuditLog` dengan relasi polymorphic.

**Bukti:**
- `Modules/Monitoring/app/Models/AuditLog.php`
- `FinanceControlService::audit()` — memanggil `AuditLog` pada invoice/payment
- `AuditLogResource` — UI review di Filament

---

### NFR-AUD-002
**Spatie Activity Log**

Package activity log dengan tabel `activity_log` dan config actions.

**Bukti:**
- `database/migrations/2026_05_22_142909_create_activity_log_table.php`
- `config/activitylog.php` — `LogActivityAction`, `enabled` env `ACTIVITYLOG_ENABLED`

---

### NFR-AUD-003
**Workflow Instance Log**

Setiap transisi workflow dicatat via `WorkflowAuditLogger` dan model log.

**Bukti:**
- `Modules/Workflow/app/Models/WorkflowInstanceLog.php`
- `DatabaseWorkflowEngine` — dependency `WorkflowAuditLogger`
- `WorkflowInstanceLogPolicy`

---

### NFR-AUD-004
**Relation Manager Audit di Resource**

Entitas utama (Student, User, Tenant) menampilkan audit logs inline.

**Bukti:**
- `Modules/School/app/Filament/Resources/Students/RelationManagers/AuditLogsRelationManager.php`

---

### NFR-AUD-005
**Retensi Audit Formal (tahun)**

**TIDAK TERDETEKSI DI KODE** — `activitylog:clean` ada di package config `clean_after_days` tetapi kebijakan bisnis retensi tidak terdokumentasi di kode aplikasi.

---

## Performance

### NFR-PERF-001
**Cache Dashboard Siswa API (5 menit)**

Endpoint dashboard siswa mem-cache hasil agregasi selama 300 detik.

**Bukti:**
- `app/Http/Controllers/Api/v1/StudentDashboardController.php` — `CACHE_TTL_SECONDS = 300`, `Cache::remember()`

---

### NFR-PERF-002
**Laravel Pulse Monitoring**

Tabel Pulse untuk observability performa request/job.

**Bukti:**
- `database/migrations/2026_05_23_073542_create_pulse_tables.php`

---

### NFR-PERF-003
**Row Lock Workflow Advance**

`DatabaseWorkflowEngine::advance()` memakai `lockForUpdate()` untuk serialisasi concurrent approval.

**Bukti:**
- `Modules/Workflow/app/Services/DatabaseWorkflowEngine.php` baris 42–45

---

### NFR-PERF-004
**Target Latency / SLA Response Time**

**TIDAK TERDETEKSI DI KODE**

---

### NFR-PERF-005
**Database Query Optimization Policy**

**TIDAK TERDETEKSI DI KODE** — tidak ada benchmark atau budget query terpusat; Larastan ada di `composer.json` sebagai tooling dev.

---

## Caching

### NFR-CACHE-001
**Default Cache Store Database**

Cache framework default ke driver `database` (env `CACHE_STORE`).

**Bukti:**
- `config/cache.php` — `'default' => env('CACHE_STORE', 'database')`
- Store `redis` tersedia tetapi tidak dipaksa di kode

---

### NFR-CACHE-002
**Idempotency Response Cache**

Middleware idempotency menyimpan response API di cache 24 jam.

**Bukti:**
- `app/Http/Middleware/IdempotencyKey.php` — `TTL_SECONDS = 86400`, `Cache::put()`

---

### NFR-CACHE-003
**HTTP Cache Headers / CDN**

**TIDAK TERDETEKSI DI KODE** untuk API; CMS public mungkin via Blade tanpa policy cache header terpusat.

---

### NFR-CACHE-004
**Application-Level Config Cache**

Laravel `php artisan config:cache` didukung framework; tidak ada custom cache layer bisnis luas di luar contoh dashboard.

**Bukti:**
- Standar Laravel — **TIDAK TERDETEKSI DI KODE** sebagai requirement eksplisit di repo

---

## Scalability

### NFR-SCALE-001
**Arsitektur Modular Monolith**

45 modul nwidart/coolsam dalam satu deployable; bukan microservice.

**Bukti:**
- `Modules/` — 45 folder modul
- `config/modules.php`, `Coolsam\Modules\ModulesPlugin`

---

### NFR-SCALE-002
**Queue Async untuk Integrasi**

Job Moodle sync dan workflow SLA dijalankan via queue (default `database`).

**Bukti:**
- `config/queue.php` — `'default' => env('QUEUE_CONNECTION', 'database')`
- `app/Jobs/ProcessMoodleSyncOutboxJob.php` — `ShouldQueue`, queue `moodle-sync`
- `Modules/Workflow/app/Jobs/CheckWorkflowSlaJob.php`

---

### NFR-SCALE-003
**Horizontal Pod Autoscaling / Load Balancer**

**TIDAK TERDETEKSI DI KODE** — tidak ada manifest K8s/Terraform di repo aplikasi.

---

### NFR-SCALE-004
**Database Sharding / Read Replica**

**TIDAK TERDETEKSI DI KODE** — shared database multi-tenancy single connection.

---

### NFR-SCALE-005
**Stateless API (Session-less)**

API Sanctum stateless; panel Filament stateful session — pola hybrid.

**Bukti:**
- `routes/api.php` — Sanctum tanpa session middleware
- `AdminPanelProvider.php` — session middleware stack

---

## Reliability

### NFR-REL-001
**Health Check Endpoint**

Laravel health route `/up` untuk probe ketersediaan.

**Bukti:**
- `bootstrap/app.php` — `health: '/up'`

---

### NFR-REL-002
**Scheduler Without Overlapping**

Perintah terjadwal memakai `withoutOverlapping()` untuk mencegah run ganda.

**Bukti:**
- `routes/console.php` — semua `Schedule::command(...)->withoutOverlapping()` (28+ entri)

---

### NFR-REL-003
**Moodle Outbox Retry & Stale Recovery**

Job sync Moodle dengan `tries` konfigurabel; scheduler sweep stale processing.

**Bukti:**
- `ProcessMoodleSyncOutboxJob.php` — `$tries = config('moodle.max_attempts', 7)`
- `routes/console.php` — `fos:moodle:sweep-stale-processing`, `fos:moodle:drain-outbox`
- `config/tenancy.php` — `moodle_processing_stale_minutes`

---

### NFR-REL-004
**Database Transaction pada Operasi Kritis**

Finance verify payment dan workflow advance dibungkus `DB::transaction()`.

**Bukti:**
- `FinanceControlService::verifyPayment()` — `DB::transaction`
- `DatabaseWorkflowEngine::advance()` — `DB::transaction`

---

### NFR-REL-005
**Idempotency Conflict Detection**

API write mendeteksi replay vs body berbeda (HTTP 409).

**Bukti:**
- `IdempotencyKey.php` — `idempotency_conflict` response

---

### NFR-REL-006
**SLA Uptime 99.9% / RTO / RPO**

**TIDAK TERDETEKSI DI KODE**

---

### NFR-REL-007
**Disaster Recovery Runbook**

**TIDAK TERDETEKSI DI KODE** di dalam repo aplikasi (mungkin di docs operasional eksternal).

---

### NFR-REL-008
**Circuit Breaker untuk Integrasi Eksternal**

**TIDAK TERDETEKSI DI KODE** — Moodle client retry ada (`MoodleSyncRetry`) tetapi bukan circuit breaker pattern eksplisit.

**Bukti partial:**
- `app/Integrations/Moodle/MoodleSyncRetry.php` — retry logic

---

## Matriks Bukti per Kategori

| Kategori | Terdeteksi | Tidak terdeteksi |
|----------|:----------:|:----------------:|
| Security | 10 | 2 |
| Logging | 3 | 2 |
| Audit | 4 | 1 |
| Performance | 3 | 2 |
| Caching | 2 | 2 |
| Scalability | 2 | 3 |
| Reliability | 5 (+1 partial) | 2 |

---

## TIDAK TERDETEKSI DI KODE (Agregat)

| Item |
|------|
| SLA uptime / latency SLO numerik |
| Kebijakan GDPR/data residency |
| Penetration test / security audit schedule |
| Horizontal scaling infrastructure as code |
| CDN / edge caching strategy |
| Portal siswa dedicated reliability path |
| Redis wajib produksi (hanya opsi env) |

---

## Regenerasi

```bash
php artisan config:show cache.default
php artisan config:show queue.default
php artisan config:show logging.default
php artisan config:show tenancy
grep -c "withoutOverlapping" routes/console.php
```

**Referensi:** `05_requirements_fungsional.md`, `02_inventaris_teknologi.md`, `bootstrap/app.php`
