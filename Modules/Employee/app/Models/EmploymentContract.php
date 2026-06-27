<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class EmploymentContract extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return BelongsTo<EmploymentContract, $this>
     */
    public function previousContract(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_contract_id');
    }

    public function isPrintable(): bool
    {
        return ! in_array((string) $this->status, ['draft', 'terminated', 'cancelled'], true);
    }
}
