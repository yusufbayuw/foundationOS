<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Employee\Models\KpiTemplate;
use Modules\Employee\Models\Position;
use Modules\Enrollment\Models\Applicant;
use Modules\School\Models\SchoolClass;

class Department extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return HasMany<Position, $this>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * @return HasMany<KpiTemplate, $this>
     */
    public function kpiTemplates(): HasMany
    {
        return $this->hasMany(KpiTemplate::class);
    }

    /**
     * @return HasMany<SchoolClass, $this>
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * @return HasMany<Applicant, $this>
     */
    public function firstChoiceApplicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'program_choice_1_id');
    }

    /**
     * @return HasMany<Applicant, $this>
     */
    public function secondChoiceApplicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'program_choice_2_id');
    }

    /**
     * @return HasMany<Applicant, $this>
     */
    public function acceptedApplicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'accepted_program_id');
    }
}
