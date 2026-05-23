# ONBOARDING.md — Panduan Konfigurasi & Penggunaan FoundationOS

> 📌 **Dokumen ini adalah panduan lengkap untuk mengkonfigurasi dan menggunakan FoundationOS setelah deployment.**
> Untuk panduan production deployment, lihat [SETUP.md](./SETUP.md).

---

## 📋 Daftar Isi

1. [First-Time Tenant Setup](#-first-time-tenant-setup)
2. [User Management](#-user-management)
3. [Organization Structure](#-organization-structure)
4. [Academic Configuration](#-academic-configuration)
5. [Module Activation](#-module-activation)
6. [Common Workflows](#-common-workflows)
7. [Financial Configuration](#-financial-configuration)
8. [Procurement Setup](#-procurement-setup)
9. [Library Setup](#-library-setup)
10. [Moodle Integration](#-moodle-integration)
11. [Bilingual UI Configuration](#-bilingual-ui-configuration)
12. [Security & Compliance](#-security--compliance)
13. [Common Tasks & Troubleshooting](#-common-tasks--troubleshooting)
14. [Best Practices](#-best-practices)

---

## 🎯 First-Time Tenant Setup

### 1. Login ke Admin Panel

```
URL: https://app.institution.edu/admin
```

Jika ini fresh installation, gunakan super admin credentials yang dibuat saat setup:

```bash
php artisan make:super-admin
# Follow prompts untuk create super admin user
```

### 2. Create Tenant (Institusi)

**Navigation**: Core → Tenants → Create

**Fields to fill**:
- **Name**: Nama institusi (misal: "Universitas ABC")
- **Domain**: Domain untuk tenant (misal: "abc")
- **Slug**: URL-friendly identifier (auto-generated)
- **Description**: Deskripsi institusi

```
Contoh:
Name:        Universitas ABC
Domain:      abc
Slug:        universitas-abc
Description: Universitas ABC adalah institusi pendidikan terkemuka
```

### 3. Create Academic Year

**Navigation**: Core → Academic Years → Create

**Fields**:
- **Tenant**: Pilih tenant yang baru dibuat
- **Year**: Tahun akademik (misal: 2024)
- **Name**: Nama tahun akademik (misal: "2024/2025")
- **Start Date**: Tanggal mulai (misal: 2024-01-01)
- **End Date**: Tanggal akhir (misal: 2024-12-31)
- **Is Active**: Check untuk set sebagai active year

### 4. Assign Super Admin to Tenant

**Navigation**: Core → Users → Select User → Edit

Pilih user yang menjadi super admin untuk tenant tersebut:
- **User Tenant Roles**: Add role
- **Organization**: Kosongkan (untuk tenant-wide access)
- **Tenant**: Pilih tenant
- **Role**: Admin atau Super Admin

### 5. Configure Tenant Settings

**Navigation**: Core → Tenant Settings

**Key settings**:
- **Default Locale**: id (Indonesia) atau en (English)
- **Timezone**: Asia/Jakarta
- **Date Format**: DD/MM/YYYY
- **Currency**: IDR (Indonesian Rupiah)
- **Logo**: Upload logo institusi
- **Color Scheme**: Warna brand institusi

---

## 👥 User Management

### Creating Users

**Navigation**: Core → Users → Create

**Required Fields**:
```
Name:            Nama lengkap user
Email:           Email unik user
Password:        Password yang kuat
Status:          Active/Inactive
```

**Optional Fields**:
```
Phone:           Nomor telepon
Gender:          Pilih jenis kelamin
Date of Birth:   Tanggal lahir
Avatar:          Foto profil
```

### Assigning User to Tenant

Setiap user yang ingin akses tenant harus di-assign dulu:

**Navigation**: Core → User Tenant Roles → Create

**Fields**:
```
User:           Pilih user
Tenant:         Pilih tenant
Organization:   Kosongkan untuk tenant-wide, atau pilih specific org
Role:           Admin, Manager, Staff, Student, dll (sesuai kebutuhan)
```

### User Roles & Permissions

Roles di-setup via Filament Shield:

**Default Roles**:
- **Super Admin**: Full access ke semua fitur & tenants
- **Admin**: Full access dalam tenant tertentu
- **Manager**: Manager-level access (bisa approve workflows)
- **Staff**: Operational staff access
- **Student**: Limited access (hanya data diri & nilai)

**Setup Permissions**:

```bash
# Regenerate permissions after adding new resources
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction

# Assign role ke user
php artisan shield:super-admin --user=1 --tenant=1 --panel=admin
```

### User Deactivation

**Navigation**: Core → Users → Select User → Edit

- **Status**: Change to "Inactive"
- User tidak bisa login lagi
- Data tidak dihapus (tetap tersimpan untuk audit)

---

## 🏢 Organization Structure

### Creating Organizations (Departments/Units)

**Navigation**: Core → Organizations → Create

**Fields**:
```
Tenant:           Pilih tenant
Name:             Nama organisasi (misal: "Fakultas Teknik")
Code:             Kode organisasi (misal: "FT")
Type:             Pilih tipe (Faculty, Department, Office, dll)
Parent:           Parent organization (kosongkan jika top-level)
Is Active:        Check untuk active
```

### Department Setup (Subtree)

Create sub-organizations (departments dalam faculty):

```
Faculty: Fakultas Teknik
├─ Department: Teknik Informatika
│  ├─ Program: D3 Informatika
│  └─ Program: S1 Informatika
├─ Department: Teknik Elektro
│  └─ Program: S1 Teknik Elektro
└─ Department: Teknik Sipil
   └─ Program: S1 Teknik Sipil
```

### Role Hierarchy

**Navigation**: Core → Tenant Roles → Assign to Organization

Assign roles ke specific organizations:
- Dekan untuk Faculty
- Ketua Program untuk Program Studi
- Koordinator untuk specific departments

---

## 🎓 Academic Configuration

### Setup K-12 School (School Module)

#### 1. Create School

**Navigation**: School → Schools → Create

```
Name:            Nama sekolah
Code:            Kode sekolah
Type:            Elementary, Middle, High School
Address:         Alamat lengkap
Phone:           Nomor telepon
Email:           Email sekolah
Organization:    Link ke organization structure
```

#### 2. Create Curriculum

**Navigation**: School → Curricula → Create

```
School:          Pilih school
Name:            Nama kurikulum (misal: "Kurikulum Nasional 2024")
Code:            Kode kurikulum
Year:            Tahun implementasi
Is Active:       Check untuk active
```

#### 3. Create Subjects

**Navigation**: School → Subjects → Create

```
Curriculum:      Pilih curriculum
Name:            Nama mata pelajaran
Code:            Kode mapel
Description:     Deskripsi mapel
Credit Hours:    Jumlah jam (untuk high school)
```

#### 4. Create Classes

**Navigation**: School → Classes → Create

```
School:          Pilih school
Name:            Nama kelas (misal: "10A")
Grade:           Tingkat kelas
Year:            Tahun akademik
Max Students:    Maksimal siswa (misal: 30)
Homeroom Teacher: Pilih guru
```

#### 5. Assign Students & Teachers

**Class Detail → Students Tab**: Add students
**Class Detail → Teachers Tab**: Add subject teachers

### Setup Higher Ed (Campus Module)

#### 1. Create Faculty

**Navigation**: Campus → Faculties → Create

```
Name:            Nama fakultas
Code:            Kode fakultas
Organization:    Link ke organizational structure
Is Active:       Check untuk active
```

#### 2. Create Study Programs

**Navigation**: Campus → Study Programs → Create

```
Faculty:         Pilih faculty
Name:            Nama program studi
Code:            Kode prodi
Degree Level:    D3, S1, S2, S3
Credits:         Total SKS/credits
```

#### 3. Create Courses

**Navigation**: Campus → Courses → Create

```
Study Program:   Pilih prodi
Name:            Nama mata kuliah
Code:            Kode matakuliah
Credits:         Jumlah SKS
Semester:        Semester tempat diajarkan
```

#### 4. Create Study Plans (Jadwal Kuliah)

**Navigation**: Campus → Study Plans → Create

```
Semester:        Pilih semester
Course:          Pilih mata kuliah
Lecturer:        Pilih dosen pengajar
Day:             Hari (Senin, Selasa, dll)
Start Time:      Jam mulai
End Time:        Jam selesai
Room:            Ruang kelas
Capacity:        Kapasitas ruangan
```

---

## 🔧 Module Activation

### Enabling Modules

Modules sudah di-install di production, tapi perlu di-enable untuk tenant:

**Navigation**: Core → Tenant Modules → Create

```
Tenant:          Pilih tenant
Module:          Pilih module dari list
Is Active:       Check untuk activate
Settings:        Module-specific configuration (opsional)
```

### Commonly Used Modules

| Module | Use Case | Priority |
|--------|----------|----------|
| School | K-12 education management | High |
| Campus | Higher education management | High |
| Finance | Accounting & budgeting | High |
| Enrollment | Student admissions | High |
| Employee | HR & payroll | Medium |
| Procurement | Purchase management | Medium |
| Library | Library management | Medium |
| Workflow | Approval workflows | Medium |
| Moodle Integration | LMS integration | Low |

### Module-Specific Setup

Setiap module butuh konfigurasi awal:

**Finance Module**:
- Setup chart of accounts
- Create budget categories
- Configure payment methods

**Procurement Module**:
- Setup vendor catalog
- Create approval workflows
- Set purchasing limits

**Library Module**:
- Import book catalog
- Setup member types
- Configure loan periods

---

## 🔄 Common Workflows

### Enrollment Workflow (Student Registration)

**Step-by-step**:

1. **Create Admission Period**
   - Navigation: Enrollment → Admission Periods → Create
   - Set start/end dates untuk enrollment period

2. **Create Applicants**
   - Students apply online atau admin create manually
   - Fill personal data, educational background

3. **Process Applications**
   - Review applications
   - Approve/reject based on criteria
   - Send acceptance letters

4. **Register Students**
   - Accepted applicants create payment
   - Pay registration fee
   - Confirm enrollment status

5. **Assign to Classes**
   - Navigation: School → Class → Students → Add Student
   - Assign enrolled students ke classes

### Approval Workflow (Purchase Requisition)

**Setup Workflow**:

**Navigation**: Workflow → Workflows → Create

```
Name:           "Purchase Approval"
Tenant:         Pilih tenant
Type:           Approval workflow
Start Event:    Purchase Requisition Created
```

**Define Steps**:

1. **Manager Review** (1-2 hari)
   - Condition: Amount < 10 juta IDR
   - Action: Auto-approve

2. **Finance Review** (2-3 hari)
   - Condition: Amount >= 10 juta IDR
   - Action: Hold untuk approval finance

3. **Executive Approval** (1 hari)
   - Condition: Amount >= 50 juta IDR
   - Action: Escalate ke executive director

4. **Procurement Create PO**
   - Action: Auto-create purchase order
   - Notify: Procurement team

### Grade Input Workflow

1. **Teacher Input Grades**
   - Navigation: School → Assessments → Create
   - Input nilai per siswa

2. **Homeroom Teacher Verify**
   - Verify dan sign-off grades

3. **School Admin Approve**
   - Final approval & archive

4. **Student Access Grades**
   - Students dapat lihat nilai mereka

---

## 💰 Financial Configuration

### Setup Chart of Accounts

**Navigation**: Finance → Chart of Accounts → Create

**Account Structure**:

```
Assets (100000)
├─ Current Assets (110000)
│  ├─ Bank Accounts (111000)
│  ├─ Cash (112000)
│  └─ Receivables (113000)
├─ Fixed Assets (120000)
│  └─ Property & Equipment (121000)

Liabilities (200000)
├─ Current Liabilities (210000)
├─ Long-term Liabilities (220000)

Equity (300000)

Income (400000)
├─ Tuition Fees (410000)
├─ Grants & Donations (420000)
├─ Service Revenue (430000)

Expenses (500000)
├─ Salaries & Benefits (510000)
├─ Operations & Maintenance (520000)
├─ Supplies & Materials (530000)
```

### Create Student Invoices

**Navigation**: Finance → Student Invoices → Create

```
Tenant:          Pilih tenant
Invoice Type:    Tuition, Service Charge, dll
Students:        Select students atau import from file
Amount:          Jumlah biaya per student
Due Date:        Tanggal jatuh tempo
Terms:           Payment terms (Net 30, dll)
```

### Student Payments

**Navigation**: Finance → Payments

Students dapat upload payment proof atau admin record manual payments:

```
Invoice:         Pilih invoice
Amount:          Jumlah dibayar
Payment Method:  Bank Transfer, Cash, E-wallet, dll
Proof:           Upload bukti transfer
Status:          Pending → Approved → Completed
```

---

## 🛒 Procurement Setup

### Create Vendors

**Navigation**: Procurement → Vendors → Create

```
Name:            Nama vendor/supplier
Contact Person:  Nama PIC
Email:           Email vendor
Phone:           Nomor telepon
Address:         Alamat lengkap
Bank Account:    Rekening bank untuk pembayaran
Tax ID:          NPWP/Tax ID vendor
Terms:           Payment terms (Net 30, dll)
Is Active:       Check untuk active vendor
```

### Create Procurement Categories

**Navigation**: Procurement → Categories → Create

```
Name:            Kategori barang/jasa
Code:            Kode kategori
Description:     Deskripsi
Budget Limit:    Batas anggaran per kategori
Approval Level:  Siapa yang harus approve
```

### Create Purchase Requisition

**Navigation**: Procurement → Purchase Requisitions → Create

```
Requisition Type:    Regular, Urgent, Emergency
Supplier:            Pilih vendor (atau leave open untuk RFQ)
Items:               Add items needed
- Item Name
- Quantity
- Unit Price
- Total Amount

Requested By:        Auto-filled (current user)
Needed By Date:      Tanggal dibutuhkan
Budget Category:     Pilih category
Notes:               Special requests
```

### Approval Process

Workflow otomatis trigger:

1. Manager review & approve
2. Finance review & approve (jika amount besar)
3. Executive approve (jika amount sangat besar)
4. Procurement create PO

---

## 📖 Library Setup

### Import Book Catalog

**Navigation**: Library → Books → Import Data

**Option 1: Manual Entry**
```
Title:           Judul buku
Author:          Pengarang
Publisher:       Penerbit
ISBN:            ISBN number
Publication Year: Tahun terbit
Category:        Kategori buku (Fiction, Reference, dll)
```

**Option 2: SLIMS Integration**

Jika sudah menggunakan SLIMS (Senayan Library Management System):

1. Setup SLIMS connection di Admin Settings
2. Sync books dari SLIMS ke FoundationOS
3. Update stock di kedua sistem secara otomatis

### Create Book Copies

**Navigation**: Library → Book Copies → Create

```
Book:            Pilih buku
Barcode:         Nomor barcode unik
Condition:       Good, Fair, Poor
Location:        Lokasi di perpustakaan
Status:          Available, Damaged, Lost, dll
Acquisition Date: Tanggal pembelian
Acquisition Cost: Harga pembelian
```

### Member Setup

**Navigation**: Library → Members → Create

```
Member Type:     Student, Faculty, Staff, External
ID Number:       ID unik (NIM, NIP, dll)
Name:            Nama member
Email:           Email address
Phone:           Nomor telepon
Membership Date:  Tanggal bergabung
Expiry Date:     Masa berlaku membership
Max Items:       Jumlah maksimal pinjam
Loan Period:     Durasi peminjaman (hari)
Fine Rate:       Denda per hari (IDR)
```

### Configure Loan Rules

**Navigation**: Library → Settings → Loan Configuration

```
Default Loan Period:    14 hari
Max Renewals:           2 kali
Renewal Period:         7 hari
Overdue Fine:           IDR 5,000 per hari
Max Fine:               IDR 100,000 max per item
Late Return Policy:     Block new loans jika ada overdue
```

---

## 🔗 Moodle Integration

### Prerequisites

- Moodle instance sudah running
- Web Services enabled di Moodle
- API token dari Moodle sudah generate

### Setup API Connection

**Navigation**: Settings → Integrations → Moodle

```
Moodle URL:      https://moodle.institution.edu
API Token:       [Generate dari Moodle Admin Panel]
Service URL:     https://moodle.institution.edu/webservice/rest/server.php
```

### Configure Data Sync

**Sync Settings**:

```
Sync Users:           Enable (sinkronisasi user)
Sync Courses:         Enable (sinkronisasi course)
Sync Enrollments:     Enable (sinkronisasi enrollment)
Sync Grades:          Enable (sinkronisasi nilai)
Sync Attendance:      Enable (sinkronisasi absensi)

Sync Direction:       FoundationOS → Moodle (one-way)
Auto Sync Schedule:   Daily at 2:00 AM
```

### Category Mapping

**Navigation**: Moodle Integration → Category Mapping

Map FoundationOS organizations ke Moodle course categories:

```
Organization:      Fakultas Teknik
Moodle Category:   Engineering
Auto Create:       Yes (buat course category otomatis)
```

### Test Sync

```bash
# Test Moodle connection
php artisan tinker
>>> Modules\Campus\Models\Moodle::testConnection()
=> true

# Trigger manual sync
php artisan moodle:sync-cohorts
php artisan moodle:pull-grades
```

---

## 🌍 Bilingual UI Configuration

### Set Default Language

**Navigation**: Core → Tenant Settings

```
Default Locale:  id (Indonesia) atau en (English)
```

### User Language Preference

Users dapat mengubah bahasa di user menu (avatar kanan atas):

- 🇮🇩 Indonesia
- 🇬🇧 English

Preference disimpan per-user & persist across sessions.

### Common UI Terms

**Indonesian → English Translation**:

```
Dashboard       = Home
Daftar          = List
Buat            = Create
Edit            = Edit
Hapus           = Delete
Simpan          = Save
Batalkan        = Cancel
Cari            = Search
Filter          = Filter
Ekspor          = Export
Impor           = Import
Pengaturan      = Settings
```

---

## 🔒 Security & Compliance

### Password Policy

**Navigation**: Core → Settings → Security

```
Minimum Length:           8 characters
Require Uppercase:        Yes
Require Numbers:          Yes
Require Special Chars:    Yes
Expiry Days:              90 days
History Check:            Cannot reuse last 5 passwords
```

### Two-Factor Authentication (if available)

Enable 2FA untuk user admin:

**Navigation**: User Profile → Security → Enable 2FA

```
Method:          TOTP (Google Authenticator)
atau             SMS OTP
```

### Audit Logs

Semua user actions tercatat di audit log:

**Navigation**: Monitoring → Audit Logs

```
View by:         Date Range
Filter by:       User, Action, Model
See:             Who did what, when, and from where
```

### Data Access Controls

Setup role-based access:

- Admin: Full access
- Manager: Can view all departments
- Staff: Can view own department only
- Student: Can view own data only

Enforce di Authorization Policies.

---

## ❓ Common Tasks & Troubleshooting

### Task: Import Student Data from Excel

**Steps**:

1. Prepare Excel file dengan columns:
   ```
   Name, Email, Phone, Date of Birth, Student ID
   ```

2. Navigate to: School → Students → Import Data
3. Select file & map columns
4. Preview & confirm
5. System auto-generate passwords

### Task: Generate Class Attendance Sheet

**Steps**:

1. Navigate to: School → Classes → Select Class
2. Click "Generate Attendance"
3. System creates printable attendance sheet
4. Teacher fills during class
5. Upload back to system

### Task: Create Billing Report

**Steps**:

1. Navigate to: Finance → Reports → Billing
2. Select date range & student(s)
3. Click "Generate Report"
4. Export as PDF atau Excel

### Troubleshooting: User Cannot Login

**Checklist**:

1. Is user account **Active**? Check Core → Users
2. Is user **assigned to tenant**? Check User Tenant Roles
3. Has user accepted **terms & conditions**? Ask to reset password
4. Is email **verified**? Check user status
5. Is there **active session** limit exceeded?

**Solution**:

```bash
# Reset user password via admin
php artisan tinker
>>> User::find(USER_ID)->update(['password' => Hash::make('new_password')])
```

### Troubleshooting: Workflow Not Processing

**Checklist**:

1. Is workflow **Active**? Check Workflow → Workflows
2. Is queue worker **running**? Check `sudo supervisorctl status`
3. Is **Redis connection** working? `redis-cli ping`
4. Are **transition rules** correctly configured?

**Solution**:

```bash
# Restart queue workers
sudo supervisorctl restart foundationos-queue:*

# Test workflow manually
php artisan tinker
>>> Modules\Workflow\Services\WorkflowEngine::advance(WORKFLOW_INSTANCE_ID)
```

### Troubleshooting: File Upload Fails

**Checklist**:

1. Is **storage directory** writable? `ls -l storage/`
2. Is **max upload size** configured? Check nginx/php limits
3. Is **storage linked**? Check if symlink exists `ls -l public/storage`

**Solution**:

```bash
# Fix permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/

# Relink storage
php artisan storage:link

# Increase upload limits in nginx
client_max_body_size 100M;
```

---

## 💡 Best Practices

### 1. Regular Backups

- Daily database backups (kept 7 days)
- Weekly file backups
- Monthly full system backup (kept 3 months)
- Test recovery procedure monthly

### 2. User Access Management

- Use strong passwords (minimal 12 chars)
- Rotate admin passwords quarterly
- Limit super admin users to minimum
- Review user access logs monthly
- Deactivate inactive accounts

### 3. Data Security

- Encrypt sensitive data (SSN, ID numbers)
- Limit data export permissions
- Audit all data access via audit logs
- Use VPN for remote administration
- Never share admin credentials

### 4. System Maintenance

- Update Laravel & packages monthly
- Update OS security patches immediately
- Monitor disk space (keep < 80% full)
- Monitor database size (vacuum weekly)
- Review error logs daily

### 5. Performance Monitoring

- Monitor page load time (target < 2s)
- Monitor database query time
- Check Redis memory usage
- Monitor server CPU & RAM
- Check queue processing time

### 6. User Training

- Train admins on core workflows
- Train staff on their specific modules
- Provide user documentation
- Have FAQ for common issues
- Schedule refresher training quarterly

### 7. Workflow Automation

- Automate repetitive tasks
- Use workflows for approvals
- Setup email notifications
- Log all important actions
- Review automation quarterly

### 8. Reporting & Analytics

- Generate monthly reports
- Monitor KPIs (enrollment, payments, etc)
- Track system usage (login stats, etc)
- Document trends & changes
- Present to stakeholders

### 9. Change Management

- Test changes in staging first
- Document all configuration changes
- Keep rollback procedure ready
- Notify users before big changes
- Have maintenance window policy

### 10. Disaster Recovery

- Have recovery procedures documented
- Test backup restoration monthly
- Keep critical contact numbers
- Have communication plan for outages
- Document lessons learned

---

## 📞 Support & Resources

- 📖 [README.md](./README.md) - Platform overview
- 📖 [SETUP.md](./SETUP.md) - Production deployment
- 📖 [WORKFLOW.md](./WORKFLOW.md) - Workflow V2 engine
- 📖 [MOODLE.md](./MOODLE.md) - Moodle integration
- 📖 [ROADMAP.md](./ROADMAP.md) - Development roadmap

---

**Last Updated**: 2026-05-23
**Version**: Configuration v1.0
**Status**: ✅ Ready to use
