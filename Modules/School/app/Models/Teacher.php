<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'user_id',
        'nip',
        'nuptk',
        'nrg',
        'status',
        'employment_status',
        'join_date',
        'resignation_date',
        'is_certified',
        'certification_year',
        'certification_number',
        'highest_education',
        'major_study',
        'university',
        'functional_position',
        'structural_position',
        'subject_specializations',
        'class_advisor_history',
        'teaching_hours_per_week',
        'base_salary',
        'allowance',
        'bpjs_tk_number',
        'bpjs_kes_number',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'resignation_date' => 'date',
            'is_certified' => 'boolean',
            'subject_specializations' => 'array',
            'class_advisor_history' => 'array',
            'teaching_hours_per_week' => 'integer',
            'base_salary' => 'decimal:2',
            'allowance' => 'decimal:2',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    public function assistantClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'assistant_teacher_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
