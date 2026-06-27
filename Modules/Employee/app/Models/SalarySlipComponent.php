<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class SalarySlipComponent extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'salary_slip_id',
        'payroll_component_id',
        'component_type',
        'component_name',
        'calculation_type',
        'amount',
        'percentage',
        'base_amount',
        'formula_used',
        'is_taxable',
        'is_mandatory',
        'display_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'percentage' => 'decimal:2',
            'base_amount' => 'decimal:2',
            'is_taxable' => 'boolean',
            'is_mandatory' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<SalarySlip, $this>
     */
    public function salarySlip(): BelongsTo
    {
        return $this->belongsTo(SalarySlip::class);
    }

    /**
     * @return BelongsTo<PayrollComponent, $this>
     */
    public function payrollComponent(): BelongsTo
    {
        return $this->belongsTo(PayrollComponent::class);
    }
}
