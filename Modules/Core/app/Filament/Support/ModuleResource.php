<?php

namespace Modules\Core\Filament\Support;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\TenantModule;
use Modules\Core\Support\FilamentUi;
use UnitEnum;

abstract class ModuleResource extends Resource
{
    public static function isScopedToTenant(): bool
    {
        if (! parent::isScopedToTenant()) {
            return false;
        }

        $modelClass = static::getModel();

        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            return false;
        }

        $ownershipRelationship = static::getTenantOwnershipRelationshipName();

        try {
            $model = app($modelClass);
        } catch (\Throwable) {
            return false;
        }

        return $model instanceof Model
            && $model->isRelation($ownershipRelationship);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canCreate(): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canCreate();
    }

    public static function canEdit(Model $record): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canEdit($record);
    }

    public static function canDelete(Model $record): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canDelete($record);
    }

    public static function canDeleteAny(): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canDeleteAny();
    }

    public static function canForceDelete(Model $record): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canForceDelete($record);
    }

    public static function canForceDeleteAny(): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canForceDeleteAny();
    }

    public static function canRestore(Model $record): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canRestore($record);
    }

    public static function canRestoreAny(): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canRestoreAny();
    }

    /** Core and Global are always required; all other modules are gated by TenantModule. */
    private const ALWAYS_VISIBLE_MODULES = ['Core', 'Global'];

    public static function shouldRegisterNavigation(): bool
    {
        $module = static::getModuleName();

        if (in_array($module, self::ALWAYS_VISIBLE_MODULES, true)) {
            return true;
        }

        $tenant = Filament::getTenant();

        if (! $tenant) {
            return true;
        }

        return Cache::remember(
            "tenant_module_active:{$tenant->getKey()}:{$module}",
            now()->addMinutes(5),
            fn () => TenantModule::query()
                ->whereHas('module', fn (Builder $q) => $q->where('code', strtolower($module)))
                ->where('tenant_id', $tenant->getKey())
                ->where('is_enabled', true)
                ->exists()
        );
    }

    public static function getNavigationSort(): ?int
    {
        $module = static::getModuleName();
        $resource = class_basename(static::class);

        return static::navigationSortMap()[$module][$resource] ?? null;
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return FilamentUi::module(static::getModuleName());
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return FilamentUi::resourceIcon(static::getModel());
    }

    public static function getNavigationLabel(): string
    {
        return static::toProperCase(FilamentUi::text(parent::getPluralModelLabel()));
    }

    public static function getModelLabel(): string
    {
        return FilamentUi::resource(static::getModel());
    }

    public static function getPluralModelLabel(): string
    {
        return FilamentUi::text(parent::getPluralModelLabel());
    }

    /**
     * Resolve a meaningful record title even when the model has no 'name' column.
     *
     * Priority: name → full_name → code → entry_number → invoice_number → payment_number → ID
     */
    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        $candidates = [
            'name', 'full_name', 'title', 'code',
            'entry_number', 'invoice_number', 'payment_number',
            'employee_number', 'student_number', 'registration_number',
            'nis', 'nip', 'subject_label',
        ];

        foreach ($candidates as $attr) {
            $value = $record->getAttribute($attr);
            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        // Fallback: try to get the name from the related user
        if ($record->isRelation('user') && $record->user?->name) {
            return $record->user->name;
        }

        return (string) $record->getKey();
    }

    protected static function getModuleName(): string
    {
        return str(static::class)->after('Modules\\')->before('\\Filament\\Resources')->beforeLast('\\')->toString();
    }

    /**
     * Navigation order per module to reflect typical operational flow.
     *
     * @return array<string, array<string, int>>
     */
    protected static function navigationSortMap(): array
    {
        return [
            'Core' => [
                'OrganizationResource' => 10,
                'DepartmentResource' => 20,
                'AcademicYearResource' => 30,
                'AcademicPeriodResource' => 40,
                'TenantResource' => 50,
                'TenantSettingResource' => 60,
                'OrganizationSettingResource' => 70,
                'TenantRoleResource' => 80,
                'UserTenantRoleResource' => 90,
                'UserResource' => 100,
                'ModuleResource' => 110,
                'TenantModuleResource' => 120,
                'SubscriptionPlanResource' => 130,
                'SubscriptionLogResource' => 140,
            ],
            'Global' => [
                'CountryResource' => 10,
                'ProvinceResource' => 20,
                'CityResource' => 30,
                'DistrictResource' => 40,
                'VillageResource' => 50,
                'TimezoneResource' => 60,
            ],
            'School' => [
                'CurriculumResource' => 10,
                'SubjectResource' => 20,
                'SchoolClassResource' => 30,
                'TeacherResource' => 40,
                'StudentResource' => 50,
                'ClassStudentResource' => 60,
                'ScheduleResource' => 70,
                'AttendanceResource' => 80,
                'AssessmentResource' => 90,
                'AssessmentItemResource' => 100,
                'StudentAssessmentAnswerResource' => 110,
                'StudentGradeResource' => 120,
                'AchievementTypeResource' => 130,
                'StudentAchievementResource' => 140,
                'ViolationTypeResource' => 150,
                'ViolationResource' => 160,
            ],
            'Campus' => [
                'FacultyResource' => 10,
                'StudyProgramResource' => 20,
                'CourseResource' => 30,
                'CourseOfferingResource' => 40,
                'LecturerResource' => 50,
                'CollageStudentResource' => 60,
                'StudyPlanResource' => 70,
                'StudyPlanItemResource' => 80,
                'StudyResultResource' => 90,
                'ThesisResource' => 100,
                'FeederLogResource' => 110,
            ],
            'Workflow' => [
                'WorkflowResource' => 10,
                'WorkflowInstanceResource' => 20,
            ],
            'Enrollment' => [
                'AdmissionPeriodResource' => 10,
                'ApplicantResource' => 20,
                'RegistrationResource' => 30,
                'ExamScheduleResource' => 40,
                'ExamResultResource' => 50,
            ],
            'Employee' => [
                'PositionResource' => 10,
                'EmployeeResource' => 20,
                'EmploymentContractResource' => 30,
                'ShiftResource' => 40,
                'AttendanceLogResource' => 50,
                'LeaveRequestResource' => 60,
                'PayrollComponentResource' => 70,
                'SalarySlipComponentResource' => 80,
                'SalarySlipResource' => 90,
                'KpiTemplateResource' => 100,
                'KpiIndicatorResource' => 110,
                'KpiScoreResource' => 120,
            ],
            'Finance' => [
                'ChartOfAccountResource' => 10,
                'TuitionTypeResource' => 20,
                'BudgetResource' => 30,
                'StudentInvoiceResource' => 40,
                'StudentInvoiceItemResource' => 50,
                'PaymentResource' => 60,
                'JournalEntryResource' => 70,
                'JournalEntryLineResource' => 80,
                'CustomerInvoiceResource' => 90,
                'CustomerInvoiceItemResource' => 100,
            ],
            'Procurement' => [
                'VendorResource' => 10,
                'ProcurementCategoryResource' => 20,
                'ProcurementItemResource' => 30,
                'PurchaseRequisitionResource' => 40,
                'PurchaseRequisitionItemResource' => 50,
                'RequestForQuotationResource' => 60,
                'RfqVendorResource' => 70,
                'RfqItemResource' => 80,
                'PurchaseOrderResource' => 90,
                'PurchaseOrderItemResource' => 100,
                'GoodsReceiptResource' => 110,
                'GoodsReceiptItemResource' => 120,
                'VendorBillResource' => 130,
                'VendorBillItemResource' => 140,
            ],
            'Library' => [
                'LibraryAuthorResource' => 5,
                'LibraryPublisherResource' => 6,
                'LibrarySubjectResource' => 7,
                'LibraryLocationResource' => 8,
                'LibraryItemStatusResource' => 9,
                'LibraryCollectionTypeResource' => 10,
                'LibraryGmdResource' => 11,
                'LibraryFrequencyResource' => 12,
                'BookCategoryResource' => 20,
                'BookResource' => 30,
                'BookCopyResource' => 40,
                'LibraryPolicyResource' => 45,
                'LibraryMemberTypeResource' => 48,
                'MemberResource' => 50,
                'BookReservationResource' => 60,
                'LoanResource' => 70,
                'FineResource' => 80,
                'LibrarySerialResource' => 90,
                'LibraryStockTakeResource' => 95,
            ],
            'Monitoring' => [
                'FileUploadResource' => 10,
                'AuditLogResource' => 20,
            ],
        ];
    }

    protected static function toProperCase(string $value): string
    {
        return collect(preg_split('/(\s+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [])
            ->map(function (string $token): string {
                if (trim($token) === '') {
                    return $token;
                }

                $parts = preg_split('/([\\-\\/])/', $token, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$token];

                $parts = array_map(function (string $part): string {
                    if ($part === '-' || $part === '/') {
                        return $part;
                    }

                    if (preg_match('/^[A-Z0-9]+$/', $part)) {
                        return $part;
                    }

                    $lower = strtolower($part);

                    return ucfirst($lower);
                }, $parts);

                return implode('', $parts);
            })
            ->implode('');
    }

    protected static function isGlobalMutationRestricted(): bool
    {
        return ! static::isScopedToTenant();
    }

    protected static function canCurrentUserMutateGlobalResource(): bool
    {
        $user = auth()->user();

        return $user && method_exists($user, 'isGlobalSuperAdmin') && $user->isGlobalSuperAdmin();
    }
}
