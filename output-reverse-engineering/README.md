# Dokumentasi Reverse Engineering — FoundationOS

**Metode:** Analisis statis kode sumber (REAP — Reverse Engineering Auditor Protocol)  
**Commit analisis:** `d3be06aa` (branch `main`)  
**Tanggal:** 2026-06-19  
**Bahasa:** Indonesia formal

---

## Tujuan

Folder ini berisi dokumentasi sistem informasi yang direkonstruksi **hanya dari bukti kode**. Setiap klaim penting memiliki referensi file/path. Bagian yang tidak dapat diverifikasi ditandai **TIDAK TERDETEKSI DI KODE** atau **INFERENSI**.

---

## Indeks Dokumen

| No | File | Isi |
|----|------|-----|
| 01 | [01_ringkasan_sistem.md](./01_ringkasan_sistem.md) | Gambaran umum produk, scope, aktor utama |
| 02 | [02_inventaris_teknologi.md](./02_inventaris_teknologi.md) | Stack, dependensi, tooling |
| 03 | [03_arsitektur_aplikasi.md](./03_arsitektur_aplikasi.md) | Layer, entry point, multi-tenancy |
| 04 | [04_analisis_modul.md](./04_analisis_modul.md) | 45 modul, hub/leaf, ketergantungan |
| 05 | [05_requirements_fungsional.md](./05_requirements_fungsional.md) | Kebutuhan fungsional berbasis kode |
| 06 | [06_requirements_nonfungsional.md](./06_requirements_nonfungsional.md) | NFR: keamanan, i18n, performa |
| 07 | [07_use_case_diagram.md](./07_use_case_diagram.md) | Aktor dan use case terbukti |
| 08 | [08_activity_diagram.md](./08_activity_diagram.md) | Alur aktivitas proses utama |
| 09 | [09_sequence_diagram.md](./09_sequence_diagram.md) | Interaksi antar komponen |
| 10 | [10_class_diagram.md](./10_class_diagram.md) | Kelas domain inti |
| 11 | [11_er_diagram.md](./11_er_diagram.md) | Entitas dan relasi (parsial) |
| 12 | [12_bpmn.md](./12_bpmn.md) | Proses bisnis terstruktur (BPMN-like) |
| 13 | [13_alur_ui.md](./13_alur_ui.md) | Panel Filament dan navigasi |
| 14 | [14_api_dan_integrasi.md](./14_api_dan_integrasi.md) | REST API, webhook, integrasi eksternal |
| 15 | [15_business_rules.md](./15_business_rules.md) | Aturan bisnis teridentifikasi di service |
| 16 | [16_data_dictionary.md](./16_data_dictionary.md) | Kamus data entitas inti |
| 17 | [17_gap_analysis.md](./17_gap_analysis.md) | Kekurangan dan area belum terverifikasi |
| 18 | [18_asumsi_dan_batasan.md](./18_asumsi_dan_batasan.md) | Asumsi metodologi dan batasan audit |
| 19 | [19_rangkuman_untuk_pengembang.md](./19_rangkuman_untuk_pengembang.md) | Panduan singkat untuk developer |

---

## Artefak Sumber di Repo (Referensi Silang)

Dokumen ini melengkapi (bukan mengganti) artefak REAP di root repositori:

| Artefak | Path |
|---------|------|
| Manifest repositori | `00-repository-manifest.md` |
| Registry bukti | `01-evidence-registry.json` |
| Katalog struktur | `02-structure-catalog.md` |
| Peta dependensi modul | `03-module-dependency-map.md` |
| Arsitektur | `ARCHITECTURE.md` |
| Use case (inggris) | `USE_CASES.md` |
| Katalog API | `API_ROUTES.md`, `docs/catalogs/api-routes-catalog.json` |
| Matriks otorisasi | `AUTHORIZATION_MATRIX.md`, `docs/catalogs/authorization-matrix.json` |

---

## Legenda Tingkat Keyakinan

| Label | Arti |
|-------|------|
| **TERVERIFIKASI** | Bukti langsung di kode (file, route, migration) |
| **INFERENSI** | Disimpulkan dari pola kode; bukti tidak eksplisit |
| **TIDAK TERDETEKSI** | Tidak ditemukan jejak di kode sumber |
| **PARSIAL** | Fitur ada tetapi implementasi tidak lengkap terbukti |

---

## Catatan Penting

- `storage/app/entity-catalog.json` dan `storage/app/verified-fks.json` **tidak ter-commit** di git pada commit ini — jumlah tabel/FK di dokumentasi ERD bersifat **PARSIAL** (berbasis migration, bukan dump DB).
- Portal login khusus **Student** sebagai panel Filament terpisah: **TIDAK TERDETEKSI DI KODE**.
