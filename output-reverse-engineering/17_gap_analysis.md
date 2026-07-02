# 17 — Gap Analysis

## Ringkasan Singkat

Analisis kesenjangan antara cakupan dokumentasi reverse engineering ini dan kenyataan kodebase. Membedakan **tidak ditemukan** vs **belum diverifikasi penuh**.

---

## Yang Berhasil Direkonstruksi (TERVERIFIKASI)

| Area | Tingkat kelengkapan | Bukti utama |
|------|---------------------|-------------|
| Stack teknologi | Tinggi | `composer.json`, `package.json` |
| Arsitektur modular monolith | Tinggi | 45 modul, `ModuleResource` |
| Multi-tenancy shared DB | Tinggi | `BelongsToTenant`, middleware |
| 3 panel Filament | Tinggi | Panel providers |
| Workflow V2 engine | Tinggi | `DatabaseWorkflowEngine` + tests |
| API v1 inti mobile | Tinggi | `routes/api.php`, katalog JSON |
| Integrasi Moodle/Midtrans/WhatsApp | Sedang-tinggi | `app/Integrations/`, Messaging |
| Business rules kunci (Finance, Procurement, Enrollment) | Sedang | Service classes |
| 101 rute API | Tinggi | `api-routes-catalog.json` |
| 257 resource authorization scaffold | Sedang | `authorization-matrix.json` |

---

## Gap Kritis

| ID | Gap | Dampak | Rekomendasi verifikasi |
|----|-----|--------|------------------------|
| GAP-01 | `entity-catalog.json` tidak di-git | ERD/dictionary tidak lengkap | Jalankan `scripts/extract-entity-catalog.php`, commit atau lampirkan artefak |
| GAP-02 | `verified-fks.json` tidak di-git | Kardinalitas FK tidak pasti | Jalankan `scripts/extract-verified-fks.php` |
| GAP-03 | Student portal UI dedicated | Use case siswa tidak lengkap | Konfirmasi product intent; cek mobile app repo terpisah |
| GAP-04 | Payroll calculation engine | Proses BPMN payroll tidak pasti | Audit `Employee` services + tests payroll |
| GAP-05 | 257 CRUD resources — rules per form | Business rules UI tidak terdokumentasi | Sampling audit + `lint-translations` |
| GAP-06 | Module REST scaffold vs production-ready | API modul mungkin boilerplate | Test integrasi per modul API |
| GAP-07 | `SEQUENCE_DIAGRAMS.md` stale line refs | Diagram root repo bisa misleading | Re-run line audit atau gunakan dokumen ini |
| GAP-08 | Runtime permission rows di DB | Matriks otorisasi efektif per tenant | Query `permissions`/`roles` di staging |
| GAP-09 | Feeder DIKTI integration protocol | Campus feeder hanya model/log | Baca service Feeder jika ada |
| GAP-10 | Deploy/IaC | NFR availability tidak pasti | Dokumentasi infra di luar repo |

---

## Gap Menengah

| ID | Gap | Status |
|----|-----|--------|
| GAP-11 | State machine semua entitas (13 diagram) | `STATE_DIAGRAMS.md` = Draft |
| GAP-12 | Event catalog listener coverage | `EVENTS.md` ada; tidak semua diverifikasi |
| GAP-13 | Exam module isolation — integrasi gradebook | Model ada; alur end-to-end PARSIAL |
| GAP-14 | Leaf modules depth (Risk, IsoCompliance, dll.) | CRUD terbukti; otomasi bisnis UNKNOWN |
| GAP-15 | Mobile Capacitor app | Config folder only |

---

## Per Modul — Kedalaman Implementasi

| Kategori | Modul | Observasi |
|----------|-------|-----------|
| Deep domain | Core, Workflow, School, Campus, Finance, Procurement, Employee, Enrollment, Library | Services + tests + pipelines |
| Integration-heavy | Monitoring (Moodle outbox), Messaging | Outbound/inbound jelas |
| CRUD-primary | 24 leaf modules | Import graph hanya ke Core |
| Isolated | Exam | Zero inbound imports |

**Keyakinan klasifikasi:** INFERENSI dari `03-module-dependency-map.md` + sampling services.

---

## Dokumentasi vs Kode

| Dokumen root repo | Gap |
|-------------------|-----|
| `ARCHITECTURE.md` | Metrik 434 tabel mengandalkan file gitignored |
| `USE_CASES.md` | Selaras; tidak punya EV-ID formal |
| `TECHNICAL_DOCUMENTATION.md` | Index; partial gaps shared |
| `REAP_AUDIT.md` | Mencatat gap yang sama — konsisten |

---

## Matriks: Tidak Ditemukan vs Tidak Dipahami

| Pernyataan | Klasifikasi |
|------------|-------------|
| Portal login siswa | **Tidak ditemukan** di kode |
| Jumlah pasti 434 tabel | **Tidak dipahami** tanpa entity-catalog |
| Apakah Marketplace production-ready | **Tidak dipahami** — resource ada, traffic rules tidak |
| Apakah `LogWhatsAppProvider` dipakai produksi | **Tidak dipahami** — binding default log |

---

## Prioritas Verifikasi Manual

1. Generate dan simpan `entity-catalog.json` + `verified-fks.json` ke `docs/catalogs/`
2. Jalankan `php artisan test --compact` penuh dan catat modul tanpa test
3. Audit 10% random `ModuleResource` untuk business validation rules
4. Trace satu alur payroll end-to-end di staging
5. Konfirmasi mobile client di repo `mobile/` atau terpisah

---

## Catatan Metodologi

Dokumentasi ini **sengaja** tidak mengisi gap dengan asumsi. Area bertanda PARSIAL memiliki bukti sebagian; area TIDAK TERDETEKSI tidak memiliki jejak kode yang cukup untuk diagram/requirement formal.
