<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExamAttempt extends ExamModel
{
    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exam_participant_id',
        'runtime_attempt_id',
        'attempt_number',
        'status',
        'score',
        'started_at',
        'submitted_at',
        'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'attempt_number' => 'integer',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'metadata_json' => 'array',
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
     * @return HasMany<ExamAnswer, $this>
     */
    public function examAnswers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    /**
     * @return HasOne<ExamResult, $this>
     */
    public function examResult(): HasOne
    {
        return $this->hasOne(ExamResult::class);
    }

    /**
     * @return HasMany<ExamActivityLog, $this>
     */
    public function examActivityLogs(): HasMany
    {
        return $this->hasMany(ExamActivityLog::class);
    }
}
