# Workflow V2

Panduan ini menjelaskan cara kerja modul Workflow V2 di FoundationOS, cara setup permission per tenant, cara membuat workflow, dan cara menjalankan pilot Procurement berbasis nominal approval.

## Gambaran Arsitektur

Workflow V2 memakai pola metadata-driven:

- definisi workflow disimpan di database
- rule dievaluasi oleh `RuleEngine`
- form runtime dibangun dari `form_schema`
- inbox/worklist dipisahkan dari engine
- instance selalu menyimpan snapshot versi workflow saat start

Alur utama:

```text
Workflow Definition
-> Steps
-> Transitions
-> Automated Actions
-> Workflow Instance
-> Assignments
-> Logs
```

Komponen penting:

- `WorkflowResolver`
- `WorkflowInstanceStarter`
- `WorkflowEngine`
- `RuleEngine`
- `WorkflowAutomatedActionRunner`

Source code utama:

- [Modules/Workflow/app/Services/DatabaseWorkflowResolver.php](/Users/yusuf/Herd/foundationOS/Modules/Workflow/app/Services/DatabaseWorkflowResolver.php)
- [Modules/Workflow/app/Services/DatabaseWorkflowInstanceStarter.php](/Users/yusuf/Herd/foundationOS/Modules/Workflow/app/Services/DatabaseWorkflowInstanceStarter.php)
- [Modules/Workflow/app/Services/DatabaseWorkflowEngine.php](/Users/yusuf/Herd/foundationOS/Modules/Workflow/app/Services/DatabaseWorkflowEngine.php)
- [Modules/Workflow/app/Services/JsonLogicRuleEngine.php](/Users/yusuf/Herd/foundationOS/Modules/Workflow/app/Services/JsonLogicRuleEngine.php)

## Konsep Scope

Workflow di FOS bersifat:

- `tenant-first`
- `organization-optional`

Aturannya:

- `tenant_id` wajib untuk definisi dan instance
- `organization_id = null` berarti workflow tenant-wide
- resolver memilih workflow paling spesifik:
  - tenant + organization exact match
  - fallback ke tenant-wide

## Shield Per Tenant

Authorization modul Workflow mengikuti Shield sebagai single source of truth.

Langkah minimal:

```bash
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
```

Kemudian assign role/permission dalam konteks tenant aktif.

Permission minimum yang biasanya dibutuhkan:

- `ViewAny:Workflow`
- `View:Workflow`
- `Create:Workflow`
- `Update:Workflow`
- `Delete:Workflow`
- `ViewAny:WorkflowInstance`
- page permission untuk:
  - `WorkflowInboxPage`
  - `WorkflowMyTasksPage`
  - `WorkflowTeamInboxPage`
  - `WorkflowTaskHistoryPage`

Untuk health check definisi aktif:

```bash
php artisan fos:workflow:health-check --tenant=1
```

Catatan penting:

- `user_tenant_roles` tetap mengatur membership tenant/organization
- Shield mengatur apa yang boleh dilakukan user di panel

## Command Operasional

Health check:

```bash
php artisan fos:workflow:health-check --tenant=1
```

Retry SLA checks:

```bash
php artisan fos:workflow:retry-sla --tenant=1
```

Retry automated actions manual:

```bash
php artisan fos:workflow:retry-automation 123 completed
```

## Cara Membuat Workflow via UI

Masuk ke modul `Workflow` di sidebar, lalu:

1. buat `Workflow`
2. isi scope:
   - tenant
   - organization bila perlu
   - module
   - subject type
3. tambahkan `Steps`
4. tambahkan `Transitions`
5. tambahkan `Automated Actions` bila dibutuhkan
6. publish workflow

### Step

Isi minimal:

- `code`
- `name`
- `step_type`
- `assignee_type`
- `assignee_value`
- `sort_order`

`form_schema` dipakai untuk membentuk form runtime. V2 mendukung metadata:

- `required`
- `validation`
- `visibility_rules`
- `disabled_rules`
- `required_rules`
- `default_value`
- `placeholder`
- `help_text`
- `column_span`

Perilaku runtime:

- field hidden tidak dirender ke approver
- field disabled tidak diterima sebagai overwrite dari client
- validasi backend tetap menjadi final authority

Contoh field:

```json
{
  "name": "approval_note",
  "label": "Approval Note",
  "type": "textarea",
  "required": true,
  "validation": ["min:10"],
  "placeholder": "Explain the decision.",
  "help_text": "This note is stored in the workflow log.",
  "column_span": "full"
}
```

### Transition

Transition memakai `JsonLogic`.

Contoh:

```json
{
  ">=": [
    { "var": "total_estimated_amount" },
    10000000
  ]
}
```

Artinya: jika `total_estimated_amount >= 10000000`, pindah ke step berikutnya.

### Automated Actions

V1.5 mendukung:

- `internal_notification`
- `audit_note`
- `dispatch_job`
- `set_computed_data`

Automated action dijalankan dari snapshot workflow instance, jadi perubahan definisi setelah instance start tidak mengubah perilaku instance yang sedang berjalan.

## Halaman Operasional

Halaman yang tersedia:

- `Workflow Worklist`
- `Workflow My Tasks`
- `Workflow Team Inbox`
- `Workflow Task History`

Fungsi:

- `Workflow Worklist`: ringkasan tugas user dengan filter module/workflow/SLA
- `Workflow My Tasks`: fokus ke tugas user sendiri
- `Workflow Team Inbox`: pending assignments lintas user dalam tenant aktif
- `Workflow Task History`: histori assignment yang selesai/dibatalkan

Guardrails operasional:

- `Workflow Team Inbox` dibatasi oleh tenant aktif
- untuk user non-global-super-admin, hasilnya juga dibatasi ke organization membership yang dimiliki user di tenant tersebut
- semua page tetap mengikuti Shield sebagai source of truth authorization

## Tutorial Pilot Procurement

Pilot pertama memakai `Purchase Requisition`.

Subject model:

- [PurchaseRequisition.php](/Users/yusuf/Herd/foundationOS/Modules/Procurement/app/Models/PurchaseRequisition.php)

### Opsi 1: Setup Cepat dengan Command

Gunakan command berikut untuk membuat workflow approval Procurement default:

```bash
php artisan fos:workflow:setup-procurement-pilot 1 --manager=10 --finance=11 --executive=12 --finance-threshold=10000000 --executive-threshold=50000000
```

Keterangan:

- `1` = tenant ID
- `--manager` = approver tahap 1
- `--finance` = approver tahap 2
- `--executive` = approver tahap 3 opsional
- di bawah `finance-threshold`, approval selesai di manager
- di atas `finance-threshold`, masuk ke finance
- di atas `executive-threshold`, lanjut ke executive

Jika ingin mengganti versi workflow yang sudah ada:

```bash
php artisan fos:workflow:setup-procurement-pilot 1 --manager=10 --finance=11 --executive=12 --replace
```

Command ini sekarang memvalidasi bahwa approver yang dipilih benar-benar member tenant dan, bila dipakai, organization scope yang dimaksud.

## Tutorial Budget Approval

Subject model:

- `Modules\Finance\Models\Budget`

Setup default budget approval workflow:

```bash
php artisan fos:workflow:setup-budget-workflow 1 --organization=5 --finance=11 --executive=12 --executive-threshold=50000000
```

Aturan default:

- `allocated_amount < executive-threshold` -> selesai di finance approver
- `allocated_amount >= executive-threshold` -> finance approve -> executive approve

Di halaman view Budget tersedia tombol:

- `Start Approval Workflow`
- `Open Active Workflow`

### Opsi 2: Manual via UI

Workflow code yang direkomendasikan:

- `purchase-requisition-approval`

Subject type:

- `Modules\Procurement\Models\PurchaseRequisition`

Step yang direkomendasikan:

1. `manager_approval`
2. `finance_approval`
3. `executive_approval`
4. `approved`

Contoh branching:

- manager approve + amount `< 10000000` -> `approved`
- manager approve + amount `>= 10000000` -> `finance_approval`
- finance approve + amount `< 50000000` -> `approved`
- finance approve + amount `>= 50000000` -> `executive_approval`
- executive approve -> `approved`
- reject dari step mana pun -> instance `rejected`

### Menjalankan dari Purchase Requisition

Di halaman view Purchase Requisition tersedia tombol:

- `Start Approval Workflow`

Setelah start:

- status PR berubah menjadi `submitted`
- assignment dibuat untuk approver step pertama
- approver memproses lewat halaman workflow instance atau worklist

Saat approval berjalan:

- step non-terminal mengubah status subject menjadi `in_review`
- complete mengubah subject menjadi `approved`
- reject mengubah subject menjadi `rejected`
- cancel mengubah subject menjadi `cancelled`

## Testing

Test utama:

- [WorkflowEngineTest.php](/Users/yusuf/Herd/foundationOS/tests/Feature/WorkflowEngineTest.php)
- [WorkflowDefinitionLifecycleTest.php](/Users/yusuf/Herd/foundationOS/tests/Feature/WorkflowDefinitionLifecycleTest.php)
- [WorkflowRuleEngineTest.php](/Users/yusuf/Herd/foundationOS/tests/Feature/WorkflowRuleEngineTest.php)
- [WorkflowProcurementPilotTest.php](/Users/yusuf/Herd/foundationOS/tests/Feature/WorkflowProcurementPilotTest.php)

Jalankan:

```bash
php artisan test tests/Feature/WorkflowEngineTest.php tests/Feature/WorkflowDefinitionLifecycleTest.php tests/Feature/WorkflowRuleEngineTest.php tests/Feature/WorkflowProcurementPilotTest.php
```

## Troubleshooting

### Workflow tidak muncul di sidebar

Periksa:

- modul `Workflow` aktif
- cache sudah dibersihkan
- permission Shield untuk page/resource Workflow sudah tergenerate
- user punya role/permission di tenant aktif

Command yang aman dijalankan:

```bash
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
php artisan optimize:clear
```

### Start Approval Workflow tidak muncul di PR

Periksa:

- belum ada workflow instance `running` untuk PR tersebut
- workflow active untuk tenant aktif memang ada
- workflow `subject_type` sesuai dengan `Modules\Procurement\Models\PurchaseRequisition`

### Approval tidak bercabang sesuai amount

Periksa:

- `condition_rules` transition valid JsonLogic
- field yang dipakai rule tersedia di `context_data`
- threshold pada command/UI sesuai kebutuhan

## Batasan Saat Ini

Yang sudah ada:

- linear + branching approval
- parallel gateway & quorum approval
- visual workflow designer (canvas + draft/versioning + JSON import/export)
- dynamic form `options_source` (eloquent + enum; endpoint kind belum)
- tenant scope & workflow snapshot deterministik (transisi dari snapshot instance)
- worklist & automated actions dasar
- pilot Procurement

Yang belum penuh:

- `options_source` kind `endpoint` (internal API)
- editing lock multi-admin pada designer (opsional)
- supervisor delegation flow yang lebih kaya
