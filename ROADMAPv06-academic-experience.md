# ROADMAP v0.6 — Academic Experience & Student/Parent Engagement

**Scope:** Parent Portal, Bimbingan Konseling, UKS/Klinik, Ekstrakurikuler & Prestasi, Seragam & Buku, Event & Kegiatan, Alumni & Career, Komunikasi Internal Broadcast, Student Profile 360°, expansion Akademik Sekolah/PT.

**Rujukan:** [ROADMAPv04-overview.md](ROADMAPv04-overview.md).

---

## Prasyarat dari Roadmap Sebelumnya

- v01 Sprint 2.2 — Self-Registration (untuk parent self-onboarding)
- v02 Sprint 1.4 — HRM Workflow integration (pattern untuk BK/UKS approval)
- v03 Sprint 3.2 — Workflow Approval Revisi Nilai (pre-existing pattern)
- v03 Sprint 3.3 — SPP Auto-Generate Scheduler (bridge tagihan)
- v05 Sprint 2.2 — DMS (untuk surat sakit / dokumen kesehatan)
- v09 Sprint 1 — WhatsApp Gateway (semua notification flow)

---

## Sprint 1 — Parent Portal MVP

### 1.1 Panel Filament Terpisah `parent`
- `php artisan make:filament-panel parent` dengan path `/parent`
- **Tanpa** `->tenant()` di panel, scope manual lewat `belongsToMany` `User` ↔ `Student` (Student dari School module)
- Tabel relasi baru: `parent_student` (parent_user_id, student_id, relationship enum: ayah/ibu/wali, is_primary)
- Authorization: Spatie role `parent` (regenerate via `shield:generate` setelah resource ditambah)

### 1.2 Resource Read-Only untuk Orang Tua
- `MyChildrenResource` — daftar anak (multi-anak support)
- `ChildAttendanceResource` — read-only Attendance
- `ChildGradeResource` — read-only StudentGrade
- `ChildInvoiceResource` — read-only StudentInvoice + action **Bayar** (link payment gateway dari v01 Sprint 3.1)
- `ChildScheduleResource` — read-only Schedule
- `AnnouncementResource` — pengumuman sekolah (model baru `Announcement` di Core, scope ke kelas/sekolah/yayasan)

### 1.3 Action Orang Tua
- Action `RequestLeave` — buat `StudentAbsence` dengan tipe `izin/sakit`, upload surat sakit ke DMS (v05 Sprint 2.2)
- Action `RequestCounseling` — buat permintaan BK (Sprint 2.1)
- Action `ContactTeacher` — chat thread sederhana (model `ParentTeacherMessage`)

### 1.4 Survey Kepuasan
- Model `ParentSurvey`, `ParentSurveyResponse`
- Filament page survey untuk parent panel + admin page lihat hasil

### Skip MVP
- Konseling online video — defer, integrasi Zoom/Meet di v09 Sprint 4
- Real-time chat — pakai threaded message dulu

---

## Sprint 2 — Bimbingan Konseling + UKS

### 2.1 BK / Counseling (modul baru `Counseling`)
- Models: `Counselor` (extends Employee), `CounselingCase`, `CounselingSession`, `CounselingNote` (confidential), `CaseFollowUp`, `AnonymousReport`, `WellbeingSurvey`
- ACL khusus: `CounselingNote.is_confidential` → hanya konselor + kepala sekolah yang akses (override `viewAny` di policy)
- Kasus polymorphic `case_target`: Student/Class
- Early warning rules: scheduled `counseling:scan-risk` → cek pelanggaran > N, absensi < N%, nilai < N → auto-create `RiskFlag` di Student profile (Sprint 5.1)
- Bullying/anonymous report: form tanpa auth di `/parent/anonymous-report` (rate-limited)
- Workflow surat panggilan orang tua (Workflow V2 + E-Office Surat v05 Sprint 5.1)
- Bridge: BK case `risk_level=high` → notify wali kelas + parent (WA gateway v09)

### 2.2 UKS / Klinik (modul baru `Clinic`)
- Models: `HealthRecord` (per Student/Employee, polymorphic owner), `Allergy`, `MedicalHistory`, `ClinicVisit`, `MedicationStock`, `VaccinationRecord`, `InjuryReport`, `MedicalConsent`
- Stok obat: bridge ke Inventory (v02 Sprint 3) — `MedicationStock` extends `StockItem` (atau relasi 1-1)
- Surat sakit otomatis: action `IssueSickNote` → PDF (dompdf) + auto-create `StudentAbsence` excused
- Rujukan: model `MedicalReferral` (klinik/RS tujuan, dokumen rujukan)
- Statistik: dashboard kunjungan UKS per bulan, top diagnoses, alergi tracker per kelas
- **Skip MVP:** integrasi asuransi kesehatan (defer ke v09 Sprint 4)

---

## Sprint 3 — Ekstrakurikuler + Event + Komunikasi Internal

### 3.1 Ekstrakurikuler & Prestasi (extend School)
**Bukan modul baru** — extend Modules/School karena tightly coupled dengan Student.

- Models di School: `Extracurricular`, `ExtracurricularEnrollment`, `ExtracurricularAttendance`, `ExtracurricularAssessment`, `Competition`, `StudentAchievement` (already exists — extend)
- `Extracurricular.advisor_user_id` link ke Employee (pembina)
- Penilaian ekstra → masuk ke raport (kolom tambahan di `StudentGrade` atau tabel terpisah `StudentExtraGrade`)
- Talent scouting: query siswa dengan achievement_count > N + auto-tag
- Sertifikat: PDF generator (dompdf), template per ekstra/kompetisi
- Beasiswa prestasi: bridge ke Finance `StudentInvoiceDiscount` (model baru) atau scholarship grant model

### 3.2 Event & Kegiatan (modul baru `Event`)
- Models: `Event`, `EventProposal`, `EventBudget`, `EventCommittee`, `EventParticipant`, `EventTicket`, `EventCheckIn`, `EventCertificate`, `EventSponsor`, `EventVendor`
- Proposal kegiatan → Workflow V2 approval (dengan budget review — bridge ke Finance v02)
- Pendaftaran event:
  - Internal: link ke User
  - Eksternal: form publik tanpa login (rate-limited)
- Tiket berbayar: bridge ke payment gateway v01 Sprint 3.1
- QR check-in: scan via Filament page admin (camera Livewire)
- Sertifikat otomatis post-event (dompdf bulk)
- Event profitability report: aggregate revenue (ticket + sponsor) − cost (budget realisasi) → auto-journal bridge Finance
- Bridge ke v05 Sprint 4 Facility booking (panitia book ruangan)

### 3.3 Komunikasi Internal Broadcast (extend Core)
**Bukan modul baru.** Tambahkan resource & service.

- Model `Broadcast` di Core (audience: tenant/unit/role/class/parent), `BroadcastChannel` enum (db/email/wa)
- Service `BroadcastService::send(Broadcast $b, Collection $users)` — dispatch via channel
- Pengumuman publik: model `Announcement` (sudah disebut Sprint 1.2) → bisa di-promote jadi broadcast
- Newsletter: scheduled `comms:send-newsletter` per tenant (template + variable substitution)
- Emergency broadcast: priority queue separate (akan dipakai juga oleh v05 Sprint 7.1 panic button)
- Survey & polling: model `Poll` ringan + relation `PollResponse`
- Forum/chat: **defer permanen** — fitur ini akan buat scope creep tanpa nilai bisnis langsung. Pakai email/WA broadcast saja.

---

## Sprint 4 — Seragam & Buku, Alumni & Career

### 4.1 Seragam & Buku (modul baru `MerchOrder`)
**Penting:** modul ini berbeda dengan Koperasi (v07) — fokus pre-order paket terkurasi untuk PPDB, bukan retail.

- Models: `UniformPackage` (paket per jenjang/jenis kelamin/ukuran), `BookPackage` (paket per kelas), `MerchOrder`, `MerchOrderItem`, `MerchPickup` (QR pengambilan)
- Bridge ke PPDB Enrollment: action **Generate Paket Standar** otomatis di akhir registrasi
- Bridge ke Finance: order paid → `StudentInvoice` lunas → `MerchOrder.status = ready`
- Bridge ke Inventory v02 Sprint 3: pengeluaran stok via `StockMove` saat pickup
- Retur barang: model `MerchReturn` + workflow approval

### 4.2 Alumni & Career Center (modul baru `Alumni`)
- Models: `Alumnus` (linked ke Student lulus, copy snapshot), `AlumnusEmployment`, `AlumnusEducation`, `AlumnusAchievement`, `JobPosting`, `InternshipPosting`, `CompanyPartner`, `MentoringSession`, `AlumniDonation`
- Tracer study: form survei tahunan, scheduled `alumni:tracer-study-blast` setiap tahun ajaran baru (depend WA gateway)
- Engagement score: poin per aktivitas (response tracer, donasi, mentoring, event hadir)
- Bridge ke v07 Donasi (alumni segment donor)
- Career fair: bridge ke v06 Sprint 3.2 Event module (special EventType)
- CV builder & portfolio: defer MVP — simple `Alumnus.cv_path` upload

---

## Sprint 5 — Student 360° + Academic Analytics

### 5.1 Student Profile 360° (extend School/Campus)
- Filament infolist baru `StudentProfile360Infolist` di `ViewStudent` page
- Tabbed layout: Akademik (nilai/absensi/jadwal) | Keuangan (invoice/payment/discount) | Kesehatan (Sprint 2.2) | Konseling (Sprint 2.1, gated by ACL) | Prestasi & Pelanggaran (Sprint 3.1) | Dokumen (DMS v05) | Transportasi (v05 Sprint 6.1) | Asrama (v05 Sprint 6.2)
- Model `StudentRiskScore`: scheduled `school:recompute-risk-scores` (harian) — formula composite dari attendance%, avg grade, pelanggaran count, tunggakan SPP, BK flags
- Tabel risk dimensions: `academic`, `financial`, `behavioral`, `health`, `attendance` — masing-masing 0–100
- Action **Mark Student at Risk** → trigger BK case auto-create

### 5.2 Academic Analytics (extend School)
- Page `AcademicAnalytics`: nilai per kelas/guru/mapel (chart line/bar), distribusi grade, deteksi outlier
- Service `AcademicAnalyticsService` — cache 1 jam
- Rekomendasi intervensi: rule-based MVP (e.g. nilai < KKM 2 ujian berturut → flag remedial)
- AI version: defer v08 AI Sprint 6

### 5.3 Akademik PT Extension (extend Campus)
- Models tambahan di Campus: `Yudisium`, `Wisuda`, `LecturerEvaluation`, `AccreditationDocument`, `MbkmActivity`, `IndustryPartnership`
- BAN-PT/LAM dokumen: bridge ke DMS v05 dengan tipe `accreditation`
- Tracer study mahasiswa: share infrastructure dengan Alumni Sprint 4.2 (DRY)
- MBKM: track aktivitas (magang/pertukaran/riset) + SKS conversion
- Evaluasi dosen oleh mahasiswa: scheduled survey end-of-semester

---

## Backlog Modul-Specific

- **Live counseling video** — defer ke v09 Zoom/Meet integration
- **Forum/chat real-time** — skip permanen
- **CV builder canggih** — defer ≥12 bulan
- **AI talent scouting** — defer v08 AI
- **App parent native iOS/Android** — pakai PWA + WebView (lihat v04 backlog)
- **Konsumsi gizi tracker harian per siswa** — skip permanen (effort tinggi, low value)

---

## Konflik & Penanganan vs Modul Existing

- `Student.status` enum existing — **extend**, jangan rewrite (tambah `alumni`, `transferred_in`, `transferred_out`)
- `StudentGrade` existing — **extend** dengan workflow approval revisi (v03 Sprint 3.2)
- `Attendance` existing — pasang listener untuk feed risk score (Sprint 5.1)
- School `StudentAchievement` & `Violation` existing — Sprint 3.1 **menggunakan** ini, bukan duplikasi
- `Tenant` panel **tetap** dipakai untuk staff sekolah; panel `parent` Sprint 1.1 **berdiri sendiri** terhubung ke tenant via relasi user-student-tenant

---

## Definition of Done (Modul-Specific)

Tambahan di atas DoD global:

1. Panel `parent` punya isolation test: parent A tidak bisa lihat data anak parent B
2. Setiap BK note dengan `is_confidential=true` punya policy test eksplisit
3. Bridge ke Finance (Seragam/Buku, Event ticket, donasi alumni) wajib auto-journal test
4. Risk score recompute punya test deterministik dengan fixture
5. Broadcast WA wajib idempotency key (anti double-send saat retry)
