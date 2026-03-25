<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;

class TuitionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'education_level',
        'amount',
        'frequency',
        'due_day',
        'grace_period_days',
        'late_fee_percentage',
        'late_fee_fixed',
        'discount_eligible',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_day' => 'integer',
            'grace_period_days' => 'integer',
            'late_fee_percentage' => 'decimal:2',
            'late_fee_fixed' => 'decimal:2',
            'discount_eligible' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function studentInvoiceItems(): HasMany
    {
        return $this->hasMany(StudentInvoiceItem::class);
    }

    public function studentInvoices(): HasMany
    {
        return $this->hasMany(StudentInvoice::class);
    }
}
