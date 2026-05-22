# ROADMAP v0.8 — Governance, Risk, Compliance & Intelligence Platform

**Scope:** Enterprise Risk Mgmt, Audit Internal, QA Pendidikan, Forensic Audit Trail, ISO 27001, KPI Enterprise, Capacity Planning, Project Mgmt, Meeting Mgmt, Executive DSS, BI/Data Warehouse, AI Assistant.

**Rujukan:** [ROADMAPv04-overview.md](ROADMAPv04-overview.md).

---

## Prasyarat dari Roadmap Sebelumnya

- v01 Sprint 1.2 — Spatie ActivityLog (forensic & audit dependency)
- v02 Sprint 2.1 — Laporan Keuangan Standar (BI dependency)
- v03 Sprint 1.2 — Laravel Pulse (capacity planning IT dependency)
- v03 Sprint 1.3 — Executive Dashboard Tab (DSS extension foundation)
- v03 Sprint 2.1 — Cross-Module Reporting (BI extension foundation)
- v03 Sprint 3.1 — Audit Trail Explorer + Diff (forensic extension foundation)
- v05 Sprint 1.2 — Legal & Kontrak (compliance dependency)
- v05 Sprint 2.2 — DMS (ISO evidence storage dependency)
- v07 Sprint 5.2 — Consulting Engagement (Project Mgmt overlap)

---

## Sprint 1 — Enterprise Risk Management + Forensic Trail Extension

### 1.1 Modul Risk (modul baru `Risk`)
- Models: `RiskCategory`, `Risk`, `RiskAssessment` (likelihood × impact), `RiskTreatment`, `RiskOwner`, `KeyRiskIndicator`, `RiskIncident` (linked v05 PhysicalSecurity `Incident` atau v05 Helpdesk `Ticket`)
- Field `Risk`: kategori (akademik/keuangan/hukum/reputasi/IT/SDM/keselamatan/operasional), score auto = likelihood × impact, residual score
- Risk heatmap: page dengan matrix 5×5 (likelihood × impact) sebagai SVG
- Top 10 risk dashboard
- Polymorphic linkage: `Risk` bisa ditempel ke `Organization` (unit) / `Project` (v08 Sprint 4) / `Contract` (v05 Legal)
- KRI thresholds: scheduled `risk:scan-kri` evaluasi rule per indikator → auto-create incident/alert
- Bridge ke v08 Audit Sprint 2 — audit finding bisa generate risk baru
- Workflow approval treatment plan (Workflow V2)

### 1.2 Forensic Trail Extension (extend v03 Sprint 3.1)
- Tambah listener untuk event sensitif: `Login`, `Failed`, `PayrollAccessed`, `SalarySlipExported`, `StudentDataExported`, `JournalEntryPosted`, `JournalEntryReversed`
- Forensic timeline page: query timeline cross-table per user/per entity dalam window waktu
- Tamper detection: hash chain pada `audit_logs` (kolom `prev_hash`, `current_hash` = sha256(prev + payload)) — verifier command `audit:verify-chain`
- Restore data: bridge ke soft delete + version history (Sprint 2 ISO)
- Suspicious activity alert: rule engine sederhana (multiple failed login, mass export, after-hours admin) → notification ke security officer
- Data retention policy: scheduled `forensic:archive-by-policy` per kategori event

---

## Sprint 2 — Audit Internal + ISO 27001 Foundation + QA Pendidikan

### 2.1 Modul Audit (modul baru `InternalAudit`)
- Models: `AuditProgram`, `AuditPlan`, `AuditEngagement`, `AuditChecklistTemplate`, `AuditChecklistItem`, `AuditFinding`, `FindingEvidence`, `CorrectiveAction`, `PreventiveAction`
- Tipe audit: keuangan/akademik/aset/IT/ISO/kepatuhan
- Bridge auditee: polymorphic ke `Organization`/`Project`/modul
- Bukti audit: bridge ke v05 DMS
- Workflow: plan → execution → reporting → follow-up
- Repeat finding detection: scan finding yang sama (kategori + auditee) cross-period
- Dashboard temuan: open/closed/overdue, by category, by unit
- Bridge ke v08 Sprint 1 Risk — finding kategori high → create Risk otomatis

### 2.2 ISO 27001 Foundation (modul baru `IsoCompliance`)
**Catatan:** scope MVP = ISO 27001:2022 control mapping + evidence + audit cycle. Tidak build framework lain (ISO 9001/27701) di awal.

- Models: `InformationAsset` (register aset informasi — beda dengan v05 `Asset` fisik), `IsoControl` (114 control, seeded), `ControlImplementation` (status per tenant: not_implemented/partial/implemented/audited), `StatementOfApplicability`, `IsoEvidence`, `IsoPolicy`, `IsoIncident` (extend v08 Risk Incident)
- Seeder: 114 control ISO 27001:2022 dengan ID standar
- Page **Compliance Dashboard**: maturity score (avg implementation level), control coverage %, audit readiness score
- Statement of Applicability auto-render PDF
- Versioning policy: bridge ke v05 DMS version
- Awareness training: model `TrainingAssignment`, `TrainingCompletion` (bridge ke v07 Training jika ada)
- Phishing simulation: defer (butuh provider eksternal, abstraction only)
- Vendor security assessment: questionnaire form → linked ke Vendor (Procurement existing)

### 2.3 QA Pendidikan (modul baru `EducationQa`)
- Models: `QualityStandard`, `QualityIndicator`, `QualitySurvey` (kepuasan: siswa/orang tua/pegawai/alumni), `SurveyResponse`, `AccreditationCycle`, `AccreditationDocument` (bridge DMS), `GapAnalysis`, `ImprovementPlan`
- Indikator mutu: KPI akademik/layanan/guru/unit (bridge v08 Sprint 3 KPI Enterprise — DRY)
- Survey: scheduled per semester per audience
- Akreditasi BAN-PT/LAM (PT) — bridge ke v06 Sprint 5.3
- Akreditasi BAN-SM (sekolah) — model `AccreditationCycle` polymorphic ke Organization
- Borang akreditasi: template engine pakai DMS template (v05 Sprint 2.2)
- Gap analysis: workflow review standar vs evidence → improvement plan auto-create
- School Health Index (premium feature §62 #3): composite score dari akademik (StudentGrade), keuangan (collection rate, runway), SDM (turnover, KPI), aset (utilization), kepuasan (survey) — scheduled `qa:compute-health-index` per unit per bulan

---

## Sprint 3 — KPI Enterprise + Capacity Planning

### 3.1 KPI Enterprise (modul baru `KpiEnterprise`)
**Catatan:** Modul Employee sudah punya KPI per pegawai (`KpiTemplate`, `KpiScore`). Sprint ini = KPI **enterprise** lintas unit/yayasan, beda level.

- Models: `KpiArea` (PPDB/Keuangan/Akademik/HR/Aset/Layanan/UnitUsaha), `KpiMetric`, `KpiTarget` (per periode), `KpiActual`, `KpiWeight`, `KpiCascade` (parent-child cascading)
- Data source: 2 mode
  - **Auto**: query database (e.g. collection_rate = sum(paid invoices) / sum(due invoices))
  - **Manual**: input via form (survey results, qualitative)
- Cascade: KPI yayasan → KPI unit → KPI departemen → KPI individual (link ke Employee KPI existing)
- Scheduled `kpi:recompute-actuals` harian/bulanan tergantung metric
- Dashboard: skor KPI per area, drill-down per unit
- Warning trigger: KPI turun > X% → alert + auto-create improvement plan (bridge QA Sprint 2.3)
- Reward berbasis KPI: bridge ke Employee bonus (link KPI score → bonus calculation v02 Sprint 1.1 payroll)

### 3.2 Capacity Planning (modul baru `Capacity`)
- Models: `CapacityResource` (kelas/guru/ruang/server/bandwidth/parkir/asrama/kendaraan/lab), `CapacityUtilization`, `CapacityForecast`
- Source: bridge per modul
  - Ruang: utilization dari Facility booking (v05)
  - Guru: workload dari Schedule + max load setting
  - Server/bandwidth: bridge Laravel Pulse (v03) + ItOps monitoring (v05)
  - Kendaraan: utilization dari Transport (v05)
  - Asrama: occupancy dari Boarding (v05)
- Forecast: linear regression + seasonality sederhana per resource (defer ML model)
- Scenario planning: what-if simulator (input growth %) → estimated capacity gap
- Utilization dashboard: heatmap per resource per periode
- Warning overload: rule-based alert
- Rekomendasi investasi: rule-based MVP, defer AI

---

## Sprint 4 — Project Management + Meeting Management

### 4.1 Project Mgmt (modul baru `ProjectMgmt`)
- Models: `Program` (program kerja yayasan/unit), `Project`, `Task`, `Subtask`, `Milestone`, `ProjectMember`, `ProjectDocument` (bridge DMS), `ProjectIssue`, `ProjectRisk` (bridge Risk Sprint 1)
- Views: Kanban board (Filament livewire), Gantt chart (defer atau simple SVG), Timeline
- PIC & deadline: notification reminder (depend WA v09)
- Budget project: bridge ke v02 Budget (link `Project.budget_id`)
- Approval deliverable: Workflow V2
- Project discussion: model `ProjectComment` (in-app, bukan chat real-time)
- Dashboard portofolio: status semua project di yayasan, by unit, by owner
- AI summary progress: defer Sprint 6
- Bridge ke v07 Sprint 5.2 Consulting Engagement (consulting project = subtype project dengan client external)

### 4.2 Meeting Mgmt (modul baru `Meeting`)
- Models: `Meeting`, `MeetingInvitation`, `MeetingAgenda`, `MeetingAttendance`, `Minutes` (notulen), `MeetingDecision`, `ActionItem`, `MeetingDocument`
- Tipe: board meeting yayasan, rapat kepsek, rapat guru, rapat orang tua, ad-hoc
- Undangan: dispatch via channel (WA/email/in-app)
- Daftar hadir: QR check-in (reuse pola Event v06 Sprint 3.2)
- Notulen approval workflow + AI summary opsional (Sprint 6)
- Action item: linked ke Task di Project Mgmt Sprint 4.1 (DRY)
- Follow-up: scheduled `meeting:scan-overdue-actions`
- Bridge ke Zoom/Meet: defer v09 Sprint 4

---

## Sprint 5 — Executive DSS + BI/Data Warehouse

### 5.1 Executive DSS Full (extend v03 Sprint 1.3)
**Tidak buat modul baru** — extend existing TabbedDashboard + `ExecutiveMetricsService` dari v03.

- Tambah dashboard per role: Kepsek/Rektor/CFO/HRD/Sarpras/Risk Officer/Direktur Unit Usaha
- Early Warning System: scheduled `dss:scan-warnings` per jam — rule library
  - Cashflow runway < 3 bulan
  - Tunggakan SPP naik > 15%
  - Ruang utilization > 90%
  - Guru workload > threshold
  - Pelanggaran kelas tertentu spike
  - Asset maintenance overdue mass
- Recommendation engine: rule-based mapping (problem → recommendation template). Format output sesuai tabel di §44.
- What-if simulator: parametric form (e.g. naikkan SPP 10% → impact ke revenue + drop-out risk)
- Forecast: time-series simple (revenue, expense, student count, cashflow) — linear + seasonality
- Board meeting report otomatis: scheduled `dss:generate-board-report` bulanan → PDF kompilasi auto kirim ke pengurus yayasan (bridge ke v05 Stakeholder dari Sprint 1)

### 5.2 BI / Data Warehouse (modul baru `Bi` atau service-only)
**Strategi:** untuk MVP, **bukan** physical DW — pakai read-replica queries + aggressive caching. Physical DW (ETL ke tabel star schema) defer hingga data volume membenarkan.

- Service `DataMartService` dengan method per data mart: akademik, keuangan, HR, aset, admission, alumni
- Materialized view (DB view atau cached aggregate table) — refresh harian via scheduled command
- Custom report builder: page `ReportBuilder` (Filament) — select data mart + dimension + measure + filter (defer kompleksitas, MVP fixed templates)
- Pivot report: defer v2
- Export: queue + email link (reuse v03 Sprint 2.4 Export Center)
- Trend analysis, cohort analysis siswa, funnel PPDB: prebuilt query templates
- Benchmark antar-unit: page comparison side-by-side
- Data dictionary: page auto-generated dari schema + manual description per field
- Master Data Governance: per-master-data approval workflow (e.g. tahun ajaran baru, kurikulum baru)
- Data quality monitoring: scheduled checks (null in required fields, orphan FK, duplicate)
- **Skip MVP:** physical DW dengan dedicated server, Apache Superset embed

---

## Sprint 6 — AI Assistant (Advisor Only)

**Filosofi:** AI = advisor, bukan decision maker. Semua AI output marked `ai_suggestion`, human-in-the-loop approval untuk action.

### 6.1 AI Infrastructure (extend Core atau modul baru `Ai`)
- Abstraction layer `AiProvider` interface — implementasi: Anthropic Claude (primary), OpenAI fallback
- Config: API key per tenant atau platform-wide, usage quota per tenant
- Prompt template registry: model `AiPromptTemplate` (versioned, reviewable)
- Audit log setiap AI call (input + output + cost) — bridge ke v08 Sprint 1.2 forensic
- Streaming response untuk chat UI

### 6.2 AI Use Cases (gated per `TenantModule`)
- **Executive Q&A**: natural language → query DataMartService (Sprint 5.2) → narasi jawaban + chart
- **Board Report Generator**: ringkasan bulanan + rekomendasi + grafik (use DSS data Sprint 5.1)
- **Draft Surat/SOP/Kebijakan**: prompt template + DMS save
- **Draft Materi Ajar & Soal**: untuk guru (gated per modul Guru/Dosen)
- **Analisis Nilai & Sentimen Survey**: jalankan saat batch nilai/survey selesai
- **Klasifikasi Helpdesk Ticket**: auto-suggest category (bridge v05 Helpdesk)
- **Rekomendasi Lead Follow-up**: bridge PPDB CRM v07 Sprint 1.1
- **OCR Dokumen + Ekstraksi PDF**: bridge v05 DMS (gunakan Claude vision atau Tesseract fallback)
- **Deteksi Anomali Keuangan**: scan JournalEntry tidak wajar (saldo, pattern)
- **Prediksi Risk Score Siswa**: refine RiskScore v06 Sprint 5.1 dari rule-based ke ML
- **AI Meeting Summary**: transkrip audio (bridge external transcription) → minutes draft
- **Chatbot Internal**: knowledge base RAG dari DMS + FAQ + dokumentasi

### 6.3 Safety & Governance
- Tidak ada "auto-execute" — semua action butuh human approval
- Mark AI output di UI dengan badge "AI Suggestion"
- Cost dashboard per tenant (token usage, biaya)
- Opt-out per fitur AI di TenantSetting

---

## Sprint 7 — ISO 27001 Compliance Automation (extend Sprint 2.2)

Late-stage karena butuh foundation lengkap. Focus on automation, bukan policy authoring tools.

- Automated evidence collection: scheduled jobs scan compliance state per control & generate evidence record
- Continuous compliance: pasang monitoring untuk control yang trackable (mis. password policy, access review, backup success)
- Audit prep checklist generator (bridge Audit Sprint 2.1)
- External auditor portal (read-only access ke evidence)
- Compliance dashboard publik untuk pengurus yayasan

---

## Backlog Modul-Specific

- **Physical Data Warehouse / Lakehouse** — defer hingga data volume membenarkan (~3 tahun produksi)
- **ML model training pipeline in-house** — pakai LLM API (Anthropic/OpenAI) dulu, no in-house ML
- **AI auto-decision (bukan advisor)** — skip permanen untuk safety
- **Custom OLAP cube builder** — skip, pakai pre-built data marts
- **ISO 9001/27701/22301 frameworks** — defer ≥24 bulan
- **Real-time streaming analytics** — skip, batch sudah cukup untuk konteks yayasan

---

## Konflik & DRY

- **KPI Enterprise v08 Sprint 3.1 vs Employee KPI existing** — Employee KPI = individual performance; Enterprise KPI = unit/yayasan level. Cascade explicit lewat `KpiCascade.child_id` ke Employee KPI.
- **Project Mgmt v08 Sprint 4.1 vs Consulting v07 Sprint 5.2** — Consulting Engagement `has_one Project` (DRY shared scheduling/budget/task).
- **Meeting Action Item Sprint 4.2 vs Project Task Sprint 4.1** — satu model `Task`, action item = `Task` dengan `meeting_id` set.
- **Audit Finding Sprint 2.1 vs Risk Sprint 1.1** — Finding `high` auto-creates Risk via listener, satu source of truth via FK.
- **Risk Incident Sprint 1.1 vs Helpdesk Incident v05 Sprint 3.1** — Helpdesk ticket dengan kategori `security` auto-promotes ke Risk Incident.
- **QA Sprint 2.3 Survey vs Parent Survey v06 Sprint 1.4** — Parent Survey adalah ParentSurvey subset; QA Survey lebih luas (siswa/pegawai/alumni). Pertimbangkan unify ke `Survey` polymorphic later, tapi untuk MVP biarkan dua model dengan code-reuse via trait.
- **AI Sprint 6 vs Pulse v03 Sprint 1.2** — Pulse untuk infrastructure observability, AI cost dashboard untuk business AI usage. Separate concern.

---

## Definition of Done (Modul-Specific)

1. Risk & Audit & Finding cross-link wajib test event chain (Audit Finding high → Risk created)
2. Forensic hash chain wajib pass `audit:verify-chain` di CI
3. KPI cascade wajib test parent → child rollup
4. Executive DSS rule wajib unit test per warning category
5. AI calls wajib mock di test (no real API call), cost dashboard wajib test
6. Compliance dashboard score wajib snapshot test (deterministic per fixture)
7. School Health Index wajib regression test dengan fixture multi-unit
