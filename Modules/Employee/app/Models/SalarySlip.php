<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Employee\Enums\SalarySlipStatus;

class SalarySlip extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'period_month',
        'period_year',
        'period_label',
        'basic_salary',
        'earnings_details',
        'deductions_details',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'tax_details',
        'bpjs_details',
        'working_days',
        'working_hours',
        'overtime_hours',
        'leave_days',
        'absent_days',
        'status',
        'paid_at',
        'paid_via',
        'notes',
        'is_sent',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'earnings_details' => 'array',
            'deductions_details' => 'array',
            'total_earnings' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'tax_details' => 'array',
            'bpjs_details' => 'array',
            'working_days' => 'integer',
            'working_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'leave_days' => 'integer',
            'absent_days' => 'integer',
            'status' => SalarySlipStatus::class,
            'paid_at' => 'datetime',
            'is_sent' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function isLockedForMutation(): bool
    {
        return $this->status?->isLockedForMutation() ?? false;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(SalarySlipComponent::class);
    }
}
