# ROADMAP v0.9 — Platform Extension: Mobile, Integrations, Workflow Builder, Public API, SaaS Premium

**Scope:** Mobile App, External Integrations (WhatsApp/SMS/SSO/Dapodik/PDDikti/Fingerprint/RFID/CCTV/MikroTik/Google/Microsoft/Zoom), Workflow Engine Expansion (delegation/escalation/builder UI), Public API & Developer Platform, Super Admin Expansion, SaaS Reseller & White-Label Expansion.

**Rujukan:** [ROADMAPv04-overview.md](ROADMAPv04-overview.md).

---

## Prasyarat dari Roadmap Sebelumnya

- v01 Sprint 1.1 — MFA Filament v5
- v01 Sprint 2.3 — Super Admin Panel platform owner
- v01 Sprint 3 — Payment Gateway (Midtrans/Stripe)
- v02 Sprint 1.5 — GPS/Foto Attendance (mobile use case prasyarat)
- v02 Sprint 2.3 — Evidence Requirement Workflow
- v02 Sprint 2.4 — Workflow Approval Limit Seeder
- v03 Sprint 1.1 — Global Search (Mobile reuse)

---

## Sprint 1 — WhatsApp Gateway + Notification Channel Expansion

**Prioritas tertinggi** karena banyak modul (v05–v08) depend ke ini.

### 1.1 Notification Channel Abstraction
- Service `NotificationDispatcher` dengan channel: `database` (existing), `mail` (existing), `whatsapp`, `sms`, `push`, `telegram`
- Setiap user punya `NotificationPreference` per channel per category
- Notification template registry: model `NotificationTemplate` (versioned, variable substitution)
- Retry & idempotency: pakai Laravel queue retry + idempotency key di payload
- Notification log: model `NotificationDelivery` (status: queued/sent/delivered/failed/read)

### 1.2 WhatsApp Gateway
- **Strategi:** abstraction layer, support multiple providers (Wablas/Fonnte/Twilio WA/360dialog/Meta Cloud API)
- Interface `WhatsAppProvider` dengan method `sendMessage`, `sendTemplate`, `sendMedia`
- Default: **Meta Cloud API** (official) untuk production; **Wablas/Fonnte** untuk fallback Indonesian provider
- Webhook receiver: `/webhooks/whatsapp/{provider}` dengan signature validation
- Inbound message handling: parse intent (reservasi/konfirmasi pembayaran/balas approval) → forward ke handler
- Approval via WhatsApp: pakai interactive button (Meta) atau code reply (Wablas). Linked ke v09 Sprint 3 Workflow expansion.
- Template management: pre-register WhatsApp Business templates (Meta requirement)

### 1.3 SMS Gateway (lighter scope)
- Provider abstraction (Twilio, Vonage, lokal Zenziva/Nusasms)
- Use case terbatas: OTP fallback, emergency alert, payment reminder rural area

### 1.4 Push Notification (Web + Mobile)
- Web Push: Firebase Cloud Messaging / Self-hosted VAPID
- Mobile: FCM (Android) + APNS (iOS) — gating per device token registry

### 1.5 Telegram Bot (Optional)
- Lightweight provider, untuk admin alerts internal saja (bukan parent/student)

---

## Sprint 2 — Mobile App (PWA + Native Shell)

**Strategi:** PWA-first (Filament responsive + manifest) untuk semua role; native WebView shell hanya jika ada kebutuhan offline/biometric/native sensor.

### 2.1 PWA Setup
- Service worker: cache strategi (offline-first untuk view, network-first untuk write)
- Manifest.json + icon set
- Install prompt
- Push notification subscription (Sprint 1.4)
- Filament responsive verified untuk semua panel (admin, parent v06, platform owner v01)

### 2.2 Native Mobile Shell (Capacitor / Bubblewrap TWA)
**Hanya jika butuh:**
- Presensi GPS offline (v02 Sprint 1.5 extension)
- Foto check-in dengan compression native
- Biometric login (FaceID/Fingerprint)
- Push notification native channel
- Scan barcode/QR native (lebih cepat dari web)

**Bukan native rewrite** — pakai Capacitor (Ionic) wrapping PWA. App ID per role:
- `app.fos.parent` — bridge ke `/parent` panel
- `app.fos.staff` — bridge ke `/admin` panel
- `app.fos.student` — read-only subset (defer Sprint 5)

### 2.3 Mobile-Specific Pages
- Presensi cepat dengan GPS + foto + offline queue
- Scan QR (aset/surat/event/visitor)
- Approval inbox (paginate Workflow tasks user)
- Upload bukti pembayaran (parent role)
- **Skip MVP:** chat in-app, video conferencing, full LMS mobile

### 2.4 Offline Mode (Limited)
- Outbox pattern lokal (IndexedDB) untuk: presensi, approval queue
- Sync saat online: bridge ke API endpoints Sprint 4

---

## Sprint 3 — Workflow Engine Expansion

**Extend** `Modules\Workflow` V2 — bukan rewrite. Semua additions backward-compatible.

### 3.1 Delegation
- Model `WorkflowDelegation`: from_user → to_user, valid period, scope (workflow types or all)
- Saat assign task: lookup active delegation → reroute
- Audit log delegation usage

### 3.2 Escalation
- Field `WorkflowStep.sla_hours` (sudah ada partially)
- Scheduled `workflow:escalate-overdue` per jam — eskalasi ke supervisor (lookup org hierarchy)
- Reminder per N% SLA (50%, 80%, 100%, overdue)

### 3.3 Parallel & Serial Approval
- `WorkflowStep.parallel_group_id` — steps dalam group sama = parallel, butuh all complete untuk advance
- Conditional path sudah didukung (JsonLogic via RuleEngine existing)

### 3.4 Multi-Channel Approval
- Approve via email link (signed URL, exp 24h)
- Approve via WhatsApp interactive button (depend Sprint 1.2)
- Approve via mobile (depend Sprint 2.3)
- Audit channel di `WorkflowAssignment.approved_via`

### 3.5 Workflow Builder UI
**Defer ke akhir** karena complex. MVP: workflow definition tetap di seeder/database. Builder UI = nice-to-have.

- Visual editor: nodes (steps) + edges (transitions) + condition (JsonLogic GUI)
- Versi workflow: existing snapshot pattern (`WorkflowInstanceStarter`) di-extend untuk publish-version flow
- Library: import/export workflow as JSON

### 3.6 Custom Form Workflow
- Workflow yang punya form input di setiap step (bukan hanya approve/reject)
- Model `WorkflowFormSchema` per step
- Render via Filament Form schema dynamic
- Use cases: pengajuan beasiswa, pengajuan keringanan biaya, pengajuan akses sistem

---

## Sprint 4 — External Integrations Expansion

Build sebagai **adapter layer** di Core atau modul `Integration`. Setiap integrasi behind feature flag.

### 4.1 Authentication Integrations
- **OAuth 2.0 Server (Laravel Passport)**: jadikan FoS sebagai SSO provider untuk app pihak ketiga
- **OAuth Client**: support login dengan Google Workspace, Microsoft 365 (Filament socialite plugin)
- **SAML 2.0**: untuk enterprise customer (defer hingga ada permintaan)
- **LDAP/AD**: skip on-prem direct (per v04 backlog) — pakai Azure AD OAuth bridge

### 4.2 Indonesian Education Compliance Integrations
- **Dapodik** (sekolah K-12): export data siswa/guru/sekolah ke format Dapodik (SQLite/CSV) — scheduled & manual trigger
- **PDDikti** (PT): bridge ke feeder API untuk pelaporan mahasiswa/dosen
- **EMIS** (Madrasah Kemenag, jika ada unit madrasah): defer hingga permintaan
- **Verifikasi NIK** Dukcapil (PPDB): defer (butuh kerja sama formal)

### 4.3 Communication Integrations
- **Zoom**: meeting create/list (bridge v08 Sprint 4.2 Meeting)
- **Google Meet**: link generation via Calendar API
- **Google Workspace**: email/calendar/drive (untuk user yayasan yang pakai GWS)
- **Microsoft 365**: equivalent (Graph API)

### 4.4 Hardware Integrations
- **Fingerprint machine**: model `BiometricDevice` + protocol adapter (Fingerspot via SDK push, Solution X100 HTTP push)
- **RFID card reader**: HID adapter untuk akses ruangan & kantin/koperasi
- **CCTV**: hanya catat link RTSP & event metadata, bukan stream (kompliance privacy)
- **MikroTik**: `MikroTikClient` (routeros-api PHP) untuk hotspot management, bandwidth limit per user/role, captive portal integration

### 4.5 Financial Integrations Expansion
- **Virtual Account Bank**: BCA/Mandiri/BNI/BRI/Permata via aggregator (Faspay/iPaymu/Xendit)
- **QRIS dinamis**: per-invoice
- **Accounting software bridge**: export ke Accurate/Zahir/Jurnal.id (CSV/API)
- **Pajak**: e-Faktur integration (defer, butuh sertifikasi DJP)
- **E-sign**: Privy/Mekari Sign adapter (bridge v05 Legal Sprint 1.2)

### 4.6 Storage Integrations
- **Cloud Storage**: S3/GCS/Azure Blob (Laravel filesystem driver — sudah didukung)
- **Backup off-site**: scheduled push ke S3 dengan encryption at rest

### 4.7 Existing Integrations Polish
- **Moodle** (existing): tambah edge cases, finalize MoodleEnrollmentReconciler (v03 prasyarat polish)
- **SLiMS** (existing Library): finalize integration tests

---

## Sprint 5 — Public API & Developer Platform

### 5.1 Public API (modul baru `Api` atau extend Core)
- Versioned: `/api/v1/...` (Sanctum existing untuk internal/admin token; Passport untuk OAuth public)
- Resource endpoints: per modul, gated by TenantModule + OAuth scope
- Rate limiting: per token + per IP
- API documentation: auto-generated dengan Scribe (`knuckleswtf/scribe`)
- API logs: model `ApiRequestLog` (sampling-based untuk volume)

### 5.2 Webhooks
- Modul `Webhook` (already partially exists via `WebhookSubscriptions` di Monitoring)
- Subscriber registration UI + secret management
- Event registry: `student.created`, `invoice.paid`, `enrollment.approved`, `donation.received`, etc.
- Delivery retries with exponential backoff
- Signature: HMAC-SHA256

### 5.3 Developer Portal
- Subdomain/path `/developers` — public landing
- Docs viewer (Scribe output embed)
- API key/OAuth client management self-service
- Sandbox environment per tenant (opsional, defer)
- Integration marketplace listing (3rd party connector showcase)

### 5.4 Plugin System (defer atau partial)
- Module system **sudah ada** via coolsam/modules — leverage existing
- 3rd party plugins: defer, butuh sandboxing untuk safety
- Custom report API: Sprint 5.2 BI v08 sebagai data source via API

---

## Sprint 6 — Super Admin Expansion + SaaS Premium Features

### 6.1 Super Admin Panel Expansion (extend v01 Sprint 2.3)
- Feature toggle UI: enable/disable per tenant atau global
- Maintenance mode toggle dengan whitelist IP
- Queue monitor: pakai Laravel Horizon (`composer require laravel/horizon`)
- Job monitor: failed jobs replay UI
- Error log viewer: integrasi Laravel Pulse exceptions panel
- License management: per-tenant license key, expiry, feature flags
- System health check: dashboard ringkasan (DB connections, Redis, queue depth, disk, Pulse status)

### 6.2 SaaS Multi-Tenant Premium Features
**Extend** v01 Sprint 1.3 App Store + v01 Sprint 3 Billing. Premium tier features:

- **Custom domain per tenant**: model `TenantDomain` + middleware resolver (selain subdomain path-based v01)
- **Branding per tenant lanjut**: bukan hanya logo+color (v01 Sprint 1.4), tapi typography, email template, PDF letterhead per tenant
- **White Label penuh**: hilangkan branding FoS untuk tier enterprise
- **Tenant isolation hardening**: query log scanner untuk cek tenant_id always applied (CI gate)
- **Tenant backup**: scheduled per-tenant dump ke S3, restore self-service
- **Tenant migration**: export → import tooling untuk pindah server
- **Tenant analytics**: usage tracking per tenant (active users, storage, API calls, modul aktif)
- **Usage-based pricing**: bridge billing v01 Sprint 3.1 — track quotient per resource → bill overage
- **Upgrade/downgrade paket**: self-service di Tenant Settings dengan prorating
- **Reseller management**: model `Reseller`, `ResellerCommission`, partner dashboard view tenants mereka
- **License key generator**: untuk reseller distribute manual
- **Support ticket per tenant**: bridge v05 Helpdesk dengan kategori `saas_support` visible ke platform owner panel

### 6.3 SaaS Onboarding Improvements
- Trial account (14/30 hari free)
- Onboarding wizard: setelah register (v01 Sprint 2.2) → step-by-step setup (profil yayasan → unit pertama → user admin → modul awal)
- Empty state guide di tiap modul aktif
- In-app tour: pakai `intro.js` atau `shepherd.js`

---

## Konflik & DRY

- **Push Notification Sprint 1.4 vs Database Notifications existing** — DB notifications adalah channel `database` di `NotificationDispatcher`. Push = channel terpisah. Satu unified API.
- **OAuth Server Sprint 4.1 vs Sanctum existing** — Sanctum untuk internal/SPA; Passport untuk public OAuth. Coexist.
- **Mobile presensi Sprint 2.3 vs v02 Sprint 1.5 GPS Attendance** — v02 buat schema & form web; v09 Sprint 2 tambah native experience + offline.
- **Reseller management Sprint 6.2 vs Tenant management v01** — Reseller = pihak yang manage multiple tenants atas nama mereka, tambahan layer di atas Tenant existing.
- **Workflow Builder UI Sprint 3.5 vs Workflow V2 existing** — Builder = GUI di atas data model existing, bukan rewrite. JSON spec yang dihasilkan tetap kompatibel dengan `WorkflowInstanceStarter`.

---

## Backlog Modul-Specific

- **Native iOS/Android rewrite penuh** — skip permanen kecuali ada KPI bisnis kuat
- **SAML 2.0 IdP** — defer hingga 3 customer enterprise minta
- **EMIS Kemenag full integration** — defer
- **e-Faktur DJP** — defer, butuh sertifikasi
- **In-house ML hosting untuk AI** — pakai LLM API (v08 Sprint 6)
- **Plugin marketplace 3rd party** — defer, security concern

---

## Definition of Done (Modul-Specific)

1. Setiap integrasi eksternal wajib:
   - Abstraction interface (untuk multi-provider swap)
   - Mock di test (no real API call)
   - Webhook signature verification test
   - Feature flag di config + TenantSetting
2. WhatsApp/SMS provider wajib idempotency + retry test
3. PWA wajib lulus Lighthouse PWA audit ≥90
4. Native shell wajib build success Android + iOS di CI
5. Public API wajib OpenAPI spec di-publish + test contract per endpoint
6. Webhook wajib delivery retry test + signature verify test
7. Custom domain wajib SSL provisioning test (Let's Encrypt auto)
8. Tenant migration export → import wajib parity test (row count + checksum)
9. Workflow delegation/escalation wajib test SLA edge cases (timezone, holiday)
