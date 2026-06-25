# 17 — Gap Analysis

**Tanggal analisis:** 2026-06-25  
**Metode:** Pembacaan seluruh `docs-reverse-engineering/01`–`19` + `README.md`, cross-check spot-check terhadap codebase & katalog ter-commit (`docs/catalogs/*.json`).  
**Commit acuan dokumen sumber:** `d3be06aa` (semua file RE memakai commit ini). Spot-check dijalankan pada working tree saat ini.

**Prinsip:** Gap tidak ditutup dengan asumsi. Setiap temuan memuat bukti atau label **TIDAK TERDETEKSI DI KODE** / **PERLU VERIFIKASI MANUSIA**.

---

## 1. Ringkasan Eksekutif

| Tingkat risiko | Jumlah gap | Ringkasan |
|----------------|----------:|-----------|
| **High Risk** | **12** | Tenancy/provisioning, otorisasi, enrollment, workflow/procurement, integrasi kritis, artefak schema tidak terverifikasi di git |
| **Medium Risk** | **16** | Cakupan diagram/FR tidak selaras, modul leaf dangkal, integrasi sekunder, ketidakkonsistenan metrik & relasi antar dokumen |
| **Low Risk** | **12** | Scope produk di luar kode, infrastruktur, pola arsitektur kosmetik, duplikasi folder dokumen |
| **Total** | **40** | |

**Temuan lintas-dokumen utama:** Set `07_use_case_diagram.md` (20 UC) tidak sepenuhnya diturunkan ke `08_activity_diagram.md` (14 AD) dan `09_sequence_diagram.md` (15 SD). `12_bpmn.md` jauh lebih lengkap daripada activity/sequence untuk domain yang sama. `11_er_diagram.md` dan `16_data_dictionary.md` hanya mencakup subset kecil dari klaim **434 tabel**. `15_business_rules.md` hanya ~20 BR eksplisit vs **257** resource Shield di `authorization-matrix.json`.

**Artefak kritis tidak di-git:** `storage/app/entity-catalog.json`, `storage/app/verified-fks.json` — disebut di `02`, `11`, `16`, `18` tetapi **TIDAK TERDETEKSI DI GIT** (`git ls-files` kosong).

---

## 2. High Risk

| ID | Type | Gap | Dokumen terkait | Bukti / TIDAK TERDETEKSI | Review manusia diperlukan |
|----|------|-----|-----------------|--------------------------|---------------------------|
| HR-01 | entity | Katalog 434 tabel & 402 model tidak dapat diverifikasi ulang dari git | `11_er_diagram.md`, `16_data_dictionary.md`, `02_inventaris_teknologi.md`, `18_asumsi_dan_batasan.md` | `entity-catalog.json` **TIDAK TERDETEKSI DI GIT**; angka 434 hanya dari script lokal | Regenerasi `php scripts/extract-entity-catalog.php`, commit ke `docs/catalogs/`, bandingkan hitungan dengan migrasi |
| HR-02 | relationship | 5.473 FK verified tidak dapat diaudit peer-review | `11_er_diagram.md`, `04_analisis_modul.md` | `verified-fks.json` **TIDAK TERDETEKSI DI GIT** | Regenerasi `extract-verified-fks.php`, sampling FK antar-modul (School↔Enrollment↔Finance) |
| HR-03 | requirement | Modul **Marketplace** & **Printing**: 0 file Policy, tetap ada 8 resource Filament & entri permission di matrix | `04_analisis_modul.md` (Policies=0), `07_use_case_diagram.md`, `authorization-matrix.json`, `15_business_rules.md` | Spot-check: `rg "class.*Policy" Modules/Marketplace` → 0; `Modules/Printing` → 0; matrix tetap daftar `ViewAny:MarketplaceOrder` dll. | Uji akses panel: user tanpa permission Marketplace/Printing — apakah CRUD terbuka, ditolak Shield default, atau hanya super_admin? |
| HR-04 | process | Registrasi tenant tanpa transaksi DB eksplisit; kegagalan provisioner setelah `Tenant::create` | `07_use_case_diagram.md` UC-002, `08_activity_diagram.md` AD-02, `12_bpmn.md` SP-01 | `RegisterTenant::handleRegistration()` baris 71–86: tidak ada `DB::transaction`; UC-002 mencatat rollback **TIDAK TERDETEKSI DI KODE** | Simulasi gagal di `TenantModuleProvisioner`/`assignShieldSuperAdmin` — apakah tenant orphan? |
| HR-05 | requirement | Super admin global bypass total + flag `is_super_admin` pada `$fillable` | `07_use_case_diagram.md` ACT-03, `06_requirements_nonfungsional.md` NFR-SEC, `05_requirements_fungsional.md` FR terkait auth | `AppServiceProvider.php` `Gate::before`; `User.php` `$fillable` mencakup `is_super_admin` | Audit semua jalur mass-assignment User; konfirmasi kebijakan produk untuk bypass global |
| HR-06 | process | Konversi applicant→student bergantung setting & event; jalur revert kurang ditelusuri di activity/sequence | `15_business_rules.md` BR-E01/E02, `12_bpmn.md` CP-01/EP-06, `08_activity_diagram.md` AD-07, `09_sequence_diagram.md` SD-06 | `ApplicantPromotionService::isEnabledFor()`; event `ApplicantAcceptanceReverted` ada di BPMN EP-06 tetapi **tidak** ada AD/SD dedicated | Verifikasi E2E: accepted→promote, accepted tanpa auto-promote, revert acceptance, idempotensi `converted_to_student_id` |
| HR-07 | process | API cuti (`UC-020`/`FR-038`) tidak terhubung ke workflow di dokumentasi sequence/activity | `07_use_case_diagram.md` UC-020, `05_requirements_fungsional.md` FR-038, `08_activity_diagram.md`, `09_sequence_diagram.md` | UC-020: listener workflow **TIDAK TERDETEKSI DI KODE** pada path API; tidak ada AD/SD untuk UC-020 | Bandingkan `LeaveRequestController::store()` vs Filament `LeaveRequestResource` + workflow instance — kapan workflow distart? |
| HR-08 | process | Webhook WhatsApp: handler tidak mempersist pesan masuk | `09_sequence_diagram.md` SD-15, `14_api_dan_integrasi.md`, `07_use_case_diagram.md` UC-018 | SD-15: penyimpanan inbound **TIDAK TERDETEKSI DI KODE** | Baca `WhatsAppWebhookController` penuh; konfirmasi apakah ini by-design atau data loss |
| HR-09 | requirement | Efektivitas ~3100 permission Shield per tenant DB runtime | `07_use_case_diagram.md`, `authorization-matrix.json`, `15_business_rules.md` | Matrix = scaffold generate; baris `roles`/`permissions` per tenant **KETIDAKPASTIAN** (tidak diaudit DB) | Query staging: role default tenant baru, user non-super-admin, akses resource acak |
| HR-10 | process | Procurement: workflow PR tidak auto-start saat create | `12_bpmn.md` §7, `08_activity_diagram.md` AD-04 | BPMN: **TIDAK TERDETEKSI DI KODE** auto-start pada create PR — start manual | Trace `ViewPurchaseRequisition` / observer PR → kapan `WorkflowInstanceStarter` dipanggil? |
| HR-11 | relationship | `moodle_sync_outbox.tenant_id` nullable tanpa FK `constrained` | `11_er_diagram.md` §8, `16_data_dictionary.md` | Migration `2026_03_25_220000_create_moodle_sync_tables.php` — catatan di ERD | Verifikasi integritas data outbox multi-tenant & orphan rows |
| HR-12 | ambiguous | Dual layer otorisasi: `TenantRole.permissions = ['*']` vs Spatie Shield per resource | `04_analisis_modul.md` Core, `07_use_case_diagram.md`, `15_business_rules.md` BR-T02 | `TenantAdminProvisioner::ensureTenantOwnerRole()` set `permissions => ['*']`; BR-T02 hanya membership panel | Klarifikasi produk: apakah `TenantRole` mempengaruhi Filament selain `canAccessPanel`? |

---

## 3. Medium Risk

| ID | Type | Gap | Dokumen terkait | Bukti / TIDAK TERDETEKSI | Review manusia diperlukan |
|----|------|-----|-----------------|--------------------------|---------------------------|
| MR-01 | process | **6 UC** tanpa activity diagram dedicated | `07_use_case_diagram.md` vs `08_activity_diagram.md` | AD hanya UC-001–009, 011–013, 015–016; hilang: **UC-010, UC-014, UC-017, UC-018, UC-019, UC-020** | Prioritaskan AD untuk UC-014 (platform), UC-020 (leave API), UC-017 (counseling) jika kritikal bisnis |
| MR-02 | process | **5 UC** tanpa sequence diagram (UC-018 punya SD-15) | `07_use_case_diagram.md` vs `09_sequence_diagram.md` | Tidak ada SD untuk UC-010, UC-014, UC-017, UC-019, UC-020 | Sama seperti MR-01 — sequence untuk platform & leave API |
| MR-03 | requirement | **40 FR** vs **45 modul** — sebagian besar modul hanya lewat FR-040 meta-CRUD | `05_requirements_fungsional.md`, `04_analisis_modul.md` | FR-001–039 = domain inti; FR-040 = 375 resource; 24 modul leaf tidak punya FR dedicated | Pilih 5 modul leaf revenue-critical; tentukan FR bisnis eksplisit |
| MR-04 | entity | Data dictionary hanya **11 tabel** vs klaim **434** | `16_data_dictionary.md` vs `11_er_diagram.md` | Dictionary: tenants, users, … moodle_sync_outbox; sisanya **TIDAK ADA** di dokumen | Perluas dictionary untuk tabel finance/procurement jika dipakai compliance |
| MR-05 | entity | Class diagram **5 cluster** vs **402** model ORM | `10_class_diagram.md` vs `11_er_diagram.md` | Class doc hanya Core, Workflow, Enrollment/School, Finance, Moodle + Filament base | Tidak wajib lengkap; tentukan cluster berikutnya (Procurement, Library) |
| MR-06 | relationship | ERD applicants: label `program_choice FK` tidak selaras kolom aktual | `11_er_diagram.md` §5 vs `16_data_dictionary.md` §applicants | ERD: `departments \|\|--o{ applicants : program_choice`; dictionary: `program_choice_1_id`, `program_choice_2_id`, `accepted_program_id` | Koreksi diagram ERD §5 — jangan oversimplify relasi |
| MR-07 | entity | Hitungan migrasi tidak konsisten antar dokumen | `README.md`, `02_inventaris_teknologi.md`, `11_er_diagram.md`, `19_rangkuman_untuk_pengembang.md` | README/02/19: **244**; ERD §1: 37+208≈**245**; spot-check `find … migrations` → **245** | Standarkan angka di semua dokumen setelah regenerasi manifest |
| MR-08 | process | Payroll end-to-end tidak terdokumentasi sebagai proses | `12_bpmn.md`, `08_activity_diagram.md`, `15_business_rules.md` | **TIDAK TERDETEKSI DI KODE** engine payroll terpusat; model `SalarySlip` ada | Audit `Modules/Employee/app/Services/` — ada kalkulasi atau hanya CRUD? |
| MR-09 | process | Integrasi Feeder DIKTI tidak lengkap | `14_api_dan_integrasi.md`, `04_analisis_modul.md` Campus | Model `FeederLog` ada; protokol API **TIDAK TERDETEKSI** penuh | Review modul Campus + migration feeder — production atau stub? |
| MR-10 | requirement | Module REST scaffold (`apiResource`) belum terbukti production-ready | `05_requirements_fungsional.md` FR-036, `14_api_dan_integrasi.md`, `04_analisis_modul.md` | Beberapa `Modules/*/routes/api.php` = boilerplate; **TERINDIKASI** | Test integrasi per modul API (Inventory, Sales, dll.) |
| MR-11 | requirement | Validasi bisnis **257** form Filament tidak diaudit per file | `05_requirements_fungsional.md`, `15_business_rules.md`, `07_use_case_diagram.md` UC-019 | **TERINDIKASI** — dominan `->required()` tanpa audit sampling | Sampling 10% `*Form.php` modul Finance, Enrollment, Procurement |
| MR-12 | process | BPMN jauh lebih lengkap daripada Activity/Sequence untuk domain sama | `12_bpmn.md` vs `08_activity_diagram.md` vs `09_sequence_diagram.md` | BPMN: CP-01–05, SP, EP; Activity: 14 AD; Sequence: 15 SD | Tentukan dokumen kanonis per domain atau tambah cross-ref eksplisit |
| MR-13 | process | Exam gradebook / bridge nilai ke School/Campus E2E | `04_analisis_modul.md` Exam, `07_use_case_diagram.md` UC-012 | `GradeBridgeInterface` ada; alur E2E **TERINDIKASI NAMUN BELUM TERBUKTI PENUH** | Trace `ExamRuntimeSyncService` → model nilai sekolah/kampus |
| MR-14 | entity | `authorization-matrix.json` berubah di working tree (belum commit) | `README.md`, `18_asumsi_dan_batasan.md` | `git diff docs/catalogs/authorization-matrix.json` → 1 baris berubah vs commit acuan `d3be06aa` | Regenerasi matrix & commit; pastikan sinkron dengan Shield generate terbaru |
| MR-15 | requirement | NFR: banyak dimensi infra **TIDAK TERDETEKSI DI KODE** | `06_requirements_nonfungsional.md` | TLS, GDPR, IaC, CDN, Redis wajib produksi — agregat §TIDAK TERDETEKSI | Pisahkan NFR aplikasi vs NFR operasional; isi dari runbook eksternal |
| MR-16 | ambiguous | `LogWhatsAppProvider` sebagai binding default | `14_api_dan_integrasi.md`, `02_inventaris_teknologi.md` | `MessagingServiceProvider` default log provider; produksi **KETIDAKPASTIAN** | Cek `.env` / binding produksi — notifikasi WhatsApp benar-benar terkirim? |

---

## 4. Low Risk

| ID | Type | Gap | Dokumen terkait | Bukti / TIDAK TERDETEKSI | Review manusia diperlukan |
|----|------|-----|-----------------|--------------------------|---------------------------|
| LR-01 | process | Portal login siswa/mahasiswa dedicated | `01_ringkasan_sistem.md`, `07_use_case_diagram.md`, `13_alur_ui.md`, `05_requirements_fungsional.md` | **TIDAK TERDETEKSI DI KODE** — hanya API dashboard (`UC-015`) | Konfirmasi product: mobile app terpisah? |
| LR-02 | process | IaC / pipeline deploy produksi | `02_inventaris_teknologi.md`, `06_requirements_nonfungsional.md`, `17_gap_analysis.md` (versi lama) | Dockerfile/K8s/Terraform **TIDAK TERDETEKSI DI KODE** | Dokumentasi infra di repo terpisah |
| LR-03 | requirement | OAuth2 / LDAP / SAML SSO | `02_inventaris_teknologi.md` | **TIDAK TERDETEKSI DI KODE** | Roadmap auth enterprise |
| LR-04 | entity | Pola Repository / DTO tidak dipakai | `03_arsitektur_aplikasi.md`, `10_class_diagram.md`, `09_sequence_diagram.md` | **TIDAK TERDETEKSI DI KODE** — Eloquent langsung | Keputusan arsitektur, bukan gap fungsional |
| LR-05 | requirement | Formula bobot penilaian K-12 global | `15_business_rules.md` | **TIDAK TERDETEKSI** — per assessment model | Konfirmasi kurikulum per tenant |
| LR-06 | entity | Database VIEW / CHECK constraint / stored procedure | `11_er_diagram.md`, `16_data_dictionary.md` | `rg Schema::view` → 0; `->check()` di migrasi → 0 | Tidak diperlukan jika semua aturan di aplikasi |
| LR-07 | ambiguous | Detail validasi kredensial login Filament | `08_activity_diagram.md` AD-01, `09_sequence_diagram.md` SD-01 | Handler credential **TIDAK TERDETEKSI DI KODE** (framework) | Cukup jika mengandalkan Laravel/Filament — dokumentasikan sebagai delegasi framework |
| LR-08 | process | Folder `output-reverse-engineering/` duplikat | `19_rangkuman_untuk_pengembang.md` | Dua set 01–19; status folder output tidak diaudit diff | Tentukan canonical: `docs-reverse-engineering/` |
| LR-09 | entity | TypeScript tidak dipakai di root | `02_inventaris_teknologi.md` | Tidak ada `tsconfig.json` | Sesuai stack PHP/Livewire |
| LR-10 | process | Marketplace order fulfillment BPMN | `12_bpmn.md` §7 | **TERINDIKASI NAMUN BELUM TERBUKTI PENUH** | Hanya jika modul Marketplace aktif di produk |
| LR-11 | process | UC-019 meta-CRUD 257 resource sengaja tidak di-AD | `08_activity_diagram.md`, `09_sequence_diagram.md` | Pola identik UC-003 — **INFERENSI** | Terima sebagai cakupan dokumentasi terbatas |
| LR-12 | requirement | KPI bisnis produk (adopsi, SLA) | `01_ringkasan_sistem.md` | **TIDAK TERDETEKSI DI KODE** | Input product owner |

---

## 5. Matriks Inkonsistensi Antar Dokumen

| # | Topik | Dokumen A | Dokumen B | Inkonsistensi | Risiko |
|---|-------|-----------|-----------|---------------|--------|
| X-01 | Jumlah migrasi | `README.md`, `02`, `19` → **244** | `11_er_diagram.md` §1 → **245** | Selisih 1 file; spot-check workspace **245** | Medium |
| X-02 | Cakupan ERD | `11_er_diagram.md` → klaim **434** tabel | `16_data_dictionary.md` → **11** tabel terdokumentasi | Dictionary ≈ 2,5% dari inventaris | Medium |
| X-03 | Cakupan kelas | `10_class_diagram.md` → 5 cluster | `11_er_diagram.md` → **402** model | Class diagram tidak mewakili seluruh domain | Medium |
| X-04 | UC → Activity | `07` → **20** UC | `08` → **14** AD | 6 UC tanpa AD (lihat MR-01) | Medium |
| X-05 | UC → Sequence | `07` → **20** UC | `09` → **15** SD | 5 UC tanpa SD (UC-018 ada SD-15) | Medium |
| X-06 | UC → BPMN | `07` → 20 UC granular | `12` → proses domain (CP/SP/EP) | BPMN tidak memetakan 1:1 ke UC-ID | Low–Medium |
| X-07 | Relasi applicants | `11_er_diagram.md` `program_choice` | `16_data_dictionary.md` `program_choice_1/2_id` | Nama relasi ERD tidak presisi | Medium |
| X-08 | Policies modul | `04_analisis_modul.md` Marketplace/Printing **Policies=0** | `authorization-matrix.json` permission lengkap | Matrix mengasumsikan Shield; file Policy absen | High |
| X-09 | Business rules vs RBAC | `15_business_rules.md` ~20 BR service | `authorization-matrix.json` ~3100 permission | Aturan bisnis service tidak memetakan ke matrix | Medium |
| X-10 | FR vs UC | `05` → **40** FR | `07` → **20** UC | Banyak FR (010–039) tanpa pasangan UC eksplisit | Medium |
| X-11 | Commit acuan vs workspace | Semua dokumen RE → `d3be06aa` | `authorization-matrix.json` working tree | Diff 1 baris belum masuk commit acuan | Medium |
| X-12 | Resource count wording | `04`/`03` → **375** total / **257** ModuleResource | `01`/`19`/`13` → sorot **257** saja | Bisa disalahartikan “hanya 257 entitas UI” | Low |
| X-13 | Moodle inbound | `14_api_dan_integrasi.md` outbound FAKTA | `14` webhook inbound Moodle | **TIDAK TERDETEKSI DI KODE** — hanya outbound | Low (arah integrasi) |
| X-14 | NFR vs FR auth | `06` NFR-SEC super admin bypass | `05` FR-001 login | Bypass global tidak selalu disebut di FR login | Medium |

---

## 6. Area Wajib Review Manusia

Daftar eksplisit — **tanpa** mengisi gap dengan asumsi:

1. **Regenerasi & commit** `entity-catalog.json` dan `verified-fks.json` ke `docs/catalogs/` — verifikasi angka 434 tabel / 5.473 FK sebelum dipakai compliance atau migrasi data.
2. **Uji otorisasi Marketplace & Printing** — konfirmasi perilaku Filament/Shield ketika modul punya resource tetapi **0** class `*Policy` di folder modul.
3. **Provisioning tenant gagal setengah jalan** — uji fault injection pada `TenantModuleProvisioner` setelah `Tenant::create` tanpa `DB::transaction`.
4. **Enrollment E2E** — skenario: inquiry → applicant → accepted → auto-promote on/off → revert acceptance → duplikasi promote; cocokkan dengan `12_bpmn.md` CP-01/EP-06 dan test `ApplicantAcceptedPipelineTest`.
5. **Leave request API vs UI workflow** — apakah `POST api/v1/leave-requests` memicu workflow instance atau hanya menyimpan draft?
6. **Webhook WhatsApp** — apakah pesan inbound harus disimpan; jika ya, gap SD-15 adalah defect produk.
7. **Permission efektif per tenant** — query DB staging setelah `shield:generate` + assign role non-admin; bandingkan dengan `authorization-matrix.json`.
8. **Procurement workflow start** — titik pemanggilan `WorkflowInstanceStarter` untuk PR (manual vs otomatis).
9. **Integrasi produksi Messaging** — provider WhatsApp non-log di environment produksi.
10. **Feeder DIKTI & SLIMS** — status implementasi vs placeholder (`FeederLog`, `LibraryImportSlimsCommand`).
11. **Payroll** — apakah `SalarySlip` dihasilkan manual CRUD atau ada job/service kalkulasi?
12. **Kebijakan `is_super_admin` $fillable** — review keamanan mass-assignment di seluruh endpoint/API impor user.
13. **Sinkronisasi dokumen** — perbarui hitungan migrasi (244→245), perbaiki ERD applicants §5, putuskan canonical antara BPMN vs Activity untuk onboarding developer.
14. **Diff `authorization-matrix.json`** — regenerasi atau commit perubahan agar selaras dengan kode Shield terkini.
15. **Scope produk siswa** — konfirmasi apakah `UC-015` API dashboard mencukupi atau portal dedicated direncanakan (saat ini **TIDAK TERDETEKSI DI KODE**).

---

## 7. TIDAK DIAUDIT (Batasan Analisis Ini)

| Batas | Keterangan |
|-------|------------|
| **Full re-audit kode** | Hanya spot-check: hitungan modul (45), ModuleResource (257), panel (3), migrasi (245), Policy Marketplace/Printing (0), `RegisterTenant` tanpa transaction |
| **Runtime / DB produksi** | Tidak ada query `roles`, `permissions`, data tenant nyata |
| **Seluruh 257 × Form.php** | Validasi bisnis per resource tidak dibaca satu per satu |
| **Seluruh 101 rute API** | Mengandalkan `api-routes-catalog.json` ter-commit; `php artisan route:list` gagal di lingkungan analisis (exit 255) — **PERLU VERIFIKASI MANUSIA** di mesin dev |
| **Folder `output-reverse-engineering/`** | Tidak dibandingkan baris-per-baris dengan `docs-reverse-engineering/` |
| **`vendor/` test suite lengkap** | Tidak menjalankan `php artisan test` full suite dalam analisis ini |
| **Visual UX / aksesibilitas** | Di luar scope `13_alur_ui.md` |
| **Dokumen root lain** | `ARCHITECTURE.md`, `REAP_AUDIT.md`, `00-repository-manifest.md` hanya referensi silang, tidak direkonsiliasi penuh |
| **Perubahan kode setelah `d3be06aa`** | Dokumen RE pinned commit lama; working tree mungkin sudah berbeda |

---

## 8. Yang Berhasil Direkonstruksi (Referensi Positif)

| Area | Kelengkapan | Bukti kunci | Dokumen |
|------|-------------|-------------|---------|
| Stack & modul | Tinggi | 45 modul, 257 ModuleResource, 3 panel | `01`, `04`, `README` |
| Tenancy pattern | Tinggi | `BelongsToTenant`, `TenantScope`, panel tenant | `03`, `15` BR-T01–T03 |
| Workflow V2 | Tinggi | `DatabaseWorkflowEngine`, test workflow | `12` CP-03, `15` BR-W* |
| API mobile inti | Tinggi | 101 rute terkatalog | `14`, `api-routes-catalog.json` |
| Finance / Procurement BR kunci | Sedang–tinggi | `FinanceControlService`, `ThreeWayMatchValidator` | `15` BR-F*, BR-P* |
| Enrollment promote | Sedang | `ApplicantPromotionService`, event pipeline | `12` CP-01, `08` AD-07 |

---

## 9. Sumber & Regenerasi

```bash
# Artefak yang seharusnya di-commit
php scripts/extract-entity-catalog.php
php scripts/extract-verified-fks.php
php scripts/extract-api-routes.php
php scripts/extract-authorization-matrix.php

# Verifikasi perilaku kritis
php artisan test --compact tests/Feature/ApplicantAcceptedPipelineTest.php
php artisan test --compact tests/Feature/WorkflowProcurementPilotTest.php
php artisan test --compact tests/Feature/PlatformPanelAccessTest.php
php artisan route:list --path=api --except-vendor
```

| Artefak | Path | Status git (spot-check 2026-06-25) |
|---------|------|-------------------------------------|
| API routes | `docs/catalogs/api-routes-catalog.json` | Ter-commit |
| Authorization matrix | `docs/catalogs/authorization-matrix.json` | Ter-commit (+ diff lokal 1 baris) |
| Entity catalog | `storage/app/entity-catalog.json` | **TIDAK TERDETEKSI DI GIT** |
| Verified FKs | `storage/app/verified-fks.json` | **TIDAK TERDETEKSI DI GIT** |

---

**Commit acuan dokumen:** `d3be06aa` · **Analisis gap:** 2026-06-25 · **File diperbarui:** `docs-reverse-engineering/17_gap_analysis.md` only.
