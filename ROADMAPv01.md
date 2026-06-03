# ROADMAP v0.1 — Fase 1 Foundation (Multi-Tenant SaaS Core)

Dokumen ini melacak gap Fase 1 (foundation multi-tenant SaaS) terhadap implementasi saat ini di FoundationOS, beserta urutan kerja yang direkomendasikan.

---

## Status Audit Fase 1

### 1. Arsitektur Multi-Tenancy
- [ ] Central Domain vs Subdomain routing (`tenant-a.erp.test`)
- [x] Tenant Scope manual (Filament `->tenant(Tenant::class)` + `tenant_id` di semua tabel operasional)
- [x] Single Database Multi-Tenancy strategy

### 2. Struktur Modul Dinamis
- [x] Registry Modul (`Modules\Core\Models\Module`)
- [x] Tenant Module Mapping (`Modules\Core\Models\TenantModule`)
- [ ] Filament Navigation Hook berbasis `tenant_modules` aktif (saat ini navigation groups statis di `AdminPanelProvider`)

### 3. Otentikasi & Keamanan
- [ ] MFA (Filament v5 `->multiFactorAuthentication()`)
- [x] RBAC (`bezhansalleh/filament-shield` + Spatie Permission teams mode)
- [ ] Audit Log via `spatie/laravel-activitylog` (modul `Monitoring` saat ini punya `AuditLog` custom, bukan Spatie)

### 4. Billing & Subscription
- [~] Payment Gateway integration (Midtrans webhook + `BillingService` / `BillingPage`; recurring engine belum lengkap)
- [ ] Pay-per-Module + per-seat calculator (model `SubscriptionPlan`, `SubscriptionLog` sudah ada, engine belum)
- [ ] Grace Period & auto-lock

### 5. Global Settings & Branding
- [ ] White Labeling (logo + primary color per tenant)
- [x] Localization id/en (`FilamentUi`, `preferred_locale`, `SetUserLocale` middleware, `TenantSetting.default_locale`)
- [ ] Format mata uang per-tenant (IDR/USD)

### Deliverables Fase 1
- [ ] Landing Page publik
- [ ] Self-registration (tenant + super-admin user)
- [ ] Super Admin (platform owner) Dashboard cross-tenant
- [x] Tenant Dashboard (`TabbedDashboard`)
- [ ] "App Store" internal untuk aktivasi modul

---

## Urutan Kerja yang Direkomendasikan

### Sprint 1 — Quick Wins (independen, paralel-able)

#### 1.1 MFA Filament v5
- Aktifkan `->multiFactorAuthentication()` di `AdminPanelProvider`
- Migration kolom MFA di `users`
- Wajibkan untuk role Admin & Finance via policy

#### 1.2 Spatie ActivityLog
- `composer require spatie/laravel-activitylog`
- Attach `LogsActivity` trait ke model sensitif (User, Tenant, StudentInvoice, Payment, PurchaseOrder, dst.)
- Putuskan: jalankan paralel dengan `Monitoring\AuditLog` atau swap source-nya

#### 1.3 App Store Modul + Navigation Gating
- Filament page `ModuleMarketplace` di Core: list `Module`, toggle aktivasi → tulis ke `TenantModule`
- Override `ModuleResource::shouldRegisterNavigation()` untuk cek modul aktif pada tenant berjalan
- Cache resolver modul aktif per tenant

#### 1.4 White-Label Dasar
- Tambah `TenantSetting` keys: `brand_logo`, `primary_color`
- Inject ke panel via `->brandLogo(fn () => …)` dan `->colors(fn () => …)` dengan closure baca tenant aktif
- Form upload logo di Tenant Settings page

### Sprint 2 — UX Onboarding

#### 2.1 Landing Page
- Ganti route `/` dengan halaman marketing sederhana
- CTA: Daftar / Login

#### 2.2 Self-Registration
- Aktifkan `->registration()` di panel
- Custom `RegisterTenant` page: buat `Tenant` → `User` → assign super-admin role via Shield
- Email verification

#### 2.3 Super Admin (Platform Owner) Panel
- `PlatformPanelProvider` baru, path `/platform`, **tanpa** `->tenant()`
- Resource: Tenant list, MRR widget, modul terpakai, recent signups
- Akses hanya untuk role `platform_owner`

### Sprint 3 — Monetization (butuh keputusan bisnis dulu)

**Keputusan dibutuhkan:**
- Gateway: Midtrans (Indonesia) vs Stripe Cashier (global)
- Model billing: per-seat × per-module vs flat per plan
- Currency default

#### 3.1 Billing Engine
- Integrasi gateway terpilih
- Recurring invoice generator dari `SubscriptionPlan` + `TenantModule` aktif + jumlah user

#### 3.2 Grace Period & Auto-Lock
- Job harian cek status pembayaran
- Middleware tenant: lock akses modul jika `subscription_status = past_due` lewat grace period

---

## Backlog / Tunda

- **Subdomain Routing** — path-based (`/admin`) sudah cukup untuk MVP. Subdomain menambah cost ops (wildcard SSL, session config, env complexity) tanpa nilai pengguna langsung. Re-evaluate setelah ada 10+ tenant aktif.

---

## Definition of Done per Item

Setiap item Sprint dianggap selesai bila:
1. Feature test PHPUnit lulus (happy path + 1 edge case)
2. Label UI lewat `FilamentUi` (id/en)
3. `php artisan shield:generate --all --panel=admin --option=permissions --no-interaction` dijalankan jika ada resource baru
4. `vendor/bin/pint --dirty --format agent` bersih
5. Tercatat di `CHANGELOG` atau commit message yang deskriptif
