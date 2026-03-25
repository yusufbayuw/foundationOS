<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;

class ViolationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'category',
        'severity_level',
        'default_sanctions',
        'point_weight',
        'description',
        'prevention_measures',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_sanctions' => 'array',
            'point_weight' => 'integer',
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

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }
}
