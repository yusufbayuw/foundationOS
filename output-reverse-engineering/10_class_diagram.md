# 10 — Class Diagram

## Ringkasan Singkat

Diagram kelas untuk komponen domain **inti** yang paling menentukan arsitektur. Tidak mencakup 400+ model Eloquent — lihat `11_er_diagram.md` dan `16_data_dictionary.md`.

---

## Cluster: Multi-Tenancy & User

```mermaid
classDiagram
    class Tenant {
        +uuid
        +code
        +name
        +status
        +isSubscriptionActive()
        +isLocked()
    }

    class User {
        +isGlobalSuperAdmin()
        +canAccessPanel(panel)
    }

    class TenantRole {
        +slug
        +is_super_admin
    }

    class UserTenantRole {
        +tenant_id
        +user_id
        +tenant_role_id
    }

    class Organization {
        +tenant_id
        +name
    }

    Tenant "1" --> "*" Organization : has
    Tenant "1" --> "*" UserTenantRole : scopes
    User "1" --> "*" UserTenantRole : membership
    TenantRole "1" --> "*" UserTenantRole : defines
```

**Bukti:** `Modules/Core/app/Models/Tenant.php`, `User.php`, `TenantRole.php`, `UserTenantRole.php`, `Organization.php`

---

## Cluster: Filament Resource Base

```mermaid
classDiagram
    class ModuleResource {
        +isScopedToTenant()
        +getNavigationLabel()
        +getEloquentQuery()
    }

    class FilamentResource {
        <<Filament>>
    }

    class BelongsToTenant {
        <<trait>>
        +bootBelongsToTenant()
    }

    ModuleResource --|> FilamentResource
    ModuleResource ..> BelongsToTenant : queries models with
```

**Bukti:** `Modules/Core/app/Filament/Support/ModuleResource.php:19-48`

---

## Cluster: Workflow V2

```mermaid
classDiagram
    class WorkflowEngine {
        <<interface>>
        +advance()
        +returnToStep()
        +cancel()
        +reassign()
    }

    class WorkflowResolver {
        <<interface>>
        +resolveForSubject()
    }

    class WorkflowInstanceStarter {
        <<interface>>
        +start()
    }

    class DatabaseWorkflowEngine {
        +advance()
        +advanceParallel()
        +authorizeActor()
    }

    class DatabaseWorkflowResolver {
        +resolveForSubject()
    }

    class DatabaseWorkflowInstanceStarter {
        +start()
        +buildSnapshot()
    }

    class Workflow {
        +tenant_id
        +subject_type
        +status
    }

    class WorkflowInstance {
        +workflow_snapshot
        +context_data
        +current_step_id
        +status
    }

    class WorkflowAssignment {
        +assignee_id
        +status
    }

    WorkflowEngine <|.. DatabaseWorkflowEngine
    WorkflowResolver <|.. DatabaseWorkflowResolver
    WorkflowInstanceStarter <|.. DatabaseWorkflowInstanceStarter
    Workflow "1" --> "*" WorkflowInstance : defines
    WorkflowInstance "1" --> "*" WorkflowAssignment : has
```

**Bukti:**
- Contracts: `Modules/Workflow/app/Contracts/`
- Implementations: `Modules/Workflow/app/Services/DatabaseWorkflow*.php`
- Models: `Modules/Workflow/app/Models/`

---

## Cluster: Moodle Integration

```mermaid
classDiagram
    class MoodleClient {
        +call(function, params)
    }

    class MoodleOutboxService {
        +enqueue(tenant, entity, action, payload)
    }

    class MoodleMapper {
        +mapUser()
        +mapCourse()
    }

    class ProcessMoodleSyncOutboxJob {
        +handle()
    }

    MoodleOutboxService --> ProcessMoodleSyncOutboxJob : dispatches
    ProcessMoodleSyncOutboxJob --> MoodleClient : uses
    ProcessMoodleSyncOutboxJob --> MoodleMapper : uses
```

**Bukti:** `app/Integrations/Moodle/`

---

## Cluster: Finance Controls

```mermaid
classDiagram
    class FinanceControlService {
        +assertInvoiceMutable()
        +verifyPayment()
        +postJournal()
        +recalculateInvoiceStatus()
    }

    class StudentInvoice {
        +status
        +total_amount
    }

    class Payment {
        +status
        +amount
    }

    class JournalEntry {
        +lines
    }

    FinanceControlService ..> StudentInvoice : validates
    FinanceControlService ..> Payment : verifies
    FinanceControlService ..> JournalEntry : posts
```

**Bukti:** `Modules/Finance/app/Services/FinanceControlService.php`

---

## DTO / Repository Pattern

| Pola | Status | Bukti |
|------|--------|-------|
| Repository interface per aggregate | **TIDAK TERDETEKSI** | Tidak ada folder `Repositories/` dominan |
| DTO classes | **PARSIAL** | Array context di workflow; tidak ada namespace DTO konsisten |
| Service layer | TERVERIFIKASI | `Modules/*/Services/` |

---

## Catatan Ketidakpastian

- Diagram tidak menampilkan Policy classes (100+) — pola seragam `*Policy extends BasePolicy`.
- Relasi Eloquent lengkap per model: lihat migration + model `$fillable`/`relations()`.
