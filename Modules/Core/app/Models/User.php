<?php

namespace Modules\Core\Models;

use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Modules\Campus\Models\FeederLog;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyPlan;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\KpiScore;
use Modules\Employee\Models\LeaveRequest;
use Modules\Enrollment\Models\ExamResult;
use Modules\Enrollment\Models\Registration;
use Modules\Finance\Models\Budget as FinanceBudget;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Payment;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\VendorBill;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;
use Modules\School\Models\StudentAchievement;
use Modules\School\Models\StudentAssessmentAnswer;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Teacher;
use Modules\School\Models\Violation;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasDefaultTenant, HasEmailAuthentication, HasTenants
{
    use HasApiTokens, HasFactory, HasRoles, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, InteractsWithEmailAuthentication, LogsActivity, Notifiable, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'status', 'is_super_admin', 'preferred_locale'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'avatar',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'timezone',
        'locale',
        'preferred_locale',
        'status',
        'is_super_admin',
        'pinned_menus',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
        'has_email_authentication',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'login_attempts' => 'integer',
            'is_super_admin' => 'boolean',
            'password' => 'hashed',
            'pinned_menus' => 'array',
        ];
    }

    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    public function assignedTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class, 'assigned_by');
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'user_tenant_roles')
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

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'user_tenant_roles')
            ->withPivot([
                'tenant_id',
                'tenant_role_id',
                'assigned_by',
                'assigned_at',
                'expires_at',
                'is_primary',
            ])
            ->withTimestamps();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (! $tenant instanceof Tenant) {
            return false;
        }

        return $this->userTenantRoles()
            ->where('tenant_id', $tenant->getKey())
            ->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->tenants()
            ->select('tenants.*')
            ->distinct()
            ->orderBy('tenants.name')
            ->get();
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        $primaryAssignment = $this->userTenantRoles()
            ->where('is_primary', true)
            ->with('tenant')
            ->first();

        if ($primaryAssignment?->tenant) {
            return $primaryAssignment->tenant;
        }

        return $this->getTenants($panel)->first();
    }

    public function createdTenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'created_by');
    }

    public function principalOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'principal_user_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function libraryMembers(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    public function postedJournalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'posted_by');
    }

    public function approvedBudgets(): HasMany
    {
        return $this->hasMany(FinanceBudget::class, 'approved_by');
    }

    public function requestedPurchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class, 'requested_by');
    }

    public function ownedPurchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class);
    }

    public function approvedPurchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class, 'approved_by');
    }

    public function createdRequestForQuotations(): HasMany
    {
        return $this->hasMany(RequestForQuotation::class, 'created_by');
    }

    public function approvedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    public function receivedGoodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class, 'received_by');
    }

    public function inspectedGoodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class, 'inspected_by');
    }

    public function processedVendorBills(): HasMany
    {
        return $this->hasMany(VendorBill::class, 'processed_by');
    }

    public function processedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'processed_by');
    }

    public function returnedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'returned_by');
    }

    public function approvedAttendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'approved_by');
    }

    public function supervisedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'supervisor_id');
    }

    public function approvedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'approver_id');
    }

    public function evaluatedKpiScores(): HasMany
    {
        return $this->hasMany(KpiScore::class, 'evaluator_id');
    }

    public function verifiedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'verified_by');
    }

    public function reportedViolations(): HasMany
    {
        return $this->hasMany(Violation::class, 'reported_by');
    }

    public function handledViolations(): HasMany
    {
        return $this->hasMany(Violation::class, 'handled_by');
    }

    public function gradedStudentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class, 'graded_by');
    }

    public function gradedStudentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class, 'graded_by');
    }

    public function verifiedStudentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class, 'verified_by');
    }

    public function completedRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'completed_by');
    }

    public function examinedExamResults(): HasMany
    {
        return $this->hasMany(ExamResult::class, 'examiner_id');
    }

    public function approvedStudyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class, 'approved_by');
    }

    public function syncedFeederLogs(): HasMany
    {
        return $this->hasMany(FeederLog::class, 'synced_by');
    }

    public function processedSubscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class, 'processed_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(FileUpload::class, 'uploaded_by');
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function isGlobalSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'platform') {
            setPermissionsTeamId(0);

            return $this->hasRole('platform_owner');
        }

        if ($panel->getId() === 'admin') {
            if ($this->isGlobalSuperAdmin()) {
                return true;
            }

            return $this->userTenantRoles()->exists();
        }

        return false;
    }
}
