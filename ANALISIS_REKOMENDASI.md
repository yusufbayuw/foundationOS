# Analisis Mendalam & Rekomendasi — FoundationOS

> Dokumen ini disusun dari sudut pandang **Senior ERP / Integration Architect** dengan fokus Laravel 13, Filament v5, dan Livewire v4. Tujuannya: memetakan kondisi teknis aplikasi saat ini secara jujur, menyoroti risiko, dan memberikan rekomendasi yang dapat dieksekusi.
>
> **Tanggal analisis:** 1 Juni 2026
> **Cakupan:** 45 modul, ~4.877 file PHP, 209 migrasi, ~100 file test (≈400+ metode test).
> **Metode:** Penelusuran statik kode (arsitektur tenancy, Workflow V2, integrasi Moodle, layer Filament, testing, CI, performa, dependensi).

---

## 1. Ringkasan Eksekutif

FoundationOS adalah ERP modular untuk lembaga pendidikan dengan **fondasi arsitektur yang matang dan konsisten** pada domain inti (Tenancy, Workflow, Exam, Procurement, Finance, Moodle). Pola multi-tenancy, base class `ModuleResource`, dan engine workflow berbasis metadata menunjukkan disiplin engineering yang baik.

Semua modul yang diaktifkan di `modules_statuses.json` telah dipromosikan ke tier **GA** (`config/fos_module_maturity.php`: 45 modul GA, `experimental` kosong). Risiko paling material kini bergeser ke **peningkatan PHPStan bertahap, opsi tenancy fail-closed di produksi, dan epik ROADMAP** (Workflow V3 lanjutan, Moodle reconcile, public API).

### Penilaian per dimensi

| Dimensi | Skor | Catatan |
|---------|:----:|---------|
| Arsitektur inti | **A−** | Tenancy + Workflow + Exam + Procurement dirancang dengan baik & teruji |
| Keamanan & isolasi tenant | **B** | Pola kuat, tetapi *fail-open* by design + beberapa celah model/form |
| Keandalan integrasi (Moodle/Workflow) | **B** | Outbox atomik + sweeper; workflow lock & queued automation (Gelombang 2) |
| Layer Filament | **A−** | Konsistensi struktural sangat tinggi (378 resource, 1 base class) |
| Kematangan testing | **B−** | Volume tinggi, tetapi sempit; UI/Livewire & factory minim |
| Kematangan CI/CD | **B** | `tests.yml` + `static.yml` (Pint + Larastan **level 1** + baseline); branch protection disarankan di GitHub |
| Kesiapan performa | **C+** | Index FK ada, tetapi risiko N+1 & index komposit kurang |
| Konsistensi antar modul | **B** | Pola seragam, tetapi 18+ modul masih scaffold tanpa test |

### Tiga prioritas teratas (jika hanya bisa kerjakan tiga)
1. **Aktifkan CI test pipeline + static analysis** — saat ini perubahan apa pun bisa merusak 400+ test tanpa terdeteksi.
2. **Perbaiki bug & race condition integrasi** — `AcademicPeriodObserver` akan *crash*, dan klaim outbox/workflow rawan pemrosesan ganda.
3. **Hardening isolasi tenant** — buat `TenantScope` *fail-closed* untuk jalur HTTP/API/job, audit `Select::make('tenant_id')`, dan amankan `is_super_admin`.

---

## 2. Arsitektur & Struktur Modul

### Kekuatan
- **Modularisasi tegas** via `coolsam/modules` v5: setiap domain (`Modules/<Name>/`) punya struktur internal seragam (`Filament/Resources`, `Models`, `Services`, `Policies`, `Providers`, `database`).
- **Base class disiplin**: seluruh resource memperluas `Modules\Core\Filament\Support\ModuleResource` — **nol** resource memperluas `Filament\Resources\Resource` mentah.
- **Pemisahan tanggung jawab** Filament: `Schemas/*Form.php`, `Schemas/*Infolist.php`, `Tables/*Table.php`, `Pages/` — konsisten di seluruh modul matang.
- **Bilingual terpusat** melalui `Modules\Core\Support\FilamentUi` + linter anti-hardcode.

### Kelemahan
- **Kedalaman domain masih tidak seragam.** Semua modul aktif sudah GA (service registrasi + factory + importer + test), tetapi banyak entitas sekunder di modul besar masih CRUD tanpa service khusus. Contoh pola lama yang perlu diaudit berkala:

```16:24:Modules/Property/app/Filament/Resources/Properties/Schemas/PropertyForm.php
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->numeric(),
```
  `organization_id` sebagai `TextInput()->numeric()` alih-alih `Select::relationship()` — anti-pola yang akan menimbulkan masalah integritas referensial dan UX.

- **Modul `Ai`**: `AiAdvisorService` + registry `AiPromptTemplate` dengan Filament resource `AiPromptTemplateResource` (CRUD + impor CSV).
- **`meta` sebagai `Textarea` JSON** di modul scaffold — rapuh, tanpa validasi skema.

### Rekomendasi
1. Tetapkan **definisi "Definition of Done" per modul** (model + factory + service domain + policy + test + importer) dan tandai modul scaffold sebagai *experimental* di `modules_statuses.json`/dokumentasi agar tidak dianggap GA.
2. Ganti `TextInput('organization_id')` di seluruh scaffold dengan `Select::make('organization_id')->relationship('organization','name')`.
3. Untuk kolom `meta`, gunakan `KeyValue` / skema terstruktur, bukan `Textarea` JSON bebas.

---

## 3. Multi-Tenancy & Keamanan

Arsitektur isolasi bertumpu pada tiga lapis terkoordinasi: **Filament tenancy** (slug `uuid`) + **`CurrentTenant`/`TenantScope`** (global scope Eloquent) + **Spatie Permission teams mode** (`team_foreign_key = tenant_id`) bersama Filament Shield.

### Kekuatan
- Trait `BelongsToTenant` diterapkan konsisten di 200+ model operasional; auto-set `tenant_id` saat `creating`.
- Akses panel berbasis **keanggotaan** (`user_tenant_roles`), bukan bypass super-admin — terbukti via `AdminPanelTenantAccessTest` (super admin global *tidak* bisa membuka tenant tanpa keanggotaan).
- Audit perpindahan tenant via event `TenantSwitched`.
- `GlobalResourceGuard` mencegah mutasi data referensial global oleh non-super-admin.

### Kelemahan / Risiko

**Tinggi**
1. **`TenantScope` bersifat *fail-open* (opt-in).** Jika konteks tenant tidak terikat, scope menjadi *no-op* dan mengembalikan **semua baris**:
```15:25:app/Scopes/TenantScope.php
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = $this->resolveTenantId();

        if ($tenantId === null) {
            return;
        }
```
   Ini disengaja agar seeder/Artisan lintas-tenant bekerja, tetapi setiap jalur HTTP/job/API yang lupa mengikat konteks berisiko **kebocoran data lintas-tenant**.

2. **Token API tanpa `tenant_id` melewati isolasi.** `ResolveApiTenant` hanya men-set konteks bila `token->tenant_id !== null`; tidak ada penolakan *default-deny*.

3. **`is_super_admin` bersifat `$fillable`** pada `Modules\Core\Models\User` → risiko *privilege escalation* bila ada `User::create($request->all())` di jalur mana pun. Dikombinasikan dengan `Gate::before` yang memberi **semua** ability ke super admin, satu kesalahan = katastrofik.

**Menengah**
4. **`Select::make('tenant_id')` tersebar** di banyak form panel-tenant (Core & modul roadmap), padahal sudah ada `TenantField::make()` yang aman. Berpotensi menetapkan record ke tenant lain.
5. **`LibraryPolicy` & `BookReservation`** meng-*import* `BelongsToTenant` tetapi **tidak memakainya** → query Eloquent langsung tidak ter-scope.
6. **`UserTenantRole.expires_at` tidak ditegakkan** — keanggotaan kedaluwarsa tetap memberi akses.
7. **Policy tanpa cek tenant level-record** (mis. `OrganizationPolicy`) — mengandalkan scoping query; rawan IDOR bila scoping di-bypass.

### Rekomendasi (berurutan)
1. Sediakan mode **fail-closed opsional** untuk request web/API: middleware yang *mewajibkan* konteks tenant pada panel & API, sehingga tanpa tenant query melempar exception (bukan mengembalikan semua data).
2. **Wajibkan `tenant_id` pada seluruh token Sanctum** untuk API tenant; tolak/`abort(403)` bila kosong. Tambahkan test isolasi.
3. Pindahkan `is_super_admin` ke `$guarded` atau buang dari `$fillable`; set hanya via service khusus + audit.
4. Audit lint untuk **melarang `Select::make('tenant_id')`** di resource panel-tenant; arahkan ke `TenantField::make()`.
5. Tambah trait `BelongsToTenant` pada `LibraryPolicy` & `BookReservation`.
6. Tegakkan `expires_at` di `canAccessTenant()` dan `getTenants()`.
7. Buat **test arsitektur** (mis. via reflection) yang memastikan: setiap model dengan kolom `tenant_id` memakai `BelongsToTenant`.

---

## 4. Workflow Engine V2

Engine metadata-driven dengan kontrak yang dapat ditukar (`WorkflowResolver`, `WorkflowInstanceStarter`, `WorkflowEngine`, `RuleEngine` JSONLogic, `WorkflowAutomatedActionRunner`). Siklus: *resolve → snapshot + start → buat assignment → advance (dengan quorum paralel) → fire events → sync subject state + automated actions*.

### Kekuatan
- `advance()` memakai `lockForUpdate()` di dalam transaksi — serialisasi benar untuk approver paralel.
- Snapshot definisi (`workflow_snapshot`) saat start menjaga metadata & konfigurasi automasi tetap stabil.
- Quorum paralel (`All`/`Majority`/`Count`/`Percentage`) terimplementasi & teruji.
- Audit ganda: `workflow_instance_logs` + `AuditLog`.
- Perkakas operasional: `fos:workflow:health-check`, `retry-sla`, `retry-automation`.

### Kelemahan / Risiko
1. **Resolusi transisi memakai DB *live*, bukan snapshot.** `JsonLogicWorkflowTransitionResolver` query `workflow_transitions` terkini, dan `currentStep` adalah `WorkflowStep` live — mengedit/publish definisi dapat mengubah perilaku instance yang sedang berjalan, meski snapshot metadata stabil.
2. **Tidak ada lock pada `returnToStep()`/`cancel()`/`reassign()`** — `advance` + `cancel` konkuren dapat *interleave* (`DatabaseWorkflowEngine.php`).
3. **TOCTOU pada cek evidence** — pengecekan evidence dilakukan *sebelum* lock diperoleh.
4. **Listener pasca-advance sinkron & dapat melempar** — `WorkflowAutomatedActionRunner` *rethrow* setelah transaksi commit; user bisa melihat error padahal workflow sudah berpindah. Tidak ada `ShouldQueue`.
5. **SLA breach tidak idempoten** — `CheckWorkflowSlaJob` tanpa *guard* "sudah breach" → log `sla_breached` ganda.
6. **`workflow:escalate-overdue` hanya menulis metadata** (`meta.sla_state=overdue`) — tidak reassign/notify; `WorkflowEscalationService::createAssignmentWithDelegation()` ada tapi tidak dipakai produksi.
7. **`NotifyWorkflowAssignees` masih stub** (hanya set `meta.notified_at`).
8. **JSONLogic terbatas** — operator tak dikenal mengembalikan `false` secara diam-diam.

### Rekomendasi
1. Resolusi transisi & step **dari snapshot instance** (atau bekukan ID transisi saat start) agar instance berjalan deterministik terhadap perubahan definisi.
2. Tambahkan `lockForUpdate()` pada `returnToStep`/`cancel`/`reassign`; pindahkan cek evidence ke dalam lock.
3. Bungkus automated action & sync subject dalam **queued listener** (`ShouldQueue`) dengan penanganan error terisolasi, sehingga UX advance tidak gagal karena automasi.
4. Tambah flag idempotensi breach pada instance.
5. Integrasikan `createAssignmentWithDelegation` ke `CreateAssignmentsForCurrentStep` dan implementasikan kanal notifikasi nyata.

---

## 5. Integrasi (Moodle Outbox & Observers)

Pola *transactional outbox*: observer → `moodle_sync_outbox` → `ProcessMoodleSyncOutboxJob` (dispatch `afterCommit`) → `MoodleSyncService` (upsert idempoten via `moodle_entity_mappings` + `idnumber`). Recovery via scheduler `fos:moodle:drain-outbox` + backoff `MoodleSyncRetry`.

### Bug & Risiko Konkret

**Tinggi**
1. **`AcademicPeriodObserver` akan *crash*.** `enqueue()` mensyaratkan 6 argumen (`entityType, entityId, tenantId, action, payload, dedupeKey`), tetapi observer memanggil hanya 4:
```29:34:app/Observers/AcademicPeriodObserver.php
                $this->outbox->enqueue(
                    MoodleSyncOutbox::ENTITY_COURSE_OFFERING,
                    (int) $offering->id,
                    (int) $offering->tenant_id,
                    MoodleSyncOutbox::ACTION_UPSERT,
                );
```
   Akan melempar `ArgumentCountError` saat ada `CourseOffering` terkait period. Test hanya menutup jalur tanpa offering.

2. **Race condition klaim outbox.** Tidak ada lock/atomic claim di `ProcessMoodleSyncOutboxJob` — dua job (enqueue + drain) bisa membaca `pending` lalu sama-sama set `processing` → pemrosesan ganda.

3. **State `processing` tersangkut.** Bila worker mati di tengah job, tidak ada *sweeper* yang me-reset `processing` → baris tak terlihat oleh `scopeReady()`/drain. Hanya terhitung di `fos:moodle:ops-report`, tanpa recovery otomatis.

**Menengah**
4. **Enrol Moodle tidak idempoten** — `enrol_manual_enrol_users` selalu dipanggil; *re-enroll* gagal & menghabiskan jatah retry.
5. **`moodle.readonly=true` menandai sukses tanpa sinkron** — `callMoodle()` mengembalikan `[]`, job set `synced`. Berbahaya bila tak sengaja aktif di produksi.
6. **Fan-out & overlap observer**: `StudentObserver` membuat 1 baris outbox per kelas (O(kelas) per update), dan tumpang tindih dengan `ClassStudentObserver` → job ganda.

### Rekomendasi
1. **Perbaiki `AcademicPeriodObserver`** — sertakan `payload` + `dedupeKey` (tiru `CourseOfferingObserver`). Tambah test untuk kasus *period punya offering*.
2. Klaim outbox **atomik**: `UPDATE ... SET status='processing' WHERE id=? AND status IN('pending','failed')` lalu cek *affected rows*; atau `lockForUpdate`.
3. Tambah command/scheduler **sweeper** untuk reset baris `processing` yang lebih tua dari N menit.
4. Buat enrol *tolerant* terhadap error "already enrolled" (anggap sukses).
5. Tambah **UI Filament** untuk inspeksi/retry baris outbox `failed` (setara `WebhookDeliveries` di Monitoring).

---

## 6. Kualitas Kode & Testing

### Konfigurasi test
- **SQLite in-memory** (`phpunit.xml`), `BCRYPT_ROUNDS=4`, `QUEUE=sync`, `CACHE=array` — tuning cepat. ✔
- **Coverage hanya `app/`**, mengabaikan `Modules/` → laporan coverage akan *menyesatkan* (≈95% kode tak terhitung).
- **Tanpa `LazilyRefreshDatabase`** — ~87 kelas `RefreshDatabase` menjalankan ulang seluruh stack migrasi 45 modul.

### Cakupan
- **Kuat & dalam**: Workflow (15 file), Exam (20 file), Procurement (pipeline), Moodle, Tenancy/API, Finance (parsial), Library, i18n, dan test struktural `ModuleFilamentResourceCoverageTest`.
- **Kosong/sangat tipis**: `Property`, `Transport`, `Cafeteria`, `Boarding`, `Alumni`, `Printing`, `PhysicalSecurity`, `Consulting`, `MerchOrder`, `Event`, `Clinic`, `Capacity`, `Dms` — tanpa referensi test sama sekali.
- **UI/Livewire hampir nihil**: hanya `WorkflowDesignerTest` memakai `Livewire::test()`. 378 resource tidak punya test create/edit/list di layer UI.
- **Factory**: hanya `UserFactory`. Tidak ada factory model modul → boilerplate `Model::create([...])` berlimpah & rapuh.
- **`tests/TestCase.php` kosong** → helper `makeTenant()` disalin di 20+ file.

### Rekomendasi
1. Tambah **factory per model domain** dan helper bersama (`makeTenant()`, `actingAsTenantAdmin()`) di base `TestCase`/trait.
2. Sertakan `Modules/` dalam sumber coverage; targetkan ambang minimum bertahap.
3. Pakai `LazilyRefreshDatabase` untuk mempercepat suite.
4. Tambah **test Livewire** untuk minimal CRUD pada resource bernilai tinggi (Finance, School, Enrollment).
5. Hindari hardcode opsi filter (mis. `StudentInvoicesTable` `SelectFilter::options(['draft'=>'Draft',...])`) — alirkan lewat enum + `FilamentUi`.

---

## 7. CI/CD & Gerbang Kualitas

### Kondisi saat ini

| Workflow | Fungsi |
|----------|--------|
| `tests.yml` | PHPUnit penuh pada PHP 8.4 |
| `static.yml` | Laravel Pint + Larastan level 0 (semua modul GA) |
| `lint-translations.yml` | `scripts/lint-translations.php` |
| `lint-tenant-fields.yml` | `scripts/lint-tenant-fields.php` |
| `mobile-shell.yml` | Validasi Capacitor/PWA manifest |

### Rekomendasi (prioritas tinggi)
1. Aktifkan **branch protection** pada `main` (require PR + status checks hijau).
2. Naikkan PHPStan ke **level 1** dengan baseline (`composer analyse` → generate baseline bila perlu).
3. Produksi: set `TENANCY_SCOPE_FAIL_CLOSED=true` setelah smoke test panel/API.

---

## 8. Performa & Skalabilitas

### Temuan
1. **Risiko N+1 di tabel Filament**: 150+ file Table memakai notasi relasi (`tenant.name`, dll.); **hanya `ExamParticipantsTable`** yang memakai eager-load (`modifyQueryUsing`/`with()`). Di skala besar, list page rawan N+1.
2. **Index komposit jarang**: FK `tenant_id` ter-index, tetapi index seperti `(tenant_id, status)` / `(tenant_id, created_at)` minim. Modul School: 16 tabel `tenant_id`, **nol** `->index()` eksplisit. Hanya `2026_05_23_210000_phase3_...` menambah index untuk 6 tabel terpilih.
3. **Boot panel berat**: 378 resource dalam satu panel `admin` (terbantu cache navigasi, tetapi tetap besar).

### Rekomendasi
1. Tambahkan `->modifyQueryUsing(fn ($q) => $q->with([...]))` pada resource list bertrafik tinggi; jadikan konvensi di `ModuleResource`/dokumentasi.
2. Tambah **index komposit** `(tenant_id, <kolom_filter_umum>)` pada tabel volume tinggi (invoice, attendance, journal lines, audit).
3. Pertimbangkan **enable Laravel Pulse** (sudah ada di dependency) di staging untuk memburu query lambat & N+1 nyata.
4. Audit query observer fan-out (Student/ClassStudent) agar tidak menimbulkan *sync storm*.

---

## 9. Dependensi & Konfigurasi

| Paket | Constraint | Catatan |
|-------|------------|---------|
| `php` | `^8.3` | Dokumen/CI menyebut 8.4 — selaraskan |
| `laravel/framework` | `^13.0` | OK |
| `filament/filament` | `^5.4` | OK |
| `barryvdh/laravel-dompdf` | `"*"` | **Wildcard** — `composer update` bisa lompat versi mayor; **pin** ke `^3.1` |
| `bezhansalleh/filament-shield` | `^4.2` | Verifikasi kompatibilitas dengan Filament v5 |
| (dev) static analysis | — | **Tidak ada** Larastan/PHPStan/Rector |

### Rekomendasi
1. **Pin `barryvdh/laravel-dompdf`** ke rentang mayor yang aman.
2. Selaraskan minimum PHP `^8.4` agar konsisten dengan README/CI.
3. Tambahkan `larastan/larastan` ke `require-dev`.

---

## 10. Roadmap Perbaikan (Prioritas)

### Gelombang 1 — Stabilisasi (risiko tertinggi, usaha rendah)
- [x] Buat `tests.yml` CI + jadikan required check pada `main`. *(branch protection: set di GitHub UI)*
- [x] Perbaiki bug `AcademicPeriodObserver` (argumen `enqueue`) + test regresi.
- [x] Pin `barryvdh/laravel-dompdf`; selaraskan PHP `^8.4`.
- [x] Pindahkan `is_super_admin` keluar dari `$fillable` (`$guarded` + `promoteToGlobalSuperAdmin()`).

### Gelombang 2 — Hardening keamanan & keandalan
- [x] Klaim outbox atomik + sweeper `processing` tersangkut (`tryClaim`, `fos:moodle:sweep-stale-processing`).
- [x] Wajibkan `tenant_id` pada token API (`config/tenancy.php`, `ResolveApiTenant`).
- [x] Tambah `lockForUpdate` pada `cancel/return/reassign` workflow; evidence gate di dalam lock; queue-kan `RunWorkflowAutomatedActions`.
- [x] SLA breach idempoten (`markBreached` skip jika log sudah ada).
- [x] Tambah `BelongsToTenant` pada `LibraryPolicy`/`BookReservation`; tegakkan `expires_at`.
- [x] Lint `Select::make('tenant_id')` → `scripts/lint-tenant-fields.php` + `composer lint:tenant-fields` (modul matang).

### Gelombang 3 — Kualitas & performa
- [x] Larastan + Pint sebagai gate CI (level 0 di `phpstan.neon` + job `phpstan` di `static.yml`).
- [x] Trait `Tests\Concerns\CreatesTenantForTests` untuk konteks tenant bersama.
- [x] `LazilyRefreshDatabase` — pilot + perluasan workflow/procurement/finance/core (19+ file test).
- [x] Eager-loading contoh: `StudentInvoicesTable`, `ApplicantsTable` + index `(tenant_id, status)`.
- [x] Test Livewire untuk CRUD inti: `CoreModuleFilamentCrudTest` (list Enrollment/Finance/School + create invoice).

### Gelombang 4 — Pematangan modul roadmap
- [x] Tandai modul scaffold sebagai *experimental* di `config/fos_module_maturity.php` + Definition of Done.
- [x] Ganti `TextInput('organization_id')` → `TenantField::organizationSelect()` di modul experimental (31 form); contoh `meta` → `KeyValue` di Risk.
- [x] Tambah service domain + test untuk modul pilot GA (`RiskCategoryService`, `DonorRegistrationService`, `CustomerRegistrationService` + tests).
- [x] Tier `maturing` di `config/fos_module_maturity.php`; Risk/Donation/Sales dipromosikan ke **GA** (factory, importer, form alignment).
- [x] `LazilyRefreshDatabase` pada seluruh `tests/Feature/*` (kecuali `ExampleTest`).
- [x] Resolusi transisi workflow dari snapshot (`JsonLogicWorkflowTransitionResolver` + test).
- [x] Promosi batch GA modul experimental → **45 modul GA**, tier `experimental` kosong.

### Gelombang 5 — Pasca-GA (kualitas & hardening)
- [x] Larastan mencakup modul inti yang sebelumnya terlewat (`Global`, `Campus`, `Employee`, `Monitoring`, `Inventory`).
- [x] Opsi `tenancy.scope_fail_closed` + `MissingTenantContextException` + test.
- [x] Produksi: `scope_fail_closed` default **true** bila `APP_ENV=production`; panduan [.github/BRANCH_PROTECTION.md](.github/BRANCH_PROTECTION.md).
- [x] Test: semua modul di `modules_statuses.json` harus ada di tier `ga`.
- [x] PHPStan **level 1** + `phpstan-baseline.neon` (302 temuan; turun dari 316 setelah perbaikan duplikat & ExportCenter).
- [x] Perbaikan bug: duplikat key `academic_period_id` di `AcademicPeriodObserver` payload outbox.
- [x] Filament resource `AiPromptTemplate` (modul Ai).
- [x] Fondasi **API v2** (`GET /api/v2`, `/api/v2/me`, `/api/v2/tenants/current` + meta `api_version`).
- [x] Perbaikan `FilamentUi` duplicate keys, `WorkflowDesignerPage::text()`, hapus retry export rusak.
- [ ] Epik ROADMAP lanjutan: endpoint v2 read/write penuh, Workflow V3 designer polish.

---

## 11. Kesimpulan

FoundationOS memiliki **pondasi arsitektur kelas produksi** pada domain intinya — multi-tenancy, workflow engine, dan integrasi Moodle dirancang dengan pola yang benar dan teruji. Aplikasi ini **layak produksi untuk alur akademik/keuangan/procurement** yang sudah tertutup test.

Kelemahan utama bersifat **operasional dan kedalaman fitur**, bukan desain fundamental:
- **PHPStan level 1 + baseline** — CI hijau; kurangi baseline secara bertahap (target level 2+).
- **Tenant scope** — produksi default fail-closed; lokal tetap `TENANCY_SCOPE_FAIL_CLOSED=false` di `.env`.
- **Epik integrasi lanjutan** — sebagian besar Epic 3–6 & 5 di `ROADMAP.md` sudah `[x]`; sisa polish dan API v2.

Platform kini **GA penuh pada modul aktif**, dengan CI test + static analysis. Fokus berikutnya: hardening produksi dan epik roadmap bernilai tinggi.

---

*Disusun oleh AI Senior ERP/Integration Architect. Seluruh temuan merujuk file & baris kode aktual untuk verifikasi langsung.*
