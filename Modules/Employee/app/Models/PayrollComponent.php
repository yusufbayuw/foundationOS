<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Concerns\BelongsToTenant;

class PayrollComponent extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'type',
        'category',
        'calculation_type',
        'amount',
        'percentage',
        'formula',
        'is_taxable',
        'is_mandatory',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'percentage' => 'decimal:2',
            'is_taxable' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function salarySlipComponents(): HasMany
    {
        return $this->hasMany(SalarySlipComponent::class);
    }
}
