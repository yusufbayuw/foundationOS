<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamToken extends ExamModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'exam_participant_id',
        'token',
        'expires_at',
        'is_active',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ExamParticipant, $this>
     */
    public function examParticipant(): BelongsTo
    {
        return $this->belongsTo(ExamParticipant::class);
    }
}
