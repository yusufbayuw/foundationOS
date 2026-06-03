# FoundationOS Development Roadmap V2 — Standardisasi Translasi UI

> **Status terpusat:** [PLAN_STATUS.md](PLAN_STATUS.md) — Epic 1 locale & switcher sebagian besar sudah `[x]` di kode; checkbox di dokumen ini belum disinkronkan penuh.

Dokumen ini fokus pada **satu inisiatif besar**: merapikan dan menstandarisasi translasi (label, judul, placeholder, helper) di seluruh resource, form, infolist, dan table Filament. Saat ini implementasi sangat tidak konsisten — sebagian module memakai `FilamentUi::field()` / `FilamentUi::text()`, sebagian lain hardcode English, sebagian lagi hardcode Indonesian, dan banyak yang mengandalkan auto-generated label Filament tanpa lapisan translasi.

Roadmap ini dibagi per epic seperti `ROADMAP.md` utama. Status legend: `[ ]` belum mulai · `[~]` sedang berjalan · `[x]` selesai.

---

## Ringkasan Temuan Audit (baseline 2026-05-22)

| Kategori | Jumlah | Catatan |
|---|---|---|
| Hardcoded field label (`->label('English')`) | ~219 instance, 21 file | Workflow paling parah (156), Procurement 25, Finance 18, Employee 18, Campus 2 |
| Hardcoded section/tab/fieldset title (`Section::make('English')`) | ~700+ instance lintas modul | School 118, Core 116, Procurement 107, Employee 83, Campus 75, Library 68, Finance 57, Enrollment 43, Workflow 45, Global 18, Monitoring 16 |
| Hardcoded placeholder | ~978 instance | School 219, Procurement 149, Core 124, Employee 121, Campus 103, Library 60, Finance 58, Enrollment 58, Workflow 45, Monitoring 28, Global 13 |
| Hardcoded helperText | ~30 instance | Library 27, Workflow 3 — beberapa sudah berbahasa Indonesia langsung di kode |
| Table column tanpa `->label()` eksplisit | ~70% kolom | Mengandalkan auto-label Filament yang tidak melewati `FilamentUi` |
| Infolist entry tanpa `->label()` eksplisit | mayoritas | Sama dengan tabel |
| Module dengan struktur tidak standar | Workflow | Tidak punya folder `Schemas/` & `Tables/`; semua inline di Resource |
| Locale tidak pernah di-switch | `APP_LOCALE=en` default; tidak ada pemanggilan `App::setLocale('id')` di mana pun → `FilamentUi::isIndonesian()` selalu false di production |
| Helper bahasa campur | Workflow `helperText('Kosongkan untuk workflow tenant-wide.')` & `helperText('Isi FQCN model subject...')` |

**Konsekuensi.** Walau `FilamentUi` punya kamus 100+ frasa & 80+ kata, hampir semua hasilnya tidak terlihat user — karena (a) banyak label tidak melewati helper, (b) locale tidak pernah `id`. Akar masalah harus dibereskan dari fondasi (locale switching + konvensi) sebelum cleanup massal.

---

## Epic 1 — Fondasi Locale & Konvensi `[ ]`

**Tujuan.** Pastikan locale benar-benar berpindah ke `id` per-user/per-tenant, lengkapi kamus `FilamentUi`, dan tulis konvensi resmi sebelum cleanup massal dimulai. Tanpa fondasi ini, refaktor lain tidak akan terlihat hasilnya.

### Fase 1.1 — Locale Switching Berdasarkan User Preference `[ ]`

**Konteks.** `Modules\Core\Support\FilamentUi::isIndonesian()` membaca `app()->getLocale() === 'id'`. Tapi `config/app.php` default `en`, `.env.example` set `en`, dan tidak ada `App::setLocale()` di middleware mana pun. Praktis semua kamus FilamentUi mati.

**Deliverable.**
- Kolom baru `users.preferred_locale` (`enum('id','en')`, default `'id'`) via migration.
- Middleware baru `Modules\Core\Http\Middleware\SetUserLocale` — dipasang di group `web` & panel `admin`. Urutan: setelah `Authenticate`, sebelum `SubstituteBindings`.
- Field "Bahasa" di profil user (Filament `EditProfile` page) → `Select` dengan opsi `id`/`en`.
- Update `config/app.php`: `locale => env('APP_LOCALE', 'id')`, `fallback_locale => 'en'`.
- Update `.env.example`: `APP_LOCALE=id`.
- Setting tenant `core.default_locale` (`TenantSetting`) sebagai default untuk user baru.
- Queue worker harus respect locale instance saat job di-dispatch: bind via `LocaleAware` job middleware (mirip `Modules\Core\Queue\TenantAwareJobMiddleware`).

**Acceptance Criteria.**
- Test: `UserLocalePreferencePersistsTest` — set `preferred_locale=id` → request panel admin → `app()->getLocale()` = `id`.
- Test: `FilamentUiHonorsLocaleTest` — locale `id` → `FilamentUi::text('Student')` = `Siswa`; locale `en` → `Student`.
- Test: `QueueJobInheritsLocaleTest` — dispatch job dengan locale `id` → handler melihat locale `id`.
- Manual: login → set bahasa Indonesia → semua sidebar group + resource label berbahasa Indonesia.

**Dependensi.** Tidak ada.

**Risiko.** Cache panel Filament menyimpan navigation label per-locale → wajib clear `optimize:clear` setelah deploy. Tambah note di `CLAUDE.md`.

---

### Fase 1.2 — Lengkapi Kamus FilamentUi `[ ]`

**Konteks.** Audit menemukan frasa umum yang tidak ada di `PHRASES` maupun `WORDS`, sehingga fallback word-by-word menghasilkan label aneh ("Started At" → "Started At" karena `started` tidak ada di kamus).

**Deliverable.**
- Tambah ke `PHRASES` (singular + plural di mana relevan):
  - `Phone number` → `Nomor telepon`
  - `Started at` → `Dimulai pada`
  - `Finished at` → `Selesai pada`
  - `Due at` → `Jatuh tempo`
  - `Published at` → `Dipublikasikan pada`
  - `Birth date` → `Tanggal lahir`
  - `Birth place` → `Tempat lahir`
  - `Father name` → `Nama ayah`
  - `Mother name` → `Nama ibu`
  - `Quantity` → `Jumlah`
  - `Unit price` → `Harga satuan`
  - `Total amount` → `Total`
  - `Sub total` → `Subtotal`
  - `Tax` → `Pajak`
  - `Discount` → `Diskon`
  - `Reference number` → `Nomor referensi`
  - `Reference` → `Referensi`
  - `Remarks` → `Catatan`
  - `Notes` → `Catatan`
  - `Description` → `Deskripsi`
  - `Status` → `Status`
  - `General information` → `Informasi umum`
  - `Settings` → `Pengaturan`
  - `Scope` → `Cakupan`
  - `Configuration` → `Konfigurasi`
  - `Approval` → `Persetujuan`
  - `Trigger event` → `Event pemicu`
  - `Trigger mode` → `Mode pemicu`
  - `Subject type` → `Tipe subjek`
  - `Subject label` → `Label subjek`
  - `Step` / `Steps` → `Langkah`
  - `Action type` → `Jenis aksi`
  - `Sort order` → `Urutan`
  - `Version` → `Versi`
  - `Current step` → `Langkah saat ini`
  - `Requester` → `Pemohon`
  - `Status before` → `Status sebelum`
  - `Status after` → `Status sesudah`
  - `Workflow version` → `Versi workflow`
  - `Step type` → `Tipe langkah`
  - `To step` → `Ke langkah`
  - `SLA hours` → `Batas waktu (jam)`
  - `Calculation` → `Kalkulasi`
  - `Payment details` → `Detail pembayaran`
  - `Payment method` → `Metode pembayaran`
  - `Loan limits` → `Batas peminjaman`
  - `Quantity and pricing` → `Jumlah & harga`
  - `Is taxable` → `Kena pajak`
  - `Is mandatory` → `Wajib`
  - `Is active` (sudah ada) — verify
  - `Created by` → `Dibuat oleh`
  - `Updated by` → `Diperbarui oleh`
- Tambah ke `WORDS`:
  - `started` → `dimulai`
  - `finished` → `selesai`
  - `published` → `dipublikasikan`
  - `remarks` → `catatan`
  - `phase` → `fase`
  - `cycle` → `siklus`
  - `mode` → `mode`
  - `gateway` → `gerbang`
  - `quorum` → `kuorum`
  - `assignee` → `penanggung jawab`
  - `approver` → `penyetuju`
  - `reviewer` → `peninjau`
  - `outcome` → `hasil`
  - `branch` → `cabang`
  - `instance` → `instans`
- Perbaiki typo: `Collage student` tetap dipertahankan (digunakan di Resource name `CollageStudentResource`, hanya alias UI) — tambah komentar `// Typo asli di kelas Resource; jangan diubah tanpa rename file`.
- Unit test baru `FilamentUiTranslationsTest` — assert tiap frasa baru menghasilkan output yang diharapkan, dan assert plural form-nya pun ada.

**Acceptance Criteria.**
- Test `FilamentUiTranslationsTest` hijau dengan ≥40 assertion frasa baru.
- Tidak ada hasil `FilamentUi::text()` yang berisi kata berbahasa Inggris saat locale `id` (selain proper noun seperti `KPI`, `SLA`, `RFQ`).
- Dokumentasi inline di header `FilamentUi.php` mencantumkan tanggal update + sumber.

**Dependensi.** Tidak ada (paralel dengan 1.1).

**Risiko.** Konflik plural form (mis. `Notes` sudah dipakai sebagai singular field, tapi `Notes` juga bisa muncul sebagai plural). Solusi: pertahankan `Notes` sebagai singular `Catatan`; jika butuh plural beda, tambah `Note` singular dulu.

---

### Fase 1.3 — Konvensi Resmi & Linter `[ ]`

**Konteks.** Tidak ada panduan tertulis tentang kapan pakai `FilamentUi::field()` vs `::text()` vs `::resource()`. Developer baru meniru file terdekat — yang sering juga salah.

**Deliverable.**
- Section baru di `CLAUDE.md` → "Translasi & Label" berisi aturan:
  1. **Field label** (form field, table column, infolist entry) wajib `->label(FilamentUi::field('field_name'))`. Jangan andalkan auto-label Filament.
  2. **Section / Tab / Fieldset / Wizard step title** wajib `Section::make(FilamentUi::text('Title'))`.
  3. **Placeholder, helperText, prefix, suffix** wajib lewat `FilamentUi::text(...)` jika ada teks naratif.
  4. **Action label** custom wajib `Action::make('name')->label(FilamentUi::text('Action Name'))`. Action bawaan Filament (Create/Edit/Delete) tidak perlu diubah — sudah ditranslasi Filament.
  5. **Modal heading / description** wajib lewat helper.
  6. **Resource navigation** sudah otomatis via `ModuleResource::getNavigationLabel()` & `getModelLabel()` — tidak perlu override.
- Script PHP `scripts/lint-translations.php` — scan `Modules/*/app/Filament` & `app/Filament`, deteksi anti-pattern:
  - `->label('Capitalized English Word'` (regex: `->label\(['"][A-Z][a-zA-Z ]+['"]\)`)
  - `Section::make('English Title'`
  - `Tabs\Tab::make('English Title'`
  - `Fieldset::make('English Title'`
  - `->placeholder('non-dash text')` (allowlist `-`, `0`, kode unik)
- Tambah Composer script: `composer run lint:translations` → exit 1 jika ada match.
- Github Actions workflow `lint-translations.yml` — jalankan di PR.

**Acceptance Criteria.**
- `composer run lint:translations` exit 1 di baseline saat ini (membuktikan linter berfungsi).
- CLAUDE.md punya section "Translasi & Label" dengan 6 aturan + contoh ✅/❌.
- README.md menyebut keberadaan linter.

**Dependensi.** Sebaiknya setelah 1.2 (kamus lengkap) supaya linter tidak salah false-positive nanti.

**Risiko.** Linter regex bisa false positive (mis. `->label('-')`). Buat allowlist + opsi `// fos:lint-ignore-translation` untuk override per baris.

---

## Epic 2 — Refaktor Workflow Module `[ ]`

**Tujuan.** Workflow adalah modul paling kritis (156 hardcoded label, 9 file, struktur Schemas/Tables belum dipisah). Tuntaskan dulu sebelum modul lain agar pola refaktor jadi referensi.

### Fase 2.1 — Pecah Struktur Workflow Resource sesuai Konvensi `[ ]`

**Konteks.** Tidak seperti modul lain, file `WorkflowResource.php`, `WorkflowInstanceResource.php`, `WorkflowStepResource.php`, `WorkflowTransitionResource.php` masih menampung form/table/infolist inline. `CLAUDE.md` mewajibkan pemisahan ke `Schemas/` dan `Tables/`.

**Deliverable.**
- Buat folder per resource:
  - `Modules/Workflow/app/Filament/Resources/Workflows/Schemas/WorkflowForm.php`, `WorkflowInfolist.php`
  - `Modules/Workflow/app/Filament/Resources/Workflows/Tables/WorkflowsTable.php`
  - Sama untuk `WorkflowInstances`, `WorkflowSteps`, `WorkflowTransitions`.
- Pindahkan method `form()`, `infolist()`, `table()` dari Resource class ke kelas Schema/Table baru, dipanggil via static method.
- Update Resource class agar delegate: `public static function form(Schema $schema): Schema { return WorkflowForm::configure($schema); }`.
- 3 RelationManager (`StepsRelationManager`, `TransitionsRelationManager`, `AutomatedActionsRelationManager`, `AssignmentsRelationManager`, `LogsRelationManager`) tetap di tempat — tapi schema-nya di-extract ke method statis terpisah supaya bisa dites.

**Acceptance Criteria.**
- Test eksisting `tests/Feature/WorkflowDefinitionLifecycleTest.php`, `WorkflowEngineCancellationTest.php`, dst. tetap hijau.
- Tidak ada lagi method `form()`/`table()`/`infolist()` inline di `WorkflowResource.php` cs.
- Diff file Resource < 150 baris (semula 250+).

**Dependensi.** Fase 1.3 (konvensi disepakati).

**Risiko.** RelationManager test ber-reference path lama → run full Workflow test suite setelah pindah.

---

### Fase 2.2 — Translasi Semua Label Workflow `[ ]`

**Konteks.** 156 hardcoded label lintas 9 file. Daftar lengkap di laporan audit: `Organization`, `Code`, `Name`, `Module`, `Subject Type`, `Trigger Mode`, `Version`, `Status`, `Is Active`, `Published At`, `Description`, `Step`, `Trigger Event`, `Action Type`, `Sort Order`, `Config`, `Started At`, `Due At`, `Current Step`, `Requester`, `Status Before`, `Status After`, `Notes`, `Workflow Version`, `Step Type`, `To Step`, `Subject Label`, `Ready For Sourcing`, `SLA Hours`, dst.

**Deliverable.**
- Ganti seluruh `->label('English')` menjadi `->label(FilamentUi::field('snake_case_field'))` jika berasal dari kolom DB, atau `->label(FilamentUi::text('English'))` jika label deskriptif.
- Ganti `Section::make('English')` dengan `Section::make(FilamentUi::text('English'))`.
- Ganti `helperText('Kosongkan untuk workflow tenant-wide.')` dan helper-text Indonesia lainnya menjadi `helperText(FilamentUi::text('...'))` — push terjemahan ke `PHRASES` jika belum ada.
- Verifikasi field `is_active`, `is_global`, `is_published`, dst. memakai `FilamentUi::field()` (saat ini banyak yang masih `->label('Is Active')`).

**Acceptance Criteria.**
- `grep -rE "->label\('[A-Z][a-zA-Z ]+'\)" Modules/Workflow` mengembalikan 0 baris.
- `grep -rE "Section::make\('[A-Z][a-zA-Z ]+'\)" Modules/Workflow` mengembalikan 0 baris.
- Test snapshot baru `WorkflowFormUsesTranslatedLabelsTest` — login locale `id`, render form → assert teks "Penyetuju", "Tipe subjek", dst. ada di DOM.
- `composer run lint:translations` clean untuk `Modules/Workflow/`.

**Dependensi.** Fase 1.2 (kamus), Fase 2.1 (struktur sudah dipecah).

**Risiko.** Beberapa field workflow merujuk JSONLogic key (`subject_type` adalah FQCN, bukan kata) → pastikan tidak salah translate. Untuk label developer-facing (`Config (JSON)`) tetap pakai phrase yang dimasukkan ke `PHRASES`.

---

## Epic 3 — Standardisasi Section & Layout Title `[ ]`

**Tujuan.** ~700+ `Section::make('English')` lintas semua modul harus dibungkus `FilamentUi::text()`. Eksekusi per-batch modul agar PR review-able.

### Fase 3.1 — School + Core + Global `[ ]`

**Deliverable.**
- Refaktor semua `Section::make(...)`, `Tabs\Tab::make(...)`, `Fieldset::make(...)`, `Wizard\Step::make(...)` di:
  - `Modules/School/app/Filament/Resources/**` (118 instance)
  - `Modules/Core/app/Filament/Resources/**` (116 instance)
  - `Modules/Global/app/Filament/Resources/**` (18 instance)
- Update `PHRASES` untuk frasa unik yang belum ada (mis. `Academic information`, `Parent contact`, `Address details`).

**Acceptance Criteria.**
- `composer run lint:translations Modules/School Modules/Core Modules/Global` clean.
- Test render satu form per resource (pakai `Livewire::test`) — pastikan title section muncul Indonesian.

**Dependensi.** Epic 1.

**Risiko.** Banyak section yang sama menampilkan label berbeda antar resource → konsolidasikan ke phrase yang konsisten (mis. `General information` saja, bukan `Basic info` & `General Info` campur).

---

### Fase 3.2 — Campus + Library + Procurement `[ ]`

**Deliverable.**
- Refaktor `Modules/Campus/**` (75), `Modules/Library/**` (68), `Modules/Procurement/**` (107).

**Acceptance Criteria.**
- Linter clean untuk 3 modul.
- Test smoke per resource (1 list page, 1 create page).

**Dependensi.** Fase 3.1 (pola sudah mapan).

---

### Fase 3.3 — Employee + Finance + Enrollment + Monitoring `[ ]`

**Deliverable.**
- Refaktor `Modules/Employee/**` (83), `Modules/Finance/**` (57), `Modules/Enrollment/**` (43), `Modules/Monitoring/**` (16), sisa Workflow (45 — yang belum ditangani di Epic 2 bila ada).

**Acceptance Criteria.**
- Total `Section::make('English')` se-repo = 0.
- Linter CI hijau.

**Dependensi.** Fase 3.2.

---

## Epic 4 — Tabel & Infolist Label Standardization `[ ]`

**Tujuan.** ~70% kolom tabel & entry infolist mengandalkan auto-label Filament. Auto-label memakai `Str::headline()` dari nama kolom, tidak melewati `FilamentUi`. Wajibkan `->label(FilamentUi::field(...))` di setiap kolom & entry.

### Fase 4.1 — Cek Apakah Auto-Label Filament Bisa Dihook `[ ]`

**Konteks.** Sebelum cleanup massal di 100+ file Table/Infolist, eksplor apakah ada cara global meng-hook label resolver Filament untuk lewat `FilamentUi`. Jika ya → cukup register satu service provider, tidak perlu sentuh 1000+ kolom.

**Deliverable.**
- Spike teknis: pelajari `Filament\Tables\Columns\Column::getLabel()` dan `Filament\Infolists\Components\Entry::getLabel()` — lihat apakah ada `defaultLabel()` macro atau closure global yang bisa di-override.
- Bila ada → buat `Modules\Core\Providers\FilamentTranslationServiceProvider` yang call `Column::configureUsing(fn ($c) => $c->label(fn () => FilamentUi::field($c->getName())))`.
- Bila tidak ada → konfirmasi rute manual, lanjut ke 4.2.
- Dokumentasikan hasil spike di `Modules/Core/app/Filament/Support/TRANSLATION_NOTES.md`.

**Acceptance Criteria.**
- Salah satu dari: (a) Provider hook berhasil & 1 modul demo (Library) berhenti perlu `->label()` eksplisit; ATAU (b) Catatan tertulis kenapa hook tidak feasible dan keputusan manual.
- Test integrasi: render `LibraryPoliciesTable` setelah provider → header kolom muncul Indonesian.

**Dependensi.** Epic 1.

**Risiko.** Hook global mungkin menabrak Filament internal (mis. action column, bulk action). Whitelisting per Column class kemungkinan diperlukan.

---

### Fase 4.2 — Cleanup Kolom Tabel Per-Modul `[ ]`

**Konteks.** Kalau 4.1 jalan, Fase ini hanya untuk kasus dengan custom label (relasi `tenant.name` → ingin "Tenant" bukan "Tenant.Name"). Kalau 4.1 tidak feasible, eksekusi manual.

**Deliverable.**
- Sweep semua `Modules/*/app/Filament/Resources/*/Tables/*.php`.
- Tambahkan `->label(FilamentUi::field($columnName))` di setiap `TextColumn::make()`, `IconColumn::make()`, `BadgeColumn::make()`, `ImageColumn::make()`.
- Khusus kolom relasi (`->make('relation.field')`): pakai `FilamentUi::field('relation')` (helper sudah handle stripping `.field`).

**Acceptance Criteria.**
- Linter scan tambahan: `Column tanpa ->label()` = 0 (atau seluruhnya di-cover provider 4.1).
- Test per modul: 1 list page render → header kolom Indonesia.

**Dependensi.** Hasil 4.1.

---

### Fase 4.3 — Cleanup Entry Infolist Per-Modul `[ ]`

**Deliverable.** Sama dengan 4.2 untuk file `Modules/*/app/Filament/Resources/*/Schemas/*Infolist.php`.

**Acceptance Criteria.** Sama dengan 4.2.

**Dependensi.** 4.2 (pola sama).

---

## Epic 5 — Placeholder & Helper Text Cleanup `[ ]`

**Tujuan.** ~978 hardcoded placeholder + ~30 helperText. Mayoritas placeholder adalah `'-'` (decorative, tidak perlu translate). Pisahkan yang naratif dari yang dekoratif, lalu translate yang naratif.

### Fase 5.1 — Inventaris & Klasifikasi `[ ]`

**Deliverable.**
- Script `scripts/audit-placeholders.php` — kategorikan tiap placeholder:
  - `decorative` — `-`, `0`, simbol → biarkan.
  - `informative` — frasa Inggris/Indonesia → kandidat refaktor.
  - `dynamic` — `placeholder(fn () => ...)` → review case-by-case.
- Output JSON `storage/lint/placeholder-audit.json` dengan path + line + kategori.

**Acceptance Criteria.**
- Script jalan tanpa error, hasil JSON < 1MB.
- Manual review confirm < 5% misclassification.

**Dependensi.** Tidak ada.

---

### Fase 5.2 — Translate Placeholder Informative `[ ]`

**Deliverable.**
- Untuk tiap entry kategori `informative`: bungkus `placeholder(FilamentUi::text('...'))` atau hapus jika redundant dengan label.
- Tambah frasa baru ke `PHRASES` saat perlu.

**Acceptance Criteria.**
- Audit ulang → kategori `informative` = 0 (semua sudah dilewatkan helper).
- Test: render satu form per modul → tidak ada placeholder English.

**Dependensi.** 5.1.

---

### Fase 5.3 — Translate HelperText `[ ]`

**Deliverable.**
- Sweep semua `->helperText('...')` di semua modul.
- Pindahkan teks ke `PHRASES` (untuk frasa pendek) atau ke `lang/id/helper.php` baru (untuk paragraf panjang) — jika ada paragraf, buat helper `FilamentUi::helper('key')` yang membaca file `lang/`.
- Library 27 instance — banyak yang sudah berbahasa Indonesia → konsolidasikan ke helper.

**Acceptance Criteria.**
- `grep -rE "helperText\('" Modules/` tidak menemukan literal English/Indonesian — semua via helper.
- Test snapshot helper text di Workflow form.

**Dependensi.** 5.1.

**Risiko.** Helper text bisa panjang & format markdown — pastikan `FilamentUi::text()` tidak mengubah/menormalisasi konten.

---

## Epic 6 — Per-Module Field Label Cleanup (di luar Workflow) `[ ]`

**Tujuan.** Audit menyebut Procurement 25, Finance 18, Employee 18, Campus 2 hardcoded field label. Tuntaskan paralel dengan epic lain.

### Fase 6.1 — Procurement `[ ]`

**Deliverable.** Convert 25 instance di 4 file (PR, RFQ, PO, GR forms/tables) ke `FilamentUi::field()` / `FilamentUi::text()`.

**Acceptance Criteria.** Linter clean untuk `Modules/Procurement/`.

**Dependensi.** Epic 1.

---

### Fase 6.2 — Finance + Employee + Campus `[ ]`

**Deliverable.** Convert 18 (Finance) + 18 (Employee) + 2 (Campus) instance.

**Acceptance Criteria.** Linter clean untuk 3 modul.

**Dependensi.** Epic 1.

---

## Epic 7 — Language Switcher & Persistence UX `[ ]`

**Tujuan.** Beri user UI untuk switch bahasa tanpa perlu admin masuk DB.

### Fase 7.1 — User Menu Item `[ ]`

**Deliverable.**
- Tambah `UserMenuItem` di `AdminPanelProvider::userMenuItems()` → "Bahasa: Indonesia ▾" dengan submenu `Indonesia` / `English`.
- Endpoint Livewire `App\Livewire\SwitchLocale` untuk update `users.preferred_locale` + flash notification.
- Toggle ikon 🇮🇩 / 🇬🇧 di topbar (opsional).

**Acceptance Criteria.**
- Manual: klik switch → halaman reload → sidebar & form berubah bahasa.
- Test browser-level (Dusk atau Pest browser, jika tersedia): klik switch → assert label berubah.

**Dependensi.** Fase 1.1.

---

### Fase 7.2 — Tenant Default Locale `[ ]`

**Deliverable.**
- Setting `core.default_locale` di `TenantSettingResource` — pilih default bahasa untuk semua user baru tenant tsb.
- User baru otomatis mengikuti tenant default kecuali override.

**Acceptance Criteria.**
- Test: buat user via `make:super-admin --tenant=1` saat setting `id` → user.preferred_locale = `id`.

**Dependensi.** 7.1.

---

## Epic 8 — Regression Guard & Documentation `[ ]`

**Tujuan.** Pastikan refaktor besar ini tidak regress 6 bulan ke depan.

### Fase 8.1 — CI Linter Wajib Hijau `[ ]`

**Deliverable.**
- GitHub Actions step `lint:translations` di-set required check di branch protection.
- README badge `Translations: clean ✅`.

**Acceptance Criteria.** PR baru dengan `->label('Hardcoded English')` ditolak otomatis.

**Dependensi.** Fase 1.3.

---

### Fase 8.2 — Snapshot Test Bilingual `[ ]`

**Deliverable.**
- Test baru `tests/Feature/Localization/BilingualResourceSnapshotTest.php` — untuk tiap resource utama (Workflow, Procurement, Finance, School, Campus, Library, Employee), render list + create + view dalam locale `id` & `en`, snapshot output HTML.
- Jika snapshot berubah signifikan, butuh approval reviewer (`./tests/snapshots/` dicommit).

**Acceptance Criteria.**
- Test suite hijau di kedua locale.
- Snapshot file untuk 7 resource utama × 3 page × 2 locale = 42 file.

**Dependensi.** Semua epic sebelumnya.

**Risiko.** Snapshot rapuh terhadap perubahan Filament minor → set granularity per komponen, bukan full HTML.

---

### Fase 8.3 — Update CLAUDE.md & Onboarding `[ ]`

**Deliverable.**
- Section "Translasi & Label" di `CLAUDE.md` (sudah disiapkan di Fase 1.3) di-update dengan contoh sebelum/sesudah hasil refaktor.
- Tambah subbagian "Cara menambah frasa baru ke FilamentUi" — alur PR.
- Tambah pengingat di "Adding a New Module Resource" untuk selalu pakai helper.

**Acceptance Criteria.**
- `CLAUDE.md` review oleh tim lead, di-merge.

**Dependensi.** Semua epic selesai.

---

## Urutan Eksekusi yang Disarankan

```
Sprint 1 (1 minggu)   : Epic 1 (1.1, 1.2, 1.3) — fondasi mutlak
Sprint 2 (1 minggu)   : Epic 2 (Workflow) + Epic 4.1 (spike provider)
Sprint 3 (1 minggu)   : Epic 3.1 + 3.2 + 4.2/4.3 berdasarkan hasil spike
Sprint 4 (1 minggu)   : Epic 3.3 + Epic 5 + Epic 6
Sprint 5 (½ minggu)   : Epic 7 + Epic 8
```

**Total estimasi.** 4–5 minggu engineering untuk 1 senior. Bisa dipotong ~40% bila spike Epic 4.1 berhasil (hook global menghilangkan kebutuhan touch 1000+ kolom manual).

## KPI Akhir

- [ ] `composer run lint:translations` exit 0 untuk seluruh repo.
- [ ] Tidak ada string English yang tampil ke user saat locale `id` (selain proper noun).
- [ ] Tidak ada string Indonesian yang tampil saat locale `en`.
- [ ] User dapat switch bahasa dari menu profil tanpa restart.
- [ ] CI memblokir PR yang memperkenalkan hardcoded label.
- [ ] Dokumentasi konvensi tersedia di `CLAUDE.md`.
