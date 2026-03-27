<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;

class KpiIndicator extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'description',
        'category',
        'measurement_unit',
        'target_type',
        'target_value',
        'target_minimum',
        'target_maximum',
        'weight_percentage',
        'scoring_method',
        'formula',
        'data_source',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_value' => 'decimal:2',
            'target_minimum' => 'decimal:2',
            'target_maximum' => 'decimal:2',
            'weight_percentage' => 'decimal:2',
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
}
