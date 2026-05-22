# ROADMAP v0.5 — Foundation Governance & Facilities Operations

**Scope:** Tata kelola yayasan, legalitas, sarpras, gedung/ruangan, dokumen & arsip, e-office, helpdesk, IT management, transportasi, asrama, kantin, keamanan fisik, sustainability.

**Rujukan:** lihat [ROADMAPv04-overview.md](ROADMAPv04-overview.md) untuk konvensi modul baru, wave planning, dan DoD global.

---

## Prasyarat dari Roadmap Sebelumnya

- v01 Sprint 1.3 — App Store Modul + Navigation Gating (semua modul di sini wajib pakai gating `TenantModule`)
- v01 Sprint 1.2 — Spatie ActivityLog (DMS audit trail piggyback)
- v01 Sprint 2.3 — Super Admin Panel (untuk legalitas yayasan cross-tenant view)
- v02 Sprint 2.3 — Evidence Requirement Workflow (DMS approval gates)
- v09 Sprint 1 — WhatsApp Gateway (reminder kontrak/dokumen kedaluwarsa)

---

## Sprint 1 — Foundation Governance + Legal & Kontrak

### 1.1 Foundation Governance (extend Core)
**Tidak buat modul baru.** Ekstensi di Core karena `Tenant`/`Organization`/`Department` sudah ada.

- Model baru di `Modules\Core`: `FoundationProfile` (1-1 dengan Tenant: visi, misi, nilai inti, logo, dokumen profil)
- Model `Stakeholder` polymorphic (pengurus/pembina/pengawas/komite) — terhubung ke `User` opsional
- `Organization` sudah mendukung hirarki — pastikan kolom `parent_organization_id` + scope tree query
- Enum `OrganizationType`: `unit_pendidikan` (TK/SD/SMP/SMA/SMK/PT/kursus/pesantren/daycare), `unit_usaha`, `direktorat`, `cabang`
- Filament page **Struktur Yayasan** (org chart pakai `livewire-charts` atau static SVG)

### 1.2 Legal & Kontrak (modul baru `Legal`)
- `php artisan module:make Legal`
- Models: `LegalDocument` (akta, SK Kemenkumham, NIB, NPWP, izin operasional, MoU/MoA), `Contract` (pegawai/vendor/tenant/kerja sama), `ContractParty`, `ContractAttachment`
- Field: `effective_date`, `expires_at`, `auto_renew`, `notice_period_days`, `owner_unit_id`
- Listener event `ContractExpiringSoon` (cron harian) → notification ke owner via channel (lihat v09 Sprint 1 WhatsApp)
- Workflow approval kontrak (extend Workflow V2 — pola Procurement)
- E-sign integration **abstraction layer** (provider: Privy/Mekari Sign/manual upload signed PDF) — implementasi konkret tunda ke v09 Sprint 4 integrasi eksternal
- Riwayat perubahan struktur via Spatie ActivityLog (v01 Sprint 1.2)

---

## Sprint 2 — Aset/Sarpras + DMS

### 2.1 Modul Aset (modul baru `Asset`)
- Models: `AssetCategory`, `Asset`, `AssetMovement` (mutasi), `AssetMaintenance`, `AssetDepreciation`, `AssetInsurance`, `AssetLoan` (peminjaman internal)
- Field `Asset`: kode (auto + prefix unit), QR code (lib `simple-software-io/simple-qrcode`), kondisi enum, foto (multi), nilai perolehan, umur ekonomis, metode depresiasi enum (straight-line/declining)
- Service `AssetDepreciationService` — generate jurnal depresiasi bulanan (bridge ke Finance — pola `VendorBillAutoCreationService::postDraftJournal`)
- Scheduled command harian: `asset:check-maintenance-due`, `asset:check-insurance-expiry`
- Filament action: Cetak QR Label A4/Thermal (dompdf), Stock Opname session (wizard)
- **Skip MVP:** IoT sensor real-time, lelang aset, peta aset visual per ruangan (defer ke Sprint 4 Facility)

### 2.2 DMS — Document Management System (modul baru `Dms`)
- Models: `DocumentFolder` (tree per unit), `Document`, `DocumentVersion`, `DocumentAccessLog`
- Storage: gunakan filesystem `s3`/`local` dengan visibility `private` (CLAUDE.md note: file visibility private by default)
- Versioning: setiap upload baru → row `DocumentVersion`, current = latest
- Full-text search: kolom `searchable_content` (di-extract pakai `smalot/pdfparser` untuk PDF, fallback filename)
- OCR untuk scan: **opsional**, behind feature flag — pakai `thiagoalessio/tesseract_ocr` jika diaktifkan
- Approval dokumen: integrasi Workflow V2
- Retensi: enum `retention_policy` + scheduled command `dms:archive-expired`
- Hak akses: per-folder ACL via Spatie Permission custom + `DocumentAccessLog` setiap akses
- E-signature: defer abstraction sama dengan Legal Sprint 1.2

---

## Sprint 3 — Helpdesk

### 3.1 Modul Helpdesk (modul baru `Helpdesk`)
- Models: `Ticket`, `TicketCategory`, `TicketComment`, `TicketAttachment`, `KnowledgeBaseArticle`
- Field `Ticket`: kategori (IT/sarpras/akademik/keuangan/HR/umum), prioritas enum, SLA per kategori (jam respon/jam resolusi), `assigned_to_user_id`, `escalated_at`
- Listener: `TicketCreated` → assign default petugas per kategori (round-robin atau least-load)
- Scheduled `helpdesk:check-sla` (per 15 menit) → escalate jika overdue + notification
- Filament page **Helpdesk Dashboard**: SLA compliance, avg response, avg resolution, beban per petugas
- Survey kepuasan: model `TicketSatisfaction` (1–5 + komentar) — link survei dikirim saat ticket closed
- AI auto-categorization & recommendation: defer ke v08 AI Sprint 6 (placeholder kolom `ai_suggested_category`)
- **Catatan:** "Maintenance request" → bridge ke `AssetMaintenance` Sprint 2.1

---

## Sprint 4 — Gedung/Ruangan + Booking

### 4.1 Modul Facility (modul baru `Facility`)
- Models: `Building`, `Floor`, `Room`, `RoomEquipment`, `RoomBooking`, `RoomMaintenanceLog`
- Field `Room`: kapasitas, tipe enum (kelas/lab/aula/studio/meeting/lainnya), `is_bookable`, dependencies (AC, listrik, jaringan, proyektor)
- Booking: model `RoomBooking` (start, end, purpose, requester_user_id, status enum draft/pending/approved/rejected)
- Konflik detection: scope query overlap interval + DB index `(room_id, start_at, end_at)`
- Approval booking via Workflow V2 (kondisional: hanya untuk tipe ruangan tertentu via setting)
- Calendar view: pakai Filament v5 `Filament\Schemas\Components\Calendar` atau Fullcalendar.js
- Bridge: `Schedule` (School) ↔ `RoomBooking` — listener: schedule pelajaran auto-book ruangan
- **Skip MVP:** energy monitoring real-time, CCTV integration, smart classroom dashboard, denah ruangan grafis (defer)

### 4.2 Sewa Fasilitas (bridge ke Unit Usaha)
Ruangan dengan flag `is_rentable=true` muncul juga di v07 Sprint 3 Unit Usaha Sewa Fasilitas — **satu model, dua use case**, tidak duplikasi.

---

## Sprint 5 — E-Office Surat + IT Management

### 5.1 E-Office Surat (modul baru `EOffice`)
- Models: `LetterCategory` (surat masuk/keluar/SK/SP/SR/dll), `Letter`, `LetterAttachment`, `LetterDisposition`, `LetterTemplate`
- Nomor surat otomatis: `LetterNumberingService` dengan format per-template + counter per-tenant per-tahun-bulan
- Workflow approval surat keluar (Workflow V2 + Evidence Requirement v02 Sprint 2.3)
- QR validasi: setiap surat keluar ter-stempel QR yang link ke endpoint publik `/letters/verify/{token}` (read-only)
- Tracking disposisi: chain `LetterDisposition` (from_user → to_user, instruksi, deadline)
- Dashboard: surat belum diproses, surat overdue, beban disposisi per user
- Template engine: Blade + variabel `{{ student_name }}`, `{{ today_id }}` dst. di `LetterTemplate.body`
- Email integration: outbound via Laravel Mail (config existing); inbound parser opsional via IMAP (defer)

### 5.2 IT Management (modul baru `ItOps`)
- Reuse models `Asset` (Sprint 2.1) dengan kategori IT — TIDAK buat tabel terpisah untuk laptop/PC/server (gunakan `AssetCategory` + custom field via `meta` json)
- Models baru spesifik IT: `SoftwareLicense`, `UserAccount` (akun email/aplikasi/SSL/domain), `BackupJob`, `IpAddressRecord`
- Scheduled `itops:check-expiry` (lisensi, SSL, domain, hosting)
- Integration **abstraction**:
  - `MikroTikClient` interface — implementasi konkret tunda v09 (provider: routeros-api)
  - `MonitoringWebhookReceiver` — endpoint untuk Zabbix/Prometheus alert (1 endpoint, 1 listener `ItIncidentReceived`)
- User Access Review: scheduled report per quarter — list user + role + last login
- Password policy: ekstensi `UserPasswordPolicy` di Core (force change interval, min length, complexity)
- **Skip MVP:** patch management automation, vulnerability scanner (tunda ke v08 ISO 27001)

---

## Sprint 6 — Operasional Kondisional (per Tipe Sekolah)

Aktifkan hanya jika `TenantModule` modul yang sesuai diaktifkan.

### 6.1 Transportasi Sekolah (modul baru `Transport`)
- Models: `Vehicle`, `Driver`, `Route`, `RouteStop`, `RouteSchedule`, `StudentShuttleSubscription`, `BoardingLog` (presensi naik/turun), `VehicleMaintenance`
- Bridge `Vehicle` ke `Asset` (vehicle = asset dengan kategori khusus + 1-1 detail)
- Tagihan transportasi: bridge ke Finance `StudentInvoiceItem` via `TuitionType` baru "Transportasi"
- Tracking real-time GPS: **defer**, hanya checkpoint manual via QR di titik jemput untuk MVP
- BBM, pajak STNK/KIR/asuransi: model `VehicleOperatingCost` + reminder kedaluwarsa
- Notification ke orang tua saat anak naik/turun: depend v09 WhatsApp Sprint 1

### 6.2 Asrama / Boarding (modul baru `Boarding`)
- Models: `Dormitory`, `Room` (reuse `Facility\Room` dengan tipe `dormitory`), `RoomAssignment`, `BoardingAttendance`, `BoardingLeavePermit`, `RoomInspection`, `BoardingMealRecord`, `LaundryRecord`
- Konsumsi/makan: bridge ke Kantin Sprint 6.3 (jika kantin sekolah juga handle asrama)
- Tagihan asrama: pola sama dengan transportasi
- CCTV: hanya log akses (config link RTSP per area), bukan stream
- **Skip MVP:** kebersihan otomatis, IoT pintu

### 6.3 Kantin (modul baru `Cafeteria`)
- Models: `CafeteriaTenant`, `Menu`, `MenuStock`, `CafeteriaTransaction`, `MealSubscription` (paket makan bulanan), `MealRating`
- Pembayaran cashless: integrasi kartu siswa via QR + saldo prepaid (model `StudentWallet`)
- Revenue sharing tenant: scheduled `cafeteria:settle-tenants` (mingguan/bulanan)
- Sertifikasi halal & inspeksi kebersihan: model `CafeteriaInspection`
- Monitoring gizi: tag menu (kalori, protein) — manual entry by tenant
- **Skip MVP:** pre-order makanan real-time, app khusus tenant (tenant operate via Filament guest panel — defer)

---

## Sprint 7 — Keamanan Fisik + Sustainability

### 7.1 Keamanan Fisik (modul baru `PhysicalSecurity`)
- Models: `Visitor`, `VisitorLog`, `Guard`, `PatrolSchedule`, `PatrolLog`, `Incident` (linked ke `Risk` di v08 Sprint 1)
- Visitor management: form check-in dengan KTP scan (OCR opsional dari DMS Sprint 2.2), QR badge cetak
- Buku tamu digital: tablet kiosk page (Livewire fullscreen, tidak butuh login)
- Gate pass: izin keluar siswa selama jam sekolah → workflow approval wali kelas + orang tua (parent portal v06)
- Emergency / panic button: model `EmergencyAlert` + listener broadcast (depend v09 channel WA/SMS)
- K3 checklist: model `SafetyChecklist` + scheduled `safety:daily-rounds`
- **Skip MVP:** integrasi access control hardware, face recognition (skip permanen)

### 7.2 Sustainability (lightweight dashboard, BUKAN modul baru)
- Tambah ke `Facility` module sebagai page `SustainabilityDashboard`
- Input meter manual: `UtilityReading` (listrik/air/internet/sampah) per gedung per bulan
- Auto-import: bridge dari IT monitoring webhook (Sprint 5.2) jika ada smart meter
- Carbon footprint: kalkulator sederhana per `UtilityReading` × faktor emisi (configurable)
- Kampanye penghijauan: bridge ke v06 Event Module
- **Skip MVP:** solar panel monitoring native (delegasi ke provider via API webhook), audit lingkungan otomatis

---

## Backlog Modul-Specific

- **Asset IoT real-time sensor** — defer, butuh device standardization
- **Smart classroom dashboard** — defer ≥18 bulan
- **Pre-order makanan kantin via mobile** — defer ke v09 mobile app phase
- **Face recognition visitor** — skip permanen (privacy + biaya)
- **GPS real-time vehicle tracking** — defer, partner solution Trello/GPS provider

---

## Definition of Done (Modul-Specific)

Tambahan di atas DoD global di v04:

1. Setiap modul baru wajib `TenantModule` registration + factory + seeder
2. Bridge ke `Asset` (untuk IT/Vehicle/etc.) wajib pakai relasi, bukan duplikasi tabel
3. Workflow integration wajib seeder workflow default (minimal 1 happy path)
4. QR code generation pakai `simple-software-io/simple-qrcode` (konsisten cross-module)
5. Scheduled command terdaftar di `app/Console/Kernel.php` dengan `withoutOverlapping()`
