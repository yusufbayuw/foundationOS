<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamPublishSnapshot extends ExamModel
{
    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'version',
        'payload_json',
        'runtime_exam_id',
        'publish_status',
        'error_message',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'payload_json' => 'array',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ExamDefinition, $this>
     */
    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }
}
