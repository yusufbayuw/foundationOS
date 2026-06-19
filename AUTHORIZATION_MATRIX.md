# Authorization Matrix

Evidence-based Shield / Spatie permission catalog for FoundationOS admin panel (`/admin`).

**Generated:** 2026-06-19T04:50:19+00:00  
**Source:** `scripts/extract-authorization-matrix.php` → `docs/catalogs/authorization-matrix.json`  
**Full machine catalog:** `docs/catalogs/authorization-matrix.json` (257 resources, ~3100 permissions)

---

## Permission naming (Filament Shield)

| Setting | Value | Evidence |
|---------|-------|----------|
| Separator | `:` | `config/filament-shield.php` `permissions.separator` |
| Case | `pascal` | `config/filament-shield.php` `permissions.case` |
| Policy methods | viewAny, view, create, update, delete, deleteAny, restore, forceDelete, forceDeleteAny, restoreAny, replicate, reorder | `config/filament-shield.php` `policies.methods` |
| Teams mode | `tenant_id` as team key | `config/permission.php` |

**Pattern:** `{Method}:{ModelShortName}` — example for `Competition`:
- `ViewAny:Competition`
- `View:Competition`
- `Create:Competition`

Each of **257** `ModuleResource` classes maps to **12** resource permissions + **16** custom exam permissions in `config/filament-shield.php`.

---

## Panel access (authentication boundary)

| Panel | Gate | Rule | Evidence |
|-------|------|------|----------|
| `admin` | `User::canAccessPanel('admin')` | `is_super_admin` OR `user_tenant_roles` exists | `User.php:471-476` |
| `platform` | `User::canAccessPanel('platform')` | Spatie role `platform_owner` (team `tenant_id = 0`) | `User.php:465-468` |
| `parent` | `User::canAccessPanel('parent')` | `parent_students.parent_user_id` link | `User.php:479-481` |

---

## Global authorization bypass

| Mechanism | Behavior | Evidence |
|-----------|----------|----------|
| `users.is_super_admin` | `Gate::before` returns `true` for all abilities | `AppServiceProvider.php:93-98` |
| Shield `super_admin` role | Configured; teams-scoped per tenant via `shield:super-admin` | `config/filament-shield.php`, `CLAUDE.md` |
| `panel_user` role | Basic panel access role (Shield) | `config/filament-shield.php` |

---

## Domain membership vs panel permissions

| Layer | Purpose | Tables / classes |
|-------|---------|------------------|
| Domain membership | Which tenant/org a user belongs to | `user_tenant_roles`, `tenant_roles` |
| Panel authorization | What actions are allowed in Filament | Spatie `roles`, `permissions`, Shield-generated keys |
| API authorization | Token + tenant resolve; **not** Shield per route | Sanctum + `resolve.api.tenant` |

---

## Custom permissions (Exam module)

| Permission key | Label |
|----------------|-------|
| `view_exam` | View exams |
| `create_exam` | Create exams |
| `update_exam` | Update exams |
| `delete_exam` | Delete exams |
| `publish_exam` | Publish exams |
| `sync_exam_result` | Sync exam results |
| `push_exam_gradebook` | Push exam results to gradebook |
| `grade_exam_answer` | Grade exam answers |
| `export_exam_result` | Export exam results |
| `manage_question_bank` | Manage question banks |
| `import_question` | Import questions |
| `generate_question_ai` | Generate questions with AI |
| `manage_osn_prep` | Manage OSN preparation |
| `manage_exam_token` | Manage exam tokens |
| `manage_exam_proctor` | Manage exam proctors |
| `open_control_room` | Open control room |

Evidence: `config/filament-shield.php` `custom_permissions`.

---

## Resources by module

| Module | Resources | Example permission subject |
|--------|-----------|----------------------------|
| Ai | 1 | `AiPromptTemplate` |
| Alumni | 9 | `AlumniDonation` |
| Asset | 7 | `AssetCategory` |
| Boarding | 7 | `BoardingAttendance` |
| Cafeteria | 8 | `CafeteriaInspection` |
| Campus | 6 | `CourseOfferingLecturer` |
| Capacity | 3 | `CapacityForecast` |
| Clinic | 9 | `Allergy` |
| Cms | 10 | `ArticleCategory` |
| Consulting | 6 | `ConsultantTimesheet` |
| Core | 10 | `Announcement` |
| Counseling | 7 | `AnonymousReport` |
| Dms | 4 | `DocumentAccessLog` |
| Donation | 7 | `CampaignUpdate` |
| EOffice | 5 | `LetterAttachment` |
| EducationQa | 8 | `AccreditationCycle` |
| Employee | 1 | `EmployeeDocument` |
| Enrollment | 5 | `LeadActivity` |
| Event | 10 | `EventBudget` |
| Facility | 8 | `Building` |
| Finance | 1 | `CustomerInvoiceItem` |
| Helpdesk | 6 | `KnowledgeBaseArticle` |
| InternalAudit | 9 | `AuditChecklistItem` |
| Inventory | 7 | `StockAdjustmentLine` |
| IsoCompliance | 7 | `ControlImplementation` |
| ItOps | 4 | `BackupJob` |
| KpiEnterprise | 6 | `KpiActual` |
| Legal | 4 | `ContractAttachment` |
| Library | 1 | `Library` |
| Marketplace | 8 | `MarketplaceOrderItem` |
| MerchOrder | 6 | `BookPackage` |
| Messaging | 3 | `NotificationDelivery` |
| Monitoring | 2 | `MoodleSyncOutbox` |
| PhysicalSecurity | 8 | `EmergencyAlert` |
| Printing | 8 | `PrintMaterial` |
| Property | 6 | `CommercialTenant` |
| Risk | 7 | `KeyRiskIndicator` |
| Sales | 4 | `CooperativeSaving` |
| School | 5 | `Competition` |
| Training | 9 | `CorporateTrainingPackage` |
| Transport | 9 | `BoardingLog` |
| Workflow | 6 | `WorkflowAssignment` |

---

## Sample resource permissions (School module)

| Resource class | Model | Permissions |
|----------------|-------|-------------|
| `Modules\School\Filament\Resources\Competitions\CompetitionResource` | `Modules\School\Filament\Resources\Competitions\Competition` | `ViewAny:Competition`, `View:Competition`, `Create:Competition`, `Update:Competition` …` |
| `Modules\School\Filament\Resources\ExtracurricularEnrollments\ExtracurricularEnrollmentResource` | `Modules\School\Filament\Resources\ExtracurricularEnrollments\ExtracurricularEnrollment` | `ViewAny:ExtracurricularEnrollment`, `View:ExtracurricularEnrollment`, `Create:ExtracurricularEnrollment`, `Update:ExtracurricularEnrollment` …` |
| `Modules\School\Filament\Resources\Extracurriculars\ExtracurricularResource` | `Modules\School\Filament\Resources\Extracurriculars\Extracurricular` | `ViewAny:Extracurricular`, `View:Extracurricular`, `Create:Extracurricular`, `Update:Extracurricular` …` |
| `Modules\School\Filament\Resources\StudentAbsences\StudentAbsenceResource` | `Modules\School\Filament\Resources\StudentAbsences\StudentAbsence` | `ViewAny:StudentAbsence`, `View:StudentAbsence`, `Create:StudentAbsence`, `Update:StudentAbsence` …` |
| `Modules\School\Filament\Resources\StudentRiskScores\StudentRiskScoreResource` | `Modules\School\Filament\Resources\StudentRiskScores\StudentRiskScore` | `ViewAny:StudentRiskScore`, `View:StudentRiskScore`, `Create:StudentRiskScore`, `Update:StudentRiskScore` …` |

---

## Regenerate

```bash
php scripts/extract-authorization-matrix.php
php scripts/generate-docs-appendices.php
```
