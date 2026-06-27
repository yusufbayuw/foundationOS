<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamPurpose;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ExamType;
use Modules\Exam\Enums\GradeSyncMode;
use Modules\School\Models\Assessment;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Subject;
use Modules\School\Models\Teacher;

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
        'exam_type',
        'context_reference_type',
        'context_reference_id',
        'context_reference_uuid',
        'metadata_json',
        'grade_sync_mode',
        'grade_sync_target',
        'academic_year_reference',
        'school_semester_reference',
        'school_class_reference',
        'school_subject_reference',
        'school_grade_level_reference',
        'school_teacher_reference',
        'campus_academic_year_reference',
        'campus_academic_term_reference',
        'campus_faculty_reference',
        'campus_study_program_reference',
        'campus_course_reference',
        'campus_class_reference',
        'campus_lecturer_reference',
        'standalone_subject',
        'standalone_level',
        'target_description',
        'name',
        'code',
        'description',
        'status',
        'max_score',
        'passing_score',
        'duration_minutes',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'show_result',
        'show_explanation',
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
            'exam_type' => ExamType::class,
            'status' => ExamStatus::class,
            'grade_sync_mode' => GradeSyncMode::class,
            'metadata_json' => 'array',
            'max_score' => 'decimal:2',
            'passing_score' => 'decimal:2',
            'duration_minutes' => 'integer',
            'max_attempts' => 'integer',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_result' => 'boolean',
            'show_explanation' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
            'last_published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ExamDefinition $definition): void {
            if ($definition->exam_type !== null && $definition->exam_purpose === null) {
                $definition->exam_purpose = $definition->exam_type->toExamPurpose();
            }

            if ($definition->school_semester_reference !== null) {
                $definition->academic_period_id = $definition->school_semester_reference;
            } elseif ($definition->campus_academic_term_reference !== null) {
                $definition->academic_period_id = $definition->campus_academic_term_reference;
            }
        });
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * @return BelongsTo<Assessment, $this>
     */
    public function schoolAssessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'school_assessment_id');
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_reference');
    }

    /**
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function schoolSemester(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class, 'school_semester_reference');
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_reference');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function schoolSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'school_subject_reference');
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function schoolTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'school_teacher_reference');
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function campusAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'campus_academic_year_reference');
    }

    /**
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function campusAcademicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class, 'campus_academic_term_reference');
    }

    /**
     * @return BelongsTo<Faculty, $this>
     */
    public function campusFaculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'campus_faculty_reference');
    }

    /**
     * @return BelongsTo<StudyProgram, $this>
     */
    public function campusStudyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'campus_study_program_reference');
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function campusCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'campus_course_reference');
    }

    /**
     * @return BelongsTo<CourseOffering, $this>
     */
    public function campusClass(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'campus_class_reference');
    }

    /**
     * @return BelongsTo<Lecturer, $this>
     */
    public function campusLecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'campus_lecturer_reference');
    }

    /**
     * @return HasMany<ExamPackage, $this>
     */
    public function examPackages(): HasMany
    {
        return $this->hasMany(ExamPackage::class);
    }

    /**
     * @return HasMany<ExamDefinitionQuestion, $this>
     */
    public function examDefinitionQuestions(): HasMany
    {
        return $this->hasMany(ExamDefinitionQuestion::class)->orderBy('sort_order');
    }

    /**
     * @return BelongsToMany<ExamQuestion, $this, ExamDefinitionQuestion, 'pivot'>
     */
    public function examQuestions(): BelongsToMany
    {
        return $this->belongsToMany(ExamQuestion::class, 'exam_definition_questions')
            ->using(ExamDefinitionQuestion::class)
            ->withPivot(['id', 'sort_order', 'score_override'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * @return HasMany<ExamParticipant, $this>
     */
    public function examParticipants(): HasMany
    {
        return $this->hasMany(ExamParticipant::class);
    }

    /**
     * @return HasMany<ExamPublishSnapshot, $this>
     */
    public function examPublishSnapshots(): HasMany
    {
        return $this->hasMany(ExamPublishSnapshot::class);
    }

    /**
     * @return HasMany<ExamAttemptSync, $this>
     */
    public function examAttemptSyncs(): HasMany
    {
        return $this->hasMany(ExamAttemptSync::class);
    }

    /**
     * @return HasMany<ExamRuntimeSyncLog, $this>
     */
    public function examRuntimeSyncLogs(): HasMany
    {
        return $this->hasMany(ExamRuntimeSyncLog::class);
    }

    /**
     * @return HasMany<ExamAttempt, $this>
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * @return HasMany<ExamResult, $this>
     */
    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Alias for analytics tab (same underlying results).
     */
    public function examAnalytics(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * @return HasMany<ExamAnswer, $this>
     */
    public function examAnswers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    /**
     * @return HasMany<ExamActivityLog, $this>
     */
    public function examActivityLogs(): HasMany
    {
        return $this->hasMany(ExamActivityLog::class);
    }

    /**
     * @return HasMany<ExamExportLog, $this>
     */
    public function examExportLogs(): HasMany
    {
        return $this->hasMany(ExamExportLog::class);
    }

    /**
     * @return HasMany<ExamGradebookExportLog, $this>
     */
    public function examGradebookExportLogs(): HasMany
    {
        return $this->hasMany(ExamGradebookExportLog::class);
    }

    public function isSchool(): bool
    {
        return $this->exam_academic_context === ExamAcademicContext::School;
    }

    public function isCampus(): bool
    {
        return $this->exam_academic_context === ExamAcademicContext::Campus;
    }

    public function isStandalone(): bool
    {
        return $this->exam_academic_context === ExamAcademicContext::Standalone;
    }
}
