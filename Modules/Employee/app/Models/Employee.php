<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Department;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;

class Employee extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'user_id',
        'department_id',
        'position_id',
        'employee_number',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'marital_status',
        'id_number',
        'npwp',
        'address',
        'phone',
        'email',
        'emergency_contact',
        'photo',
        'employment_type',
        'employment_status',
        'join_date',
        'end_date',
        'probation_end_date',
        'grade_level',
        'basic_salary',
        'bank_account',
        'bank_name',
        'account_holder',
        'bpjs_tk_number',
        'bpjs_kes_number',
        'insurance_number',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'emergency_contact' => 'array',
            'join_date' => 'date',
            'end_date' => 'date',
            'probation_end_date' => 'date',
            'basic_salary' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
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
     * @return HasMany<SalarySlip, $this>
     */
    public function salarySlips(): HasMany
    {
        return $this->hasMany(SalarySlip::class);
    }

    /**
     * @return HasMany<KpiScore, $this>
     */
    public function kpiScores(): HasMany
    {
        return $this->hasMany(KpiScore::class);
    }

    /**
     * @return HasMany<EmployeeDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }
}
