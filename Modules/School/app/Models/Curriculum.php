<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Curriculum extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'academic_period_id',
        'name',
        'code',
        'type',
        'grade_levels',
        'effective_date',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'grade_levels' => 'array',
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
