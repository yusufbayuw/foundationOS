# 06 — Requirements Non-Fungsional

## Ringkasan Singkat

Kebutuhan non-fungsional (NFR) di bawah ini diidentifikasi dari konfigurasi, middleware, dependensi, dan pola kode — bukan dari dokumen SRS formal.

---

## Keamanan

| ID | NFR | Bukti | Keyakinan |
|----|-----|-------|-----------|
| NFR-SEC-01 | Autentikasi session untuk Filament | `Authenticate` middleware — `AdminPanelProvider.php` | TERVERIFIKASI |
| NFR-SEC-02 | MFA (TOTP app + email) pada admin | `->multiFactorAuthentication([...])` — `AdminPanelProvider.php:53-56` | TERVERIFIKASI |
| NFR-SEC-03 | API token via Sanctum | `laravel/sanctum`, `personal_access_tokens` migration | TERVERIFIKASI |
| NFR-SEC-04 | RBAC granular per resource/action | Filament Shield + Spatie Permission | TERVERIFIKASI |
| NFR-SEC-05 | CSRF protection web (kecuali webhook) | `bootstrap/app.php:30-33` | TERVERIFIKASI |
| NFR-SEC-06 | Tenant isolation fail-closed | `config/tenancy.php`, `TenantScope.php` | TERVERIFIKASI |
| NFR-SEC-07 | Verifikasi signature webhook Midtrans | `MidtransWebhookVerifier.php` | TERVERIFIKASI |
| NFR-SEC-08 | Rate limit API 60 req/menit | `RateLimiter::for('api')` — `routes/api.php:24-28` | TERVERIFIKASI |
| NFR-SEC-09 | Enkripsi at rest untuk secrets `.env` | **TIDAK TERDETEKSI** — konvensi deploy | — |

---

## Ketersediaan & Skalabilitas

| ID | NFR | Bukti | Keyakinan |
|----|-----|-------|-----------|
| NFR-AVL-01 | Health check endpoint `/up` | `bootstrap/app.php:23` | TERVERIFIKASI |
| NFR-AVL-02 | Background queue untuk sync Moodle | `ProcessMoodleSyncOutboxJob` | TERVERIFIKASI |
| NFR-AVL-03 | Scheduler untuk retry SLA workflow | `fos:workflow:retry-sla` command | TERVERIFIKASI |
| NFR-AVL-04 | Horizontal scaling / auto-scaling | **TIDAK TERDETEKSI DI KODE** | — |
| NFR-AVL-05 | SLA uptime target | **TIDAK TERDETEKSI DI KODE** | — |

---

## Performa

| ID | NFR | Bukti | Keyakinan |
|----|-----|-------|-----------|
| NFR-PERF-01 | Laravel Pulse observability | `laravel/pulse`, migration pulse tables | TERVERIFIKASI |
| NFR-PERF-02 | Cache driver configurable | `config/cache.php` | TERVERIFIKASI |
| NFR-PERF-03 | Response time target | **TIDAK TERDETEKSI DI KODE** | — |
| NFR-PERF-04 | Eager loading policy global | **TIDAK TERDETEKSI** — per-query | — |

---

## Internasionalisasi (i18n)

| ID | NFR | Bukti | Keyakinan |
|----|-----|-------|-----------|
| NFR-I18N-01 | Dua locale: `id`, `en` | `FilamentUi.php`, `users.preferred_locale` | TERVERIFIKASI |
| NFR-I18N-02 | Linter anti-hardcoded label | `scripts/lint-translations.php` | TERVERIFIKASI |
| NFR-I18N-03 | Default locale per tenant | `TenantSetting` key `default_locale` — `CLAUDE.md` | TERVERIFIKASI |

---

## Maintainability

| ID | NFR | Bukti | Keyakinan |
|----|-----|-------|-----------|
| NFR-MAINT-01 | Modular boundaries 45 modul | `Modules/` | TERVERIFIKASI |
| NFR-MAINT-02 | PHPUnit test suite | 173 test files | TERVERIFIKASI |
| NFR-MAINT-03 | Static analysis Larastan | `composer.json` require-dev | TERVERIFIKASI |
| NFR-MAINT-04 | Code style Pint | `vendor/bin/pint` | TERVERIFIKASI |
| NFR-MAINT-05 | CI GitHub Actions | `.github/workflows/` | TERVERIFIKASI |

---

## Auditability

| ID | NFR | Bukti | Keyakinan |
|----|-----|-------|-----------|
| NFR-AUD-01 | Audit log model | `Monitoring/AuditLog` | TERVERIFIKASI |
| NFR-AUD-02 | Spatie activity log | `activity_log` migration | TERVERIFIKASI |
| NFR-AUD-03 | Workflow instance log | `WorkflowInstanceLog` | TERVERIFIKASI |
| NFR-AUD-04 | Retention policy otomatis | **TIDAK TERDETEKSI** — mungkin command manual | — |

---

## Kompatibilitas

| ID | NFR | Bukti | Keyakinan |
|----|-----|-------|-----------|
| NFR-COMP-01 | REST API versioning v1/v2 | `routes/api.php`, `AttachApiVersionMeta` | TERVERIFIKASI |
| NFR-COMP-02 | OpenAPI dokumentasi | `OpenApiController` | TERVERIFIKASI |
| NFR-COMP-03 | Mobile shell Capacitor | folder `mobile/` | TERVERIFIKASI (config only) |

---

## Recoverability

| ID | NFR | Bukti | Keyakinan |
|----|-----|-------|-----------|
| NFR-REC-01 | Idempotency API writes | `IdempotencyKey` middleware | TERVERIFIKASI |
| NFR-REC-02 | Moodle outbox retry | `MoodleSyncRetry`, `moodle:retry-failed` | TERVERIFIKASI |
| NFR-REC-03 | Backup/restore procedure | **TIDAK TERDETEKSI DI KODE** | — |

---

## Catatan Ketidakpastian

- NFR numerik (RTO, RPO, latency p95): **TIDAK TERDETEKSI**.
- Compliance sertifikasi (ISO 27001 operasional): modul `IsoCompliance` ada sebagai fitur aplikasi, bukti sertifikasi deploy **TIDAK TERDETEKSI**.
