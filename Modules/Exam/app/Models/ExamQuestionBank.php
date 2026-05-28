<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\Organization;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\School\Models\Curriculum;
use Modules\School\Models\Subject;

class ExamQuestionBank extends ExamModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'academic_context_type',
        'school_subject_reference',
        'school_grade_level_reference',
        'school_curriculum_reference',
        'campus_course_reference',
        'campus_study_program_reference',
        'standalone_subject',
        'standalone_level',
        'name',
        'code',
        'description',
        'status',
        'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'academic_context_type' => ExamAcademicContext::class,
            'status' => QuestionBankStatus::class,
            'metadata_json' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function examQuestions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function isSchool(): bool
    {
        return $this->academic_context_type === ExamAcademicContext::School;
    }

    public function isCampus(): bool
    {
        return $this->academic_context_type === ExamAcademicContext::Campus;
    }

    public function isStandalone(): bool
    {
        return $this->academic_context_type === ExamAcademicContext::Standalone;
    }

    public function schoolSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'school_subject_reference');
    }

    public function schoolCurriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class, 'school_curriculum_reference');
    }

    public function campusCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'campus_course_reference');
    }

    public function campusStudyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'campus_study_program_reference');
    }
}
