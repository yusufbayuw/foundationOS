<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\User;

class ExamManualScore extends ExamModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exam_participant_id',
        'graded_by',
        'score',
        'feedback',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
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

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
