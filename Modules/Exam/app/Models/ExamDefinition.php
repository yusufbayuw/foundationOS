<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamPurpose;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\GradeSyncMode;
use Modules\School\Models\Assessment;

class ExamDefinition extends ExamModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'academic_period_id',
        'owner_user_id',
        'school_assessment_id',
        'exam_academic_context',
        'exam_purpose',
        'context_reference_type',
        'context_reference_id',
        'context_reference_uuid',
        'metadata_json',
        'grade_sync_mode',
        'grade_sync_target',
        'name',
        'code',
        'description',
        'status',
        'max_score',
        'passing_score',
        'duration_minutes',
        'max_attempts',
        'shuffle_questions',
        'starts_at',
        'ends_at',
        'runtime_exam_id',
        'published_at',
        'last_published_at',
    ];

    protected function casts(): array
    {
        return [
            'exam_academic_context' => ExamAcademicContext::class,
            'exam_purpose' => ExamPurpose::class,
            'status' => ExamStatus::class,
            'grade_sync_mode' => GradeSyncMode::class,
            'metadata_json' => 'array',
            'max_score' => 'decimal:2',
            'passing_score' => 'decimal:2',
            'duration_minutes' => 'integer',
            'max_attempts' => 'integer',
            'shuffle_questions' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
            'last_published_at' => 'datetime',
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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function schoolAssessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'school_assessment_id');
    }

    public function examPackages(): HasMany
    {
        return $this->hasMany(ExamPackage::class);
    }

    public function examParticipants(): HasMany
    {
        return $this->hasMany(ExamParticipant::class);
    }

    public function examPublishSnapshots(): HasMany
    {
        return $this->hasMany(ExamPublishSnapshot::class);
    }

    public function examAttemptSyncs(): HasMany
    {
        return $this->hasMany(ExamAttemptSync::class);
    }
}
