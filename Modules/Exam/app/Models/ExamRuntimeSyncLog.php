<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Exam\Enums\ExamRuntimeSyncAction;
use Modules\Exam\Enums\ExamRuntimeSyncStatus;

class ExamRuntimeSyncLog extends ExamModel
{
    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'action',
        'status',
        'runtime_id',
        'request_summary',
        'response_summary',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'action' => ExamRuntimeSyncAction::class,
            'status' => ExamRuntimeSyncStatus::class,
            'request_summary' => 'array',
            'response_summary' => 'array',
        ];
    }

    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }
}
