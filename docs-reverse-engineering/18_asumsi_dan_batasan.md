# 18 — Asumsi dan Batasan

**Commit analisis:** `d3be06aa` · **Tanggal:** 2026-06-19  
Dokumen ini mencatat asumsi metodologi, batas cakupan audit reverse engineering, inferensi yang mempengaruhi interpretasi dokumen `01`–`17`, serta area yang **wajib** diverifikasi manusia.

---

## Ringkasan

Reverse engineering FoundationOS dilakukan **hanya dari kode sumber dan artefak ter-commit**, tanpa observasi produksi. Dokumentasi di folder `docs-reverse-engineering/` **bukan** SRS resmi atau kontrak compliance.

---

## Asumsi Eksplisit

| ID | Asumsi | Dasar bukti | Dampak jika salah |
|----|--------|-------------|-------------------|
| ASM-01 | Commit `d3be06aa` representatif untuk seluruh set dokumen RE | `git rev-parse HEAD` | Perubahan uncommitted atau commit setelah tanggal audit tidak tercakup |
| ASM-02 | Boundary audit = file yang di-track git | `git ls-files` | Artefak lokal `storage/app/*.json` diabaikan kecuali diregenerasi |
| ASM-03 | `docs/catalogs/api-routes-catalog.json` akurat pada tanggal generate | `scripts/extract-api-routes.php` | Rute API baru setelah generate tidak tercatat di `14_api_dan_integrasi.md` |
| ASM-04 | Pola `ModuleResource` seragam untuk 257 resource | `rg -l "extends ModuleResource"` | Outlier resource (custom page, override query) bisa berperilaku berbeda |
| ASM-05 | README dan `module.json` mencerminkan positioning produk | `README.md`, `Modules/*/module.json` | Modul "ada di repo" ≠ aktif di semua tenant (`TenantModule`) |
| ASM-06 | Feature tests yang lulus mencerminkan perilaku yang diharapkan | `tests/Feature/*.php` | Test yang tidak ada = perilaku tidak terverifikasi otomatis |
| ASM-07 | `authorization-matrix.json` mencerminkan policy Shield ter-generate | `extract-authorization-matrix.php` | Permission efektif per tenant DB bisa berbeda dari matriks statis (GAP-07) |
| ASM-08 | Migrasi Laravel = sumber kebenaran schema | `database/migrations/`, `Modules/*/database/migrations/` | DB produksi dengan drift migrasi tidak terdeteksi |
| ASM-09 | Satu instance aplikasi = satu database shared multi-tenant | `BelongsToTenant`, `tenant_id` di tabel operasional | Pola `stancl/tenancy` (DB terpisah) **TIDAK TERDETEKSI DI KODE** |

---

## Batasan Cakupan

### In scope (FAKTA — metode audit)

| Area | Detail |
|------|--------|
| Kode aplikasi | `app/`, `Modules/` (45 modul), `routes/`, `config/` |
| Schema | 244+ migrasi PHP, pola `tenant_id`, soft delete |
| UI admin | Filament panel `admin`, `platform`, `parent` |
| API | `routes/api.php`, katalog 101 rute |
| Integrasi terbukti | Moodle outbox, Midtrans billing, webhook WhatsApp/exam |
| Tests | Feature tests sebagai bukti perilaku (workflow, finance, library, tenancy) |
| Script ekstraksi | `scripts/extract-*.php`, `scripts/generate-docs-appendices.php` |
| Katalog ter-commit | `docs/catalogs/api-routes-catalog.json`, `authorization-matrix.json` |

### Out of scope (tidak diaudit)

| Area | Alasan | Label dalam dokumen |
|------|--------|---------------------|
| `vendor/`, `node_modules/` | Dependensi pihak ketiga | — |
| `.env` produksi / secret | Keamanan | — |
| Database runtime produksi | Tidak diakses | **KETIDAKPASTIAN** untuk data live |
| Call graph / profiling runtime | Butuh APM atau Xdebug | — |
| Audit visual UX / aksesibilitas | Di luar kode | — |
| Infrastruktur deploy (IaC, K8s, Terraform) | **TIDAK TERDETEKSI DI KODE** | GAP-08 |
| Binary aplikasi mobile native | Hanya indikasi API consumer | GAP-03 |
| Kebijakan organisasi / SOP institusi | Bukan di service layer | — |
| Performa di bawah beban nyata | Tidak ada load test dalam audit | — |

---

## Yang TIDAK Diaudit (eksplisit)

| # | Area | Status | Referensi gap |
|---|------|--------|---------------|
| NA-01 | Validasi form 257 resource Filament (`*Form.php`) per field | **TERINDIKASI** — tidak sampling 100% | GAP-05 |
| NA-02 | 24 modul leaf — kedalaman otomasi bisnis di luar CRUD | CRUD terbukti; workflow/event tidak diaudit semua | GAP-13 |
| NA-03 | Engine payroll end-to-end (gaji bulanan terpusat) | **TIDAK TERDETEKSI DI KODE** | GAP-04 |
| NA-04 | Portal siswa/mahasiswa Filament dedicated | **TIDAK TERDETEKSI DI KODE** | GAP-03 |
| NA-05 | Module REST scaffold (`Modules/*/routes/api.php` generik) | **TERINDIKASI** — belum uji integrasi per modul | GAP-06 |
| NA-06 | Provider WhatsApp produksi (non-log) | Binding default `LogWhatsAppProvider` | GAP-14 |
| NA-07 | Protokol Feeder DIKTI penuh | Model `FeederLog` ada; API **TIDAK TERDETEKSI** penuh | `14_api_dan_integrasi.md` |
| NA-08 | Mapping SLIMS library lengkap | Command import ada; mapping detail tidak diaudit | `14_api_dan_integrasi.md` |
| NA-09 | Formula bobot penilaian K-12 global | **TIDAK TERDETEKSI** — per model assessment | `15_business_rules.md` |
| NA-10 | Trigger otomatis PR → workflow instance (baris demi baris) | **TERINDIKASI NAMUN BELUM TERBUKTI PENUH** | `12_bpmn.md` BPMN-01 |
| NA-11 | ERD lengkap 434 tabel | Hanya cluster kritis + referensi `entity-catalog.json` | GAP-01, GAP-02 |
| NA-12 | Permission rows efektif di DB staging/produksi | Matriks statis saja | GAP-07 |

---

## Inferensi yang Dipakai

| ID | Inferensi | Label dalam dokumen | Risiko |
|----|-----------|---------------------|--------|
| INF-01 | Modul leaf dominan CRUD-primary | INFERENSI di `04_analisis_modul.md` | Fitur tersembunyi di service tidak terdokumentasi |
| INF-02 | Siswa memakai API dashboard, bukan panel Filament | INFERENSI — tidak ada `StudentPanelProvider` | Product bisa menambah panel baru |
| INF-03 | ~3100 permission ≈ 257 resource × 12 method + custom | INFERENSI — `REAP_AUDIT.md` | Custom permission exam bisa tidak terhitung |
| INF-04 | Import Core ↔ domain = shared kernel acceptable | INFERENSI arsitektural | Coupling tinggi antar modul hub |
| INF-05 | `entity-catalog.json` (~434 tabel) akurat jika script dijalankan | KETIDAKPASTIAN tanpa file di git | Jumlah tabel bisa salah tanpa regenerasi |
| INF-06 | Marketplace / leaf modules production-ready | **TIDAK DIPAHAMI** tanpa traffic rules | `17_gap_analysis.md` |

---

## Area Review Manual (dari Gap Analysis)

Prioritas verifikasi manusia sebelum mengandalkan dokumen RE untuk keputusan produk atau compliance:

1. **Generate & commit katalog** — `php scripts/extract-entity-catalog.php` dan `extract-verified-fks.php`; commit ke `docs/catalogs/` (GAP-01, GAP-02).
2. **Test suite penuh** — `php artisan test --compact` setelah pull `main`.
3. **Trace payroll satu siklus** — di staging; konfirmasi apakah `SalarySlip` cukup atau ada engine terpisah (GAP-04).
4. **Sampling validasi form** — audit 10% random `*Form.php` untuk business rule di UI (GAP-05).
5. **Konfirmasi integrasi produksi** — `.env` WhatsApp provider, Moodle URL, Midtrans keys (GAP-14).
6. **Konfirmasi product** — apakah mobile app / portal siswa ada di repo terpisah (GAP-03).
7. **Dokumentasi infra** — deploy, backup, DR di luar repo (GAP-08).

---

## Prinsip Anti-Halusinasi (checklist audit)

- [x] Tidak ada use case tanpa route/controller/page
- [x] Tidak ada integrasi tanpa file client/webhook
- [x] Diagram punya tabel `## Bukti`
- [x] Gap dilabelkan eksplisit di `17_gap_analysis.md`
- [ ] ERD 434 tabel lengkap — **belum** (bergantung entity-catalog di git)
- [ ] BPMN semua proses bisnis — **belum** (hanya 3 proses + billing)
- [ ] Business rules semua modul — **belum** (cluster kritis saja)

---

## Ketergantungan Artefak

| Artefak | Di-git? | Dipakai untuk | Status jika hilang |
|---------|---------|---------------|-------------------|
| `api-routes-catalog.json` | Ya | `05`, `07`, `14` | Regenerasi via script |
| `authorization-matrix.json` | Ya | `07`, inferensi RBAC | Regenerasi + shield:generate |
| `entity-catalog.json` | **Tidak** | `11`, `16` | **TIDAK TERDETEKSI DI GIT** |
| `verified-fks.json` | **Tidak** | `11` kardinalitas FK | **TIDAK TERDETEKSI DI GIT** |

---

## Disclaimer

Dokumentasi reverse engineering ini:

- **Bukan** kontrak SRS, SLA, atau kebijakan keamanan resmi.
- **Tidak** menggantikan review arsitektur atau penetration test.
- Keputusan compliance, legal, dan operasional memerlukan verifikasi stakeholder dan lingkungan runtime.

Untuk onboarding developer, lanjut ke [`19_rangkuman_untuk_pengembang.md`](19_rangkuman_untuk_pengembang.md).
