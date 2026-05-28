<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamParticipant extends ExamModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'display_name',
        'email',
        'participant_code',
        'context_reference_type',
        'context_reference_id',
        'context_reference_uuid',
        'metadata_json',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'metadata_json' => 'array',
        ];
    }

    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }

    public function examTokens(): HasMany
    {
        return $this->hasMany(ExamToken::class);
    }

    public function examAttemptSyncs(): HasMany
    {
        return $this->hasMany(ExamAttemptSync::class);
    }
}
