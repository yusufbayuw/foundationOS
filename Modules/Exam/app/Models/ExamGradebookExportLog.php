<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;
use Modules\Exam\Enums\GradebookExportStatus;
use Modules\Exam\Enums\GradebookExportTargetModule;

class ExamGradebookExportLog extends ExamModel
{
    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exam_result_id',
        'exam_participant_id',
        'target_module',
        'target_reference_type',
        'target_reference_id',
        'status',
        'error_message',
        'pushed_by',
        'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'target_module' => GradebookExportTargetModule::class,
            'status' => GradebookExportStatus::class,
            'target_reference_id' => 'integer',
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
     * @return BelongsTo<ExamResult, $this>
     */
    public function examResult(): BelongsTo
    {
        return $this->belongsTo(ExamResult::class);
    }

    /**
     * @return BelongsTo<ExamParticipant, $this>
     */
    public function examParticipant(): BelongsTo
    {
        return $this->belongsTo(ExamParticipant::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function pushedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pushed_by');
    }
}
