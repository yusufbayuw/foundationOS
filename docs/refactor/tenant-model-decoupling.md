# Tenant Model Decoupling Audit

Date: 2026-05-24

## Goal

`Modules\Core\Models\Tenant` should remain the SaaS/Core tenant aggregate, not a registry for every ERP module. The preferred long-term target is that Tenant keeps Core/SaaS relationships and business modules query their own models by `tenant_id`.

This pass is intentionally compatibility-first. No relationship was removed.

## Inspection Method

The relationship inventory was built from `Tenant` relationship methods and repository-wide usage searches for:

- direct method calls such as `$tenant->relation()`;
- dynamic property access such as `$tenant->relation`;
- string relationship references such as `protected static string $relationship = 'relation'`;
- eager-loading and count references such as `with('relation')` and `counts('relation')`.

Migrations and unrelated model methods were considered context, but not treated as proof that a `Tenant` relationship is actively used.

## Core Relationships To Keep

These are Core/SaaS relationships and should remain on `Tenant`:

| Relationship | Why it stays |
| --- | --- |
| `subscriptionPlan` | Billing and subscription status depend on the plan. |
| `creator` | Core tenant ownership/audit metadata. |
| `organizations` | Core organization hierarchy. |
| `tenantRoles` | Core tenant authorization. |
| `userTenantRoles` | Core tenant membership assignment. |
| `subscriptionLogs` | Core billing history. |
| `tenantSettings` | Core tenant configuration. |
| `tenantModules` | Core module enablement. |
| `modules` | Core enabled-module pivot. |
| `users` | Core tenant membership. |
| `academicYears` | Current Core academic structure. |
| `academicPeriods` | Current Core academic structure. |
| `departments` | Current Core organization structure. |

## Business-Module Relationships To Move Later

These relationships make Core depend on business modules. They should be migrated gradually, but they are not safe to remove now because Filament relation managers, tests, dashboards, services, or module models still reference them.

### School

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `curricula` | Tenant and Organization relation managers. | Query `Modules\School\Models\Curriculum::where('tenant_id', $tenantId)`. |
| `subjects` | Tenant and Organization relation managers; report card services/views use same domain name. | Query `Modules\School\Models\Subject` by `tenant_id`. |
| `students` | Tenant relation manager; API and user/org relation managers use student domain. | Query `Modules\School\Models\Student` by `tenant_id`. |
| `teachers` | Tenant, Organization, and User relation managers. | Query `Modules\School\Models\Teacher` by `tenant_id`. |
| `schoolClasses` | Tenant, Organization, and Department relation managers. | Query `Modules\School\Models\SchoolClass` by `tenant_id`. |
| `classStudents` | Tenant relation manager and School services/pages. | Query `Modules\School\Models\ClassStudent` by `tenant_id`. |
| `schedules` | Tenant/Organization relation managers and School relations. | Query `Modules\School\Models\Schedule` by `tenant_id`. |
| `attendances` | Tenant relation manager and School student/schedule relations. | Query `Modules\School\Models\Attendance` by `tenant_id`. |
| `assessments` | Tenant/Organization relation managers and School reporting. | Query `Modules\School\Models\Assessment` by `tenant_id`. |
| `assessmentItems` | Tenant relation manager and Assessment relation manager. | Query `Modules\School\Models\AssessmentItem` by `tenant_id`. |
| `studentAssessmentAnswers` | Tenant relation manager and School assessment relations. | Query `Modules\School\Models\StudentAssessmentAnswer` by `tenant_id`. |
| `studentGrades` | Tenant relation manager and School profile/assessment relations. | Query `Modules\School\Models\StudentGrade` by `tenant_id`. |
| `violationTypes` | Tenant/Organization relation managers. | Query `Modules\School\Models\ViolationType` by `tenant_id`. |
| `violations` | Tenant relation manager and School reporting/profile relations. | Query `Modules\School\Models\Violation` by `tenant_id`. |
| `achievementTypes` | Tenant/Organization relation managers. | Query `Modules\School\Models\AchievementType` by `tenant_id`. |
| `studentAchievements` | Tenant/Organization relation managers and School profile relations. | Query `Modules\School\Models\StudentAchievement` by `tenant_id`. |

### Enrollment

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `admissionPeriods` | Organization model currently exposes the same domain relationship. | Query `Modules\Enrollment\Models\AdmissionPeriod` by `tenant_id`. |
| `applicants` | API/write tests and Enrollment models use applicant domain. | Query `Modules\Enrollment\Models\Applicant` by `tenant_id`. |
| `examSchedules` | Enrollment model relationships use same domain name. | Query `Modules\Enrollment\Models\ExamSchedule` by `tenant_id`. |
| `examResults` | Enrollment integrity tests and models use same domain name. | Query `Modules\Enrollment\Models\ExamResult` by `tenant_id`. |
| `registrations` | Enrollment integrity tests and models use same domain name. | Query `Modules\Enrollment\Models\Registration` by `tenant_id`. |

### Finance

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `chartOfAccounts` | Organization model exposes the finance domain relationship. | Query `Modules\Finance\Models\ChartOfAccount` by `tenant_id`. |
| `tuitionTypes` | Organization model exposes the finance domain relationship. | Query `Modules\Finance\Models\TuitionType` by `tenant_id`. |
| `studentInvoices` | Feature tests and student/applicant/college-student relations use invoices. | Query `Modules\Finance\Models\StudentInvoice` by `tenant_id`. |
| `studentInvoiceItems` | Finance model relationships use invoice items. | Query `Modules\Finance\Models\StudentInvoiceItem` by `tenant_id`. |
| `payments` | API and Finance service/model references use payments. | Query `Modules\Finance\Models\Payment` by `tenant_id`. |
| `journalEntries` | Organization model exposes the finance domain relationship. | Query `Modules\Finance\Models\JournalEntry` by `tenant_id`. |
| `journalEntryLines` | Finance model relationships use journal entry lines. | Query `Modules\Finance\Models\JournalEntryLine` by `tenant_id`. |
| `budgets` | Organization and Finance model relationships use budgets. | Query `Modules\Finance\Models\Budget` by `tenant_id`. |

### Library

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `bookCategories` | Organization model exposes the library relationship. | Query `Modules\Library\Models\BookCategory` by `tenant_id`. |
| `books` | Import command, OPAC controller, and category relation managers use books. | Query `Modules\Library\Models\Book` by `tenant_id`. |
| `members` | Import command, OPAC controller, Moodle sync, and member-type relations use members. | Query `Modules\Library\Models\Member` by `tenant_id`. |
| `bookReservations` | Organization model exposes the library relationship. | Query `Modules\Library\Models\BookReservation` by `tenant_id`. |
| `libraryPolicies` | Organization model exposes the library relationship. | Query `Modules\Library\Models\LibraryPolicy` by `tenant_id`. |
| `loans` | Import command and Library relation managers/models use loans. | Query `Modules\Library\Models\Loan` by `tenant_id`. |
| `fines` | Loan relation manager/model uses fines. | Query `Modules\Library\Models\Fine` by `tenant_id`. |

### Employee

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `positions` | Organization and Department expose HR structure relationships. | Query `Modules\Employee\Models\Position` by `tenant_id`. |
| `shifts` | Organization model exposes shift relationship. | Query `Modules\Employee\Models\Shift` by `tenant_id`. |
| `employees` | API/routes and Organization/Position relations use employees. | Query `Modules\Employee\Models\Employee` by `tenant_id`. |
| `employmentContracts` | Employee model relations use contracts. | Query `Modules\Employee\Models\EmploymentContract` by `tenant_id`. |
| `attendanceLogs` | Employee and Shift model relations use attendance logs. | Query `Modules\Employee\Models\AttendanceLog` by `tenant_id`. |
| `leaveRequests` | Employee model relation uses leave requests. | Query `Modules\Employee\Models\LeaveRequest` by `tenant_id`. |
| `payrollComponents` | Organization model exposes payroll components. | Query `Modules\Employee\Models\PayrollComponent` by `tenant_id`. |
| `salarySlips` | Employee model relation uses salary slips. | Query `Modules\Employee\Models\SalarySlip` by `tenant_id`. |
| `salarySlipComponents` | Payroll component relation uses salary slip components. | Query `Modules\Employee\Models\SalarySlipComponent` by `tenant_id`. |
| `kpiTemplates` | Position, Department, and Organization relations use KPI templates. | Query `Modules\Employee\Models\KpiTemplate` by `tenant_id`. |
| `kpiIndicators` | Organization model exposes KPI indicators. | Query `Modules\Employee\Models\KpiIndicator` by `tenant_id`. |
| `kpiScores` | Employee and KPI template relations use KPI scores. | Query `Modules\Employee\Models\KpiScore` by `tenant_id`. |

### Procurement

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `vendors` | Procurement models use vendors; table/migration domain is active. | Query `Modules\Procurement\Models\Vendor` by `tenant_id`. |
| `procurementCategories` | Organization model exposes procurement categories. | Query `Modules\Procurement\Models\ProcurementCategory` by `tenant_id`. |
| `procurementItems` | Finance chart-of-account relationship uses procurement items. | Query `Modules\Procurement\Models\ProcurementItem` by `tenant_id`. |
| `purchaseRequisitionItems` | Finance chart-of-account relationship uses requisition items. | Query `Modules\Procurement\Models\PurchaseRequisitionItem` by `tenant_id`. |
| `rfqItems` | Procurement item relationship uses RFQ items. | Query `Modules\Procurement\Models\RfqItem` by `tenant_id`. |
| `rfqVendors` | Vendor relationship uses RFQ vendors. | Query `Modules\Procurement\Models\RfqVendor` by `tenant_id`. |
| `purchaseOrders` | Feature test directly uses `$tenant->purchaseOrders`; Procurement models also use purchase orders. | Query `Modules\Procurement\Models\PurchaseOrder` by `tenant_id`. |
| `purchaseOrderItems` | Procurement item relationship uses purchase order items. | Query `Modules\Procurement\Models\PurchaseOrderItem` by `tenant_id`. |
| `goodsReceipts` | Purchase order relationship uses goods receipts. | Query `Modules\Procurement\Models\GoodsReceipt` by `tenant_id`. |
| `goodsReceiptItems` | Purchase order item relationship uses receipt items. | Query `Modules\Procurement\Models\GoodsReceiptItem` by `tenant_id`. |
| `vendorBills` | Vendor, goods receipt, and purchase order relationships use vendor bills. | Query `Modules\Procurement\Models\VendorBill` by `tenant_id`. |
| `vendorBillItems` | Purchase order item relationship uses vendor bill items. | Query `Modules\Procurement\Models\VendorBillItem` by `tenant_id`. |

### Campus

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `faculties` | Tenant/Organization relation managers. | Query `Modules\Campus\Models\Faculty` by `tenant_id`. |
| `studyPrograms` | Tenant/Organization relation managers and Faculty relationship. | Query `Modules\Campus\Models\StudyProgram` by `tenant_id`. |
| `courses` | API, Moodle integration, Tenant relation manager, and StudyProgram relation. | Query `Modules\Campus\Models\Course` by `tenant_id`. |
| `lecturers` | Tenant/Organization/User relation managers and Campus relationships. | Query `Modules\Campus\Models\Lecturer` by `tenant_id`. |
| `collageStudents` | Tenant/Organization relation managers. | Query `Modules\Campus\Models\CollageStudent` by `tenant_id`. |
| `courseOfferings` | Tenant/Organization relation managers and Course/Lecturer relationships. | Query `Modules\Campus\Models\CourseOffering` by `tenant_id`. |
| `studyPlans` | Tenant relation manager and CollageStudent relationship. | Query `Modules\Campus\Models\StudyPlan` by `tenant_id`. |
| `studyPlanItems` | Tenant relation manager and Course/CourseOffering relationships. | Query `Modules\Campus\Models\StudyPlanItem` by `tenant_id`. |
| `studyResults` | Tenant relation manager. | Query `Modules\Campus\Models\StudyResult` by `tenant_id`. |
| `feederLogs` | Tenant/Organization relation managers. | Query `Modules\Campus\Models\FeederLog` by `tenant_id`. |
| `theses` | Tenant relation manager and CollageStudent relationship. | Query `Modules\Campus\Models\Thesis` by `tenant_id`. |

### Monitoring

| Relationship | Current usage signal | Future replacement |
| --- | --- | --- |
| `auditLogs` | Feature test directly uses `$tenant->auditLogs()`; Tenant relation manager exists. | Query `Modules\Monitoring\Models\AuditLog` by `tenant_id`, or via polymorphic subject where appropriate. |
| `fileUploads` | Feature test directly uses `$tenant->fileUploads`; Tenant relation manager exists. | Query `Modules\Monitoring\Models\FileUpload` by `tenant_id`, or via polymorphic owner where appropriate. |
| `auditableLogs` | Tenant/Organization relation managers use the polymorphic audit relationship. | Use Monitoring `HasAuditTrail`/polymorphic audit relation from the owning model. |
| `attachedFiles` | Tenant/Organization relation managers use the polymorphic file relationship. | Use Monitoring polymorphic file relation from the owning model. |

## Relationships Currently Unused As Tenant Relationships

No repository usage was found for these `Tenant` relationships. They are low-risk candidates for future removal, but removal is still not done in this phase because external code, Filament conventions, or future generated relation managers may still call them.

| Relationship | Owning module | Recommendation |
| --- | --- | --- |
| `bookCopies` | Library | Mark deprecated; later replace with `BookCopy::where('tenant_id', $tenantId)` from Library code. |
| `purchaseRequisitions` | Procurement | Mark deprecated; later replace with `PurchaseRequisition::where('tenant_id', $tenantId)` from Procurement code. |
| `requestForQuotations` | Procurement | Mark deprecated; later replace with `RequestForQuotation::where('tenant_id', $tenantId)` from Procurement code. |

## Replacement Strategy

For business modules, migrate in this order:

1. Add module-owned query methods or small query classes only when replacing a real caller.
2. Update that caller to query the owning module model by `tenant_id`.
3. Keep the `Tenant` relationship as a compatibility layer until all usages are migrated.
4. Add tests around the migrated caller.
5. Remove deprecated `Tenant` relationships only after a repository-wide usage search and one release cycle.

Preferred examples:

```php
// School
Student::query()->where('tenant_id', $tenant->getKey());

// Procurement
PurchaseOrder::query()->where('tenant_id', $tenant->getKey());

// Monitoring
AuditLog::query()->where('tenant_id', $tenant->getKey());
```

## No New Services In This Phase

No module-level query/service class was added in this phase. The only truly unused relationships have no production caller to migrate, and adding a service without a caller would create unused abstraction. For active relationships such as `purchaseOrders`, `auditLogs`, and `fileUploads`, migration should be done in a focused phase with tests for the exact feature or relation manager being changed.
