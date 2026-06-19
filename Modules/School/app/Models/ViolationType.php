<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;

class ViolationType extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }
}
