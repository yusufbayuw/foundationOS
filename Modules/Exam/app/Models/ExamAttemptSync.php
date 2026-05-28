<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttemptSync extends ExamModel
{
    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exam_participant_id',
        'runtime_attempt_id',
        'sync_status',
        'score',
        'result_json',
        'submitted_at',
        'synced_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'result_json' => 'array',
            'submitted_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }

    public function examParticipant(): BelongsTo
    {
        return $this->belongsTo(ExamParticipant::class);
    }
}
