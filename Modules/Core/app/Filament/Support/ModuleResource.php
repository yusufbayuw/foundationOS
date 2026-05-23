<?php

namespace Modules\Core\Filament\Support;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
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
    protected static function resolveNavigationIconFromResourceName(): BackedEnum
    {
        $resourceName = str(class_basename(static::class))
            ->beforeLast('Resource')
            ->kebab()
            ->toString();

        return match (true) {
            str_contains($resourceName, 'academic-year'),
            str_contains($resourceName, 'academic-period'),
            str_contains($resourceName, 'admission-period') => Heroicon::CalendarDateRange,
            str_contains($resourceName, 'announcement'),
            str_contains($resourceName, 'broadcast') => Heroicon::Megaphone,
            str_contains($resourceName, 'assessment-answer'),
            str_contains($resourceName, 'assessment-item') => Heroicon::ClipboardDocumentCheck,
            str_contains($resourceName, 'assessment'),
            str_contains($resourceName, 'exam-result') => Heroicon::DocumentCheck,
            str_contains($resourceName, 'attendance') => Heroicon::CalendarDays,
            str_contains($resourceName, 'audit'),
            str_contains($resourceName, 'log') => Heroicon::DocumentMagnifyingGlass,
            str_contains($resourceName, 'author') => Heroicon::PencilSquare,
            str_contains($resourceName, 'book-copy'),
            str_contains($resourceName, 'stock-take-item') => Heroicon::Square2Stack,
            str_contains($resourceName, 'book-reservation') => Heroicon::BookmarkSquare,
            str_contains($resourceName, 'book'),
            str_contains($resourceName, 'course') => Heroicon::BookOpen,
            str_contains($resourceName, 'budget'),
            str_contains($resourceName, 'invoice'),
            str_contains($resourceName, 'tuition') => Heroicon::DocumentCurrencyDollar,
            str_contains($resourceName, 'campaign') => Heroicon::Gift,
            str_contains($resourceName, 'capacity') => Heroicon::ChartBarSquare,
            str_contains($resourceName, 'category'),
            str_contains($resourceName, 'collection-type'),
            str_contains($resourceName, 'subject'),
            str_contains($resourceName, 'type') => Heroicon::Tag,
            str_contains($resourceName, 'chart-of-account') => Heroicon::Calculator,
            str_contains($resourceName, 'city'),
            str_contains($resourceName, 'country'),
            str_contains($resourceName, 'district'),
            str_contains($resourceName, 'province'),
            str_contains($resourceName, 'village') => Heroicon::MapPin,
            str_contains($resourceName, 'clinic') => Heroicon::Heart,
            str_contains($resourceName, 'contract'),
            str_contains($resourceName, 'legal-document') => Heroicon::Scale,
            str_contains($resourceName, 'counseling') => Heroicon::ChatBubbleLeftRight,
            str_contains($resourceName, 'curriculum'),
            str_contains($resourceName, 'training') => Heroicon::AcademicCap,
            str_contains($resourceName, 'department'),
            str_contains($resourceName, 'faculty'),
            str_contains($resourceName, 'organization'),
            str_contains($resourceName, 'tenant') => Heroicon::BuildingOffice2,
            str_contains($resourceName, 'document'),
            str_contains($resourceName, 'letter') => Heroicon::DocumentText,
            str_contains($resourceName, 'dormitory'),
            str_contains($resourceName, 'room') => Heroicon::HomeModern,
            str_contains($resourceName, 'employee'),
            str_contains($resourceName, 'lecturer'),
            str_contains($resourceName, 'teacher') => Heroicon::Identification,
            str_contains($resourceName, 'event') => Heroicon::Calendar,
            str_contains($resourceName, 'extracurricular') => Heroicon::Sparkles,
            str_contains($resourceName, 'file-upload') => Heroicon::CloudArrowUp,
            str_contains($resourceName, 'fine'),
            str_contains($resourceName, 'payment') => Heroicon::CreditCard,
            str_contains($resourceName, 'goods-receipt') => Heroicon::InboxArrowDown,
            str_contains($resourceName, 'helpdesk'),
            str_contains($resourceName, 'ticket') => Heroicon::Lifebuoy,
            str_contains($resourceName, 'iso-control'),
            str_contains($resourceName, 'quality-standard') => Heroicon::ShieldCheck,
            str_contains($resourceName, 'journal-entry') => Heroicon::ClipboardDocumentList,
            str_contains($resourceName, 'kpi') => Heroicon::PresentationChartLine,
            str_contains($resourceName, 'lead'),
            str_contains($resourceName, 'applicant'),
            str_contains($resourceName, 'registration') => Heroicon::UserPlus,
            str_contains($resourceName, 'leave-request') => Heroicon::PaperAirplane,
            str_contains($resourceName, 'loan') => Heroicon::ArrowRightCircle,
            str_contains($resourceName, 'member') => Heroicon::UserCircle,
            str_contains($resourceName, 'menu') => Heroicon::QueueList,
            str_contains($resourceName, 'merch-order') => Heroicon::ShoppingBag,
            str_contains($resourceName, 'module') => Heroicon::PuzzlePiece,
            str_contains($resourceName, 'notification-template') => Heroicon::BellAlert,
            str_contains($resourceName, 'payroll'),
            str_contains($resourceName, 'salary') => Heroicon::Banknotes,
            str_contains($resourceName, 'policy') => Heroicon::ClipboardDocument,
            str_contains($resourceName, 'position') => Heroicon::Briefcase,
            str_contains($resourceName, 'procurement-item'),
            str_contains($resourceName, 'stock-item') => Heroicon::ArchiveBox,
            str_contains($resourceName, 'purchase-order'),
            str_contains($resourceName, 'purchase-requisition'),
            str_contains($resourceName, 'quotation'),
            str_contains($resourceName, 'rfq') => Heroicon::ShoppingCart,
            str_contains($resourceName, 'publisher') => Heroicon::BuildingLibrary,
            str_contains($resourceName, 'risk'),
            str_contains($resourceName, 'violation') => Heroicon::ExclamationTriangle,
            str_contains($resourceName, 'schedule'),
            str_contains($resourceName, 'shift'),
            str_contains($resourceName, 'timezone') => Heroicon::Clock,
            str_contains($resourceName, 'serial') => Heroicon::Newspaper,
            str_contains($resourceName, 'site') => Heroicon::GlobeAlt,
            str_contains($resourceName, 'software-license') => Heroicon::Key,
            str_contains($resourceName, 'stock-adjustment') => Heroicon::AdjustmentsHorizontal,
            str_contains($resourceName, 'stock-move') => Heroicon::ArrowsRightLeft,
            str_contains($resourceName, 'student-achievement') => Heroicon::Trophy,
            str_contains($resourceName, 'student-grade'),
            str_contains($resourceName, 'study-result') => Heroicon::ChartBar,
            str_contains($resourceName, 'student'),
            str_contains($resourceName, 'alumnus') => Heroicon::UserGroup,
            str_contains($resourceName, 'study-plan') => Heroicon::Map,
            str_contains($resourceName, 'subscription') => Heroicon::ReceiptPercent,
            str_contains($resourceName, 'thesis') => Heroicon::DocumentDuplicate,
            str_contains($resourceName, 'user') => Heroicon::Users,
            str_contains($resourceName, 'vehicle') => Heroicon::Truck,
            str_contains($resourceName, 'vendor-bill') => Heroicon::ReceiptRefund,
            str_contains($resourceName, 'vendor') => Heroicon::BuildingStorefront,
            str_contains($resourceName, 'visitor') => Heroicon::QrCode,
            str_contains($resourceName, 'warehouse') => Heroicon::ServerStack,
            str_contains($resourceName, 'webhook') => Heroicon::Signal,
            str_contains($resourceName, 'workflow-instance') => Heroicon::PlayCircle,
            str_contains($resourceName, 'workflow-step') => Heroicon::QueueList,
            str_contains($resourceName, 'workflow-transition') => Heroicon::ArrowPathRoundedSquare,
            str_contains($resourceName, 'workflow') => Heroicon::RectangleStack,
            default => Heroicon::Squares2x2,
        };
    }

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
        return static::$navigationIcon ?? static::resolveNavigationIconFromResourceName();
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
                'FoundationStructurePage' => 15,
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
                'AnnouncementResource' => 150,
                'BroadcastResource' => 160,
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
                'ExtracurricularResource' => 170,
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
                'LeadResource' => 15,
                'ApplicantResource' => 20,
                'RegistrationResource' => 30,
                'ExamScheduleResource' => 40,
                'ExamResultResource' => 50,
            ],
            'Cms' => [
                'SiteResource' => 10,
            ],
            'Donation' => [
                'CampaignResource' => 10,
            ],
            'Training' => [
                'TrainingProgramResource' => 10,
            ],
            'Sales' => [
                'CustomerResource' => 10,
            ],
            'Marketplace' => [
                'SellerResource' => 10,
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
            'Inventory' => [
                'WarehouseResource' => 10,
                'StockItemResource' => 20,
                'StockMoveResource' => 30,
                'StockAdjustmentResource' => 40,
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
            'Legal' => [
                'LegalDocumentResource' => 10,
                'ContractResource' => 20,
            ],
            'Asset' => [
                'AssetResource' => 10,
            ],
            'Dms' => [
                'DocumentResource' => 10,
            ],
            'Helpdesk' => [
                'HelpdeskDashboard' => 5,
                'TicketResource' => 10,
            ],
            'Facility' => [
                'RoomResource' => 10,
                'SustainabilityDashboard' => 90,
            ],
            'EOffice' => [
                'LetterResource' => 10,
            ],
            'ItOps' => [
                'SoftwareLicenseResource' => 10,
            ],
            'Transport' => [
                'VehicleResource' => 10,
            ],
            'Boarding' => [
                'DormitoryResource' => 10,
            ],
            'Cafeteria' => [
                'MenuResource' => 10,
            ],
            'PhysicalSecurity' => [
                'VisitorResource' => 10,
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
