<?php

namespace Modules\Core\Filament\Support\Navigation;

class NavigationSortRegistry
{
    public static function sortFor(string $module, string $resource): ?int
    {
        return self::map()[$module][$resource] ?? null;
    }

    /**
     * Navigation order per module to reflect typical operational flow.
     *
     * @return array<string, array<string, int>>
     */
    public static function map(): array
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
            'Exam' => [
                'ExamDefinitionResource' => 10,
                'ExamQuestionBankResource' => 20,
                'ExamQuestionResource' => 25,
                'ExamParticipantResource' => 30,
                'ExamTokenResource' => 40,
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
                'MoodleSyncOutboxResource' => 25,
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
            'Ai' => [
                'AiPromptTemplateResource' => 10,
            ],
        ];
    }
}
