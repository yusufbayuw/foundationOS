<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Course extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'study_program_id',
        'code',
        'name',
        'credits',
        'theory_credits',
        'practicum_credits',
        'semester_level',
        'course_type',
        'is_mandatory',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'theory_credits' => 'integer',
            'practicum_credits' => 'integer',
            'semester_level' => 'integer',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function studyPlanItems(): HasMany
    {
        return $this->hasMany(StudyPlanItem::class);
    }
}
