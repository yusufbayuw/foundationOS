<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Department;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Employee extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

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
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function employmentContracts(): HasMany
    {
        return $this->hasMany(EmploymentContract::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function salarySlips(): HasMany
    {
        return $this->hasMany(SalarySlip::class);
    }

    public function kpiScores(): HasMany
    {
        return $this->hasMany(KpiScore::class);
    }
}
