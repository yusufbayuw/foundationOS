<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\FeederLog;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Campus\Models\StudyResult;
use Modules\Campus\Models\Thesis;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmploymentContract;
use Modules\Employee\Models\KpiIndicator;
use Modules\Employee\Models\KpiScore;
use Modules\Employee\Models\KpiTemplate;
use Modules\Employee\Models\LeaveRequest;
use Modules\Employee\Models\PayrollComponent;
use Modules\Employee\Models\Position;
use Modules\Employee\Models\SalarySlip;
use Modules\Employee\Models\SalarySlipComponent;
use Modules\Employee\Models\Shift;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Models\ExamResult;
use Modules\Enrollment\Models\ExamSchedule;
use Modules\Enrollment\Models\Registration;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Models\StudentInvoiceItem;
use Modules\Finance\Models\TuitionType;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCategory;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\BookReservation;
use Modules\Library\Models\Fine;
use Modules\Library\Models\LibraryPolicy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\GoodsReceiptItem;
use Modules\Procurement\Models\ProcurementCategory;
use Modules\Procurement\Models\ProcurementItem;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionItem;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\RfqItem;
use Modules\Procurement\Models\RfqVendor;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Modules\Procurement\Models\VendorBillItem;
use Modules\School\Models\AchievementType;
use Modules\School\Models\Assessment;
use Modules\School\Models\AssessmentItem;
use Modules\School\Models\Attendance;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Curriculum;
use Modules\School\Models\Schedule;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\StudentAchievement;
use Modules\School\Models\StudentAssessmentAnswer;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Subject;
use Modules\School\Models\Teacher;
use Modules\School\Models\Violation;
use Modules\School\Models\ViolationType;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Tenant extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory, LogsActivity, SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'domain', 'status', 'subscription_plan_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'domain',
        'subdomain',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'timezone',
        'currency',
        'locale',
        'billing_cycle',
        'status',
        'trial_ends_at',
        'subscribed_at',
        'subscription_expires_at',
        'settings',
        'max_users',
        'max_organizations',
        'max_storage_mb',
        'meta_title',
        'meta_description',
        'subscription_plan_id',
        'grace_period_ends_at',
        'midtrans_customer_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'subscribed_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'grace_period_ends_at' => 'datetime',
            'settings' => 'array',
            'max_users' => 'integer',
            'max_organizations' => 'integer',
            'max_storage_mb' => 'integer',
        ];
    }

    public function isSubscriptionActive(): bool
    {
        return in_array($this->status, ['active', 'trial'], true)
            && ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    public function isInGracePeriod(): bool
    {
        return $this->status === 'past_due'
            && $this->grace_period_ends_at !== null
            && $this->grace_period_ends_at->isFuture();
    }

    public function isLocked(): bool
    {
        return $this->status === 'suspended'
            || ($this->status === 'past_due' && ($this->grace_period_ends_at === null || $this->grace_period_ends_at->isPast()));
    }

    /**
     * @return BelongsTo<SubscriptionPlan, $this>
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<Organization, $this>
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * @return HasMany<TenantRole, $this>
     */
    public function tenantRoles(): HasMany
    {
        return $this->hasMany(TenantRole::class);
    }

    /**
     * @return HasMany<UserTenantRole, $this>
     */
    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    /**
     * @return HasMany<SubscriptionLog, $this>
     */
    public function subscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class);
    }

    /**
     * @return HasMany<TenantSetting, $this>
     */
    public function tenantSettings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    /**
     * @return HasMany<AcademicYear, $this>
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    /**
     * @return HasMany<Department, $this>
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * @return HasMany<TenantModule, $this>
     */
    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    /**
     * @return BelongsToMany<Module, $this>
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot(['is_enabled', 'enabled_at', 'disabled_at', 'settings'])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tenant_roles')
            ->withPivot([
                'organization_id',
                'tenant_role_id',
                'assigned_by',
                'assigned_at',
                'expires_at',
                'is_primary',
            ])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Curriculum, $this>
     */
    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    /**
     * @return HasMany<Subject, $this>
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * @return HasMany<Teacher, $this>
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * @return HasMany<SchoolClass, $this>
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'tenant_id');
    }

    /**
     * @return HasMany<ClassStudent, $this>
     */
    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class);
    }

    /**
     * @return HasMany<Schedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return HasMany<Assessment, $this>
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * @return HasMany<AssessmentItem, $this>
     */
    public function assessmentItems(): HasMany
    {
        return $this->hasMany(AssessmentItem::class);
    }

    /**
     * @return HasMany<StudentAssessmentAnswer, $this>
     */
    public function studentAssessmentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class);
    }

    /**
     * @return HasMany<StudentGrade, $this>
     */
    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * @return HasMany<ViolationType, $this>
     */
    public function violationTypes(): HasMany
    {
        return $this->hasMany(ViolationType::class);
    }

    /**
     * @return HasMany<Violation, $this>
     */
    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    /**
     * @return HasMany<AchievementType, $this>
     */
    public function achievementTypes(): HasMany
    {
        return $this->hasMany(AchievementType::class);
    }

    /**
     * @return HasMany<StudentAchievement, $this>
     */
    public function studentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }

    /**
     * @return HasMany<AdmissionPeriod, $this>
     */
    public function admissionPeriods(): HasMany
    {
        return $this->hasMany(AdmissionPeriod::class);
    }

    /**
     * @return HasMany<Applicant, $this>
     */
    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    /**
     * @return HasMany<ExamSchedule, $this>
     */
    public function examSchedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    /**
     * @return HasMany<ExamResult, $this>
     */
    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * @return HasMany<ChartOfAccount, $this>
     */
    public function chartOfAccounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class);
    }

    /**
     * @return HasMany<TuitionType, $this>
     */
    public function tuitionTypes(): HasMany
    {
        return $this->hasMany(TuitionType::class);
    }

    /**
     * @return HasMany<StudentInvoice, $this>
     */
    public function studentInvoices(): HasMany
    {
        return $this->hasMany(StudentInvoice::class);
    }

    /**
     * @return HasMany<StudentInvoiceItem, $this>
     */
    public function studentInvoiceItems(): HasMany
    {
        return $this->hasMany(StudentInvoiceItem::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<JournalEntry, $this>
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * @return HasMany<JournalEntryLine, $this>
     */
    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    /**
     * @return HasMany<Budget, $this>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * @return HasMany<BookCategory, $this>
     */
    public function bookCategories(): HasMany
    {
        return $this->hasMany(BookCategory::class);
    }

    /**
     * @return HasMany<Book, $this>
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * @deprecated Query Modules\Library\Models\BookCopy by tenant_id from the Library module instead.
     */
    public function bookCopies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    /**
     * @return HasMany<Member, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * @return HasMany<BookReservation, $this>
     */
    public function bookReservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }

    /**
     * @return HasMany<LibraryPolicy, $this>
     */
    public function libraryPolicies(): HasMany
    {
        return $this->hasMany(LibraryPolicy::class);
    }

    /**
     * @return HasMany<Loan, $this>
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * @return HasMany<Fine, $this>
     */
    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * @return HasMany<Position, $this>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * @return HasMany<Shift, $this>
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @return HasMany<EmploymentContract, $this>
     */
    public function employmentContracts(): HasMany
    {
        return $this->hasMany(EmploymentContract::class);
    }

    /**
     * @return HasMany<AttendanceLog, $this>
     */
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    /**
     * @return HasMany<LeaveRequest, $this>
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * @return HasMany<PayrollComponent, $this>
     */
    public function payrollComponents(): HasMany
    {
        return $this->hasMany(PayrollComponent::class);
    }

    /**
     * @return HasMany<SalarySlip, $this>
     */
    public function salarySlips(): HasMany
    {
        return $this->hasMany(SalarySlip::class);
    }

    /**
     * @return HasMany<SalarySlipComponent, $this>
     */
    public function salarySlipComponents(): HasMany
    {
        return $this->hasMany(SalarySlipComponent::class);
    }

    /**
     * @return HasMany<KpiTemplate, $this>
     */
    public function kpiTemplates(): HasMany
    {
        return $this->hasMany(KpiTemplate::class);
    }

    /**
     * @return HasMany<KpiIndicator, $this>
     */
    public function kpiIndicators(): HasMany
    {
        return $this->hasMany(KpiIndicator::class);
    }

    /**
     * @return HasMany<KpiScore, $this>
     */
    public function kpiScores(): HasMany
    {
        return $this->hasMany(KpiScore::class);
    }

    /**
     * @return HasMany<Vendor, $this>
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * @return HasMany<ProcurementCategory, $this>
     */
    public function procurementCategories(): HasMany
    {
        return $this->hasMany(ProcurementCategory::class);
    }

    /**
     * @return HasMany<ProcurementItem, $this>
     */
    public function procurementItems(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }

    /**
     * @deprecated Query Modules\Procurement\Models\PurchaseRequisition by tenant_id from the Procurement module instead.
     */
    public function purchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class);
    }

    /**
     * @return HasMany<PurchaseRequisitionItem, $this>
     */
    public function purchaseRequisitionItems(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    /**
     * @deprecated Query Modules\Procurement\Models\RequestForQuotation by tenant_id from the Procurement module instead.
     */
    public function requestForQuotations(): HasMany
    {
        return $this->hasMany(RequestForQuotation::class);
    }

    /**
     * @return HasMany<RfqItem, $this>
     */
    public function rfqItems(): HasMany
    {
        return $this->hasMany(RfqItem::class);
    }

    /**
     * @return HasMany<RfqVendor, $this>
     */
    public function rfqVendors(): HasMany
    {
        return $this->hasMany(RfqVendor::class);
    }

    /**
     * @return HasMany<PurchaseOrder, $this>
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * @return HasMany<PurchaseOrderItem, $this>
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * @return HasMany<GoodsReceipt, $this>
     */
    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    /**
     * @return HasMany<GoodsReceiptItem, $this>
     */
    public function goodsReceiptItems(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    /**
     * @return HasMany<VendorBill, $this>
     */
    public function vendorBills(): HasMany
    {
        return $this->hasMany(VendorBill::class);
    }

    /**
     * @return HasMany<VendorBillItem, $this>
     */
    public function vendorBillItems(): HasMany
    {
        return $this->hasMany(VendorBillItem::class);
    }

    /**
     * @return HasMany<Faculty, $this>
     */
    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    /**
     * @return HasMany<StudyProgram, $this>
     */
    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class);
    }

    /**
     * @return HasMany<Course, $this>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * @return HasMany<Lecturer, $this>
     */
    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    /**
     * @return HasMany<CollageStudent, $this>
     */
    public function collageStudents(): HasMany
    {
        return $this->hasMany(CollageStudent::class);
    }

    /**
     * @return HasMany<CourseOffering, $this>
     */
    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    /**
     * @return HasMany<StudyPlan, $this>
     */
    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    /**
     * @return HasMany<StudyPlanItem, $this>
     */
    public function studyPlanItems(): HasMany
    {
        return $this->hasMany(StudyPlanItem::class);
    }

    /**
     * @return HasMany<StudyResult, $this>
     */
    public function studyResults(): HasMany
    {
        return $this->hasMany(StudyResult::class);
    }

    /**
     * @return HasMany<FeederLog, $this>
     */
    public function feederLogs(): HasMany
    {
        return $this->hasMany(FeederLog::class);
    }

    /**
     * @return HasMany<Thesis, $this>
     */
    public function theses(): HasMany
    {
        return $this->hasMany(Thesis::class);
    }

    /**
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * @return HasMany<FileUpload, $this>
     */
    public function fileUploads(): HasMany
    {
        return $this->hasMany(FileUpload::class);
    }

    /**
     * @return MorphMany<AuditLog, $this>
     */
    public function auditableLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * @return MorphMany<FileUpload, $this>
     */
    public function attachedFiles(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /** @return HasMany<AcademicPeriod, $this> */
    public function academicPeriods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class);
    }
}
