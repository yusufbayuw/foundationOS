<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;
use Modules\Exam\Enums\QuestionType;

class ExamAnswer extends ExamModel
{
    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exam_attempt_id',
        'exam_question_id',
        'runtime_answer_id',
        'answer_value',
        'is_correct',
        'score',
        'manual_score',
        'feedback',
        'rubric_json',
        'graded_by',
        'graded_at',
        'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'score' => 'decimal:2',
            'manual_score' => 'decimal:2',
            'rubric_json' => 'array',
            'graded_at' => 'datetime',
            'metadata_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function effectiveScore(): ?float
    {
        if ($this->manual_score !== null) {
            return (float) $this->manual_score;
        }

        return $this->score !== null ? (float) $this->score : null;
    }

    public function requiresManualGrading(): bool
    {
        return $this->examQuestion?->type === QuestionType::Essay;
    }

    /**
     * @return BelongsTo<ExamAttempt, $this>
     */
    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    /**
     * @return BelongsTo<ExamDefinition, $this>
     */
    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }

    /**
     * @return BelongsTo<ExamQuestion, $this>
     */
    public function examQuestion(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class);
    }
}
