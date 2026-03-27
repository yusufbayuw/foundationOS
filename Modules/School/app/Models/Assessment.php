<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'academic_period_id',
        'subject_id',
        'class_id',
        'name',
        'code',
        'type',
        'assessment_category',
        'weight',
        'max_score',
        'passing_score',
        'schedule_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'instructions',
        'attachments',
        'is_published',
        'published_at',
        'allow_retake',
        'max_attempts',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'max_score' => 'decimal:2',
            'passing_score' => 'decimal:2',
            'schedule_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'duration_minutes' => 'integer',
            'attachments' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'allow_retake' => 'boolean',
            'max_attempts' => 'integer',
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

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function assessmentItems(): HasMany
    {
        return $this->hasMany(AssessmentItem::class);
    }

    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function studentAssessmentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class);
    }
}
