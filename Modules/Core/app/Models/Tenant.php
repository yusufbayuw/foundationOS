<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

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
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'subscribed_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'settings' => 'array',
            'max_users' => 'integer',
            'max_organizations' => 'integer',
            'max_storage_mb' => 'integer',
        ];
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function tenantRoles(): HasMany
    {
        return $this->hasMany(TenantRole::class);
    }

    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    public function subscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class);
    }

    public function tenantSettings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot(['is_enabled', 'enabled_at', 'disabled_at', 'settings'])
            ->withTimestamps();
    }

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

    public function curricula(): HasMany { return $this->hasMany(Curriculum::class); }
    public function subjects(): HasMany { return $this->hasMany(Subject::class); }
    public function students(): HasMany { return $this->hasMany(Student::class); }
    public function teachers(): HasMany { return $this->hasMany(Teacher::class); }
    public function schoolClasses(): HasMany { return $this->hasMany(SchoolClass::class, 'tenant_id'); }
    public function classStudents(): HasMany { return $this->hasMany(ClassStudent::class); }
    public function schedules(): HasMany { return $this->hasMany(Schedule::class); }
    public function attendances(): HasMany { return $this->hasMany(Attendance::class); }
    public function assessments(): HasMany { return $this->hasMany(Assessment::class); }
    public function assessmentItems(): HasMany { return $this->hasMany(AssessmentItem::class); }
    public function studentAssessmentAnswers(): HasMany { return $this->hasMany(StudentAssessmentAnswer::class); }
    public function studentGrades(): HasMany { return $this->hasMany(StudentGrade::class); }
    public function violationTypes(): HasMany { return $this->hasMany(ViolationType::class); }
    public function violations(): HasMany { return $this->hasMany(Violation::class); }
    public function achievementTypes(): HasMany { return $this->hasMany(AchievementType::class); }
    public function studentAchievements(): HasMany { return $this->hasMany(StudentAchievement::class); }
    public function admissionPeriods(): HasMany { return $this->hasMany(AdmissionPeriod::class); }
    public function applicants(): HasMany { return $this->hasMany(Applicant::class); }
    public function examSchedules(): HasMany { return $this->hasMany(ExamSchedule::class); }
    public function examResults(): HasMany { return $this->hasMany(ExamResult::class); }
    public function registrations(): HasMany { return $this->hasMany(Registration::class); }
    public function chartOfAccounts(): HasMany { return $this->hasMany(ChartOfAccount::class); }
    public function tuitionTypes(): HasMany { return $this->hasMany(TuitionType::class); }
    public function studentInvoices(): HasMany { return $this->hasMany(StudentInvoice::class); }
    public function studentInvoiceItems(): HasMany { return $this->hasMany(StudentInvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function journalEntries(): HasMany { return $this->hasMany(JournalEntry::class); }
    public function journalEntryLines(): HasMany { return $this->hasMany(JournalEntryLine::class); }
    public function budgets(): HasMany { return $this->hasMany(Budget::class); }
    public function bookCategories(): HasMany { return $this->hasMany(BookCategory::class); }
    public function books(): HasMany { return $this->hasMany(Book::class); }
    public function bookCopies(): HasMany { return $this->hasMany(BookCopy::class); }
    public function members(): HasMany { return $this->hasMany(Member::class); }
    public function bookReservations(): HasMany { return $this->hasMany(BookReservation::class); }
    public function libraryPolicies(): HasMany { return $this->hasMany(LibraryPolicy::class); }
    public function loans(): HasMany { return $this->hasMany(Loan::class); }
    public function fines(): HasMany { return $this->hasMany(Fine::class); }
    public function positions(): HasMany { return $this->hasMany(Position::class); }
    public function shifts(): HasMany { return $this->hasMany(Shift::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function employmentContracts(): HasMany { return $this->hasMany(EmploymentContract::class); }
    public function attendanceLogs(): HasMany { return $this->hasMany(AttendanceLog::class); }
    public function leaveRequests(): HasMany { return $this->hasMany(LeaveRequest::class); }
    public function payrollComponents(): HasMany { return $this->hasMany(PayrollComponent::class); }
    public function salarySlips(): HasMany { return $this->hasMany(SalarySlip::class); }
    public function salarySlipComponents(): HasMany { return $this->hasMany(SalarySlipComponent::class); }
    public function kpiTemplates(): HasMany { return $this->hasMany(KpiTemplate::class); }
    public function kpiIndicators(): HasMany { return $this->hasMany(KpiIndicator::class); }
    public function kpiScores(): HasMany { return $this->hasMany(KpiScore::class); }
    public function vendors(): HasMany { return $this->hasMany(Vendor::class); }
    public function procurementCategories(): HasMany { return $this->hasMany(ProcurementCategory::class); }
    public function procurementItems(): HasMany { return $this->hasMany(ProcurementItem::class); }
    public function purchaseRequisitions(): HasMany { return $this->hasMany(PurchaseRequisition::class); }
    public function purchaseRequisitionItems(): HasMany { return $this->hasMany(PurchaseRequisitionItem::class); }
    public function requestForQuotations(): HasMany { return $this->hasMany(RequestForQuotation::class); }
    public function rfqItems(): HasMany { return $this->hasMany(RfqItem::class); }
    public function rfqVendors(): HasMany { return $this->hasMany(RfqVendor::class); }
    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }
    public function purchaseOrderItems(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
    public function goodsReceipts(): HasMany { return $this->hasMany(GoodsReceipt::class); }
    public function goodsReceiptItems(): HasMany { return $this->hasMany(GoodsReceiptItem::class); }
    public function vendorBills(): HasMany { return $this->hasMany(VendorBill::class); }
    public function vendorBillItems(): HasMany { return $this->hasMany(VendorBillItem::class); }
    public function faculties(): HasMany { return $this->hasMany(Faculty::class); }
    public function studyPrograms(): HasMany { return $this->hasMany(StudyProgram::class); }
    public function courses(): HasMany { return $this->hasMany(Course::class); }
    public function lecturers(): HasMany { return $this->hasMany(Lecturer::class); }
    public function collageStudents(): HasMany { return $this->hasMany(CollageStudent::class); }
    public function courseOfferings(): HasMany { return $this->hasMany(CourseOffering::class); }
    public function studyPlans(): HasMany { return $this->hasMany(StudyPlan::class); }
    public function studyPlanItems(): HasMany { return $this->hasMany(StudyPlanItem::class); }
    public function studyResults(): HasMany { return $this->hasMany(StudyResult::class); }
    public function feederLogs(): HasMany { return $this->hasMany(FeederLog::class); }
    public function theses(): HasMany { return $this->hasMany(Thesis::class); }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function fileUploads(): HasMany
    {
        return $this->hasMany(FileUpload::class);
    }

    public function auditableLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function attachedFiles(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /** @return HasMany<\Modules\Core\Models\AcademicPeriod, self> */
    public function academicPeriods(): HasMany
    {
        return $this->hasMany(\Modules\Core\Models\AcademicPeriod::class);
    }

}
