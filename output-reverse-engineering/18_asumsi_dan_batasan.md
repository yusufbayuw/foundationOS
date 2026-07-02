# 18 — Asumsi dan Batasan

## Ringkasan Singkat

Dokumen ini mencatat asumsi metodologi audit, batasan cakupan, dan inferensi yang digunakan saat menyusun dokumentasi reverse engineering.

---

## Asumsi Metodologi

| ID | Asumsi | Dasar | Risiko jika salah |
|----|--------|-------|-------------------|
| ASM-01 | Kode di commit `d3be06aa` branch `main` representatif | `git rev-parse HEAD` | Perubahan lokal uncommitted tidak tercakup |
| ASM-02 | File git-tracked = boundary audit | `git ls-files` | Artefak lokal di `storage/app/` diabaikan |
| ASM-03 | `composer.json` constraint mencerminkan runtime dev | Herd PHP 8.4 | Produksi bisa beda patch version |
| ASM-04 | Pola `ModuleResource` seragam untuk 257 resource | Sampling + grep | Outlier resource mungkin punya perilaku khusus |
| ASM-05 | README.md akurat untuk positioning produk | Marketing vs kode | Fitur "40+ modul" tidak berarti semua aktif |
| ASM-06 | Dokumen REAP root (`ARCHITECTURE.md`, dll.) sudah diaudit sebagian | `REAP_AUDIT.md` | Beberapa diagram root = Draft |

---

## Batasan Cakupan

### In Scope

- Struktur folder dan modul
- Routing HTTP/API/console (statis)
- Model, migration, service, policy (sampling + hub)
- Konfigurasi aplikasi (`config/`, module config)
- Test feature sebagai bukti perilaku
- Script ekstraksi di `scripts/`
- Katalog JSON di `docs/catalogs/`

### Out of Scope

| Area | Alasan |
|------|--------|
| `vendor/`, `node_modules/` | Dependensi pihak ketiga |
| Isi `.env` produksi | Secret |
| Runtime database production | Tidak ada akses |
| Call graph runtime / profiling | Butuh eksekusi |
| UI visual / UX qualitative | Di luar analisis kode |
| Performa load test | Tidak ada benchmark di repo |
| Isi penuh 6.062 file satu-per-satu | Skala; sampling + tooling |

---

## Inferensi yang Digunakan

| ID | Inferensi | Alasan | Label di dokumen |
|----|-----------|--------|------------------|
| INF-01 | Modul leaf = dominan CRUD | Import graph hanya Core | INFERENSI |
| INF-02 | `platform_owner` = satu-satunya role platform | Satu migration role | TERVERIFIKASI untuk role tersebut |
| INF-03 | Student menggunakan API dashboard, bukan panel | Tidak ada panel siswa | INFERENSI |
| INF-04 | ~3100 permission keys dari 257×12+16 | `REAP_AUDIT.md` EV-00008 | INFERENSI |
| INF-05 | Core↔domain coupling = shared kernel acceptable | Pola umum modular monolith | INFERENSI arsitektural |

---

## Prinsip Anti-Halusinasi yang Diterapkan

1. Tidak membuat use case tanpa route/controller/page.
2. Tidak mengisi tipe data tanpa migration/model.
3. Tidak mendokumentasikan integrasi tanpa file client/webhook.
4. Memisahkan fakta (TERVERIFIKASI), inferensi, dan gap.
5. Diagram hanya untuk alur dengan jejak service/event.

---

## Ketergantungan pada Artefak Pihak Ketiga

| Artefak | Digunakan untuk | Commit di git? |
|---------|-----------------|----------------|
| `docs/catalogs/api-routes-catalog.json` | Daftar API | Ya |
| `docs/catalogs/authorization-matrix.json` | Permission scaffold | Ya |
| `storage/app/entity-catalog.json` | Jumlah tabel/model | **Tidak** |
| `storage/app/verified-fks.json` | FK graph | **Tidak** |

---

## Versi dan Lingkungan Analisis

| Item | Nilai |
|------|-------|
| Commit | `d3be06aa` |
| Branch | `main` |
| Tanggal | 2026-06-19 |
| Metode | Static analysis + REAP artifacts cross-check |
| Bahasa output | Indonesia formal |

---

## Disclaimer

Dokumentasi ini **bukan** spesifikasi kontrak (SRS) resmi. Untuk keputusan compliance, legal, atau procurement, verifikasi manual dan review stakeholder tetap diperlukan — terutama pada area bertanda GAP di `17_gap_analysis.md`.
