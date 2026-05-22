# ROADMAP v0.4 — Master Index: Integrated Education Foundation OS

Roadmap ini mengonsolidasi visi besar "Sistem Informasi Terintegrasi Yayasan Pendidikan" (64 area fitur) ke dalam jalur pengembangan yang **tidak bertabrakan** dengan roadmap sebelumnya dan kode existing.

**Rujukan sebelumnya:**
- [ROADMAPv01.md](ROADMAPv01.md) — Fase 1 Foundation Multi-Tenant SaaS
- [ROADMAPv02.md](ROADMAPv02.md) — Fase 2 Operasional Inti (HRM, Finance, Inventory, Procurement)
- [ROADMAPv03.md](ROADMAPv03.md) — Fase 3 BI, Reporting, Audit, Edutech Polish, Sales/POS

---

## Strategi Pemecahan

64 area fitur dipecah ke **5 roadmap tambahan** (v05–v09) berdasarkan domain & dependensi modul.

**Aturan main:**
1. **Tidak ada duplikasi** — item yang sudah ada di v01–v03 hanya dirujuk, tidak diulang.
2. **Dependensi eksplisit** — setiap modul baru menyebut prasyarat (modul existing atau sprint v01–v03).
3. **MVP first** — scope MVP dapat dieksekusi 1–2 sprint; fitur lanjut masuk backlog modul.
4. **Skip eksplisit** — item yang tidak akan dibangun ditulis di backlog dengan justifikasi.
5. **Bridge antar-modul** lewat Event/Listener (pola `PurchaseRequisitionApproved` di Procurement).

---

## Pemetaan 64 Area → Lokasi Roadmap

| § | Area | Status | Lokasi |
|---|------|--------|--------|
| 1 | Manajemen Struktur Yayasan | partial (Tenant/Organization/Department) | v05 |
| 2 | User/Role/Akses | partial (Shield+Spatie) | v01 (MFA), v09 (SSO/OAuth/LDAP/impersonate) |
| 3 | Master Data Terpadu | mostly exist | tersebar |
| 4 | Admission/PPDB | Enrollment module exist | v07 (PPDB CRM) |
| 5 | Akademik Sekolah | School module exist | v06 (analytics & risk) |
| 6 | Akademik PT | Campus module exist | v06 (tracer/BAN-PT/MBKM) |
| 7 | LMS | delegasi ke Moodle existing | **skip** in-house |
| 8 | Siswa | exist | v06 (profile 360°, risk score) |
| 9 | Parent Portal | NEW | v06 |
| 10 | Guru/Dosen | exist | v06 + v08 (AI assistant) |
| 11 | HRIS | partial (Employee) | v02 + v05 (rekrutmen/onboarding) |
| 12 | Keuangan Terpadu | exist (Finance) | v02 + v07 (donasi/wakaf) |
| 13 | Anggaran/Budgeting | exist (Budget) | v02 + v08 (RKAT lanjut) |
| 14 | Procurement | exist | v02 |
| 15 | Inventory | planned v02 | v02 |
| 16 | Aset/Sarpras | NEW | v05 |
| 17 | Gedung/Ruangan | NEW | v05 |
| 18 | Helpdesk/Ticketing | NEW | v05 |
| 19 | IT Management | NEW | v05 |
| 20 | ISO 27001 | NEW | v08 |
| 21 | DMS Dokumen & Arsip | NEW | v05 |
| 22 | E-Office Surat | NEW | v05 |
| 23 | Komunikasi Internal | partial (db notif) | v06 (broadcast WA/email) |
| 24 | Event & Kegiatan | NEW | v06 |
| 25 | Ekstrakurikuler & Prestasi | NEW | v06 |
| 26 | Bimbingan Konseling | NEW | v06 |
| 27 | UKS/Klinik | NEW | v06 |
| 28 | Perpustakaan | exist (Library) | — |
| 29 | Transportasi Sekolah | NEW | v05 |
| 30 | Asrama/Boarding | NEW | v05 |
| 31 | Kantin | NEW | v05 |
| 32 | Koperasi/Toko POS | NEW (extend Sales/POS v03 Sprint 4) | v07 |
| 33 | Seragam & Buku | NEW (overlap Koperasi) | v06 |
| 34 | Unit Usaha Yayasan | NEW (kursus, EO, sewa, cetak, konsul, properti) | v07 |
| 35 | Donasi/Wakaf/Endowment | NEW | v07 |
| 36 | Alumni & Career Center | partial (alumni status) | v06 |
| 37 | Marketing/CRM | partial (Enrollment) | v07 (pipeline CRM) |
| 38 | Website/CMS Yayasan | NEW | v07 |
| 39 | Legal & Kontrak | NEW | v05 |
| 40 | Project Management | NEW | v08 |
| 41 | Risk Management Enterprise | NEW | v08 |
| 42 | Audit Internal | NEW | v08 |
| 43 | QA Pendidikan | NEW | v08 |
| 44 | Decision Support System | partial (v03 Sprint 1.3) | v08 |
| 45 | BI/Data Warehouse | partial (v03 Sprint 2.1) | v08 |
| 46 | AI Assistant | NEW | v08 |
| 47 | Workflow Engine | exist (V2) | v09 (delegation/escalation/builder UI) |
| 48 | Mobile App | NEW | v09 |
| 49 | Integrasi Eksternal | partial (Moodle) | v09 |
| 50 | Notifikasi | partial (db) | v06 + v09 (channel expansion) |
| 51 | Meeting Mgmt | NEW | v08 |
| 52 | KPI Enterprise | partial (Employee KPI) | v08 |
| 53 | Capacity Planning | NEW | v08 |
| 54 | Keamanan Fisik & Visitor | NEW | v05 |
| 55 | Sustainability | NEW | v05 |
| 56 | Research & Incubator | NEW | **backlog** (defer ≥18 bln) |
| 57 | Marketplace Internal | NEW | v07 |
| 58 | API & Developer Platform | partial (Sanctum) | v09 |
| 59 | Forensic Audit Trail | partial (v03 Sprint 3.1) | v08 |
| 60 | Super Admin / System Config | partial (v01) | v09 (feature toggle, queue/job monitor) |
| 61 | SaaS Features | partial (v01) | v09 (reseller, white-label premium, tenant migration) |
| 62 | Premium Features | komposit | tersebar v07/v08 |
| 63 | Tahapan Build | meta | — |
| 64 | Branding "Integrated Education Foundation OS" | meta | dokumen pemasaran |

---

## Eksekusi: Waves Lintas-Roadmap

Roadmap dieksekusi **per wave**, bukan ketat per file. Wave A–E lintas v05–v09:

### Wave A — Operasional Yayasan Dasar (Bulan 7–9)
- v05 Sprint 1: Foundation Governance + Legal & Kontrak
- v05 Sprint 2: Aset/Sarpras MVP + DMS MVP
- v06 Sprint 1: Parent Portal MVP
- v09 Sprint 1: WhatsApp Gateway (prasyarat banyak modul)
- v05 Sprint 3: Helpdesk MVP

### Wave B — Layanan Sekolah Lengkap (Bulan 10–12)
- v05 Sprint 4: Gedung/Ruangan + Booking
- v05 Sprint 5: E-Office Surat + IT Management
- v06 Sprint 2: BK + UKS + Ekstrakurikuler + Event
- v05 Sprint 6 (kondisional per tipe sekolah): Transportasi, Kantin, Asrama

### Wave C — Revenue & Pertumbuhan (Bulan 13–15)
- v07 Sprint 1: PPDB CRM + Website/CMS
- v07 Sprint 2: Donasi/Wakaf/Endowment
- v07 Sprint 3: Unit Usaha (Kursus & Sewa Fasilitas)
- v06 Sprint 3: Alumni & Career Center
- v06 Sprint 4: Seragam & Buku (jika tidak via Koperasi)

### Wave D — Governance & Intelligence (Bulan 16–18)
- v08 Sprint 1: Enterprise Risk Mgmt + Forensic Trail extension
- v08 Sprint 2: Audit Internal + QA Pendidikan
- v08 Sprint 3: KPI Enterprise + Capacity Planning
- v08 Sprint 4: Project Mgmt + Meeting Mgmt
- v08 Sprint 5: Executive DSS + BI/DW lanjut

### Wave E — Platform Premium (Bulan 19–24)
- v09 Sprint 2: Mobile App (PWA + native shell)
- v09 Sprint 3: Workflow Expansion (delegation, escalation, builder UI)
- v09 Sprint 4: Public API & Developer Platform
- v08 Sprint 6: AI Assistant (advisor-only, bukan auto-decision)
- v07 Sprint 4: Marketplace Internal + Unit Usaha lanjut (Percetakan, Konsultan, Properti)
- v08 Sprint 7: ISO 27001 Compliance Automation

---

## Konvensi Modul Baru

Wajib untuk setiap modul baru di v05–v09:

1. Buat via `php artisan module:make <Name>` (coolsam/nwidart).
2. Tabel operasional wajib `tenant_id` (+ `organization_id` opsional, ikut pola Workflow).
3. Resource extend `Modules\Core\Filament\Support\ModuleResource`.
4. Form/infolist/table di `Schemas/` & `Tables/` (tidak inline).
5. Label lewat `FilamentUi` (id/en); frasa baru → `PHRASES`/`WORDS`.
6. Workflow integration ikut pola `Modules\Procurement\…\RelationManagers\WorkflowInstancesRelationManager`.
7. Bridge antar-modul via Event + Listener; jangan panggil model module lain langsung dari Filament action.
8. Setiap fitur premium **terikat ke `TenantModule`** aktivasi (App Store v01 Sprint 1.3) — nonaktifkan navigation jika modul tidak diaktifkan.
9. Importer extend `App\Filament\Imports\BaseModelImporter`.
10. Feature test `RefreshDatabase` + `Livewire::test()` happy + minimal 1 edge + 1 failure path.

---

## Backlog Permanen (Tidak Dibangun)

- **LMS Lite in-house** — Moodle integration sudah matang & production-grade.
- **Custom Widget Builder UI** — high effort, low priority. Pertimbangkan setelah permintaan eksplisit.
- **Face recognition AI native** — delegasi ke vendor existing (Hikvision/Dahua API).
- **Mobile native iOS/Android terpisah** — pakai PWA Filament + WebView shell, kecuali fitur GPS/foto offline benar-benar butuh native.
- **LDAP/AD on-prem konektor langsung** — hanya OAuth/SAML cloud-mediated.
- **Research & Incubator (§56)** — defer ≥18 bulan, butuh pasar khusus.

---

## Definition of Done Global

Setiap modul/sprint baru, minimal:

1. Feature test PHPUnit (happy + 1 edge + 1 failure)
2. `php artisan shield:generate --all --panel=admin --option=permissions --no-interaction`
3. `vendor/bin/pint --dirty --format agent` bersih
4. `php artisan optimize:clear`
5. Label `FilamentUi` lulus `composer run lint:translations`
6. Gating `TenantModule` aktivasi (jika premium/opsional)
7. Bridge antar-modul: assertion event fired + listener dijalankan + state side-effect tervalidasi
8. Dokumentasi 1 paragraf di `CHANGELOG` modul (jika ada)
