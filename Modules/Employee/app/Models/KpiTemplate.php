<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Department;
use Modules\Core\Models\Organization;

class KpiTemplate extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'department_id',
        'position_id',
        'name',
        'indicators',
        'total_weight',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'indicators' => 'array',
            'total_weight' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function kpiScores(): HasMany
    {
        return $this->hasMany(KpiScore::class);
    }
}
