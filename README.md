# FoundationOS — Platform ERP Modular untuk Lembaga Pendidikan

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-13.0-FF2D20?style=flat-square&logo=laravel)
![Filament](https://img.shields.io/badge/Filament-5-FDA41C?style=flat-square)
![Livewire](https://img.shields.io/badge/Livewire-4-FB70A9?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

**Sistem ERP terpadu, modular, dan scalable untuk mengelola operasional lembaga pendidikan tinggi dan menengah.**

[Demo](#-fitur-utama) • [Dokumentasi](#-dokumentasi) • [Kontribusi](#-kontribusi)

</div>

---

## 🎯 Tentang FoundationOS

FoundationOS adalah fondasi aplikasi **ERP (Enterprise Resource Planning) yang dirancang khusus untuk lembaga pendidikan** (K-12, kampus, universitas). Platform ini menyediakan ekosistem modul terpadu untuk mengelola:

- **Akademik**: kurikulum, mata pelajaran, kelas, penilaian, dan absensi
- **Kepegawaian & HR**: posisi, kontrak kerja, gaji, KPI, dan cuti
- **Keuangan**: bagan akun, anggaran, invoice, pembayaran, dan jurnal
- **Procurement**: requisisi pembelian, RFQ, PO, dan vendor bills
- **Perpustakaan**: manajemen buku, peminjaman, denda, dan integrasi SLIMS
- **Alur Kerja**: approval engine berbasis metadata dengan automasi
- **Monitoring**: audit log, tracking file upload, dan compliance
- **Serta 40+ modul lainnya** untuk berbagai aspek operasional

**Teknologi Stack**:
- **Backend**: Laravel 13, PHP 8.4
- **Frontend**: Livewire 4, Alpine.js, Tailwind CSS v4
- **Admin Panel**: Filament v5 (berbasis Laravel)
- **Arsitektur Modul**: coolsam/modules v5 (nwidart-compatible)
- **Multi-Tenancy**: shared-database (tanpa paket external seperti stancl/tenancy)
- **Authorization**: Spatie Permission + Filament Shield
- **Database**: Laravel migrations, Eloquent ORM

---

## ⚡ Quick Start

### Prerequisites

- PHP 8.4+
- Node.js 18+
- Composer
- SQLite atau database relasional lainnya

### Installation

```bash
# 1. Clone repository
git clone <repo-url>
cd foundationOS

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Database migrations & seeds
php artisan migrate
php artisan db:seed

# 5. Link storage (jika file uploads diperlukan)
php artisan storage:link

# 6. Start development servers
composer run dev
```

**Akses panel admin**: https://foundationOS.test/admin

Jika belum memiliki akun, buat super admin terlebih dahulu:

```bash
php artisan make:super-admin
```

---

## 🏗️ Arsitektur Sistem

### Multi-Tenancy Model

FoundationOS menggunakan **shared-database multi-tenancy** (satu instance aplikasi melayani banyak tenant):

```
┌─────────────────────────────────────────┐
│      Single FoundationOS Instance       │
├─────────────────────────────────────────┤
│                                         │
│  ┌──────────────┐  ┌──────────────┐   │
│  │  Tenant A    │  │  Tenant B    │   │
│  │ (tenant_id:1)│  │ (tenant_id:2)│   │
│  └──────────────┘  └──────────────┘   │
│                                         │
│  Shared Database (dengan tenant_id)    │
│  ├─ Users (global, dapat multi-tenant) │
│  ├─ Operasional (students, courses)    │
│  └─ Config (settings per-tenant)       │
│                                         │
└─────────────────────────────────────────┘
```

**Komponen Utama**:

- **Tenant**: Boundary akun SaaS (universitas, sekolah, institusi)
- **User**: Global, dapat bergabung ke multiple tenants
- **User-Tenant Roles**: Menghubungkan user ke tenant dengan role tertentu
- **Tenant-Scoped Data**: Setiap tabel operasional membawa `tenant_id`
- **Organization** (opsional): Unit/departemen dalam tenant (membawa `organization_id`)

**Akibatnya**:

- Setiap query & policy harus tenant-aware
- Panel Filament dilindungi dengan tenant middleware
- Permission/Role dikelola per-tenant (Spatie Permission teams mode)

### Filament Admin Panel

- **Panel ID**: `admin`
- **URL**: `/admin`
- **Tenant Model**: `Modules\Core\Models\Tenant`
- **Discovery**: Otomatis dari `app/Filament/Resources` & `Modules/*/app/Filament/Resources`

Setelah menambah resource baru:

```bash
php artisan optimize:clear  # Refresh auto-discovery
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
```

### Module Architecture

Sistem modul menggunakan **coolsam/modules v5** dengan struktur per-module:

```
Modules/<Name>/
├─ app/
│  ├─ Filament/
│  │  ├─ Resources/          # Filament resources per domain
│  │  ├─ Pages/              # Custom pages
│  │  └─ Widgets/            # Dashboard widgets
│  ├─ Models/                # Eloquent models
│  ├─ Services/              # Business logic
│  ├─ Policies/              # Authorization policies
│  ├─ Events/                # Domain events
│  ├─ Observers/             # Model observers
│  └─ Exceptions/
├─ database/
│  ├─ migrations/
│  ├─ seeders/
│  └─ factories/
├─ routes/
│  ├─ web.php
│  └─ api.php
├─ tests/
│  ├─ Feature/
│  └─ Unit/
└─ resources/views/
```

---

## 📚 Struktur Direktori Utama

```
foundationOS/
├─ app/                           # Core application layer
│  ├─ Filament/Imports/          # 100+ Filament CSV importers
│  ├─ Integrations/Moodle/       # Moodle API client & sync
│  ├─ Jobs/                      # Queue jobs
│  ├─ Models/User.php            # Bridge untuk Laravel ecosystem
│  ├─ Observers/                 # Model observers
│  ├─ Policies/                  # Authorization policies
│  └─ Providers/Filament/        # Panel configuration
│
├─ Modules/                      # Feature modules (45+)
│  ├─ Core/                      # 🔴 Fondasi: tenancy, users, orgs
│  ├─ Global/                    # 🔴 Referensi: negara, provinsi, kota
│  ├─ School/                    # 🎓 K-12: kurikulum, kelas, siswa
│  ├─ Campus/                    # 🎓 Higher-ed: fakultas, program studi
│  ├─ Enrollment/                # 📝 Admisi & registrasi
│  ├─ Finance/                   # 💰 Akuntansi & pembayaran
│  ├─ Procurement/               # 🛒 Pembelian & vendor
│  ├─ Employee/                  # 👥 HR & Payroll
│  ├─ Library/                   # 📖 Manajemen perpustakaan
│  ├─ Workflow/                  # ✅ Approval engine (V2)
│  ├─ Monitoring/                # 📊 Audit & compliance
│  └─ [40+ modul lainnya]/       # Asset, DMS, Helpdesk, Facility, dll
│
├─ config/                       # Laravel & package configuration
├─ database/
│  ├─ migrations/                # Database schema
│  └─ seeders/
├─ routes/
│  ├─ web.php                    # Web routes
│  └─ api.php                    # API routes
├─ resources/
│  ├─ views/                     # Blade templates (jika diperlukan)
│  └─ css/js/                    # Frontend assets
├─ storage/                      # File uploads, logs, cache
├─ tests/                        # PHPUnit tests
│  ├─ Feature/
│  └─ Unit/
├─ scripts/                      # One-off PHP utilities
├─ bootstrap/                    # Bootstrap files
├─ public/                       # Document root
├─ composer.json                 # PHP dependencies
├─ package.json                  # JavaScript dependencies
└─ README.md                     # This file
```

---

## 🌟 Fitur Utama

### 1. **Multi-Tenancy Terintegrasi**

- Satu instance, banyak tenant (universitas, sekolah, institusi)
- Isolasi data otomatis dengan `tenant_id` di setiap tabel
- Role & permission per-tenant (Spatie Permission teams mode)
- Tenant settings & konfigurasi terisolasi

### 2. **Filament Admin Panel (v5)**

- UI responsif & intuitif berbasis Livewire v4
- Bilingual (Indonesia & English) dengan `FilamentUi` helper
- Import/Export CSV built-in untuk setiap resource
- Soft delete support & activity logging
- Real-time Livewire components

### 3. **45+ Modul Terintegrasi**

Lihat [Module Index](#-modul-tersedia) di bawah untuk daftar lengkap.

### 4. **Workflow Engine (V2) — Metadata-Driven**

Approval engine berbasis JSONLogic rules:

```php
// Definisi workflow di database
// Automatis advance instance based on condition rules
Workflow -> WorkflowInstance -> WorkflowStep -> WorkflowAssignment
```

**Artisan commands**:

```bash
php artisan fos:workflow:setup-procurement-pilot 1 --manager=10
php artisan fos:workflow:health-check --tenant=1
```

Lihat [WORKFLOW.md](./WORKFLOW.md) untuk detail lengkap.

### 5. **Moodle Integration**

One-way sync: FoundationOS → Moodle (master data)

- Sync users, courses, cohorts, grades, attendance
- Outbox pattern untuk reliable sync
- Health check & reconcile commands

```bash
php artisan moodle:sync-cohorts
php artisan moodle:pull-grades
php artisan moodle:health-check
```

Lihat [MOODLE.md](./MOODLE.md) dan [MOODLE_HARDENING_CHECKLIST.md](./MOODLE_HARDENING_CHECKLIST.md).

### 6. **Activity Logging (spatie/laravel-activitylog)**

Automatic audit trail untuk semua model changes:

```php
$activity = Activity::forSubject($course)->get();
// Tracks: who, what, when, old values, new values
```

### 7. **Authorization & Permissions**

- **Filament Shield** untuk UI management
- **Spatie Permission** untuk granular permissions
- Role-based access control (RBAC) per-tenant
- Policy classes untuk model authorization

### 8. **Import/Export CSV**

Setiap resource memiliki:

- ✅ Import Data button (bulk CSV upload)
- 📥 Download Template button (struktur CSV)
- 100+ custom importers di `app/Filament/Imports/`

### 9. **Responsive Design**

- Tailwind CSS v4
- Mobile-first UI
- Alpine.js interactivity
- Filament components (Tables, Forms, Infolists)

---

## 📦 Modul Tersedia

### Core & Foundation (Wajib)

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **Core** | Fondasi sistem | Tenancy, Users, Organizations, Subscriptions, Academic Years, Departments |
| **Global** | Reference data | Countries, Provinces, Cities, Districts, Timezones |
| **Monitoring** | Audit & Compliance | Activity logs, File uploads, System health |

### Akademik & Pendidikan

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **School** | K-12 Education | Curricula, Subjects, Classes, Teachers, Students, Attendance, Assessments |
| **Campus** | Higher Ed | Faculties, Study Programs, Courses, Lecturers, Theses, Study Plans |
| **Enrollment** | Admissions & Registration | Admission Periods, Applicants, Registrations, Exams |
| **EducationQa** | Quality Assurance | Academic quality monitoring & metrics |

### Operasional

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **Finance** | Accounting & Payments | Chart of Accounts, Budgets, Invoices, Journal Entries, Payments |
| **Procurement** | Purchasing & Vendor Mgmt | Purchase Requisitions, RFQs, POs, Goods Receipts, Vendor Bills |
| **Employee** | HR & Payroll | Positions, Contracts, Salary Slips, KPIs, Leave Requests |
| **Library** | Library Management | Books, Copies, Members, Loans, Fines, SLIMS Integration |
| **Inventory** | Inventory Management | Stock tracking, warehouse operations |
| **Asset** | Asset Management | Fixed asset tracking, depreciation, maintenance |
| **Facility** | Facilities Management | Buildings, rooms, maintenance, utilization |
| **Property** | Property Management | Real estate, leasing, tenant management |

### Workflow & Approval

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **Workflow** | Approval Engine | Metadata-driven workflows, multi-step approvals, SLA automation |

### Komunikasi & Layanan

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **Messaging** | Internal Messaging | Notifications, announcements, messaging system |
| **EOffice** | Document Management | Electronic office, correspondence tracking |
| **Helpdesk** | IT Support | Ticketing system, issue tracking |
| **Clinic** | Medical Services | Student/staff health services |
| **Counseling** | Student Services | Academic & personal counseling |
| **Transport** | Transportation | Vehicle & route management |
| **Cafeteria** | Dining Services | Menu, inventory, transactions |
| **Boarding** | Student Housing | Dormitory management |
| **Event** | Events Management | Event planning & management |

### Keamanan & Compliance

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **PhysicalSecurity** | Security | CCTV, access control, incidents |
| **IsoCompliance** | ISO Standards | ISO 9001, ISO 27001, audit trails |
| **InternalAudit** | Internal Audit | Audit planning, findings, follow-up |
| **Risk** | Risk Management | Risk register, mitigation, monitoring |
| **Legal** | Legal Management | Contracts, agreements, compliance |

### Bisnis & Commerce

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **Sales** | Sales Management | Customers, quotations, orders, invoicing |
| **Marketplace** | E-Commerce | Product catalog, online ordering |
| **MerchOrder** | Merchandise | Internal product ordering |
| **Donation** | Fundraising | Donation tracking, campaigns |
| **Alumni** | Alumni Relations | Alumni network, engagement |

### Analytics & Intelligence

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **KpiEnterprise** | KPI Tracking | Enterprise metrics, dashboards |
| **Capacity** | Capacity Planning | Resource planning, forecasting |
| **InternalAudit** | Internal Audit | Audit operations & reporting |
| **EducationQa** | Quality Metrics | Academic quality indicators |

### Khusus

| Modul | Domain | Deskripsi |
|-------|--------|-----------|
| **Cms** | Content Management | Website content, pages, posts |
| **Training** | Training & Development | Training programs, certifications |
| **Printing** | Print Management | Print jobs, quotas, billing |
| **ItOps** | IT Operations | Infrastructure, systems, monitoring |
| **Consulting** | Consulting Services | Project-based services |
| **Dms** | Document Management | Digital records, OCR, archival |

---

## 🛠️ Development

### Artisan Commands

```bash
# Modules
php artisan module:list                    # Lihat daftar modules
php artisan module:make <Name>            # Buat module baru
php artisan module:enable <Name>          # Aktifkan module
php artisan module:disable <Name>         # Nonaktifkan module

# Database
php artisan migrate                       # Jalankan migrations
php artisan migrate:rollback              # Rollback migrations
php artisan db:seed                       # Run seeders
php artisan db:seed --class=<Seeder>     # Run specific seeder

# Filament
php artisan make:filament-resource <Name> --no-interaction
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
php artisan shield:super-admin --user=1 --tenant=1

# Testing
php artisan test --compact                # Run all tests
php artisan test --compact --filter=testName  # Run specific test
php artisan test --compact tests/Feature/ExampleTest.php

# Code Quality
vendor/bin/pint --dirty --format agent    # Format code
php artisan tinker                        # Interactive shell

# Workflow
php artisan fos:workflow:health-check --tenant=1
php artisan fos:workflow:setup-procurement-pilot 1 --manager=10 --finance=11
```

### Development Server

```bash
# Start all dev services (Laravel, queue, Vite, Pail)
composer run dev

# Or individually:
php artisan serve                   # Server on 127.0.0.1:8000
php artisan queue:listen            # Queue worker
npm run dev                         # Vite dev server
php artisan pail                    # Live logs
```

### Testing

FoundationOS menggunakan **PHPUnit v12** untuk semua testing:

```bash
# Run all tests
php artisan test --compact

# Run specific file
php artisan test --compact tests/Feature/CoreTenancyFoundationTest.php

# Run with filter
php artisan test --compact --filter=testWorkflowDefinition

# Generate code coverage
php artisan test --coverage
```

**Testing conventions**:
- Feature tests di `tests/Feature/`
- Unit tests di `tests/Unit/`
- Use factories untuk test data
- Always use `RefreshDatabase` trait

### Code Formatting

FoundationOS menggunakan **Laravel Pint** untuk style consistency:

```bash
# Auto-format modified PHP files
vendor/bin/pint --dirty --format agent

# Format specific file
vendor/bin/pint resources/views/app.blade.php --format agent
```

### Bilingual UI (Indonesia & English)

Semua label UI harus melalui `FilamentUi` helper:

```php
// ✅ Benar
TextInput::make('email')->label(FilamentUi::field('email'))
Section::make(FilamentUi::text('Personal Information'))

// ❌ Salah — hardcoded label
TextInput::make('email')->label('Email Address')
Section::make('Personal Information')
```

Tambah frasa baru ke `Modules/Core/app/Support/FilamentUi.php`:

```php
private const PHRASES = [
    'English phrase' => 'Terjemahan Indonesia',
    'New workflow name' => 'Nama alur kerja baru',
];
```

---

## 📖 Dokumentasi Lengkap

Dokumentasi spesifik untuk fitur & modul tersedia di:

| Dokumen | Topik |
|---------|-------|
| [ROADMAP.md](./ROADMAP.md) | Development roadmap & versioning |
| [WORKFLOW.md](./WORKFLOW.md) | Workflow V2 engine, setup, testing |
| [MOODLE.md](./MOODLE.md) | Moodle integration, sync setup |
| [MOODLE_HARDENING_CHECKLIST.md](./MOODLE_HARDENING_CHECKLIST.md) | Security hardening untuk Moodle |
| [PROCUREMENT.md](./PROCUREMENT.md) | Procurement workflows & approval chains |
| [FINANCE.md](./FINANCE.md) | Financial management & accounting flows |
| [LIBRARY.md](./LIBRARY.md) | Library module & SLIMS integration |
| [AGENTS.md](./AGENTS.md) | AI agents & automation |

---

## 🚀 Deployment

### Production Deployment dengan Laravel Cloud

```bash
# Laravel Cloud adalah cara tercepat untuk deploy & scale aplikasi Laravel
# https://cloud.laravel.com/

laravel-cloud deploy
```

### Manual Deployment

```bash
# Build frontend assets
npm run build

# Install production dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear & optimize caches
php artisan optimize

# Start queue worker (jika ada jobs)
php artisan queue:work
```

### Environment Configuration

Key `.env` variables:

```env
# Multi-Tenancy
TENANCY_ENABLED=true
TENANCY_API_REQUIRE_TENANT=true
# Production: defaults to true when APP_ENV=production (fail-closed tenant scope on HTTP).
# Local: set false in .env (see .env.example).
TENANCY_SCOPE_FAIL_CLOSED=false

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=foundationos
DB_USERNAME=root
DB_PASSWORD=secret

# Mail (untuk notifikasi)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io

# Moodle Integration (opsional)
MOODLE_URL=https://moodle.example.com
MOODLE_TOKEN=your-moodle-webservice-token

# Queue
QUEUE_CONNECTION=database
# atau: redis, sync (development)
```

---

## 🤝 Kontribusi

Kami menerima kontribusi dari community! Berikut guidelines:

### Development Process

1. **Fork repository** & buat branch fitur
2. **Ikuti code conventions** (lihat [code-style.md](./docs/code-style.md))
3. **Tulis tests** untuk fitur baru (PHPUnit)
4. **Format code** dengan `vendor/bin/pint --dirty`
5. **Buat pull request** dengan deskripsi jelas

Repository admins: enable **branch protection** on `main` per [.github/BRANCH_PROTECTION.md](.github/BRANCH_PROTECTION.md) (required status checks: Pint, Larastan, PHPUnit).

### Branch Naming

```
feature/short-description        # Fitur baru
bugfix/issue-description         # Bug fixes
refactor/component-name          # Refactoring
docs/what-was-documented         # Documentation
```

### Pull Request Checklist

- [ ] Tests written & passing (`php artisan test --compact`)
- [ ] Code formatted (`vendor/bin/pint --dirty`)
- [ ] Migrations created (jika ada schema changes)
- [ ] Documentation updated
- [ ] No breaking changes (atau documented clearly)
- [ ] Bilingual UI labels (via `FilamentUi`)

---

## 📋 Requirements

### System Requirements

```
- PHP 8.4+
- Composer 2.4+
- Node.js 18+ (untuk frontend bundling)
- MySQL 8.0+ atau PostgreSQL 12+
- Redis 6.0+ (untuk caching & queue, optional)
```

### PHP Extensions

```
- BCMath
- Ctype
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
```

---

## 📄 Lisensi

FoundationOS di-license di bawah [MIT License](./LICENSE).

---

## 💬 Support & Community

Untuk bantuan & pertanyaan:

- 📧 **Email**: support@foundationos.com
- 🐛 **Issue Tracker**: [GitHub Issues](https://github.com/yusufbayuw/foundationOS/issues)
- 📚 **Documentation**: [Lihat ROADMAP.md](./ROADMAP.md) untuk development updates
- 💡 **Feature Requests**: [Diskusi komunitas](https://github.com/yusufbayuw/foundationOS/discussions)

---

## 🎓 Learning Resources

### Getting Started

1. Baca [Quick Start](#-quick-start) di atas
2. Jalankan aplikasi lokal dengan `composer run dev`
3. Login ke panel admin di `/admin`
4. Explore modules & features di Filament UI

### Deep Dive

- **Module Development**: Baca struktur di `Modules/Core/` sebagai contoh
- **Filament Resources**: Check `Modules/School/app/Filament/Resources/`
- **Workflow**: Lihat [WORKFLOW.md](./WORKFLOW.md) untuk automation
- **Testing**: Review `tests/Feature/` untuk test examples

---

## 🙏 Acknowledgments

FoundationOS dibangun dengan teknologi terbaik:

- [Laravel](https://laravel.com/) - PHP Framework
- [Filament](https://filamentphp.com/) - Admin Panel
- [Livewire](https://livewire.laravel.com/) - Reactive Components
- [Tailwind CSS](https://tailwindcss.com/) - Styling
- [Spatie](https://spatie.be/) - Permission & Activity Logging Packages
- [coolsam/modules](https://github.com/coolsam/modules) - Module System

---

**Last Updated**: 2026-05-23 | **Version**: Comprehensive v1.0

