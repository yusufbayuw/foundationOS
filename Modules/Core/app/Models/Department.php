<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Employee\Models\KpiTemplate;
use Modules\Employee\Models\Position;
use Modules\Enrollment\Models\Applicant;
use Modules\School\Models\SchoolClass;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Department extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function kpiTemplates(): HasMany
    {
        return $this->hasMany(KpiTemplate::class);
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function firstChoiceApplicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'program_choice_1_id');
    }

    public function secondChoiceApplicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'program_choice_2_id');
    }

    public function acceptedApplicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'accepted_program_id');
    }
}
