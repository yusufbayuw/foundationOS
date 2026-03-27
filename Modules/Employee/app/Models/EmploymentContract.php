<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;

class EmploymentContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'previous_contract_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'probation_period_months',
        'basic_salary',
        'allowance_details',
        'benefits',
        'work_location',
        'work_hours_per_week',
        'termination_clause',
        'status',
        'signed_by_employee',
        'signed_by_employer',
        'document_file',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'probation_period_months' => 'integer',
            'basic_salary' => 'decimal:2',
            'allowance_details' => 'array',
            'benefits' => 'array',
            'work_hours_per_week' => 'integer',
            'signed_by_employee' => 'boolean',
            'signed_by_employer' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function previousContract(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_contract_id');
    }
}
