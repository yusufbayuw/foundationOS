# Reverse Engineering Documentation

Dokumentasi rekonstruksi sistem FoundationOS dari analisis statis kode sumber. Setiap dokumen memakai label bukti: **FAKTA**, **INFERENSI**, **KETIDAKPASTIAN**, **TIDAK TERDETEKSI DI KODE**.

---

## Daftar Dokumen

01 [Ringkasan Sistem](01_ringkasan_sistem.md) — Identitas produk ERP pendidikan multi-tenant, scope, aktor, dan domain bisnis inti. **Status: LENGKAP**

02 [Inventaris Teknologi](02_inventaris_teknologi.md) — Stack PHP 8.4, Laravel 13, Filament 5, dependensi, CI, middleware, dan autentikasi. **Status: LENGKAP**

03 [Arsitektur](03_arsitektur_aplikasi.md) — Modular monolith, layered MVC, komponen inti, entry point, dan pola tenancy. **Status: LENGKAP**

04 [Modul](04_analisis_modul.md) — 45 modul fitur: tier hub/leaf, model, service, resource Filament, graf dependensi. **Status: LENGKAP**

05 [Functional Requirement](05_requirements_fungsional.md) — FR diturunkan dari route, controller, service, UI Filament, policy, dan validasi. **Status: LENGKAP**

06 [Non Functional Requirement](06_requirements_nonfungsional.md) — NFR keamanan, audit, cache, queue, dan reliability dari config. **Status: LENGKAP**

07 [Use Case](07_use_case_diagram.md) — Aktor, use case terbukti, matriks akses RBAC, diagram per UC. **Status: LENGKAP**

08 [Activity](08_activity_diagram.md) — Activity diagram 14 alur utama: login, workflow, finance, enrollment, exam, billing. **Status: LENGKAP**

09 [Sequence](09_sequence_diagram.md) — Sequence diagram 15 interaksi termasuk Moodle outbox dan webhook. **Status: LENGKAP**

10 [Class Diagram](10_class_diagram.md) — Cluster kelas Core, Workflow, Finance, Enrollment; interface dan relasi UML. **Status: LENGKAP**

11 [ERD](11_er_diagram.md) — Entitas dan relasi dari migrasi; cluster tenancy, school, finance, workflow. **Status: LENGKAP**

12 [BPMN](12_bpmn.md) — 17 diagram proses (core/support/exception): enrollment, finance, workflow, procurement; legenda BPMN Mermaid. **Status: LENGKAP** (attendance/payroll engine TIDAK TERDETEKSI)

13 [UI Flow](13_alur_ui.md) — Tiga panel Filament, navigasi modul, screen flow per domain, UI publik. **Status: LENGKAP**

14 [API](14_api_dan_integrasi.md) — 101 rute REST, detail endpoint JSON, queue/event, integrasi Moodle/Midtrans/WhatsApp; dependency map Mermaid. **Status: LENGKAP** (55 scaffold modul non-JSON; GraphQL/WebSocket TIDAK TERDETEKSI)

15 [Business Rules](15_business_rules.md) — 56 aturan BR-001–056: validation, domain, security, workflow dengan lokasi kode. **Status: LENGKAP** (audit 257 form Filament TIDAK TERDETEKSI)

16 [Data Dictionary](16_data_dictionary.md) — Kamus kolom tabel kritis; 428 tabel lain via entity-catalog. **Status: PARSIAL**

17 [Gap Analysis](17_gap_analysis.md) — 40 gap (12 High / 16 Medium / 12 Low), matriks inkonsistensi lintas dokumen, 15 area review manusia. **Status: LENGKAP**

18 [Asumsi dan Batasan](18_asumsi_dan_batasan.md) — Asumsi metodologi, batas audit, inferensi, area review manual. **Status: LENGKAP**

19 [Developer Summary](19_rangkuman_untuk_pengembang.md) — Onboarding developer: arsitektur, modul, risiko, technical debt, diagram. **Status: LENGKAP**

---

## Metodologi

1. **Analisis statis** — Pembacaan `app/`, `Modules/`, `routes/`, `config/`, `database/migrations/`, dan feature tests; tanpa akses database produksi.
2. **Bukti per temuan** — Klaim utama disertai tabel `## Bukti` (file, komponen, keterangan).
3. **Label ketat** — Tanpa jejak kode → **TIDAK TERDETEKSI DI KODE**; simpulan pola → **INFERENSI**.
4. **Artefak ter-generate** — Katalog API dan matriks otorisasi di-commit; entity/FK catalog diregenerasi lokal.
5. **Cross-reference** — UC → Activity → Sequence; gap analysis sebagai filter kelengkapan.

---

## Commit Referensi

| Item | Nilai |
|------|-------|
| Commit analisis | `d3be06aa` |
| Branch | `main` |
| Tanggal rekonstruksi | 2026-06-19 |

Perubahan setelah commit ini tidak tercakup hingga dokumentasi diregenerasi.

---

## Cara Regenerasi Artefak

| Artefak | Perintah | Output |
|---------|----------|--------|
| Katalog rute API | `php scripts/extract-api-routes.php` | `docs/catalogs/api-routes-catalog.json` |
| Matriks otorisasi | `php scripts/extract-authorization-matrix.php` | `docs/catalogs/authorization-matrix.json` |
| Entity catalog | `php scripts/extract-entity-catalog.php` | `storage/app/entity-catalog.json` |
| Foreign key terverifikasi | `php scripts/extract-verified-fks.php` | `storage/app/verified-fks.json` |
| Appendices | `php scripts/generate-docs-appendices.php` | Berbagai file di `docs/` |

**Setelah menambah resource Filament:**

```bash
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
php scripts/extract-authorization-matrix.php
php artisan optimize:clear
```

**Rekomendasi:** commit `entity-catalog.json` dan `verified-fks.json` ke `docs/catalogs/` (lihat GAP-01, GAP-02 di `17_gap_analysis.md`).

---

## Konvensi Label

| Label | Arti |
|-------|------|
| **FAKTA** | Terbukti langsung di kode |
| **INFERENSI** | Disimpulkan dari pola |
| **KETIDAKPASTIAN** | Bukti tidak cukup |
| **TIDAK TERDETEKSI DI KODE** | Tidak ada jejak memadai |
| **TERINDIKASI NAMUN BELUM TERBUKTI PENUH** | Bukti sebagian; alur E2E belum lengkap |
