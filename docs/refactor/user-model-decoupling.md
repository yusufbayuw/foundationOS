# User Model Decoupling Audit

Date: 2026-05-24

## Goal

`Modules\Core\Models\User` should stay focused on identity:

- authentication credentials and MFA;
- API tokens;
- tenant membership and default tenant selection;
- roles and platform/admin/parent panel access;
- preferences and localization;
- audit metadata owned by Core.

Business modules should own profile and actor relationships. A user may be linked to domain profiles such as Employee, Student, Teacher, Lecturer, Library Member, or Parent, but those profiles should live in their modules and query `user_id`, `verified_by`, `approved_by`, or other actor columns from that module.

This pass is compatibility-first. No relationship was removed and authentication behavior was not changed.

## Inspection Method

The relationship inventory was built from `User` relationship methods and repository-wide usage searches for:

- direct method calls such as `$user->relation()`;
- dynamic property access such as `$user->relation`;
- string relationship references such as `protected static string $relationship = 'relation'`;
- eager-loading/count references such as `with('relation')`.

Migrations and unrelated model methods with the same name were treated as context, not proof that the `User` relationship itself is used.

## Auth/Core Relationships To Keep

These should remain on `User`:

| Relationship or method | Why it stays |
| --- | --- |
| `userTenantRoles` | Core tenant membership and authorization assignment. |
| `assignedTenantRoles` | Core audit trail for tenant role assignment. |
| `tenants` | Required by Filament `HasTenants`. |
| `organizations` | Core tenant membership organization pivot. |
| `createdTenants` | Core tenant ownership/audit relation and User resource relation manager. |
| `principalOrganizations` | Core organization leadership relation manager. |
| `processedSubscriptionLogs` | Core billing actor relation; currently unused but still Core/SaaS. |
| `auditLogs` | Current Core/Monitoring audit relation manager depends on this relation. |
| `uploadedFiles` | Current User resource relation manager depends on this relation. |
| `canAccessTenant` | Required by Filament tenancy. |
| `getTenants` | Required by Filament tenancy. |
| `getDefaultTenant` | Required by Filament tenancy. |
| `isGlobalSuperAdmin` | Used by platform/admin authorization and policies. |
| `canAccessPanel` | Required by Filament panel access. |

## Domain/Profile Relationships To Move Later

These relationships make Core depend on business modules. They should be migrated to module-owned profiles/services only after the concrete caller is migrated and tested.

### Profile Relationships

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `students` | User resource relation manager exists. | School should query `Modules\School\Models\Student::where('user_id', $userId)`. |
| `teachers` | User resource relation manager exists. | School should query `Modules\School\Models\Teacher::where('user_id', $userId)`. |
| `lecturers` | User resource relation manager exists. | Campus should query `Modules\Campus\Models\Lecturer::where('user_id', $userId)`. |
| `verifiedAttendances` | User resource relation manager exists. | School should query `Modules\School\Models\Attendance::where('verified_by', $userId)`. |
| `reportedViolations` | User resource relation manager exists. | School should query `Modules\School\Models\Violation::where('reported_by', $userId)`. |
| `handledViolations` | User resource relation manager exists. | School should query `Modules\School\Models\Violation::where('handled_by', $userId)`. |
| `gradedStudentAnswers` | User resource relation manager exists. | School should query `Modules\School\Models\StudentAssessmentAnswer::where('graded_by', $userId)`. |
| `gradedStudentGrades` | User resource relation manager exists. | School should query `Modules\School\Models\StudentGrade::where('graded_by', $userId)`. |
| `verifiedStudentAchievements` | User resource relation manager exists. | School should query `Modules\School\Models\StudentAchievement::where('verified_by', $userId)`. |
| `approvedStudyPlans` | User resource relation manager exists. | Campus should query `Modules\Campus\Models\StudyPlan::where('approved_by', $userId)`. |
| `syncedFeederLogs` | User resource relation manager exists. | Campus should query `Modules\Campus\Models\FeederLog::where('synced_by', $userId)`. |

### Unused Business-Domain Relationships

No repository usage was found for these as `User` relationships. They are low-risk candidates for future removal, but they are only marked deprecated in this phase.

| Relationship | Owning module | Replacement direction |
| --- | --- | --- |
| `employees` | Employee | Query `Modules\Employee\Models\Employee` by `user_id`. |
| `libraryMembers` | Library | Query `Modules\Library\Models\Member` by `user_id`. |
| `verifiedPayments` | Finance | Query `Modules\Finance\Models\Payment` by `verified_by`. |
| `postedJournalEntries` | Finance | Query `Modules\Finance\Models\JournalEntry` by `posted_by`. |
| `approvedBudgets` | Finance | Query `Modules\Finance\Models\Budget` by `approved_by`. |
| `requestedPurchaseRequisitions` | Procurement | Query `Modules\Procurement\Models\PurchaseRequisition` by `requested_by`. |
| `ownedPurchaseRequisitions` | Procurement | Query `Modules\Procurement\Models\PurchaseRequisition` by `user_id`. |
| `approvedPurchaseRequisitions` | Procurement | Query `Modules\Procurement\Models\PurchaseRequisition` by `approved_by`. |
| `createdRequestForQuotations` | Procurement | Query `Modules\Procurement\Models\RequestForQuotation` by `created_by`. |
| `approvedPurchaseOrders` | Procurement | Query `Modules\Procurement\Models\PurchaseOrder` by `approved_by`. |
| `receivedGoodsReceipts` | Procurement | Query `Modules\Procurement\Models\GoodsReceipt` by `received_by`. |
| `inspectedGoodsReceipts` | Procurement | Query `Modules\Procurement\Models\GoodsReceipt` by `inspected_by`. |
| `processedVendorBills` | Procurement | Query `Modules\Procurement\Models\VendorBill` by `processed_by`. |
| `processedLoans` | Library | Query `Modules\Library\Models\Loan` by `processed_by`. |
| `returnedLoans` | Library | Query `Modules\Library\Models\Loan` by `returned_by`. |
| `approvedAttendanceLogs` | Employee | Query `Modules\Employee\Models\AttendanceLog` by `approved_by`. |
| `supervisedLeaveRequests` | Employee | Query `Modules\Employee\Models\LeaveRequest` by `supervisor_id`. |
| `approvedLeaveRequests` | Employee | Query `Modules\Employee\Models\LeaveRequest` by `approver_id`. |
| `evaluatedKpiScores` | Employee | Query `Modules\Employee\Models\KpiScore` by `evaluator_id`. |
| `completedRegistrations` | Enrollment | Query `Modules\Enrollment\Models\Registration` by `completed_by`. |
| `examinedExamResults` | Enrollment | Query `Modules\Enrollment\Models\ExamResult` by `examiner_id`. |

## Target Identity Model

Preferred model split:

- `User`: auth identity, credentials, MFA, Sanctum tokens, roles, tenant membership, preferences, and Filament panel access.
- `Employee`: HR profile linked by `user_id`.
- `Student`: school learner profile linked by `user_id`.
- `Teacher`: school staff profile linked by `user_id`.
- `Lecturer`: campus staff profile linked by `user_id`.
- `Member`: library profile linked by `user_id`.
- `ParentStudent`: parent/guardian association linked by `parent_user_id`.

The domain profile models should answer domain questions. `User` should answer identity and access questions.

## Replacement Strategy

1. Keep existing `User` relationships as compatibility shims while callers exist.
2. Migrate one module at a time.
3. For each real caller, add a module-owned query/service only when the caller needs it.
4. Replace `User` relationship usage with explicit module model queries by actor/profile foreign key.
5. Add focused tests for the migrated caller.
6. Remove deprecated relationships only after a usage search and release window.

Example replacements:

```php
// School profile lookup
Student::query()->where('user_id', $user->getKey());

// Finance actor lookup
Payment::query()->where('verified_by', $user->getKey());

// Procurement actor lookup
PurchaseOrder::query()->where('approved_by', $user->getKey());
```

## Tests Added

`tests/Feature/UserIdentityAccessTest.php` now characterizes:

- `canAccessTenant`;
- `getTenants`;
- `getDefaultTenant`;
- `canAccessPanel` for admin, platform, and unknown panels.
