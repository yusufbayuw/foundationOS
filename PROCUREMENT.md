# Procurement Approval Runbook

Panduan ini merangkum alur approval Procurement tenant-first di FoundationOS, dengan fokus pada `Purchase Requisition` sebagai vertical slice operasional pertama yang sudah dihubungkan ke Workflow V2.

## Scope

Flow yang dicakup saat ini:

- `Purchase Requisition`
- approval berbasis nominal
- worklist/inbox approver
- audit trail dan workflow history
- handoff status ke proses RFQ/PO berikutnya

Target perilaku produksi:

- tenant-safe
- organization-aware bila dibutuhkan
- assignment approver tervalidasi terhadap membership tenant
- approval dapat diproses dari halaman subject maupun halaman workflow

## Arsitektur Ringkas

Komponen utama:

- subject: [PurchaseRequisition.php](/Users/yusuf/Herd/foundationOS/Modules/Procurement/app/Models/PurchaseRequisition.php)
- workflow setup command: [SetupProcurementWorkflowPilotCommand.php](/Users/yusuf/Herd/foundationOS/app/Console/Commands/SetupProcurementWorkflowPilotCommand.php)
- subject state sync: [SyncWorkflowSubjectState.php](/Users/yusuf/Herd/foundationOS/Modules/Workflow/app/Listeners/SyncWorkflowSubjectState.php)
- PR view page: [ViewPurchaseRequisition.php](/Users/yusuf/Herd/foundationOS/Modules/Procurement/app/Filament/Resources/PurchaseRequisitions/Pages/ViewPurchaseRequisition.php)

## State Map Purchase Requisition

Status subject yang dipakai saat ini:

- `draft`: PR baru, belum masuk workflow
- `submitted`: workflow baru dimulai
- `in_review`: sedang berada di step approval non-terminal
- `revision_required`: dikembalikan untuk revisi
- `approved`: workflow selesai dan approval final tercapai
- `rejected`: workflow ditolak
- `cancelled`: workflow dibatalkan

Makna handoff:

- `approved` berarti PR siap dipakai sebagai dasar proses RFQ/PO berikutnya
- `ready_for_sourcing = true` menjadi penanda eksplisit bahwa approval sudah final dan subject siap masuk proses sourcing
- fase ini **belum** otomatis membuat RFQ/PO agar kontrol bisnis tetap eksplisit

## Setup Tenant

### 1. Pastikan membership tenant benar

Sebelum workflow dibuat:

- approver manager harus menjadi member tenant
- approver finance harus menjadi member tenant
- approver executive, bila dipakai, juga harus menjadi member tenant
- bila workflow organization-scoped, approver harus cocok dengan organization itu atau punya scope tenant-wide

`user_tenant_roles` adalah source of truth membership.

### 2. Generate permission Shield

```bash
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
php artisan optimize:clear
```

Pastikan role Shield tenant memiliki akses ke:

- `PurchaseRequisition`
- `Workflow`
- `WorkflowInstance`
- `Workflow Worklist`
- `Workflow My Tasks`
- `Workflow Team Inbox`
- `Workflow Task History`

### 3. Bootstrap workflow default

Contoh tenant-wide:

```bash
php artisan fos:workflow:setup-procurement-pilot 1 --manager=10 --finance=11 --executive=12 --finance-threshold=10000000 --executive-threshold=50000000
```

Contoh organization-scoped:

```bash
php artisan fos:workflow:setup-procurement-pilot 1 --organization=5 --manager=10 --finance=11 --executive=12 --finance-threshold=10000000 --executive-threshold=50000000
```

Kalau ingin mengganti versi lama:

```bash
php artisan fos:workflow:setup-procurement-pilot 1 --manager=10 --finance=11 --replace
```

### 4. Validasi definisi workflow

```bash
php artisan fos:workflow:health-check --tenant=1
```

## Branching Amount

Default pilot saat ini:

- amount `< finance-threshold`:
  - Manager approve -> selesai
- amount `>= finance-threshold`:
  - Manager approve -> Finance
- amount `>= executive-threshold`:
  - Manager -> Finance -> Executive -> selesai

Reject dari step approval mana pun:

- workflow menjadi `rejected`
- PR menjadi `rejected`
- `ready_for_sourcing = false`

Return dari step approval:

- workflow kembali ke target step
- PR menjadi `revision_required`
- `ready_for_sourcing = false`

## Operasional Harian

### Untuk requester

1. Buat `Purchase Requisition`
2. Pastikan nominal dan justifikasi sudah benar
3. Buka halaman view PR
4. Klik `Start Approval Workflow`
5. Pantau histori workflow dari relation manager atau halaman workflow instance aktif

### Untuk approver

Approver bisa bekerja dari:

- `Workflow Worklist`
- `Workflow My Tasks`
- `Workflow Team Inbox` bila diberi akses
- halaman `Workflow Instance`
- halaman `Purchase Requisition` yang menaut ke workflow aktif

Aksi operasional yang tersedia:

- `Approve`
- `Reject`
- `Return`
- `Reassign`
- `Cancel` bila permission dan rule memperbolehkan

## Troubleshooting

### Workflow tidak bisa dimulai

Periksa:

- PR masih `draft` atau status yang memang boleh dimulai
- workflow aktif untuk tenant/organization tersebut ada
- permission Shield user benar
- tenant aktif di panel sudah sesuai

### Setup command gagal karena approver tidak valid

Biasanya artinya user yang dipilih:

- belum menjadi member tenant
- tidak cocok dengan scope organization
- atau memakai user global yang tidak terhubung ke tenant

Perbaiki `user_tenant_roles`, lalu jalankan ulang command.

### Approver tidak melihat task

Periksa:

- user punya membership tenant
- role Shield tenant punya akses ke page workflow
- tenant aktif di panel benar
- assignment memang ditujukan ke user tersebut

### Workflow aktif sudah terlanjur salah

Gunakan pendekatan immutable:

- archive versi lama
- buat versi baru
- publish versi baru

Jangan edit definisi aktif secara langsung bila instance sudah berjalan.
