<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends ExamModel
{
    protected $table = 'exam_definition_results';

    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exam_participant_id',
        'exam_attempt_id',
        'runtime_result_id',
        'score',
        'max_score',
        'is_passed',
        'grade_letter',
        'status',
        'percentage',
        'suspicious_activity_count',
        'submitted_at',
        'analytics_json',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'is_passed' => 'boolean',
            'suspicious_activity_count' => 'integer',
            'submitted_at' => 'datetime',
            'analytics_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ExamDefinition, $this>
     */
    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }

    /**
     * @return BelongsTo<ExamParticipant, $this>
     */
    public function examParticipant(): BelongsTo
    {
        return $this->belongsTo(ExamParticipant::class);
    }

    /**
     * @return BelongsTo<ExamAttempt, $this>
     */
    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }
}
