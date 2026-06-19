# QA_BROWSER_CHECKLIST.md — Pengujian Manual Browser (Market-Ready)

> Tujuan dokumen: panduan pengujian manual end-to-end FoundationOS melalui **browser**, dengan target **kualitas siap rilis ke pasar (production / GA)** — bukan sekadar MVP.
>
> Cakupan: 45 modul (semua tier **GA** per `config/fos_module_maturity.php`), 3 panel Filament (`admin`, `platform`, `parent`), multi-tenancy + subscription (Midtrans), Filament Shield RBAC, UI bilingual (id/en), Workflow engine + visual designer, integrasi Moodle & Exam (Cloudflare runtime), API Sanctum, generasi PDF, serta permukaan publik (landing page & OPAC perpustakaan).

---

## 0. Cara Memakai Checklist Ini

### Konvensi status
Tandai setiap item dengan salah satu:

- `[ ]` Belum diuji
- `[x]` **PASS** — sesuai ekspektasi
- `[!]` **FAIL** — ada bug (catat di kolom temuan)
- `[~]` **PARTIAL / N/A** — sebagian / tidak berlaku untuk konfigurasi ini

### Klasifikasi severity bug (untuk keputusan rilis)
| Severity | Definisi | Dampak rilis |
|----------|----------|--------------|
| **S1 Blocker** | Data corruption, kebocoran antar-tenant, auth bypass, panel tidak bisa diakses, kehilangan pembayaran | **Tidak boleh rilis** |
| **S2 Critical** | Fitur inti gagal tanpa workaround (CRUD, workflow approval, invoice, login) | Tidak boleh rilis tanpa fix |
| **S3 Major** | Fitur gagal tapi ada workaround, salah perhitungan non-kritis | Rilis hanya dengan persetujuan + tiket |
| **S4 Minor** | UI/teks/kosmetik, edge case langka | Boleh rilis, masuk backlog |

### Definition of "Market-Ready" (kriteria lulus keseluruhan)
- [ ] 0 bug S1 & S2 terbuka
- [ ] Seluruh **alur uang** (invoice → pembayaran → langganan) terverifikasi end-to-end
- [ ] **Isolasi tenant** terbukti pada minimal 10 resource lintas modul
- [ ] **RBAC** terbukti menolak akses yang seharusnya ditolak (negative testing)
- [ ] Bilingual (id/en) konsisten di seluruh permukaan yang diuji
- [ ] Lulus matriks browser & responsif (lihat §1.4)
- [ ] Tidak ada error JavaScript console / 500 / N+1 mencolok pada alur utama
- [ ] Sign-off QA (lihat §27)

---

## 1. Pra-Pengujian (Environment & Setup)

### 1.1 Penyiapan environment
- [ ] `composer install` & `npm install` sukses tanpa error
- [ ] `.env` ada; `php artisan key:generate` sudah dijalankan
- [ ] `php artisan migrate` sukses (cek `migrate:status` semua `Ran`)
- [ ] `php artisan db:seed` sukses (`MvpDemoSeeder` → tenant **Foundation Demo** + admin + `ExamMvpDemoSeeder`)
- [ ] `php artisan shield:generate --all --panel=admin --option=permissions --no-interaction` sukses
- [ ] `php artisan storage:link` sukses (folder `public/storage` ada)
- [ ] `npm run dev` **atau** `npm run build` berjalan (CSS/JS Filament termuat — tidak ada error Vite manifest)
- [ ] `php artisan queue:listen` aktif (wajib untuk workflow, notifikasi, Moodle sync, billing webhook async)
- [ ] `php artisan optimize:clear` dijalankan setelah seed/permission
- [ ] Aplikasi dapat diakses di `https://foundationos.test` (Herd)

### 1.2 Data uji yang harus tersedia
- [ ] **Akun super admin**: `admin@admin.com` / `password` (dari seeder)
- [ ] Tenant **Foundation Demo** aktif dengan organisasi `MAIN`
- [ ] **Tenant kedua** dibuat manual (untuk uji isolasi) — mis. "Tenant B"
- [ ] **User non-admin** dengan role terbatas (mis. Staff) untuk uji RBAC negatif
- [ ] **User parent** (untuk panel `/parent`)
- [ ] Minimal 1 data per modul yang akan diuji mendalam (lewat seeder/manual)

### 1.3 Persiapan kredensial integrasi (untuk uji integrasi penuh)
- [ ] Midtrans **Sandbox** `MIDTRANS_SERVER_KEY` / `MIDTRANS_CLIENT_KEY` terisi
- [ ] `MIDTRANS_IS_PRODUCTION=false`, `MIDTRANS_NOTIFICATION_URL` mengarah ke `/billing/webhook`
- [ ] Moodle test instance + token (jika menguji sync Moodle)
- [ ] Exam runtime (`EXAM_RUNTIME_BASE_URL`, API key) jika menguji publish exam ke Cloudflare
- [ ] Mailpit/Mailhog atau log mailer aktif untuk verifikasi email (verifikasi akun, reset password, MFA email, notifikasi)

### 1.4 Matriks browser, device & aksesibilitas
Uji alur utama (login, dashboard, 1 CRUD penuh, 1 workflow, 1 pembayaran) di:
- [ ] Chrome (desktop, terbaru)
- [ ] Firefox (desktop, terbaru)
- [ ] Safari (desktop, macOS)
- [ ] Edge (desktop)
- [ ] Chrome Android / Safari iOS (mobile)
- [ ] Viewport: 1920px, 1366px, 768px (tablet), 375px (mobile)
- [ ] Mode gelap & terang (jika tema mendukung)
- [ ] Zoom 200% tetap dapat digunakan
- [ ] Navigasi keyboard (Tab/Shift+Tab/Enter/Esc) pada form & modal
- [ ] Kontras warna & label form terbaca screen reader (cek atribut `aria`/`label`)
- [ ] Tidak ada error di **DevTools Console** selama alur utama
- [ ] Tab **Network**: tidak ada 4xx/5xx tak terduga; tidak ada request gagal diam-diam

---

## 2. Permukaan Publik & Marketing

### 2.1 Landing page (`/`)
- [ ] Halaman `/` tampil tanpa auth, tanpa error
- [ ] Tamu (guest) melihat CTA **Register** (`/admin/register`)
- [ ] User yang sudah login melihat tautan ke **Dashboard** (`/admin`), CTA register hilang
- [ ] Tautan/aset (gambar, font, CSS) termuat (no broken links / 404 aset)
- [ ] Tampilan responsif di mobile
- [ ] Metadata SEO dasar (title/description) ada
- [ ] Locale landing mengikuti default (id) dan terbaca natural

### 2.2 OPAC perpustakaan publik (`/opac/{tenant}`)
- [ ] Katalog publik dapat diakses tanpa login untuk tenant valid
- [ ] Pencarian buku publik berfungsi
- [ ] Tenant tidak valid → 404 yang rapi (bukan stack trace)
- [ ] Tidak membocorkan data tenant lain

### 2.3 Halaman verifikasi publik (jika ada)
- [ ] Verifikasi sertifikat training via token (`/training/certificates/{token}`) menampilkan status valid/invalid dengan benar
- [ ] Token tidak valid → pesan rapi, bukan error 500

---

## 3. Autentikasi & Manajemen Akun

### 3.1 Registrasi user (`/admin/register`)
- [ ] Halaman register tampil & dapat di-submit
- [ ] Validasi: email duplikat ditolak, password lemah ditolak, konfirmasi password dicek
- [ ] Setelah register, alur **email verification** terpicu (email terkirim)
- [ ] Field wajib menampilkan pesan error inline yang jelas (bilingual)

### 3.2 Verifikasi email
- [ ] Link verifikasi di email valid & mengaktifkan akun
- [ ] Link kedaluwarsa / signature salah ditolak rapi
- [ ] User belum terverifikasi dibatasi sesuai kebijakan (route `verified`)
- [ ] Resend verification berfungsi & ada rate limit

### 3.3 Login (`/admin/login`)
- [ ] Login sukses dengan kredensial benar → redirect ke dashboard
- [ ] Kredensial salah → pesan error generik (tidak membocorkan apakah email terdaftar)
- [ ] **Rate limiting / throttle** aktif setelah beberapa percobaan gagal
- [ ] User berstatus `inactive` tidak bisa login
- [ ] "Remember me" mempertahankan sesi
- [ ] Field email case-insensitive (mis. `Admin@Admin.com`)

### 3.4 Reset password (`/admin/password-reset`)
- [ ] Permintaan reset mengirim email (tidak membocorkan keberadaan email)
- [ ] Link reset valid mengubah password
- [ ] Link reset kedaluwarsa/terpakai ditolak
- [ ] Password baru harus memenuhi kebijakan kekuatan

### 3.5 Multi-Factor Authentication (MFA)
- [ ] **App Authentication (TOTP)**: setup via menu profil, QR muncul, verifikasi kode benar
- [ ] Kode TOTP salah ditolak
- [ ] **Recovery codes** tergenerate, dapat dipakai sekali, dan dapat di-regenerate
- [ ] **Email Authentication**: kode dikirim via email & memverifikasi login
- [ ] Setelah MFA aktif, login berikutnya meminta faktor kedua
- [ ] MFA dapat dinonaktifkan (dengan konfirmasi)

### 3.6 Profil & sesi (`EditProfile`)
- [ ] Ubah nama, email (re-verifikasi jika berubah), avatar, timezone, bahasa
- [ ] Ubah password dari profil (butuh password lama)
- [ ] Logout mengakhiri sesi; back button tidak membuka halaman ter-auth (cache)
- [ ] Sesi paralel / multi-device berperilaku konsisten
- [ ] Timeout sesi (`SESSION_LIFETIME`) memunculkan re-login yang rapi
- [ ] **419 Page Expired** (CSRF token kedaluwarsa) ditangani dengan pesan rapi, bukan crash

---

## 4. Multi-Tenancy (Inti — wajib lulus)

### 4.1 Registrasi & pemilihan tenant
- [ ] Setelah login, pemilih tenant muncul jika user punya >1 tenant
- [ ] User dengan 1 tenant langsung masuk ke tenant tsb
- [ ] **Tenant registration** (`RegisterTenant`) membuat tenant + meng-assign pembuat sebagai owner/super admin
- [ ] `code`/`subdomain` tenant unik divalidasi
- [ ] Tenant baru otomatis mendapat modul aktif (TenantModuleProvisioner)
- [ ] Badge tenant tampil di topbar (`filament.tenant-badge`)

### 4.2 Tenant switching
- [ ] Berpindah tenant mengganti seluruh konteks data (navigasi, daftar, dashboard)
- [ ] Setelah switch, **tidak ada data tenant sebelumnya yang bocor**
- [ ] Perpindahan tenant tercatat di audit (`TenancySwitchAudit`)
- [ ] Role/permission ikut tersinkron sesuai tenant aktif (Shield team mode)

### 4.3 Isolasi data antar-tenant (S1 jika gagal)
Untuk minimal 10 resource lintas modul (Users, Students, Invoices, Books, Purchase Orders, Employees, Courses, Assets, Workflows, Audit Logs):
- [ ] Data tenant A **tidak** muncul di tenant B
- [ ] **IDOR**: akses langsung URL record tenant lain (`/admin/.../{id}/edit`) → ditolak (403/404)
- [ ] Global search tidak mengembalikan record tenant lain
- [ ] Export/PDF hanya berisi data tenant aktif
- [ ] Relation manager tidak menampilkan opsi milik tenant lain
- [ ] Filter `tenant_id` ter-enforce di backend, bukan hanya di UI

### 4.4 Penegakan langganan (`EnsureTenantSubscriptionActive`)
- [ ] Tenant `active`/`trial` (belum kedaluwarsa) → akses penuh
- [ ] Tenant `past_due` dalam grace period → akses dengan peringatan
- [ ] Tenant `suspended`/expired → diblokir ke halaman billing (tidak bisa akses data)
- [ ] Pesan blokir jelas + jalur pembayaran tersedia
- [ ] Super admin global tetap bisa mengelola tenant terkunci (sesuai kebijakan)

### 4.5 Batas kuota tenant
- [ ] `max_users`, `max_organizations`, `max_storage_mb` ditegakkan (coba lampaui → ditolak rapi)

---

## 5. Billing & Subscription (Alur Uang — wajib lulus)

### 5.1 Plan & halaman billing (`BillingPage`)
- [ ] Daftar Subscription Plan tampil benar (harga, fitur, siklus)
- [ ] Halaman billing menampilkan status langganan tenant saat ini
- [ ] Pilih plan → memulai pembayaran Midtrans (Snap muncul)

### 5.2 Pembayaran Midtrans (sandbox)
- [ ] Snap/redirect Midtrans terbuka dengan jumlah & order id benar
- [ ] Pembayaran sukses (sandbox) → redirect `/billing/finish/{tenant}` menampilkan status sukses
- [ ] **Webhook** `/billing/webhook` memproses notifikasi → status tenant menjadi `active`, `subscription_expires_at` terupdate
- [ ] Pembayaran pending → status pending ditampilkan; tidak meng-aktivasi prematur
- [ ] Pembayaran gagal/expire → status tidak berubah jadi aktif
- [ ] **Idempotensi webhook**: notifikasi ganda tidak menggandakan langganan
- [ ] Verifikasi signature webhook (notifikasi palsu ditolak)
- [ ] Entri **SubscriptionLog** tercatat untuk setiap perubahan
- [ ] Mata uang IDR & format jumlah benar (tidak ada pembulatan salah)

### 5.3 Siklus hidup langganan
- [ ] Perpanjangan memperpanjang `subscription_expires_at` dengan benar
- [ ] Transisi `trial → active → past_due → suspended` konsisten dengan UI
- [ ] Grace period dihitung benar (`grace_period_ends_at`)
- [ ] Invoice/kuitansi (jika ada) dapat diunduh PDF

---

## 6. Otorisasi & RBAC (Filament Shield)

### 6.1 Penegakan permission (positive + negative)
- [ ] Super admin global melihat semua resource
- [ ] Role Staff/terbatas **hanya** melihat resource yang diizinkan
- [ ] Aksi (Create/Edit/Delete) tersembunyi/ditolak sesuai permission
- [ ] Akses langsung URL resource tanpa permission → 403 rapi
- [ ] Policy per-record ditegakkan (mis. hanya pemilik/penanggung jawab bisa edit)
- [ ] Setelah menambah resource baru, `shield:generate` membuat permission & UI ter-update

### 6.2 Peran & keanggotaan tenant
- [ ] Assign/unassign role ke user via UI berfungsi
- [ ] `UserTenantRole` dengan masa berlaku kedaluwarsa mencabut akses (`UserTenantMembershipExpiry`)
- [ ] `TenantRole` (domain) vs Shield permission (panel) keduanya konsisten
- [ ] Mengubah role user langsung berdampak (setelah refresh) tanpa kebocoran

---

## 7. Lokalisasi & Bilingual (id / en)

- [ ] Switch bahasa via menu avatar (🇮🇩/🇬🇧) langsung mengubah UI
- [ ] Preferensi bahasa tersimpan di `users.preferred_locale` & persist lintas sesi
- [ ] Default locale tenant (`core/default_locale`) diterapkan untuk user baru
- [ ] **Tidak ada label hardcoded** yang lolos `FilamentUi` (cek label form, kolom tabel, judul section, placeholder, helperText, navigation group)
- [ ] Navigation group ter-translasi (jalankan `optimize:clear` bila perlu)
- [ ] Email/notifikasi mengikuti locale penerima (`QueueJobInheritsLocale`)
- [ ] PDF mengikuti locale yang sesuai
- [ ] Format tanggal, angka, mata uang sesuai locale
- [ ] Tidak ada teks campur (separuh id separuh en) dalam satu layar

---

## 8. Core Admin (Modul Core)

### 8.1 Tenants
- [ ] List/Create/Edit/View tenant; relation managers (Academic Years, Departments, Study Programs, Theses, Subscription Logs, Files, Modules, Violations, Feeder Logs) tampil & terisolasi
- [ ] Pengaturan branding (warna primer, logo) di tenant settings → tampil di panel saat tenant aktif

### 8.2 Organizations
- [ ] Struktur hierarki (parent/child) dibuat & ditampilkan benar
- [ ] Relation managers (Students, Lecturers, Curricula, School Classes, Collage Students, Audit Logs, Files) berfungsi
- [ ] `FoundationStructurePage` menampilkan struktur dengan benar

### 8.3 Users
- [ ] CRUD user; relation managers (Lecturers, Graded Grades, Verified Attendances, Principal Organizations)
- [ ] Status aktif/nonaktif berdampak pada login

### 8.4 Academic Years & Periods
- [ ] CRUD tahun & periode akademik; hanya 1 aktif bila aturannya demikian
- [ ] Tanggal mulai/akhir divalidasi (akhir > mulai)

### 8.5 Tenant Settings, Modules, Marketplace
- [ ] `TenantSettings` CRUD per group/key
- [ ] `ModuleMarketplace` / `TenantModules` mengaktifkan/menonaktifkan modul → navigasi berubah sesuai
- [ ] Menonaktifkan modul menyembunyikan resource & rute terkait

### 8.6 Subscription Plans & Logs
- [ ] CRUD plan; SubscriptionLog read-only menampilkan riwayat
- [ ] FoundationProfiles, Polls, PollResponses, ParentSurveys, ParentSurveyResponses, Stakeholders berfungsi (CRUD + isolasi)

---

## 9. Navigasi, Dashboard & UX Global

- [ ] **TabbedDashboard** memuat tanpa error; widget menampilkan data tenant aktif
- [ ] Navigation group terkelompok per modul & ter-translasi
- [ ] **Global search** (jika aktif) mengembalikan hasil tenant aktif saja
- [ ] **Notifikasi database** (lonceng) muncul, polling 30s, bisa ditandai terbaca
- [ ] Breadcrumb akurat di setiap halaman
- [ ] Empty state (tabel kosong) menampilkan pesan & CTA, bukan layar kosong
- [ ] Loading state (skeleton/spinner) muncul saat memuat data berat
- [ ] Tidak ada layout pecah pada label panjang / data ekstrem

---

## 10. Pola CRUD Generik (berlaku untuk setiap resource yang diuji)

> Terapkan blok ini per resource saat menguji modul di §13. Catat resource yang diuji.

### 10.1 Create
- [ ] Form create memuat semua field & section dengan layout benar (Grid/Section span)
- [ ] Field wajib divalidasi (pesan inline bilingual)
- [ ] Field unik (mis. `code`) menolak duplikat dengan pesan jelas (uji `Duplicate*CodeException`)
- [ ] Select relasi (BelongsTo) hanya menampilkan opsi tenant aktif
- [ ] Field reaktif (`live`/`afterStateUpdated`) berfungsi (mis. slug dari title)
- [ ] Repeater (HasMany) menambah/menghapus baris & tersimpan
- [ ] Simpan sukses → notifikasi + redirect/refresh benar
- [ ] `tenant_id`/`organization_id` terisi otomatis (TenantField), bukan input numerik manual

### 10.2 Edit / View
- [ ] Data ter-load benar ke form edit
- [ ] Simpan perubahan persist; tidak menimpa field lain
- [ ] Halaman View/Infolist menampilkan data lengkap & rapi
- [ ] Edit record tenant lain via URL → ditolak

### 10.3 Delete / Restore (soft delete)
- [ ] Delete meminta konfirmasi & menghapus (soft)
- [ ] Record ter-soft-delete tidak muncul di list default
- [ ] Restore mengembalikan record (jika fitur ada)
- [ ] Force delete (jika ada) benar-benar menghapus
- [ ] Relasi dependen ditangani (cascade/restrict) tanpa error integritas

### 10.4 Validasi & edge case
- [ ] Input ekstrem (string sangat panjang, karakter unicode/emoji, HTML/script) tidak merusak (anti-XSS)
- [ ] Angka negatif/nol/desimal divalidasi sesuai aturan domain
- [ ] Tanggal mustahil ditolak
- [ ] Submit ganda cepat tidak membuat duplikat (double-submit)

---

## 11. Tabel: Pencarian, Filter, Sort, Bulk, Pagination

- [ ] Pencarian tabel mengembalikan hasil relevan & cepat
- [ ] Sort kolom (asc/desc) benar, termasuk kolom computed/`state()`
- [ ] SelectFilter (enum/relasi) & custom Filter (query) menyaring benar
- [ ] Pagination & "per page" berfungsi; jumlah total akurat
- [ ] Bulk action (delete/update massal/export terpilih) berfungsi & terbatas tenant
- [ ] Toggle kolom & reorder (jika ada) persist
- [ ] Tabel dengan **>1.000 baris** tetap responsif (cek performa & memori)

---

## 12. Import / Export

### 12.1 Import CSV (BaseModelImporter)
- [ ] Tombol **download template CSV** menghasilkan header benar per resource
- [ ] Import file valid → record dibuat, ringkasan sukses ditampilkan
- [ ] Import baris invalid → laporan error per baris (tidak gagal senyap)
- [ ] Import meng-assign `tenant_id` aktif (tidak bisa menimpa tenant lain)
- [ ] File besar diproses via queue tanpa timeout; notifikasi saat selesai
- [ ] Kolom duplikat/`code` bentrok ditangani sesuai aturan (skip/update/error)
- [ ] Encoding (UTF-8, koma/desimal lokal) ditangani benar

### 12.2 Export & Export Center
- [ ] Export tabel (CSV/XLSX) berisi hanya data tenant aktif
- [ ] `ExportCenterPage` menampilkan riwayat & file dapat diunduh
- [ ] File export besar via queue; link unduh aman (tidak bisa diakses lintas tenant)

---

## 13. Uji Fungsional Mendalam per Domain

> Terapkan §10–§12 pada resource utama tiap modul. Di bawah ini alur bisnis spesifik yang harus diverifikasi (bukan sekadar CRUD).

### 13.1 Akademik — School (K-12)
- [ ] Buat Curriculum → Subject → School Class → assign Teacher & Student
- [ ] Input **Attendance** harian; rekap (`AttendanceRecapPage`) & PDF benar
- [ ] Input **Assessment/Grade**; ledger nilai (`ClassGradeLedgerPage`) akurat
- [ ] **Report Card** (`ReportCardPage`) generate per siswa + bulk PDF satu kelas
- [ ] `AcademicAnalytics` menampilkan metrik benar
- [ ] Student Achievement → PDF
- [ ] Isolasi: siswa kelas/tenant lain tidak muncul

### 13.2 Akademik — Campus (Higher-Ed)
- [ ] Faculty → Study Program → Course → Lecturer → Study Plan (jadwal)
- [ ] Konflik jadwal (ruang/dosen/jam bentrok) terdeteksi
- [ ] Thesis lifecycle/workflow berjalan (`ThesisWorkflowLifecycle`)
- [ ] Kapasitas ruang & SKS divalidasi

### 13.3 Enrollment (Admisi)
- [ ] Admission Period → Applicant → proses → Registration
- [ ] Pipeline **Applicant Accepted** memicu langkah lanjutan (akun/role)
- [ ] Exam Schedule terhubung ke modul Exam
- [ ] Drift detector & auto-fix enrollment berfungsi (`EnrollmentDriftAutoFix`)
- [ ] Pembayaran biaya registrasi → status enrollment update

### 13.4 Employee / HR
- [ ] Position → Employee → Employment Contract
- [ ] **Payroll**: generate Salary Slip, perhitungan benar (`EmployeePayrollCalculation`)
- [ ] **KPI** template & scoring
- [ ] **Leave Request** → workflow approval → saldo cuti terupdate
- [ ] Dokumen pegawai (`EmployeeDocument`) → PDF
- [ ] HRM workflow setup (`HrmWorkflowSetup`)

### 13.5 Finance (Akuntansi)
- [ ] Chart of Accounts hierarkis benar
- [ ] Budget → kontrol anggaran (`FinanceControl`) memblokir over-budget
- [ ] **Student Invoice** → Payment → pipeline "paid" (`StudentInvoicePaidPipeline`) menutup invoice & posting jurnal
- [ ] Journal Entry seimbang (debit = kredit) divalidasi
- [ ] Laporan: `ProfitLossPage`, `BalanceSheetPage`, `CashFlowPage`, `FinanceOverviewPage` akurat & dapat diekspor
- [ ] Dokumen finance → PDF (`FinanceDocumentPdf`); ekspor laporan (`FinancialReportExport`)
- [ ] Multi-currency/pembulatan benar

### 13.6 Procurement + Inventory
- [ ] Purchase Requisition → RFQ → award → **PO otomatis** dari RFQ menang (`PoCreatedFromAwardedRfq`)
- [ ] Goods Receipt → update stok Inventory (`InventoryProcurementIntegration`)
- [ ] Vendor Bill → posting ke Finance
- [ ] Pipeline E2E (`ProcurementEndToEndPipeline`) lengkap
- [ ] Semua dokumen (PR, PO, GR, Vendor Bill, RFQ) → PDF benar
- [ ] Approval limit (nominal) memicu langkah workflow berbeda
- [ ] Stok Inventory: opname, transfer, kartu stok akurat; stok minus dicegah

### 13.7 Library + OPAC
- [ ] Book → Book Copy (barcode unik) → Member → Loan → Return
- [ ] Fine otomatis untuk keterlambatan; perhitungan denda benar
- [ ] Loan & Fine → PDF
- [ ] Renewal & batas pinjam ditegakkan
- [ ] OPAC publik (§2.2) sinkron dengan katalog
- [ ] Import SLIMS (jika diuji) memetakan field benar

### 13.8 Workflow (lihat juga §14)
- [ ] Setiap modul yang memicu workflow (Procurement, Finance, HR, Thesis) membuat WorkflowInstance benar

### 13.9 Exam (UUID-based, lihat juga §15.2)
- [ ] Question Bank → Exam Definition → token peserta
- [ ] Cross-context (School/Campus/Standalone) participant resolver benar
- [ ] Manual essay grading & analytics
- [ ] Gradebook/result export
- [ ] Keamanan exam (token sekali pakai, isolasi peserta) — `ExamSecurity`

### 13.10 Modul GA lain (uji sampling CRUD + isolasi + 1 alur khas)
Untuk masing-masing, jalankan §10 + verifikasi 1 alur khasnya:
- [ ] **Alumni** — direktori alumni, company partner, tracer study
- [ ] **Asset** — kategori, aset, depresiasi/maintenance
- [ ] **Boarding** — dormitory, penempatan santri/siswa
- [ ] **Cafeteria** — menu, transaksi, saldo
- [ ] **Clinic** — rekam medis, alergi, kunjungan (privasi data sensitif!)
- [ ] **Cms** — halaman/berita, publikasi
- [ ] **Consulting** — engagement/sesi
- [ ] **Counseling** — sesi konseling (privasi!)
- [ ] **Dms** — folder & dokumen, versi
- [ ] **Donation** — donor, kampanye, donasi
- [ ] **EducationQa** — standar mutu, instrumen
- [ ] **EOffice** — surat masuk/keluar, disposisi, dokumen → PDF
- [ ] **Event** — registrasi event, tiket/kehadiran
- [ ] **Facility** — booking fasilitas, dashboard sustainability
- [ ] **Helpdesk** — tiket, kategori, SLA, dashboard
- [ ] **InternalAudit** — program audit, temuan
- [ ] **IsoCompliance** — kontrol ISO, bukti
- [ ] **ItOps** — lisensi software, aset IT
- [ ] **KpiEnterprise** — KPI enterprise, scorecard
- [ ] **Legal** — dokumen legal, kontrak
- [ ] **Marketplace** — seller, produk, order
- [ ] **MerchOrder** — order merch → PDF
- [ ] **Messaging** — pesan/broadcast, template notifikasi
- [ ] **PhysicalSecurity** — guard, **Visitor Kiosk** (`VisitorKioskPage`), buku tamu
- [ ] **Printing** — print template, job cetak
- [ ] **Property** — lease, invoice lease → PDF
- [ ] **Risk** — register risiko, mitigasi
- [ ] **Sales** — sales order → PDF
- [ ] **Training** — kelas training, sertifikat + verifikasi token
- [ ] **Transport** — rute, kendaraan, penumpang
- [ ] **Ai** — AI prompt template / advisor (cek guard biaya & keamanan output)
- [ ] **Capacity** — perencanaan kapasitas
- [ ] **Global** — data referensi (negara/provinsi/kota) read-only konsisten lintas tenant
- [ ] **Monitoring** — audit log & file upload (lihat §20.6)

---

## 14. Workflow Engine & Visual Designer (Inti)

### 14.1 Visual Designer (`WorkflowDesignerPage`)
- [ ] Membuat workflow via designer (drag node, koneksi) tersimpan sebagai definisi valid
- [ ] **Import JSON** definisi (`WorkflowDesignerImportJson`) tervalidasi (payload invalid ditolak rapi)
- [ ] Export/clone definisi konsisten (`WorkflowDefinitionPorter`)
- [ ] Versi & publish definisi berperilaku benar (draft vs published)

### 14.2 Eksekusi & lifecycle
- [ ] Memulai instance dari trigger membuat snapshot definisi (`WorkflowInstanceStarter`)
- [ ] Advance/approve memajukan langkah; reject/return mengembalikan; cancel membatalkan
- [ ] RuleEngine (JSONLogic) mengarahkan transisi sesuai kondisi (mis. nominal)
- [ ] Automated action runner mengeksekusi hook DB-driven; retry saat gagal
- [ ] **SLA**: keterlambatan terdeteksi & eskalasi/retry berjalan
- [ ] Guardrails mencegah transisi ilegal (`WorkflowGuardrails`)

### 14.3 Inbox & tugas
- [ ] `WorkflowMyTasksPage` / `WorkflowInboxPage` / `WorkflowTeamInboxPage` menampilkan tugas user/tim yang benar
- [ ] Approver hanya melihat tugas miliknya (RBAC + tenant)
- [ ] `WorkflowTaskHistoryPage` menampilkan jejak lengkap & akurat
- [ ] Notifikasi tugas baru terkirim

---

## 15. Integrasi Eksternal

### 15.1 Moodle (FOS → Moodle, outbox)
- [ ] Membuat Course/Student/Enrollment mengantrikan event di `moodle_sync_outbox`
- [ ] `ProcessMoodleSyncOutboxJob` mengirim ke Moodle (klaim atomik — tidak dobel, `MoodleOutboxAtomicClaim`)
- [ ] Mapping idnumber (`fos_tenant_*`, `fos_course_*`, `fos_user_*`) benar
- [ ] Lecturer assignment tersinkron sebagai teacher (`LecturerAssignmentSyncsAsMoodleTeacher`)
- [ ] Pull grades/attendance dari Moodle akurat
- [ ] `moodle:health-check` & `moodle:reconcile` melaporkan status benar
- [ ] Kegagalan API ditangani (retry/log), tidak memblokir UI utama

### 15.2 Exam runtime (Cloudflare)
- [ ] Publish exam ke runtime sukses (atau gagal rapi bila kredensial kosong)
- [ ] Webhook hasil exam tersinkron balik (HMAC/secret diverifikasi)
- [ ] Control room access terkontrol

### 15.3 Midtrans
- [ ] Lihat §5 (alur uang)

---

## 16. API (Sanctum / Mobile)

> Dapat diuji via browser DevTools, Postman, atau klien API.
- [ ] Endpoint API memerlukan token (`ApiTenantTokenRequirement`) — tanpa token → 401
- [ ] Token mengikat ke tenant; akses lintas tenant ditolak
- [ ] Endpoint read (`ApiResourceRead`) mengembalikan hanya data tenant token
- [ ] Mobile API (`MobileApi`) berfungsi untuk alur utama (login, list, detail)
- [ ] Rate limiting API aktif
- [ ] Response error API konsisten (format JSON, status code benar)
- [ ] Versi API & resource transformer konsisten
- [ ] Token dapat dicabut; setelah dicabut → 401

---

## 17. Panel Tambahan

### 17.1 Platform panel (`/platform`)
- [ ] Hanya operator platform (super admin global) yang bisa akses (`PlatformPanelAccess`)
- [ ] CRUD tenant level platform (`PlatformTenantCrud`)
- [ ] User tenant biasa **tidak** bisa akses `/platform` → ditolak

### 17.2 Parent panel (`/parent`)
- [ ] User parent login & melihat data anak yang relevan saja
- [ ] Tidak ada kebocoran data siswa lain
- [ ] Survei/poll orang tua dapat diisi
- [ ] Akses fitur sesuai role parent (tidak bisa akses admin resource)

---

## 18. Generasi PDF (lintas modul)

- [ ] Setiap dokumen PDF (report card, transcript, invoice, PO, GR, vendor bill, RFQ, loan, fine, sales order, lease invoice, merch order, training certificate, employee/eoffice/finance/school docs) render benar
- [ ] Layout PDF rapi (header, logo tenant, footer, halaman, tabel tidak terpotong)
- [ ] Locale & angka/tanggal di PDF benar
- [ ] **Bulk PDF** menghormati `FOS_PDF_BULK_SYNC_LIMIT` (default 50) — di atas batas via queue
- [ ] PDF hanya berisi data tenant aktif; akses URL PDF record tenant lain ditolak
- [ ] Karakter unicode/Indonesia tampil benar (font embedding)
- [ ] File besar tidak menyebabkan timeout/memory exhaustion

---

## 19. File Upload & Storage

- [ ] Upload avatar, logo, lampiran, bukti pembayaran berfungsi
- [ ] **Visibility** default `private`; file privat tidak dapat diakses tanpa otorisasi
- [ ] File publik (logo) dapat diakses via `storage:link`
- [ ] Validasi tipe & ukuran file (tolak tipe berbahaya, batasi MB)
- [ ] Nama file disanitasi (tidak ada path traversal)
- [ ] Hapus record menghapus/menangani file terkait
- [ ] Akses file lintas tenant ditolak (uji URL langsung)

---

## 20. Keamanan (Security Testing — wajib lulus)

### 20.1 Isolasi & otorisasi
- [ ] Lihat §4.3 (isolasi tenant) & §6 (RBAC) — semua PASS
- [ ] **IDOR** lintas resource (ubah id di URL) → 403/404 konsisten
- [ ] Akses fungsi admin oleh user non-admin → ditolak (`ProductionTenancySecurity`, `TenantIsolationStudent`)

### 20.2 Input & injection
- [ ] XSS tersimpan/terefleksi (input `<script>`, event handler) ter-escape di tampilan
- [ ] SQL injection pada filter/search tidak mempan (parameterized)
- [ ] Upload file: tidak bisa upload & eksekusi skrip (php/html)
- [ ] CSV injection (formula `=`, `+`, `@`) dinetralkan pada export

### 20.3 Session & CSRF
- [ ] CSRF token diverifikasi pada form (kecuali webhook yang sengaja dikecualikan)
- [ ] Webhook `/billing/webhook` aman (signature) meski tanpa CSRF
- [ ] Session fixation: session id berganti setelah login
- [ ] Cookie `HttpOnly`, `Secure` (https), `SameSite` benar

### 20.4 Auth hardening
- [ ] Throttle login & reset password aktif
- [ ] Kebijakan password (panjang/kompleksitas) ditegakkan
- [ ] Enumerasi user dicegah (pesan generik di login/reset)
- [ ] MFA tidak dapat dilewati

### 20.5 Header & transport
- [ ] HTTPS dipaksa; tidak ada mixed content
- [ ] Security headers (CSP/HSTS/X-Frame-Options/X-Content-Type-Options) sesuai kebijakan
- [ ] Tidak ada kebocoran info sensitif di error (debug off di production)

### 20.6 Audit & monitoring
- [ ] Aksi penting (login, perubahan role, switch tenant, CRUD sensitif) tercatat di **Audit Log** (`MonitoringAuditTrail`)
- [ ] Audit log tidak dapat diubah/dihapus user biasa
- [ ] Causer & subject tercatat benar (spatie activitylog)
- [ ] Data sensitif (password, token) **tidak** muncul di log

---

## 21. Integritas Data & Edge Cases

- [ ] Soft delete konsisten; relasi tidak menggantung (orphan)
- [ ] Operasi konkuren (2 user edit record sama) ditangani (optimistic/last-write + tanpa korupsi)
- [ ] Transaksi DB rollback saat sebagian langkah gagal (mis. invoice+jurnal)
- [ ] Data referensi Global konsisten & tidak dapat dimodifikasi user non-otorisasi
- [ ] Karakter unicode/emoji & string panjang tidak merusak tampilan/DB
- [ ] Timezone konsisten (simpan UTC, tampil sesuai tenant/user)
- [ ] Angka uang tidak kehilangan presisi

---

## 22. Performa

- [ ] Halaman utama (dashboard, list besar) TTFB & load wajar (< ~2s pada data demo)
- [ ] Tidak ada **N+1** mencolok pada list/relasi (cek query count via Pulse/Debugbar/Network)
- [ ] Tabel dengan ribuan baris + filter tetap responsif
- [ ] Generasi PDF/laporan/export berat tidak memblokir UI (queue)
- [ ] Aset front-end ter-minify saat build production
- [ ] `php artisan optimize` (config/route/view cache) tidak merusak fungsi
- [ ] Tidak ada memory exhaustion pada operasi massal
- [ ] Laravel **Pulse** (`/pulse` jika diaktifkan) menampilkan metrik sehat

---

## 23. Responsif, Lintas-Browser & Aksesibilitas

- [ ] Lihat matriks §1.4 — semua kombinasi PASS untuk alur utama
- [ ] Sidebar/navigation collapse di mobile berfungsi
- [ ] Modal & form panjang dapat di-scroll di mobile
- [ ] Tabel di mobile (scroll horizontal / responsive) terbaca
- [ ] Tidak ada elemen terpotong / overflow horizontal tak diinginkan
- [ ] Fokus keyboard terlihat; urutan tab logis
- [ ] Form punya label terkait (bukan placeholder saja)
- [ ] Warna bukan satu-satunya indikator status (ada ikon/teks)

---

## 24. Penanganan Error & Notifikasi

- [ ] **404** halaman/resource tidak ada → halaman rapi (bukan stack trace)
- [ ] **403** akses ditolak → halaman rapi + arahan
- [ ] **419** session/CSRF expired → pesan rapi, mudah login ulang
- [ ] **500** → halaman generik (tanpa bocor detail di production); tercatat di log
- [ ] Validasi form menampilkan semua error sekaligus dengan jelas
- [ ] Notifikasi sukses/gagal (toast Filament) muncul & hilang wajar
- [ ] Aksi destruktif selalu minta konfirmasi
- [ ] Kehilangan koneksi saat submit ditangani (tidak kehilangan data diam-diam)

---

## 25. Email & Notifikasi

- [ ] Email verifikasi, reset password, MFA email terkirim & ter-render benar (HTML)
- [ ] Notifikasi workflow/approval terkirim ke penerima yang benar
- [ ] Email mengikuti locale penerima
- [ ] Link dalam email valid & mengarah ke tenant/halaman benar
- [ ] Template notifikasi (Messaging) dapat dikelola & dipakai
- [ ] Tidak ada email terkirim ke alamat salah/lintas tenant

---

## 26. Regresi Pasca-Perbaikan

- [ ] Setiap bug yang diperbaiki diuji ulang + skenario sekitarnya
- [ ] Jalankan suite otomatis sebagai jaring pengaman: `php artisan test --compact`
- [ ] Smoke test alur kritis (login → pilih tenant → 1 CRUD → 1 workflow → 1 pembayaran) setelah deploy/perubahan besar
- [ ] `php artisan optimize:clear` setelah perubahan konfigurasi/permission

---

## 27. Ringkasan & Sign-Off

### Rekap temuan
| ID | Modul/Area | Severity | Deskripsi | Status | Tiket |
|----|-----------|----------|-----------|--------|-------|
|    |           |          |           |        |       |

### Gate keputusan rilis
- [ ] Semua item §1 (environment) hijau
- [ ] Semua "Inti — wajib lulus" hijau: §4 Multi-tenancy, §5 Billing, §6 RBAC, §14 Workflow, §20 Security
- [ ] 0 bug **S1** & **S2** terbuka
- [ ] Bug **S3** memiliki tiket + persetujuan rilis
- [ ] Matriks browser/responsif/aksesibilitas (§1.4, §23) terpenuhi
- [ ] Alur uang (§5) terverifikasi di sandbox + uji idempotensi webhook
- [ ] Isolasi tenant (§4.3) terbukti ≥10 resource

### Tanda tangan
| Peran | Nama | Tanggal | Keputusan (GO / NO-GO) |
|-------|------|---------|------------------------|
| QA Lead |  |  |  |
| Tech Lead |  |  |  |
| Product Owner |  |  |  |

---

### Lampiran A — Skenario smoke test cepat (15 menit)
1. Buka `/` → klik Register → buat akun → verifikasi email
2. Login → buat tenant baru → pilih tenant
3. Buat 1 Student (School) → edit → export → hapus
4. Buat Purchase Requisition → jalankan approval workflow → cek PO/PDF
5. Buka Billing → bayar plan (sandbox) → cek status aktif via webhook
6. Switch ke tenant kedua → pastikan data tenant pertama tidak terlihat
7. Login sebagai user Staff → pastikan resource terlarang tidak muncul
8. Ganti bahasa id↔en → pastikan konsisten
9. Logout → coba akses `/admin` → diarahkan ke login
