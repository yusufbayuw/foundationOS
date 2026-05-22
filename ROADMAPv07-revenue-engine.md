# ROADMAP v0.7 — Revenue Engine & Business Units

**Scope:** PPDB CRM, Website/CMS Yayasan, Donasi/Wakaf/Endowment, Koperasi/Toko POS, Unit Usaha Yayasan (Kursus, EO, Sewa, Percetakan, Konsultan, Properti), Marketplace Internal.

**Rujukan:** [ROADMAPv04-overview.md](ROADMAPv04-overview.md).

---

## Prasyarat dari Roadmap Sebelumnya

- v01 Sprint 3 — Billing & Payment Gateway (Midtrans/Stripe terpasang)
- v02 Sprint 2.2 — AR umum non-student (Invoice generic)
- v02 Sprint 3 — Inventory MVP (untuk Koperasi & Percetakan stok)
- v03 Sprint 4 — Sales/POS skeleton (Koperasi extend dari sini)
- v05 Sprint 4 — Facility & Booking (Sewa Fasilitas pakai infrastruktur ini)
- v06 Sprint 3.2 — Event Module (EO pakai infrastruktur Event)
- v06 Sprint 4.2 — Alumni (donor segment alumni)
- v09 Sprint 1 — WhatsApp Gateway (CRM follow-up, donasi update donor)

---

## Sprint 1 — PPDB CRM + Website/CMS Yayasan

### 1.1 PPDB CRM (extend Enrollment)
**Bukan modul baru** — extend Modules/Enrollment yang sudah ada.

- Models tambahan: `Lead`, `LeadSource`, `LeadActivity` (call/wa/email/visit), `LeadStage` (new/contacted/interested/applied/enrolled/lost), `LeadAssignment`
- Pipeline kanban: Filament page `LeadPipeline` dengan drag-drop antar stage
- Form inquiry publik: endpoint `/inquiry` (rate-limited) → auto-create Lead
- Source tracking: UTM parameter ke Lead.source_detail
- Follow-up reminder: scheduled `enrollment:lead-followup-due` per 4 jam
- Conversion tracking: ketika `Applicant` (existing model) dibuat dari Lead → set `lead_id`; tracked di funnel
- Cost per lead / acquisition: input manual marketing spend per campaign + auto-calc
- Promo code: model `PromoCode` (discount %, valid period, max uses) → applied ke `EnrollmentFee`
- Referral program: model `Referral` (referrer User, referred Lead, reward) → bridge Finance saat conversion
- Appointment school tour & trial class: model `SchoolTour`, `TrialClass` (book slot, capacity)
- AI rekomendasi follow-up: defer ke v08 AI Sprint 6

### 1.2 Website/CMS Yayasan (modul baru `Cms`)
**Catatan strategi:** website publik bukan dibangun in-house penuh sebagai page builder canggih — fokus headless content + landing page generator.

- Models: `Site` (per tenant, multiple sites: yayasan / unit-A / unit-B), `Page`, `PageBlock` (typed: hero/text/gallery/cta/form), `Article` (berita), `ArticleCategory`, `Banner`, `Testimonial`, `Gallery`, `Media`, `MenuItem`
- Render: **bukan** Filament panel publik. Buat route `web.php` group `cms` → controller render Blade dari `PageBlock` JSON
- SEO: meta_title/description/og_image per Page/Article + auto-slug + sitemap.xml generator
- Multi-language: pakai existing `FilamentUi` infrastructure untuk content fields (`title_id`, `title_en`)
- Approval konten: Workflow V2 (draft → review → published) + scheduled publish (kolom `publish_at`)
- Landing page templates: prebuilt untuk PPDB, Event, Donasi (link ke modul masing-masing) — bukan page builder bebas
- Form kontak → bridge ke PPDB CRM Lead (Sprint 1.1)
- Live chat: integrasi WA gateway (v09) sebagai click-to-chat button, bukan in-house chat widget
- Statistik kunjungan: integrasi Plausible/Umami self-hosted via JS snippet (defer install)
- **Skip MVP:** page builder drag-drop bebas, custom theme designer

---

## Sprint 2 — Donasi / Wakaf / Endowment

### 2.1 Modul Donation (modul baru `Donation`)
- Models: `Campaign`, `CampaignUpdate`, `Donor` (linked User opsional, support anonymous), `Donation`, `RecurringDonation`, `Endowment`, `Wakaf` (jenis: uang/tanah/bangunan, dokumen sertifikat)
- Jenis donasi: pendidikan, beasiswa, fasilitas, wakaf, zakat/infaq/sedekah (tag/category)
- Pembayaran: bridge ke payment gateway v01 Sprint 3.1 + QRIS dinamis per campaign + VA per donor besar
- Sertifikat donatur: PDF auto (dompdf) + email/WA
- Update program ke donor: scheduled `donation:send-campaign-update` saat `CampaignUpdate` di-publish (target: donors of that campaign)
- Recurring donation: scheduled `donation:charge-recurring` harian (depend payment gateway support)
- Bridge ke Finance:
  - Donation paid → auto-journal: Dr Kas/Bank, Cr Pendapatan Dana Donasi (akun khusus, bisa per campaign)
  - Wakaf tanah/bangunan → buat `Asset` di v05 Sprint 2.1 dengan kategori `wakaf`, journal Dr Tanah/Bangunan, Cr Dana Wakaf
- Donor segmentation: tag-based (alumni dari v06, big donor, recurring) + filter di list
- Fundraising dashboard: total campaign goal vs raised, top campaigns, donor retention
- Transparansi publik: bridge ke CMS Sprint 1.2 — page publik per campaign dengan running total
- Prediksi potensi donasi: defer v08 AI Sprint 6

---

## Sprint 3 — Koperasi & Toko POS

**Strategi:** Koperasi = ekstensi dari Sales/POS module v03 Sprint 4, bukan modul baru terpisah.

### 3.1 Koperasi Extension
- Models di v03 Sales: `Customer` (extends untuk member koperasi: `is_cooperative_member`, `member_number`)
- Models baru di v03 Sales: `CooperativeSavings` (simpanan: pokok/wajib/sukarela), `CooperativeLoan`, `LoanInstallment`, `Shu` (sisa hasil usaha)
- POS UI: extend v03 Sales POS dengan member lookup (scan QR kartu siswa/anggota)
- Diskon member otomatis di POS
- SHU calculation: scheduled tahunan, distribusi berdasarkan kontribusi (simpanan + transaksi)
- Bridge ke Finance: auto-journal simpanan/pinjaman/SHU
- Toko online internal: bridge ke Marketplace Sprint 6
- Pre-order seragam/buku: bridge ke v06 Sprint 4.1 MerchOrder (jangan duplikasi)

---

## Sprint 4 — Unit Usaha Wave 1: Kursus + Sewa Fasilitas

### 4.1 Kursus & Pelatihan (modul baru `Course`)
**Catatan:** beda dengan `Modules\Campus\Course` (mata kuliah). Nama modul ini `Training` untuk hindari konflik.

- `php artisan module:make Training`
- Models: `TrainingProgram`, `TrainingBatch`, `TrainingSession`, `Instructor`, `TrainingEnrollment`, `TrainingPayment`, `TrainingCertificate`, `CorporateTrainingPackage`
- LMS kursus: delegasi ke Moodle (existing integration) — `TrainingProgram` punya `moodle_course_id`
- Pendaftaran publik: bridge ke CMS landing page Sprint 1.2
- Payment: bridge gateway v01 Sprint 3.1
- Sertifikat: dompdf template + QR validasi
- Affiliate/referral: model `TrainingAffiliate` + komisi sales scheduled `training:settle-affiliate`
- Bridge ke Finance: revenue per program, COGS instruktur honor
- Evaluasi peserta: survey post-training

### 4.2 Sewa Fasilitas (extend v05 Facility)
**Bukan modul baru** — extend `Modules\Facility` dengan business rental track.

- Field tambahan `Room.is_rentable`, `Room.rental_rate_hourly`, `Room.rental_rate_daily`
- Model baru `FacilityRental` (parallel dengan `RoomBooking` internal, atau extend dengan `rental_type`)
- Customer eksternal: bridge ke v03 Sales `Customer`
- Kontrak sewa: bridge ke v05 Legal `Contract` (tipe `facility_rental`)
- Deposit + denda kerusakan: model `RentalDeposit`
- Checklist serah terima: bridge ke v05 Asset (jika sewa termasuk peralatan)
- Booking publik: form di CMS Sprint 1.2 → workflow approval (Workflow V2)
- Auto-journal Finance: rental revenue per kategori ruang
- **Skip MVP:** dynamic pricing seasonality

---

## Sprint 5 — Unit Usaha Wave 2: Percetakan + Konsultan + Properti

### 5.1 Percetakan & Penerbitan (modul baru `Printing`)
- Models: `PrintOrder`, `PrintOrderItem`, `PrintTemplate`, `PrintProduction` (status: queue/printing/finishing/done), `PrintMaterial` (bridge Inventory v02)
- ISBN management: model `Publication`, `PublicationAuthor`, `Royalty` (per copy sold)
- Estimasi biaya: kalkulator berdasarkan paper size × qty × material × finishing
- Katalog buku: bridge ke CMS Sprint 1.2 (display) + Marketplace Sprint 6 (jual)
- Royalti penulis: scheduled `printing:calculate-royalty-monthly`
- Bridge Inventory v02: konsumsi material via StockMove
- Bridge Finance: revenue, COGS material, royalti expense

### 5.2 Konsultan Pendidikan (modul baru `Consulting`)
**Strategi:** lightweight, banyak overlap dengan Project Management v08 — beda namespace, isolation per modul.

- Models: `ConsultingClient` (bridge Customer v03), `ConsultingEngagement`, `EngagementProposal`, `EngagementDeliverable`, `ConsultantTimesheet`, `EngagementInvoice`
- Bridge ke v08 Project Management Sprint 4 — `ConsultingEngagement` punya `project_id` opsional
- Proposal → workflow approval (Legal review)
- Timesheet: input mingguan oleh konsultan
- Profitability per project: revenue − (hours × cost_rate)
- Invoice: bridge AR generic v02 Sprint 2.2

### 5.3 Properti Komersial (modul baru `Property`)
- Models: `Property`, `CommercialTenant` (bukan SaaS Tenant — beda namespace), `LeaseAgreement`, `LeaseInvoice`, `PropertyMaintenance`, `LeaseDeposit`
- Bridge ke v05 Asset (Property = Asset subtype dengan rent-out flag)
- Tagihan sewa: scheduled `property:generate-monthly-lease-invoice`
- Reminder kontrak: bridge ke v05 Legal contract reminder
- Occupancy rate: dashboard utilisasi
- Bridge Finance: rental revenue, maintenance expense

---

## Sprint 6 — Marketplace Internal

### 6.1 Modul Marketplace (modul baru `Marketplace`)
- Models: `Seller` (User/Vendor/CooperativeMember/StudentEntrepreneur — polymorphic), `Product`, `ProductCategory`, `ProductVariant`, `Order`, `OrderItem`, `OrderShipment`, `ProductReview`
- Multi-seller: dashboard per seller, komisi platform per transaksi
- Pembayaran: split payment ke seller via gateway (jika support) — fallback: manual settlement scheduled
- Bridge ke Inventory v02 Sprint 3 (sellers internal pakai stok existing)
- Bridge ke v07 Donasi (campaign muncul juga di marketplace sebagai "produk" donasi)
- Bridge ke v06 Sprint 3.2 Event (tiket event)
- Review & rating produk
- Seller verification workflow (Workflow V2)
- Dashboard transaksi: GMV, komisi platform, top sellers, top products
- **Skip MVP:** native delivery integration (pakai pickup atau 3rd party manual)

---

## Catatan Konflik & DRY

- **Sales/POS v03 Sprint 4 + Koperasi v07 Sprint 3** → satu codebase, dua use case (member retail vs general)
- **Event v06 + EO bisnis v07** → satu Event module, dua mode via `EventType` (internal vs commercial). **Tidak buat EO module terpisah.**
- **MerchOrder v06 Sprint 4.1 vs Marketplace v07 Sprint 6** → MerchOrder = pre-order paket PPDB terkurasi; Marketplace = retail bebas multi-seller. **Keduanya berbeda use case.**
- **Project Mgmt v08 vs Consulting Engagement v07 Sprint 5.2** → Consulting extends Project (project_id FK), bukan tabel paralel.
- **Asset v05 vs Property v07 Sprint 5.3** → Property = subtype Asset dengan kolom rental khusus, satu hierarki.
- **CMS landing page v07 Sprint 1.2** → menggantikan kebutuhan "page builder" di banyak modul (PPDB landing, donasi landing, training landing, event landing) — DRY centralized.

---

## Backlog Modul-Specific

- **Live page builder drag-drop CMS** — defer ≥12 bulan
- **Dynamic pricing facility rental** — defer
- **Marketplace delivery integration native** — pakai 3rd party
- **AI cost-per-lead optimization** — defer v08 AI

---

## Definition of Done (Modul-Specific)

1. Setiap modul revenue wajib auto-journal test dengan assertion debit=kredit
2. Payment gateway integration wajib test webhook handling (success/fail/duplicate)
3. CMS publish action wajib test workflow approval gating
4. Multi-seller marketplace wajib isolation test antar seller
5. Sertifikat PDF wajib QR validasi endpoint accessible test
6. Recurring transaction (donation/lease) wajib idempotency test
